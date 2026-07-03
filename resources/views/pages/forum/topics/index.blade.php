<x-layouts.forum :title="'Themenlandschaft'">
    <div class="space-y-8">
        <section class="max-w-3xl space-y-3">
            <p class="text-sm font-medium text-zinc-500">Öffentliches Forum</p>
            <h1 class="text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">Themenlandschaft</h1>
            <p class="text-base text-zinc-600 dark:text-zinc-300">
                Lies Diskussionen nach Themen, verfolge Kernfragen und antworte mit nachvollziehbaren Beiträgen.
            </p>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($topics as $topic)
                <a href="{{ route('forum.topics.show', $topic) }}" class="rounded-lg border border-zinc-200 bg-white p-5 transition hover:border-zinc-300 hover:shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700" wire:navigate>
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-2">
                            <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">{{ $topic->name }}</h2>
                            @if ($topic->description)
                                <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">{{ $topic->description }}</p>
                            @endif
                        </div>
                        <span class="shrink-0 rounded-full bg-zinc-100 px-3 py-1 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                            {{ $topic->discussions_count }} {{ $topic->discussions_count === 1 ? 'Diskussion' : 'Diskussionen' }}
                        </span>
                    </div>

                    @if ($topic->children->isNotEmpty())
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($topic->children as $child)
                                <span class="rounded-full border border-zinc-200 px-2.5 py-1 text-xs text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">
                                    {{ $child->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </a>
            @empty
                <div class="rounded-lg border border-dashed border-zinc-300 bg-white p-8 text-zinc-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                    Noch keine Themen angelegt. Sobald die ersten Demo-Themen importiert sind, erscheinen sie hier.
                </div>
            @endforelse
        </section>

        <section class="space-y-4">
            <h2 class="text-xl font-semibold text-zinc-950 dark:text-white">Aktuelle Diskussionen</h2>

            <div class="divide-y divide-zinc-200 rounded-lg border border-zinc-200 bg-white dark:divide-zinc-800 dark:border-zinc-800 dark:bg-zinc-900">
                @forelse ($recentDiscussions as $discussion)
                    <a href="{{ route('forum.discussions.show', $discussion) }}" class="block p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800" wire:navigate>
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h3 class="font-medium text-zinc-950 dark:text-white">{{ $discussion->title }}</h3>
                                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">{{ $discussion->core_question }}</p>
                            </div>
                            <span class="text-xs text-zinc-500">{{ $discussion->topic->name }}</span>
                        </div>
                    </a>
                @empty
                    <div class="p-6 text-sm text-zinc-600 dark:text-zinc-300">Noch keine Diskussionen.</div>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts.forum>
