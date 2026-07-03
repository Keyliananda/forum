<?php

namespace App\Http\Controllers\Forum;

use App\Forum\Discourse\Models\Discussion;
use App\Forum\Discourse\Models\Report;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreDiscussionReportController extends Controller
{
    public function __invoke(Request $request, Discussion $discussion): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:80'],
            'details' => ['nullable', 'string', 'max:2000'],
        ]);

        Report::query()->create([
            'reporter_id' => $request->user()?->id,
            'reportable_type' => Discussion::class,
            'reportable_id' => $discussion->id,
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
            'status' => 'open',
        ]);

        return redirect()->route('forum.discussions.show', $discussion);
    }
}
