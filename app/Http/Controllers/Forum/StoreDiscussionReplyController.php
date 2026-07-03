<?php

namespace App\Http\Controllers\Forum;

use App\Forum\Discourse\Models\Discussion;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StoreDiscussionReplyController extends Controller
{
    public function __invoke(Request $request, Discussion $discussion): RedirectResponse
    {
        if (! $discussion->isOpen()) {
            throw ValidationException::withMessages([
                'body' => 'Diese Diskussion ist geschlossen.',
            ]);
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'min:3', 'max:5000'],
        ]);

        $discussion->replies()->create([
            'author_id' => $request->user()?->id,
            'body' => $data['body'],
            'status' => 'visible',
        ]);

        $discussion->forceFill(['last_replied_at' => now()])->save();

        return redirect()->route('forum.discussions.show', $discussion);
    }
}
