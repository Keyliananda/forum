<?php

namespace App\Http\Controllers\Forum;

use App\Forum\Discourse\Models\Evidence;
use App\Forum\Discourse\Models\EvidenceVerification;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreEvidenceVerificationController extends Controller
{
    public function __invoke(Request $request, Evidence $evidence): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'string', Rule::in([
                'unverified',
                'reachable',
                'verified',
                'partially_verified',
                'misquoted',
                'irrelevant',
                'inaccessible',
                'contradicted',
                'disputed',
                'outdated',
            ])],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $status = $request->string('status')->toString();

        EvidenceVerification::query()->create([
            'evidence_id' => $evidence->id,
            'verifier_id' => $request->user()?->id,
            'status' => $status,
            'note' => $request->string('note')->toString() ?: null,
        ]);

        $evidence->update(['verification_status' => $status]);

        return redirect()->route('forum.discussions.show', $evidence->discussion);
    }
}
