<?php

use App\Forum\Discourse\Models\Argument;
use App\Forum\Discourse\Models\ArgumentQualityVote;
use App\Forum\Discourse\Models\Claim;
use App\Forum\Discourse\Models\DiscussionResultSnapshot;
use App\Forum\Discourse\Models\Evidence;
use App\Forum\Discourse\Models\Position;
use App\Forum\Discourse\Models\ResultProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('computes and renders a default discussion result snapshot', function () {
    $position = Position::factory()->create(['title' => 'Ja, Wärmepumpen sind sinnvoll.']);
    $claim = Claim::factory()->for($position->discussion)->create();
    $position->claims()->attach($claim);
    $argument = Argument::factory()->for($position->discussion)->for($claim)->create(['type' => 'support']);
    Evidence::factory()->for($claim)->create(['verification_status' => 'verified']);
    ArgumentQualityVote::factory()->for($argument, 'argument')->create([
        'clarity' => 5,
        'relevance' => 5,
        'logic' => 4,
        'source_usage' => 5,
        'fairness' => 4,
        'rebuttal_strength' => 3,
    ]);

    $this->post(route('forum.discussions.results.recompute', $position->discussion))
        ->assertRedirect(route('forum.discussions.show', $position->discussion));

    $snapshot = DiscussionResultSnapshot::query()->firstWhere('discussion_id', $position->discussion_id);

    expect($snapshot)
        ->not->toBeNull()
        ->and($snapshot->profile_key)->toBe('balanced')
        ->and($snapshot->position_scores)->not->toBeEmpty();

    $this->get(route('forum.discussions.show', $position->discussion))
        ->assertOk()
        ->assertSee('Ergebnis')
        ->assertSee('balanced')
        ->assertSee('Ja, Wärmepumpen sind sinnvoll.');
});

it('lets an authenticated user create a personal result profile', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('forum.result-profiles.store'), [
        'name' => 'Quellen zuerst',
        'weights' => [
            'argument_quality' => 20,
            'source_quality' => 50,
            'community' => 10,
            'reputation' => 10,
            'external_signals' => 10,
        ],
    ]);

    $profile = ResultProfile::query()->firstWhere('name', 'Quellen zuerst');

    expect($profile)
        ->not->toBeNull()
        ->and($profile->user_id)->toBe($user->id)
        ->and($profile->weights['source_quality'])->toBe(50);

    $response->assertRedirect();
});

it('requires authentication to create personal result profiles', function () {
    $this->post(route('forum.result-profiles.store'), [
        'name' => 'Mein Profil',
        'weights' => [
            'argument_quality' => 20,
            'source_quality' => 20,
            'community' => 20,
            'reputation' => 20,
            'external_signals' => 20,
        ],
    ])->assertRedirect(route('login'));

    expect(ResultProfile::query()->count())->toBe(0);
});
