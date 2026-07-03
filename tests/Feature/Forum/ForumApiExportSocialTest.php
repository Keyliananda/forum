<?php

use App\Forum\Discourse\Models\Discussion;
use App\Forum\Social\Models\ExternalSignal;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('exposes a public read api for discussions', function () {
    $discussion = Discussion::factory()->create([
        'title' => 'Soll strukturierter Diskurs öffentlich sein?',
    ]);

    $this->getJson(route('api.forum.discussions.show', $discussion))
        ->assertOk()
        ->assertJsonPath('data.title', 'Soll strukturierter Diskurs öffentlich sein?')
        ->assertJsonPath('data.slug', $discussion->slug)
        ->assertJsonStructure(['data' => ['id', 'title', 'core_question', 'topic', 'positions']]);
});

it('exports a discussion as canonical json', function () {
    $discussion = Discussion::factory()->create();

    $this->getJson(route('api.forum.discussions.export', $discussion))
        ->assertOk()
        ->assertJsonPath('schema', 'forum.canonical.v1')
        ->assertJsonPath('discussion.slug', $discussion->slug);
});

it('stores external social signals separately from internal quality', function () {
    $discussion = Discussion::factory()->create();

    ExternalSignal::factory()->create([
        'discussion_id' => $discussion->id,
        'platform' => 'instagram',
        'label' => 'Instagram Stimmung',
        'up_count' => 120,
        'down_count' => 30,
        'comment_count' => 12,
    ]);

    $this->get(route('forum.discussions.show', $discussion))
        ->assertOk()
        ->assertSee('Externe Stimmung')
        ->assertSee('Instagram Stimmung')
        ->assertSee('120')
        ->assertSee('30');
});
