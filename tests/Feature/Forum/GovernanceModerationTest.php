<?php

use App\Forum\Discourse\Models\Argument;
use App\Forum\Discourse\Models\Discussion;
use App\Forum\Discourse\Models\ModerationAction;
use App\Forum\Discourse\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders a discussion governance profile publicly', function () {
    $discussion = Discussion::factory()->create([
        'governance_profile' => 'open_democratic',
    ]);

    $this->get(route('forum.discussions.show', $discussion))
        ->assertOk()
        ->assertSee('Governance')
        ->assertSee('open_democratic');
});

it('lets an authenticated user report a discussion', function () {
    $user = User::factory()->create();
    $discussion = Discussion::factory()->create();

    $response = $this->actingAs($user)->post(route('forum.discussions.reports.store', $discussion), [
        'reason' => 'missing_source',
        'details' => 'Die zentrale Behauptung braucht eine Quelle.',
    ]);

    $report = Report::query()->firstWhere('reason', 'missing_source');

    expect($report)
        ->not->toBeNull()
        ->and($report->reporter_id)->toBe($user->id)
        ->and($report->reportable_type)->toBe(Discussion::class)
        ->and($report->reportable_id)->toBe($discussion->id)
        ->and($report->status)->toBe('open');

    $response->assertRedirect(route('forum.discussions.show', $discussion));
});

it('requires authentication to report content', function () {
    $argument = Argument::factory()->create();

    $this->post(route('forum.arguments.reports.store', $argument), [
        'reason' => 'abusive',
        'details' => 'Bitte prüfen.',
    ])->assertRedirect(route('login'));

    expect(Report::query()->count())->toBe(0);
});

it('lets an authenticated user record a moderation action and hide an argument', function () {
    $moderator = User::factory()->create();
    $argument = Argument::factory()->create(['status' => 'open']);

    $response = $this->actingAs($moderator)->post(route('forum.arguments.moderation.store', $argument), [
        'action' => 'hide',
        'public_reason' => 'Verstoß gegen Diskursregeln.',
        'internal_note' => 'Temporär ausblenden bis Quellen nachgereicht wurden.',
    ]);

    $action = ModerationAction::query()->firstWhere('action', 'hide');

    expect($action)
        ->not->toBeNull()
        ->and($action->moderator_id)->toBe($moderator->id)
        ->and($action->actionable_type)->toBe(Argument::class)
        ->and($action->actionable_id)->toBe($argument->id)
        ->and($argument->refresh()->status)->toBe('hidden');

    $response->assertRedirect(route('forum.discussions.show', $argument->discussion));
});
