<?php

namespace App\Http\Controllers\Forum;

use App\Forum\Discourse\Models\Claim;
use App\Forum\Discourse\Models\Evidence;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreClaimEvidenceController extends Controller
{
    public function __invoke(Request $request, Claim $claim): RedirectResponse
    {
        $request->validate([
            'url' => ['nullable', 'url', 'max:2048'],
            'doi' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:2000'],
            'locator' => ['nullable', 'string', 'max:255'],
            'stance' => ['required', 'string', Rule::in(['supports', 'contradicts', 'contextualizes'])],
        ]);

        Evidence::query()->create([
            'discussion_id' => $claim->discussion_id,
            'claim_id' => $claim->id,
            'author_id' => $request->user()?->id,
            'url' => $request->string('url')->toString() ?: null,
            'doi' => $request->string('doi')->toString() ?: null,
            'title' => $request->string('title')->toString(),
            'publisher' => $request->string('publisher')->toString() ?: null,
            'excerpt' => $request->string('excerpt')->toString() ?: null,
            'locator' => $request->string('locator')->toString() ?: null,
            'stance' => $request->string('stance')->toString(),
            'verification_status' => 'unverified',
        ]);

        return redirect()->route('forum.discussions.show', $claim->discussion);
    }
}
