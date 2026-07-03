<?php

namespace App\Http\Controllers\Forum;

use App\Forum\Discourse\Models\Claim;
use App\Forum\Discourse\Models\Position;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StorePositionClaimController extends Controller
{
    public function __invoke(Request $request, Position $position): RedirectResponse
    {
        $request->validate([
            'statement' => ['required', 'string', 'min:8', 'max:500'],
            'type' => ['required', 'string', Rule::in(['factual', 'causal', 'normative', 'definition', 'prediction', 'interpretation'])],
        ]);

        $claim = Claim::query()->create([
            'discussion_id' => $position->discussion_id,
            'author_id' => $request->user()?->id,
            'statement' => $request->string('statement')->toString(),
            'slug' => $this->uniqueSlug($position->discussion_id, $request->string('statement')->toString()),
            'type' => $request->string('type')->toString(),
            'status' => 'open',
        ]);

        $position->claims()->attach($claim, [
            'author_id' => $request->user()?->id,
        ]);

        return redirect()->route('forum.discussions.show', $position->discussion);
    }

    private function uniqueSlug(int $discussionId, string $statement): string
    {
        $base = Str::slug($statement) ?: 'claim';
        $slug = $base;
        $suffix = 2;

        while (Claim::query()
            ->where('discussion_id', $discussionId)
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
