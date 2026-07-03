<?php

namespace App\Http\Controllers\Forum;

use App\Forum\Discourse\Models\Argument;
use App\Forum\Reputation\Models\ReputationEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreArgumentReputationEventController extends Controller
{
    public function __invoke(Request $request, Argument $argument): RedirectResponse
    {
        $data = $request->validate([
            'dimension' => ['required', 'string', Rule::in(['argument_quality', 'source_quality', 'fairness', 'correction'])],
            'points' => ['required', 'integer', 'between:-10,10'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        if ($argument->author_id !== null) {
            ReputationEvent::query()->create([
                'recipient_user_id' => $argument->author_id,
                'actor_user_id' => $request->user()?->id,
                'reputable_type' => Argument::class,
                'reputable_id' => $argument->id,
                'dimension' => $data['dimension'],
                'points' => $data['points'],
                'reason' => $data['reason'],
            ]);
        }

        return redirect()->route('forum.discussions.show', $argument->discussion);
    }
}
