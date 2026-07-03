<?php

use App\Forum\Discourse\Models\Discussion;
use App\Forum\Discourse\Models\DiscussionReply;
use App\Forum\Discourse\Models\Space;
use App\Forum\Discourse\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders a public topic landscape for guests', function () {
    $space = Space::factory()->create(['name' => 'Public Forum', 'slug' => 'public']);
    $topic = Topic::factory()->for($space)->create([
        'name' => 'Wärmepumpen',
        'slug' => 'waermepumpen',
        'description' => 'Diskurse zu Energie, Gebäuden und Effizienz.',
    ]);

    Discussion::factory()->for($space)->for($topic)->count(2)->create();

    $this->get(route('forum.topics.index'))
        ->assertOk()
        ->assertSee('Themenlandschaft')
        ->assertSee('Wärmepumpen')
        ->assertSee('2 Diskussionen')
        ->assertSee('Diskurse zu Energie, Gebäuden und Effizienz.');
});

it('renders a topic with its public discussions', function () {
    $space = Space::factory()->create(['name' => 'Public Forum', 'slug' => 'public']);
    $parent = Topic::factory()->for($space)->create([
        'name' => 'Energie',
        'slug' => 'energie',
    ]);
    $topic = Topic::factory()->for($space)->childOf($parent)->create([
        'name' => 'Wärmepumpen',
        'slug' => 'waermepumpen',
    ]);
    $discussion = Discussion::factory()->for($space)->for($topic)->create([
        'title' => 'Sind Wärmepumpen im Gebäudebestand sinnvoll?',
        'slug' => 'waermepumpen-im-bestand',
        'core_question' => 'Sind Wärmepumpen im Gebäudebestand sinnvoll?',
    ]);

    $this->get(route('forum.topics.show', $topic))
        ->assertOk()
        ->assertSee('Energie')
        ->assertSee('Wärmepumpen')
        ->assertSee($discussion->title)
        ->assertSee($discussion->core_question);
});

it('resolves nested topics by their full path instead of a global slug', function () {
    $space = Space::factory()->create();
    $energy = Topic::factory()->for($space)->create(['name' => 'Energie', 'slug' => 'energie']);
    $society = Topic::factory()->for($space)->create(['name' => 'Gesellschaft', 'slug' => 'gesellschaft']);
    $energyDebate = Topic::factory()->for($space)->childOf($energy)->create(['name' => 'Debatte', 'slug' => 'debatte']);
    $societyDebate = Topic::factory()->for($space)->childOf($society)->create(['name' => 'Debatte', 'slug' => 'debatte']);

    Discussion::factory()->for($space)->for($energyDebate)->create([
        'title' => 'Energie-Debatte',
        'slug' => 'energie-debatte',
        'core_question' => 'Wie bewerten wir Energiepolitik?',
    ]);
    Discussion::factory()->for($space)->for($societyDebate)->create([
        'title' => 'Gesellschafts-Debatte',
        'slug' => 'gesellschafts-debatte',
        'core_question' => 'Wie bewerten wir Gesellschaftspolitik?',
    ]);

    $this->get(route('forum.topics.show', $societyDebate))
        ->assertOk()
        ->assertSee('Gesellschafts-Debatte')
        ->assertDontSee('Energie-Debatte');
});

it('renders a discussion with replies and core question', function () {
    $space = Space::factory()->create();
    $topic = Topic::factory()->for($space)->create(['name' => 'Pianouniverse']);
    $discussion = Discussion::factory()->for($space)->for($topic)->create([
        'title' => 'Soll Pianouniverse strukturierte Diskurse anbieten?',
        'core_question' => 'Soll Pianouniverse strukturierte Diskurse zu Übemethoden anbieten?',
        'body' => 'Wir wollen Erfahrungsberichte besser begründen.',
    ]);
    DiscussionReply::factory()->for($discussion)->create([
        'body' => 'Ja, wenn Quellen und Erfahrungen getrennt sichtbar bleiben.',
    ]);

    $this->get(route('forum.discussions.show', $discussion))
        ->assertOk()
        ->assertSee($discussion->title)
        ->assertSee($discussion->core_question)
        ->assertSee('Wir wollen Erfahrungsberichte besser begründen.')
        ->assertSee('Ja, wenn Quellen und Erfahrungen getrennt sichtbar bleiben.');
});

it('requires authentication to create a discussion', function () {
    $space = Space::factory()->create();
    $topic = Topic::factory()->for($space)->create();

    $this->post(route('forum.discussions.store'), [
        'topic_id' => $topic->id,
        'title' => 'Neue Kernfrage',
        'core_question' => 'Ist das eine sinnvolle Kernfrage?',
        'body' => 'Ein kurzer Kontext.',
    ])->assertRedirect(route('login'));
});

it('requires authentication to reply to a discussion', function () {
    $discussion = Discussion::factory()->create();

    $this->post(route('forum.discussions.replies.store', $discussion), [
        'body' => 'Dieser Beitrag darf ohne Login nicht gespeichert werden.',
    ])->assertRedirect(route('login'));

    expect($discussion->replies()->count())->toBe(0);
});

it('lets an authenticated user create a discussion in a public topic', function () {
    $user = User::factory()->create();
    $space = Space::factory()->create();
    $topic = Topic::factory()->for($space)->create();

    $response = $this->actingAs($user)->post(route('forum.discussions.store'), [
        'topic_id' => $topic->id,
        'title' => 'Sind Wärmepumpen effizient?',
        'core_question' => 'Sind Wärmepumpen im Bestand effizient?',
        'body' => 'Ich möchte die Aussage mit Quellen und Gegenargumenten prüfen.',
    ]);

    $discussion = Discussion::query()->firstWhere('title', 'Sind Wärmepumpen effizient?');

    expect($discussion)
        ->not->toBeNull()
        ->and($discussion->space_id)->toBe($space->id)
        ->and($discussion->topic_id)->toBe($topic->id)
        ->and($discussion->author_id)->toBe($user->id)
        ->and($discussion->slug)->toBe('sind-warmepumpen-effizient');

    $response->assertRedirect(route('forum.discussions.show', $discussion));
});

it('keeps discussion permalinks globally unique while creating discussions', function () {
    $user = User::factory()->create();
    $space = Space::factory()->create();
    $firstTopic = Topic::factory()->for($space)->create(['name' => 'Energie']);
    $secondTopic = Topic::factory()->for($space)->create(['name' => 'Gesellschaft']);

    $payload = [
        'title' => 'Ist diese Kernfrage eindeutig?',
        'core_question' => 'Ist diese Kernfrage eindeutig genug?',
        'body' => 'Gleiche Titel in unterschiedlichen Topics dürfen URLs nicht kollidieren lassen.',
    ];

    $this->actingAs($user)->post(route('forum.discussions.store'), [
        ...$payload,
        'topic_id' => $firstTopic->id,
    ])->assertRedirect();

    $this->actingAs($user)->post(route('forum.discussions.store'), [
        ...$payload,
        'topic_id' => $secondTopic->id,
    ])->assertRedirect();

    expect(Discussion::query()->pluck('slug')->all())->toContain(
        'ist-diese-kernfrage-eindeutig',
        'ist-diese-kernfrage-eindeutig-2',
    );
});

it('falls back to a stable discussion slug when a title cannot be transliterated', function () {
    $user = User::factory()->create();
    $space = Space::factory()->create();
    $topic = Topic::factory()->for($space)->create();

    $this->actingAs($user)->post(route('forum.discussions.store'), [
        'topic_id' => $topic->id,
        'title' => '------',
        'core_question' => 'Ist diese Frage trotz Titel gültig?',
        'body' => 'Der Slug darf nicht leer sein.',
    ])->assertRedirect();

    expect(Discussion::query()->firstWhere('title', '------')?->slug)->toBe('discussion');
});

it('lets an authenticated user reply to an open discussion', function () {
    $user = User::factory()->create();
    $discussion = Discussion::factory()->create();

    $response = $this->actingAs($user)->post(route('forum.discussions.replies.store', $discussion), [
        'body' => 'Das ist ein erster Einwand, der später strukturiert werden kann.',
    ]);

    expect($discussion->replies()->first())
        ->body->toBe('Das ist ein erster Einwand, der später strukturiert werden kann.')
        ->author_id->toBe($user->id);

    $response->assertRedirect(route('forum.discussions.show', $discussion));
});
