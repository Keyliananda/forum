<?php

namespace App\Http\Controllers\Forum;

use App\Forum\Discourse\Models\Argument;
use App\Forum\Discourse\Models\ModerationAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreArgumentModerationActionController extends Controller
{
    public function __invoke(Request $request, Argument $argument): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', 'string', Rule::in(['hide'])],
            'public_reason' => ['nullable', 'string', 'max:2000'],
            'internal_note' => ['nullable', 'string', 'max:2000'],
        ]);

        ModerationAction::query()->create([
            'moderator_id' => $request->user()?->id,
            'actionable_type' => Argument::class,
            'actionable_id' => $argument->id,
            'action' => $data['action'],
            'public_reason' => $data['public_reason'] ?? null,
            'internal_note' => $data['internal_note'] ?? null,
            'policy_version' => 'w5.1',
        ]);

        $argument->update(['status' => 'hidden']);

        return redirect()->route('forum.discussions.show', $argument->discussion);
    }
}
