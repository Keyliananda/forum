<?php

use App\Forum\Discourse\Models\Argument;
use App\Forum\Discourse\Models\ArgumentQualityVote;
use App\Forum\Discourse\Models\Claim;
use App\Forum\Discourse\Models\Evidence;
use App\Forum\Discourse\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders argument quality and basic claim robustness on a discussion', function () {
    $position = Position::factory()->create();
    $claim = Claim::factory()->for($position->discussion)->create([
        'statement' => 'Wärmepumpen sind effizient.',
    ]);
    $position->claims()->attach($claim);
    $support = Argument::factory()->for($position->discussion)->for($claim)->create([
        'type' => 'support',
        'body' => 'Praxisdaten stützen die Effizienzaussage.',
    ]);
    Argument::factory()->for($position->discussion)->for($claim)->create([
        'type' => 'oppose',
        'body' => 'Einwände bestehen bei hohen Vorlauftemperaturen.',
    ]);
    Evidence::factory()->for($claim)->create(['verification_status' => 'verified']);
    ArgumentQualityVote::factory()->for($support, 'argument')->create([
        'clarity' => 5,
        'relevance' => 4,
        'logic' => 4,
        'source_usage' => 5,
        'fairness' => 4,
        'rebuttal_strength' => 3,
    ]);

    $this->get(route('forum.discussions.show', $position->discussion))
        ->assertOk()
        ->assertSee('Robustheit')
        ->assertSee('1 Pro')
        ->assertSee('1 Contra')
        ->assertSee('1 verifizierte Quelle')
        ->assertSee('Argumentqualität')
        ->assertSee('4.2');
});

it('lets an authenticated user rate argument quality once and update the vote', function () {
    $user = User::factory()->create();
    $argument = Argument::factory()->create();

    $this->actingAs($user)->post(route('forum.arguments.quality.store', $argument), [
        'clarity' => 5,
        'relevance' => 4,
        'logic' => 4,
        'source_usage' => 3,
        'fairness' => 5,
        'rebuttal_strength' => 2,
        'note' => 'Klar, aber Quellen könnten stärker sein.',
    ])->assertRedirect(route('forum.discussions.show', $argument->discussion));

    $this->actingAs($user)->post(route('forum.arguments.quality.store', $argument), [
        'clarity' => 4,
        'relevance' => 4,
        'logic' => 4,
        'source_usage' => 4,
        'fairness' => 4,
        'rebuttal_strength' => 4,
        'note' => 'Nach Quellenprüfung ausgewogener.',
    ])->assertRedirect(route('forum.discussions.show', $argument->discussion));

    $vote = ArgumentQualityVote::query()->firstWhere('argument_id', $argument->id);

    expect(ArgumentQualityVote::query()->where('argument_id', $argument->id)->count())->toBe(1)
        ->and($vote?->user_id)->toBe($user->id)
        ->and($vote?->clarity)->toBe(4)
        ->and($vote?->note)->toBe('Nach Quellenprüfung ausgewogener.');
});

it('requires authentication to rate argument quality', function () {
    $argument = Argument::factory()->create();

    $this->post(route('forum.arguments.quality.store', $argument), [
        'clarity' => 5,
        'relevance' => 4,
        'logic' => 4,
        'source_usage' => 3,
        'fairness' => 5,
        'rebuttal_strength' => 2,
    ])->assertRedirect(route('login'));

    expect(ArgumentQualityVote::query()->count())->toBe(0);
});
