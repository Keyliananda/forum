<?php

use App\Http\Controllers\Forum\DiscussionShowController;
use App\Http\Controllers\Forum\StoreDiscussionController;
use App\Http\Controllers\Forum\StoreDiscussionReplyController;
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
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
