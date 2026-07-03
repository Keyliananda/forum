<?php

namespace App\Http\Controllers\Forum;

use App\Forum\Discourse\Models\Argument;
use App\Forum\Discourse\Models\Claim;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreClaimArgumentController extends Controller
{
    public function __invoke(Request $request, Claim $claim): RedirectResponse
    {
        $request->validate([
            'discussion_id' => ['required', 'integer', Rule::exists('discussions', 'id')],
            'type' => ['required', 'string', Rule::in(['support', 'oppose'])],
            'body' => ['required', 'string', 'min:8', 'max:5000'],
        ]);

        Argument::query()->create([
            'discussion_id' => $claim->discussion_id,
            'claim_id' => $claim->id,
            'author_id' => $request->user()?->id,
            'type' => $request->string('type')->toString(),
            'body' => $request->string('body')->toString(),
            'status' => 'open',
        ]);

        return redirect()->route('forum.discussions.show', $claim->discussion);
    }
}
