<?php

use App\Forum\Discourse\Models\Argument;
use App\Forum\Discourse\Models\Claim;
use App\Forum\Discourse\Models\Position;
use App\Forum\Reputation\Models\ReputationEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('records reputation events as an append only ledger', function () {
    $moderator = User::factory()->create();
    $recipient = User::factory()->create();
    $argument = Argument::factory()->create(['author_id' => $recipient->id]);

    $response = $this->actingAs($moderator)->post(route('forum.arguments.reputation.store', $argument), [
        'dimension' => 'argument_quality',
        'points' => 8,
        'reason' => 'Das Argument ist klar, fair und gut belegt.',
    ]);

    $event = ReputationEvent::query()->firstWhere('recipient_user_id', $recipient->id);

    expect($event)
        ->not->toBeNull()
        ->and($event->actor_user_id)->toBe($moderator->id)
        ->and($event->reputable_type)->toBe(Argument::class)
        ->and($event->reputable_id)->toBe($argument->id)
        ->and($event->dimension)->toBe('argument_quality')
        ->and($event->points)->toBe(8);

    $response->assertRedirect(route('forum.discussions.show', $argument->discussion));
});

it('renders a user reputation summary on authored arguments', function () {
    $author = User::factory()->create(['name' => 'Ada']);
    $position = Position::factory()->create();
    $claim = Claim::factory()->for($position->discussion)->create();
    $position->claims()->attach($claim);
    $argument = Argument::factory()->for($position->discussion)->for($claim)->create([
        'author_id' => $author->id,
        'body' => 'Ein sehr gutes Argument.',
    ]);

    ReputationEvent::factory()->create([
        'recipient_user_id' => $author->id,
        'points' => 8,
        'dimension' => 'argument_quality',
    ]);
    ReputationEvent::factory()->create([
        'recipient_user_id' => $author->id,
        'points' => 4,
        'dimension' => 'source_quality',
    ]);

    $this->get(route('forum.discussions.show', $argument->discussion))
        ->assertOk()
        ->assertSee('Ada')
        ->assertSee('Reputation 12');
});

it('requires authentication to award reputation', function () {
    $argument = Argument::factory()->create();

    $this->post(route('forum.arguments.reputation.store', $argument), [
        'dimension' => 'argument_quality',
        'points' => 5,
        'reason' => 'Guter Beitrag.',
    ])->assertRedirect(route('login'));

    expect(ReputationEvent::query()->count())->toBe(0);
});
