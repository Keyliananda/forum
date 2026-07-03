<?php

namespace App\Http\Controllers\Forum;

use App\Forum\Discourse\Models\Discussion;
use App\Forum\Discourse\Models\Topic;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreDiscussionController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'topic_id' => ['required', 'integer', Rule::exists('topics', 'id')],
            'title' => ['required', 'string', 'min:6', 'max:180'],
            'core_question' => ['required', 'string', 'min:8', 'max:500'],
            'body' => ['nullable', 'string', 'max:5000'],
        ]);

        $topic = Topic::query()->whereKey($request->integer('topic_id'))->firstOrFail();
        $title = $request->string('title')->toString();
        $slug = $this->uniqueSlug($title);

        $discussion = Discussion::query()->create([
            'space_id' => $topic->space_id,
            'topic_id' => $topic->id,
            'author_id' => $request->user()?->id,
            'title' => $title,
            'slug' => $slug,
            'core_question' => $request->string('core_question')->toString(),
            'body' => $data['body'] ?? null,
            'status' => 'open',
            'last_replied_at' => now(),
        ]);

        return redirect()->route('forum.discussions.show', $discussion);
    }

    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'discussion';
        $slug = $base;
        $suffix = 2;

        while (Discussion::query()
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
