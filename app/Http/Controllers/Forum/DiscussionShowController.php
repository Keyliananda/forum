<?php

namespace App\Http\Controllers\Forum;

use App\Forum\Discourse\Models\Discussion;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class DiscussionShowController extends Controller
{
    public function __invoke(Discussion $discussion): View
    {
        return view('pages.forum.discussions.show', [
            'discussion' => $discussion->load([
                'topic.parent',
                'author',
                'positions.claims.arguments.children',
            ]),
            'replies' => $discussion->replies()
                ->with(['author'])
                ->where('status', 'visible')
                ->get(),
        ]);
    }
}
