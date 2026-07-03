<x-layouts.forum :title="$discussion->title">
    <div class="grid gap-8 lg:grid-cols-[1fr_22rem]">
        <article class="space-y-6">
            <nav class="flex flex-wrap gap-2 text-sm text-zinc-500">
                <a href="{{ route('forum.topics.index') }}" class="hover:text-zinc-950 dark:hover:text-white" wire:navigate>Themen</a>
                <span>/</span>
                @if ($discussion->topic->parent)
                    <a href="{{ route('forum.topics.show', $discussion->topic->parent) }}" class="hover:text-zinc-950 dark:hover:text-white" wire:navigate>{{ $discussion->topic->parent->name }}</a>
                    <span>/</span>
                @endif
                <a href="{{ route('forum.topics.show', $discussion->topic) }}" class="hover:text-zinc-950 dark:hover:text-white" wire:navigate>{{ $discussion->topic->name }}</a>
            </nav>

            <header class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-sm font-medium text-zinc-500">Kernfrage</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ $discussion->title }}</h1>
                <p class="mt-4 text-lg text-zinc-700 dark:text-zinc-200">{{ $discussion->core_question }}</p>

                @if ($discussion->body)
                    <p class="mt-5 whitespace-pre-line text-zinc-600 dark:text-zinc-300">{{ $discussion->body }}</p>
                @endif
            </header>

            <section class="space-y-4">
                <h2 class="text-xl font-semibold text-zinc-950 dark:text-white">Antworten</h2>

                <div class="space-y-3">
                    @forelse ($replies as $reply)
                        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
                            <div class="flex items-center justify-between gap-3 text-sm text-zinc-500">
                                <span>{{ $reply->author?->name ?? 'Gelöschter Nutzer' }}</span>
                                <time datetime="{{ $reply->created_at?->toIso8601String() }}">{{ $reply->created_at?->diffForHumans() }}</time>
                            </div>
                            <p class="mt-3 whitespace-pre-line text-zinc-700 dark:text-zinc-200">{{ $reply->body }}</p>
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-zinc-300 bg-white p-6 text-sm text-zinc-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                            Diese Diskussion ist eröffnet, aber noch ohne Antworten.
                        </div>
                    @endforelse
                </div>
            </section>
        </article>

        <aside class="space-y-4">
            <section class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="font-semibold text-zinc-950 dark:text-white">Antwort veröffentlichen</h2>

                @auth
                    <form method="POST" action="{{ route('forum.discussions.replies.store', $discussion) }}" class="mt-4 space-y-4">
                        @csrf

                        <div>
                            <label for="body" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Deine Antwort</label>
                            <textarea id="body" name="body" rows="6" class="mt-1 w-full rounded-md border-zinc-300 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-950" required></textarea>
                        </div>

                        <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200">
                            Antwort veröffentlichen
                        </button>
                    </form>
                @else
                    <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-300">Du kannst öffentlich mitlesen. Zum Antworten brauchst du ein Konto.</p>
                    <a href="{{ route('login') }}" class="mt-4 inline-flex rounded-md bg-zinc-950 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200" wire:navigate>
                        Anmelden, um zu antworten
                    </a>
                @endauth
            </section>

            <section class="rounded-lg border border-zinc-200 bg-white p-5 text-sm text-zinc-600 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300">
                <h2 class="font-semibold text-zinc-950 dark:text-white">Status</h2>
                <dl class="mt-3 space-y-2">
                    <div class="flex justify-between gap-4">
                        <dt>Topic</dt>
                        <dd>{{ $discussion->topic->name }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt>Status</dt>
                        <dd>{{ $discussion->status === 'open' ? 'Offen' : 'Geschlossen' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt>Antworten</dt>
                        <dd>{{ $replies->count() }}</dd>
                    </div>
                </dl>
            </section>
        </aside>
    </div>
</x-layouts.forum>
