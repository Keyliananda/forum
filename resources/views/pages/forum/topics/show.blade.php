<x-layouts.forum :title="$topic->name">
    <div class="grid gap-8 lg:grid-cols-[1fr_22rem]">
        <div class="space-y-6">
            <div class="space-y-3">
                <nav class="flex flex-wrap gap-2 text-sm text-zinc-500">
                    <a href="{{ route('forum.topics.index') }}" class="hover:text-zinc-950 dark:hover:text-white" wire:navigate>Themen</a>
                    @if ($topic->parent)
                        <span>/</span>
                        <a href="{{ route('forum.topics.show', $topic->parent) }}" class="hover:text-zinc-950 dark:hover:text-white" wire:navigate>{{ $topic->parent->name }}</a>
                    @endif
                </nav>

                <h1 class="text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ $topic->name }}</h1>
                @if ($topic->description)
                    <p class="text-zinc-600 dark:text-zinc-300">{{ $topic->description }}</p>
                @endif
            </div>

            <section class="space-y-3">
                <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">Diskussionen</h2>
                <div class="divide-y divide-zinc-200 rounded-lg border border-zinc-200 bg-white dark:divide-zinc-800 dark:border-zinc-800 dark:bg-zinc-900">
                    @forelse ($discussions as $discussion)
                        <a href="{{ route('forum.discussions.show', $discussion) }}" class="block p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800" wire:navigate>
                            <div class="flex flex-col gap-2 sm:flex-row sm:justify-between">
                                <div>
                                    <h3 class="font-medium text-zinc-950 dark:text-white">{{ $discussion->title }}</h3>
                                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">{{ $discussion->core_question }}</p>
                                </div>
                                <span class="text-xs text-zinc-500">{{ $discussion->replies_count }} Antworten</span>
                            </div>
                        </a>
                    @empty
                        <div class="p-6 text-sm text-zinc-600 dark:text-zinc-300">In diesem Thema gibt es noch keine Diskussion.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="space-y-4">
            <section class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="font-semibold text-zinc-950 dark:text-white">Diskussion starten</h2>

                @auth
                    <form method="POST" action="{{ route('forum.discussions.store') }}" class="mt-4 space-y-4">
                        @csrf
                        <input type="hidden" name="topic_id" value="{{ $topic->id }}">

                        <div>
                            <label for="title" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Titel</label>
                            <input id="title" name="title" class="mt-1 w-full rounded-md border-zinc-300 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-950" required>
                        </div>

                        <div>
                            <label for="core_question" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Kernfrage</label>
                            <textarea id="core_question" name="core_question" rows="3" class="mt-1 w-full rounded-md border-zinc-300 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-950" required></textarea>
                        </div>

                        <div>
                            <label for="body" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Kontext</label>
                            <textarea id="body" name="body" rows="4" class="mt-1 w-full rounded-md border-zinc-300 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-950"></textarea>
                        </div>

                        <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200">
                            Diskussion veröffentlichen
                        </button>
                    </form>
                @else
                    <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-300">Du kannst öffentlich mitlesen. Zum Starten einer Diskussion brauchst du ein Konto.</p>
                    <a href="{{ route('login') }}" class="mt-4 inline-flex rounded-md bg-zinc-950 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200" wire:navigate>
                        Anmelden, um eine Diskussion zu starten
                    </a>
                @endauth
            </section>

            @if ($topic->children->isNotEmpty())
                <section class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="font-semibold text-zinc-950 dark:text-white">Unterthemen</h2>
                    <div class="mt-3 space-y-2">
                        @foreach ($topic->children as $child)
                            <a href="{{ route('forum.topics.show', $child) }}" class="block rounded-md px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800" wire:navigate>
                                {{ $child->name }}
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </aside>
    </div>
</x-layouts.forum>
