<?php

namespace App\Http\Controllers\Forum;

use App\Forum\Discourse\Models\ResultProfile;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreResultProfileController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'weights' => ['required', 'array'],
            'weights.argument_quality' => ['required', 'integer', 'between:0,100'],
            'weights.source_quality' => ['required', 'integer', 'between:0,100'],
            'weights.community' => ['required', 'integer', 'between:0,100'],
            'weights.reputation' => ['required', 'integer', 'between:0,100'],
            'weights.external_signals' => ['required', 'integer', 'between:0,100'],
        ]);

        ResultProfile::query()->create([
            'user_id' => $request->user()?->id,
            'name' => $data['name'],
            'weights' => $data['weights'],
        ]);

        return back();
    }
}
