<?php

namespace App\Http\Controllers\Forum;

use App\Forum\Discourse\Models\Discussion;
use App\Forum\Discourse\Models\Topic;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class TopicIndexController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.forum.topics.index', [
            'topics' => Topic::query()
                ->whereNull('parent_id')
                ->withCount('discussions')
                ->with(['children' => fn ($query) => $query->withCount('discussions')])
                ->orderBy('name')
                ->get(),
            'recentDiscussions' => Discussion::query()
                ->with(['topic', 'author'])
                ->where('status', 'open')
                ->latest('last_replied_at')
                ->limit(6)
                ->get(),
        ]);
    }
}
