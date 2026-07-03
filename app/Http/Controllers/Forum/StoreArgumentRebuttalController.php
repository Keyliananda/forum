<?php

namespace App\Http\Controllers\Forum;

use App\Forum\Discourse\Models\Argument;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreArgumentRebuttalController extends Controller
{
    public function __invoke(Request $request, Argument $argument): RedirectResponse
    {
        $request->validate([
            'body' => ['required', 'string', 'min:8', 'max:5000'],
        ]);

        Argument::query()->create([
            'discussion_id' => $argument->discussion_id,
            'claim_id' => $argument->claim_id,
            'parent_id' => $argument->id,
            'author_id' => $request->user()?->id,
            'type' => 'rebut',
            'body' => $request->string('body')->toString(),
            'status' => 'open',
        ]);

        return redirect()->route('forum.discussions.show', $argument->discussion);
    }
}
