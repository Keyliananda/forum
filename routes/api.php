<?php

use App\Http\Controllers\Api\Forum\DiscussionExportController;
use App\Http\Controllers\Api\Forum\DiscussionShowController;
use Illuminate\Support\Facades\Route;

Route::prefix('forum')->name('api.forum.')->group(function (): void {
    Route::get('discussions/{discussion:slug}', DiscussionShowController::class)->name('discussions.show');
    Route::get('discussions/{discussion:slug}/export', DiscussionExportController::class)->name('discussions.export');
});
