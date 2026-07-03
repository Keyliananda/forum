<?php

use App\Http\Controllers\Forum\DiscussionShowController;
use App\Http\Controllers\Forum\StoreArgumentEvidenceController;
use App\Http\Controllers\Forum\StoreArgumentModerationActionController;
use App\Http\Controllers\Forum\StoreArgumentQualityVoteController;
use App\Http\Controllers\Forum\StoreArgumentRebuttalController;
use App\Http\Controllers\Forum\StoreArgumentReportController;
use App\Http\Controllers\Forum\StoreClaimArgumentController;
use App\Http\Controllers\Forum\StoreClaimEvidenceController;
use App\Http\Controllers\Forum\StoreDiscussionController;
use App\Http\Controllers\Forum\StoreDiscussionReplyController;
use App\Http\Controllers\Forum\StoreDiscussionReportController;
use App\Http\Controllers\Forum\StoreEvidenceVerificationController;
use App\Http\Controllers\Forum\StorePositionClaimController;
use App\Http\Controllers\Forum\StorePositionController;
use App\Http\Controllers\Forum\TopicIndexController;
use App\Http\Controllers\Forum\TopicShowController;
use Illuminate\Support\Facades\Route;

Route::get('/', TopicIndexController::class)->name('home');
Route::get('topics', TopicIndexController::class)->name('forum.topics.index');
Route::get('topics/{topic:path}', TopicShowController::class)
    ->where('topic', '.*')
    ->name('forum.topics.show');
Route::get('discussions/{discussion:slug}', DiscussionShowController::class)->name('forum.discussions.show');

Route::middleware(['auth'])->group(function () {
    Route::post('discussions', StoreDiscussionController::class)->name('forum.discussions.store');
    Route::post('discussions/{discussion:slug}/replies', StoreDiscussionReplyController::class)->name('forum.discussions.replies.store');
    Route::post('discussions/{discussion:slug}/reports', StoreDiscussionReportController::class)->name('forum.discussions.reports.store');
    Route::post('discussions/{discussion:slug}/positions', StorePositionController::class)->name('forum.discussions.positions.store');
    Route::post('positions/{position}/claims', StorePositionClaimController::class)->name('forum.positions.claims.store');
    Route::post('claims/{claim}/arguments', StoreClaimArgumentController::class)->name('forum.claims.arguments.store');
    Route::post('arguments/{argument}/rebuttals', StoreArgumentRebuttalController::class)->name('forum.arguments.rebuttals.store');
    Route::post('arguments/{argument}/quality', StoreArgumentQualityVoteController::class)->name('forum.arguments.quality.store');
    Route::post('arguments/{argument}/reports', StoreArgumentReportController::class)->name('forum.arguments.reports.store');
    Route::post('arguments/{argument}/moderation', StoreArgumentModerationActionController::class)->name('forum.arguments.moderation.store');
    Route::post('claims/{claim}/evidence', StoreClaimEvidenceController::class)->name('forum.claims.evidence.store');
    Route::post('arguments/{argument}/evidence', StoreArgumentEvidenceController::class)->name('forum.arguments.evidence.store');
    Route::post('evidence/{evidence}/verifications', StoreEvidenceVerificationController::class)->name('forum.evidence.verifications.store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
