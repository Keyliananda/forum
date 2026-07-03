<?php

namespace App\Http\Controllers\Forum;

use App\Forum\Discourse\Models\Discussion;
use App\Forum\Discourse\Models\Position;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StorePositionController extends Controller
{
    public function __invoke(Request $request, Discussion $discussion): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'min:6', 'max:180'],
            'summary' => ['nullable', 'string', 'max:1000'],
        ]);

        Position::query()->create([
            'discussion_id' => $discussion->id,
            'author_id' => $request->user()?->id,
            'title' => $request->string('title')->toString(),
            'slug' => $this->uniqueSlug($discussion, $request->string('title')->toString()),
            'summary' => $request->string('summary')->toString() ?: null,
            'status' => 'open',
        ]);

        return redirect()->route('forum.discussions.show', $discussion);
    }

    private function uniqueSlug(Discussion $discussion, string $title): string
    {
        $base = Str::slug($title) ?: 'position';
        $slug = $base;
        $suffix = 2;

        while (Position::query()
            ->where('discussion_id', $discussion->id)
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
