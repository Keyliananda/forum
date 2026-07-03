<?php

use Illuminate\Support\Facades\File;

it('documents the phase zero domain language and demo seeds', function () {
    expect(base_path('docs/domain-glossary.md'))->toBeFile()
        ->and(base_path('docs/demo-discussions.md'))->toBeFile()
        ->and(base_path('docs/20260703-roadmap.md'))->toBeFile();

    $glossary = File::get(base_path('docs/domain-glossary.md'));

    expect($glossary)
        ->toContain('Topic')
        ->toContain('Discussion')
        ->toContain('Position')
        ->toContain('Claim')
        ->toContain('Argument')
        ->toContain('Evidence')
        ->toContain('Challenge')
        ->toContain('Rebuttal')
        ->toContain('Result Profile')
        ->toContain('External Signal');

    $demoDiscussions = File::get(base_path('docs/demo-discussions.md'));

    expect($demoDiscussions)
        ->toContain('Wärmepumpen')
        ->toContain('Feminismus')
        ->toContain('Pianouniverse');

    $roadmap = File::get(base_path('docs/20260703-roadmap.md'));

    expect($roadmap)
        ->toContain('Phase 0')
        ->toContain('Laravel 13')
        ->toContain('Forum-MVP')
        ->toContain('Package-Extraktion');
});

it('keeps the future package core separated from the Livewire UI shell', function () {
    $expectedDirectories = [
        app_path('Forum/Discourse/Actions'),
        app_path('Forum/Discourse/Contracts'),
        app_path('Forum/Discourse/Data'),
        app_path('Forum/Discourse/Events'),
        app_path('Forum/Discourse/Jobs'),
        app_path('Forum/Discourse/Models'),
        app_path('Forum/Discourse/Policies'),
        app_path('Forum/Discourse/Queries'),
        app_path('Forum/Discourse/Services'),
        app_path('Forum/Governance'),
        app_path('Forum/Reputation'),
        app_path('Forum/Sources'),
        app_path('Forum/Social'),
        app_path('Forum/Connectors'),
        app_path('Livewire/Forum'),
    ];

    foreach ($expectedDirectories as $directory) {
        expect($directory)->toBeDirectory();
        expect($directory.'/.gitkeep')->toBeFile();
    }
});

it('uses Pest as the project test runner', function () {
    $composer = json_decode(File::get(base_path('composer.json')), true);

    expect($composer['require-dev'])
        ->toHaveKey('pestphp/pest')
        ->toHaveKey('pestphp/pest-plugin-laravel');
});
