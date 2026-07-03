<?php

namespace App\Http\Controllers\Forum;

use App\Forum\Discourse\Models\Topic;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class TopicShowController extends Controller
{
    public function __invoke(Topic $topic): View
    {
        return view('pages.forum.topics.show', [
            'topic' => $topic->load(['parent', 'children']),
            'discussions' => $topic->discussions()
                ->with(['author'])
                ->where('status', 'open')
                ->withCount('replies')
                ->get(),
        ]);
    }
}
