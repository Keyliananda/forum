<?php

use App\Forum\Discourse\Models\Argument;
use App\Forum\Discourse\Models\Claim;
use App\Forum\Discourse\Models\Evidence;
use App\Forum\Discourse\Models\EvidenceVerification;
use App\Forum\Discourse\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders evidence and verification status in the structured discussion tree', function () {
    $position = Position::factory()->create(['title' => 'Ja, Wärmepumpen sind sinnvoll.']);
    $claim = Claim::factory()->for($position->discussion)->create([
        'statement' => 'Wärmepumpen sind effizient.',
    ]);
    $position->claims()->attach($claim);
    $argument = Argument::factory()->for($position->discussion)->for($claim)->create([
        'body' => 'Praxisdaten zeigen robuste Effizienzwerte.',
    ]);

    Evidence::factory()->for($claim)->create([
        'title' => 'Fraunhofer Wärmepumpen-Feldtest',
        'url' => 'https://example.org/fraunhofer-waermepumpen',
        'publisher' => 'Fraunhofer ISE',
        'excerpt' => 'Der Feldtest dokumentiert Effizienzwerte in Bestandsgebäuden.',
        'verification_status' => 'verified',
    ]);
    Evidence::factory()->for($argument)->create([
        'title' => 'Praxisbericht Gebäudehülle',
        'url' => 'https://example.org/gebaeudehuelle',
        'verification_status' => 'partially_verified',
    ]);

    $this->get(route('forum.discussions.show', $position->discussion))
        ->assertOk()
        ->assertSee('Quellen')
        ->assertSee('Fraunhofer Wärmepumpen-Feldtest')
        ->assertSee('Fraunhofer ISE')
        ->assertSee('verifiziert')
        ->assertSee('Praxisbericht Gebäudehülle')
        ->assertSee('teilweise verifiziert');
});

it('lets an authenticated user attach evidence to a claim', function () {
    $user = User::factory()->create();
    $claim = Claim::factory()->create();

    $response = $this->actingAs($user)->post(route('forum.claims.evidence.store', $claim), [
        'url' => 'https://example.org/study',
        'title' => 'Studie zur Effizienz',
        'publisher' => 'Example Institute',
        'excerpt' => 'Auszug mit relevanter Aussage.',
        'stance' => 'supports',
    ]);

    $evidence = Evidence::query()->firstWhere('title', 'Studie zur Effizienz');

    expect($evidence)
        ->not->toBeNull()
        ->and($evidence->claim_id)->toBe($claim->id)
        ->and($evidence->argument_id)->toBeNull()
        ->and($evidence->author_id)->toBe($user->id)
        ->and($evidence->verification_status)->toBe('unverified');

    $response->assertRedirect(route('forum.discussions.show', $claim->discussion));
});

it('lets an authenticated user attach evidence to an argument', function () {
    $user = User::factory()->create();
    $argument = Argument::factory()->create();

    $response = $this->actingAs($user)->post(route('forum.arguments.evidence.store', $argument), [
        'url' => 'https://example.org/report',
        'title' => 'Praxisreport',
        'stance' => 'contextualizes',
    ]);

    $evidence = Evidence::query()->firstWhere('title', 'Praxisreport');

    expect($evidence)
        ->not->toBeNull()
        ->and($evidence->argument_id)->toBe($argument->id)
        ->and($evidence->claim_id)->toBeNull()
        ->and($evidence->author_id)->toBe($user->id);

    $response->assertRedirect(route('forum.discussions.show', $argument->discussion));
});

it('lets an authenticated user verify evidence manually', function () {
    $user = User::factory()->create();
    $evidence = Evidence::factory()->create(['verification_status' => 'unverified']);

    $response = $this->actingAs($user)->post(route('forum.evidence.verifications.store', $evidence), [
        'status' => 'misquoted',
        'note' => 'Der Auszug ist erreichbar, aber die Aussage wird im Argument zu stark verwendet.',
    ]);

    $verification = EvidenceVerification::query()->firstWhere('evidence_id', $evidence->id);

    expect($verification)
        ->not->toBeNull()
        ->and($verification->verifier_id)->toBe($user->id)
        ->and($verification->status)->toBe('misquoted')
        ->and($evidence->refresh()->verification_status)->toBe('misquoted');

    $response->assertRedirect(route('forum.discussions.show', $evidence->discussion));
});

it('requires authentication to add or verify evidence', function () {
    $claim = Claim::factory()->create();
    $argument = Argument::factory()->create();
    $evidence = Evidence::factory()->create();

    $this->post(route('forum.claims.evidence.store', $claim), [
        'url' => 'https://example.org/study',
        'title' => 'Studie',
        'stance' => 'supports',
    ])->assertRedirect(route('login'));

    $this->post(route('forum.arguments.evidence.store', $argument), [
        'url' => 'https://example.org/report',
        'title' => 'Report',
        'stance' => 'supports',
    ])->assertRedirect(route('login'));

    $this->post(route('forum.evidence.verifications.store', $evidence), [
        'status' => 'verified',
    ])->assertRedirect(route('login'));
});
