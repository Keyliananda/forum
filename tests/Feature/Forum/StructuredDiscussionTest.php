<?php

use App\Forum\Discourse\Models\Argument;
use App\Forum\Discourse\Models\Claim;
use App\Forum\Discourse\Models\Discussion;
use App\Forum\Discourse\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders positions claims arguments and rebuttals on a discussion', function () {
    $discussion = Discussion::factory()->create([
        'title' => 'Sind Wärmepumpen im Gebäudebestand sinnvoll?',
        'core_question' => 'Sind Wärmepumpen im Gebäudebestand sinnvoll?',
    ]);
    $position = Position::factory()->for($discussion)->create([
        'title' => 'Ja, Wärmepumpen sind im Gebäudebestand sinnvoll.',
    ]);
    $claim = Claim::factory()->for($discussion)->create([
        'statement' => 'Wärmepumpen sind effizient.',
        'type' => 'factual',
    ]);
    $position->claims()->attach($claim, ['sort_order' => 1]);

    $argument = Argument::factory()->for($discussion)->for($claim)->create([
        'type' => 'support',
        'body' => 'Jahresarbeitszahlen zeigen, dass aus einer Kilowattstunde Strom mehrere Kilowattstunden Wärme werden.',
    ]);
    Argument::factory()->for($discussion)->for($claim)->for($argument, 'parent')->create([
        'type' => 'rebut',
        'body' => 'Das gilt nur, wenn Vorlauftemperatur und Gebäudehülle passen.',
    ]);

    $this->get(route('forum.discussions.show', $discussion))
        ->assertOk()
        ->assertSee('Positionen')
        ->assertSee('Ja, Wärmepumpen sind im Gebäudebestand sinnvoll.')
        ->assertSee('Wärmepumpen sind effizient.')
        ->assertSee('Pro')
        ->assertSee('Jahresarbeitszahlen zeigen')
        ->assertSee('Entkräftung')
        ->assertSee('Das gilt nur, wenn Vorlauftemperatur und Gebäudehülle passen.');
});

it('lets an authenticated user add a position to a discussion', function () {
    $user = User::factory()->create();
    $discussion = Discussion::factory()->create();

    $response = $this->actingAs($user)->post(route('forum.discussions.positions.store', $discussion), [
        'title' => 'Nur bei passenden baulichen Voraussetzungen.',
        'summary' => 'Die Sinnhaftigkeit hängt vom Gebäudezustand ab.',
    ]);

    $position = Position::query()->firstWhere('title', 'Nur bei passenden baulichen Voraussetzungen.');

    expect($position)
        ->not->toBeNull()
        ->and($position->discussion_id)->toBe($discussion->id)
        ->and($position->author_id)->toBe($user->id);

    $response->assertRedirect(route('forum.discussions.show', $discussion));
});

it('lets an authenticated user attach a claim to a position', function () {
    $user = User::factory()->create();
    $position = Position::factory()->create();

    $response = $this->actingAs($user)->post(route('forum.positions.claims.store', $position), [
        'statement' => 'Die Effizienz hängt stark von der Vorlauftemperatur ab.',
        'type' => 'causal',
    ]);

    $claim = Claim::query()->firstWhere('statement', 'Die Effizienz hängt stark von der Vorlauftemperatur ab.');

    expect($claim)
        ->not->toBeNull()
        ->and($claim->author_id)->toBe($user->id)
        ->and($position->claims()->whereKey($claim->id)->exists())->toBeTrue();

    $response->assertRedirect(route('forum.discussions.show', $position->discussion));
});

it('lets an authenticated user add a supporting argument to a claim', function () {
    $user = User::factory()->create();
    $position = Position::factory()->create();
    $claim = Claim::factory()->for($position->discussion)->create();
    $position->claims()->attach($claim);

    $response = $this->actingAs($user)->post(route('forum.claims.arguments.store', $claim), [
        'discussion_id' => $position->discussion_id,
        'type' => 'support',
        'body' => 'Messdaten aus Praxisanlagen zeigen robuste Effizienzwerte.',
    ]);

    $argument = Argument::query()->firstWhere('body', 'Messdaten aus Praxisanlagen zeigen robuste Effizienzwerte.');

    expect($argument)
        ->not->toBeNull()
        ->and($argument->claim_id)->toBe($claim->id)
        ->and($argument->discussion_id)->toBe($position->discussion_id)
        ->and($argument->author_id)->toBe($user->id);

    $response->assertRedirect(route('forum.discussions.show', $position->discussion));
});

it('lets an authenticated user rebut an argument', function () {
    $user = User::factory()->create();
    $argument = Argument::factory()->create(['type' => 'support']);

    $response = $this->actingAs($user)->post(route('forum.arguments.rebuttals.store', $argument), [
        'body' => 'Das Argument unterschlägt die Kosten bei unsanierten Altbauten.',
    ]);

    $rebuttal = Argument::query()->firstWhere('body', 'Das Argument unterschlägt die Kosten bei unsanierten Altbauten.');

    expect($rebuttal)
        ->not->toBeNull()
        ->and($rebuttal->parent_id)->toBe($argument->id)
        ->and($rebuttal->claim_id)->toBe($argument->claim_id)
        ->and($rebuttal->type)->toBe('rebut')
        ->and($rebuttal->author_id)->toBe($user->id);

    $response->assertRedirect(route('forum.discussions.show', $argument->discussion));
});

it('requires authentication to create structured discussion nodes', function () {
    $discussion = Discussion::factory()->create();
    $position = Position::factory()->for($discussion)->create();
    $claim = Claim::factory()->create();
    $argument = Argument::factory()->for($discussion)->for($claim)->create();

    $this->post(route('forum.discussions.positions.store', $discussion), [
        'title' => 'Eine neue Position',
    ])->assertRedirect(route('login'));

    $this->post(route('forum.positions.claims.store', $position), [
        'statement' => 'Eine neue Aussage',
        'type' => 'factual',
    ])->assertRedirect(route('login'));

    $this->post(route('forum.claims.arguments.store', $claim), [
        'discussion_id' => $discussion->id,
        'type' => 'support',
        'body' => 'Ein neues Argument',
    ])->assertRedirect(route('login'));

    $this->post(route('forum.arguments.rebuttals.store', $argument), [
        'body' => 'Eine neue Entkräftung',
    ])->assertRedirect(route('login'));
});
