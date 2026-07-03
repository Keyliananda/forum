<?php

use App\Forum\Discourse\Models\Discussion;
use App\Forum\Discourse\Models\DiscussionReply;
use App\Forum\Discourse\Models\Space;
use App\Forum\Discourse\Models\Topic;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds the public forum space with nested demo discussions', function () {
    $this->seed(DatabaseSeeder::class);

    $space = Space::query()->firstWhere('slug', 'public');

    expect($space)
        ->not->toBeNull()
        ->and($space->visibility)->toBe('public');

    $waermepumpen = Topic::query()->firstWhere('path', 'energie/gebaeude/warmepumpen');
    $feminismus = Topic::query()->firstWhere('path', 'gesellschaft/feminismus/gleichberechtigung-im-beruf');
    $pianouniverse = Topic::query()->firstWhere('path', 'pianouniverse/lernen/ueben');

    expect($waermepumpen)->not->toBeNull()
        ->and($feminismus)->not->toBeNull()
        ->and($pianouniverse)->not->toBeNull()
        ->and($waermepumpen->depth)->toBe(2)
        ->and($waermepumpen->parent?->name)->toBe('Gebäude');

    expect(Discussion::query()->where('core_question', 'Sind Wärmepumpen im Gebäudebestand sinnvoll?')->exists())->toBeTrue()
        ->and(Discussion::query()->where('core_question', 'Welche Massnahmen verbessern Gleichberechtigung im Beruf am wirksamsten?')->exists())->toBeTrue()
        ->and(Discussion::query()->where('core_question', 'Soll Pianouniverse strukturierte Diskurse zu Uebemethoden anbieten?')->exists())->toBeTrue()
        ->and(DiscussionReply::query()->count())->toBeGreaterThanOrEqual(3);
});

it('keeps demo seeding idempotent', function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    expect(Space::query()->where('slug', 'public')->count())->toBe(1)
        ->and(Topic::query()->where('path', 'energie/gebaeude/warmepumpen')->count())->toBe(1)
        ->and(Discussion::query()->where('slug', 'waermepumpen-im-gebaeudebestand')->count())->toBe(1);
});
