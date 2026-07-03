<?php

namespace App\Http\Controllers\Forum;

use App\Forum\Discourse\Models\Argument;
use App\Forum\Discourse\Models\ArgumentQualityVote;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreArgumentQualityVoteController extends Controller
{
    public function __invoke(Request $request, Argument $argument): RedirectResponse
    {
        $data = $request->validate([
            'clarity' => ['required', 'integer', 'between:1,5'],
            'relevance' => ['required', 'integer', 'between:1,5'],
            'logic' => ['required', 'integer', 'between:1,5'],
            'source_usage' => ['required', 'integer', 'between:1,5'],
            'fairness' => ['required', 'integer', 'between:1,5'],
            'rebuttal_strength' => ['required', 'integer', 'between:1,5'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        ArgumentQualityVote::query()->updateOrCreate(
            [
                'argument_id' => $argument->id,
                'user_id' => $request->user()?->id,
            ],
            [
                'clarity' => $data['clarity'],
                'relevance' => $data['relevance'],
                'logic' => $data['logic'],
                'source_usage' => $data['source_usage'],
                'fairness' => $data['fairness'],
                'rebuttal_strength' => $data['rebuttal_strength'],
                'note' => $data['note'] ?? null,
            ],
        );

        return redirect()->route('forum.discussions.show', $argument->discussion);
    }
}
