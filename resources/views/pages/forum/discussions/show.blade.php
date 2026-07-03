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

            @php
                $evidenceStatusLabels = [
                    'unverified' => 'ungeprüft',
                    'reachable' => 'erreichbar',
                    'verified' => 'verifiziert',
                    'partially_verified' => 'teilweise verifiziert',
                    'misquoted' => 'problematisch zitiert',
                    'irrelevant' => 'irrelevant',
                    'inaccessible' => 'nicht erreichbar',
                    'contradicted' => 'widersprochen',
                    'disputed' => 'umstritten',
                    'outdated' => 'veraltet',
                ];
            @endphp

            <section class="space-y-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-zinc-950 dark:text-white">Positionen</h2>
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">Claims und Argumente zur Kernfrage, als Baum statt losem Kommentarstrom.</p>
                    </div>
                </div>

                @auth
                    @if ($discussion->isOpen())
                        <form method="POST" action="{{ route('forum.discussions.positions.store', $discussion) }}" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
                            @csrf
                            <div class="grid gap-3 md:grid-cols-[1fr_1fr_auto] md:items-end">
                                <div>
                                    <label for="position_title" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Deine Position</label>
                                    <input id="position_title" name="title" class="mt-1 w-full rounded-md border-zinc-300 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-950" required>
                                </div>
                                <div>
                                    <label for="position_summary" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Kurzbegründung</label>
                                    <input id="position_summary" name="summary" class="mt-1 w-full rounded-md border-zinc-300 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-950">
                                </div>
                                <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200">
                                    Position veröffentlichen
                                </button>
                            </div>
                        </form>
                    @endif
                @else
                    <div class="rounded-lg border border-zinc-200 bg-white p-4 text-sm text-zinc-600 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300">
                        Du kannst strukturierte Argumente öffentlich lesen. <a href="{{ route('login') }}" class="font-medium text-zinc-950 underline dark:text-white" wire:navigate>Anmelden, um eine Position zu ergänzen</a>.
                    </div>
                @endauth

                <div class="space-y-4">
                    @forelse ($discussion->positions as $position)
                        <article class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                            <div class="space-y-2">
                                <span class="inline-flex rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">Position</span>
                                <h3 class="text-lg font-semibold text-zinc-950 dark:text-white">{{ $position->title }}</h3>
                                @if ($position->summary)
                                    <p class="text-sm text-zinc-600 dark:text-zinc-300">{{ $position->summary }}</p>
                                @endif
                            </div>

                            @auth
                                @if ($discussion->isOpen())
                                    <details class="mt-4 rounded-md border border-zinc-200 p-3 dark:border-zinc-800">
                                        <summary class="cursor-pointer text-sm font-medium text-zinc-700 dark:text-zinc-200">Claim hinzufügen</summary>
                                        <form method="POST" action="{{ route('forum.positions.claims.store', $position) }}" class="mt-3 grid gap-3 md:grid-cols-[1fr_10rem_auto] md:items-end">
                                            @csrf
                                            <div>
                                                <label for="claim_statement_{{ $position->id }}" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Claim</label>
                                                <input id="claim_statement_{{ $position->id }}" name="statement" class="mt-1 w-full rounded-md border-zinc-300 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-950" required>
                                            </div>
                                            <div>
                                                <label for="claim_type_{{ $position->id }}" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Typ</label>
                                                <select id="claim_type_{{ $position->id }}" name="type" class="mt-1 w-full rounded-md border-zinc-300 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-950">
                                                    <option value="factual">factual</option>
                                                    <option value="causal">causal</option>
                                                    <option value="normative">normative</option>
                                                    <option value="definition">definition</option>
                                                    <option value="prediction">prediction</option>
                                                    <option value="interpretation">interpretation</option>
                                                </select>
                                            </div>
                                            <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200">
                                                Claim speichern
                                            </button>
                                        </form>
                                    </details>
                                @endif
                            @endauth

                            <div class="mt-5 space-y-3">
                                @forelse ($position->claims as $claim)
                                    <div class="border-l-2 border-zinc-200 pl-4 dark:border-zinc-700">
                                        <span class="inline-flex rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">Claim</span>
                                        <p class="mt-2 font-medium text-zinc-950 dark:text-white">{{ $claim->statement }}</p>
                                        <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                            <span class="rounded-full bg-zinc-100 px-2.5 py-1 font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">Robustheit</span>
                                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 font-medium text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ $claim->arguments->where('type', 'support')->count() }} Pro</span>
                                            <span class="rounded-full bg-rose-50 px-2.5 py-1 font-medium text-rose-700 dark:bg-rose-950 dark:text-rose-300">{{ $claim->arguments->where('type', 'oppose')->count() }} Contra</span>
                                            <span class="rounded-full bg-zinc-100 px-2.5 py-1 font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $claim->evidence->where('verification_status', 'verified')->count() }} verifizierte Quelle</span>
                                        </div>

                                        <details class="mt-3 rounded-md border border-zinc-200 p-3 dark:border-zinc-800" open>
                                            <summary class="cursor-pointer text-sm font-medium text-zinc-700 dark:text-zinc-200">
                                                Quellen ({{ $claim->evidence->count() }})
                                            </summary>

                                            <div class="mt-3 space-y-3">
                                                @forelse ($claim->evidence as $evidence)
                                                    <div class="rounded-md bg-zinc-50 p-3 dark:bg-zinc-950">
                                                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                                            <div>
                                                                <a href="{{ $evidence->url }}" class="font-medium text-zinc-950 underline-offset-4 hover:underline dark:text-white" target="_blank" rel="noreferrer">
                                                                    {{ $evidence->title }}
                                                                </a>
                                                                @if ($evidence->publisher)
                                                                    <p class="mt-1 text-xs text-zinc-500">{{ $evidence->publisher }}</p>
                                                                @endif
                                                            </div>
                                                            <span class="shrink-0 rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                                                {{ $evidenceStatusLabels[$evidence->verification_status] ?? $evidence->verification_status }}
                                                            </span>
                                                        </div>
                                                        @if ($evidence->excerpt)
                                                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">{{ $evidence->excerpt }}</p>
                                                        @endif

                                                        @auth
                                                            @if ($discussion->isOpen())
                                                                <form method="POST" action="{{ route('forum.evidence.verifications.store', $evidence) }}" class="mt-3 flex flex-col gap-2 sm:flex-row">
                                                                    @csrf
                                                                    <select name="status" class="rounded-md border-zinc-300 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-950">
                                                                        <option value="verified">verifiziert</option>
                                                                        <option value="partially_verified">teilweise verifiziert</option>
                                                                        <option value="misquoted">problematisch zitiert</option>
                                                                        <option value="irrelevant">irrelevant</option>
                                                                        <option value="inaccessible">nicht erreichbar</option>
                                                                        <option value="contradicted">widersprochen</option>
                                                                        <option value="disputed">umstritten</option>
                                                                        <option value="outdated">veraltet</option>
                                                                    </select>
                                                                    <input name="note" placeholder="Notiz" class="min-w-0 flex-1 rounded-md border-zinc-300 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-950">
                                                                    <button class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                                                                        Prüfen
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        @endauth
                                                    </div>
                                                @empty
                                                    <p class="text-sm text-zinc-500">Noch keine Quellen für diesen Claim.</p>
                                                @endforelse
                                            </div>

                                            @auth
                                                @if ($discussion->isOpen())
                                                    <form method="POST" action="{{ route('forum.claims.evidence.store', $claim) }}" class="mt-4 space-y-3 border-t border-zinc-200 pt-3 dark:border-zinc-800">
                                                        @csrf
                                                        <div class="grid gap-3 md:grid-cols-2">
                                                            <div>
                                                                <label for="claim_evidence_url_{{ $claim->id }}" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Quellen-URL</label>
                                                                <input id="claim_evidence_url_{{ $claim->id }}" name="url" class="mt-1 w-full rounded-md border-zinc-300 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-950">
                                                            </div>
                                                            <div>
                                                                <label for="claim_evidence_title_{{ $claim->id }}" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Titel</label>
                                                                <input id="claim_evidence_title_{{ $claim->id }}" name="title" class="mt-1 w-full rounded-md border-zinc-300 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-950" required>
                                                            </div>
                                                        </div>
                                                        <input name="publisher" placeholder="Publisher" class="w-full rounded-md border-zinc-300 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-950">
                                                        <textarea name="excerpt" rows="2" placeholder="Bezug zum Claim" class="w-full rounded-md border-zinc-300 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-950"></textarea>
                                                        <select name="stance" class="rounded-md border-zinc-300 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-950">
                                                            <option value="supports">stützt</option>
                                                            <option value="contradicts">widerspricht</option>
                                                            <option value="contextualizes">ordnet ein</option>
                                                        </select>
                                                        <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200">
                                                            Quelle speichern
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <p class="mt-3 text-sm text-zinc-500">Anmelden, um eine Quelle zu ergänzen.</p>
                                            @endauth
                                        </details>

                                        @auth
                                            @if ($discussion->isOpen())
                                                <details class="mt-3 rounded-md border border-zinc-200 p-3 dark:border-zinc-800">
                                                    <summary class="cursor-pointer text-sm font-medium text-zinc-700 dark:text-zinc-200">Argument hinzufügen</summary>
                                                    <form method="POST" action="{{ route('forum.claims.arguments.store', $claim) }}" class="mt-3 space-y-3">
                                                        @csrf
                                                        <input type="hidden" name="discussion_id" value="{{ $discussion->id }}">
                                                        <div>
                                                            <label for="argument_type_{{ $claim->id }}" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Typ</label>
                                                            <select id="argument_type_{{ $claim->id }}" name="type" class="mt-1 w-full rounded-md border-zinc-300 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-950">
                                                                <option value="support">Pro</option>
                                                                <option value="oppose">Contra</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label for="argument_body_{{ $claim->id }}" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Argument</label>
                                                            <textarea id="argument_body_{{ $claim->id }}" name="body" rows="3" class="mt-1 w-full rounded-md border-zinc-300 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-950" required></textarea>
                                                        </div>
                                                        <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200">
                                                            Argument speichern
                                                        </button>
                                                    </form>
                                                </details>
                                            @endif
                                        @endauth

                                        <div class="mt-4 space-y-3">
                                            @foreach ($claim->arguments as $argument)
                                                <div class="rounded-md border border-zinc-200 p-3 dark:border-zinc-800">
                                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $argument->type === 'support' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300' }}">
                                                        {{ $argument->type === 'support' ? 'Pro' : 'Contra' }}
                                                    </span>
                                                    <p class="mt-2 text-sm text-zinc-700 dark:text-zinc-200">{{ $argument->body }}</p>
                                                    @if ($argument->averageQualityScore() !== null)
                                                        <p class="mt-2 text-sm font-medium text-zinc-600 dark:text-zinc-300">
                                                            Argumentqualität {{ number_format($argument->averageQualityScore(), 1) }}
                                                        </p>
                                                    @endif

                                                    @if ($argument->evidence->isNotEmpty())
                                                        <div class="mt-3 space-y-2">
                                                            <h4 class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Quellen</h4>
                                                            @foreach ($argument->evidence as $evidence)
                                                                <div class="rounded-md bg-zinc-50 p-3 dark:bg-zinc-950">
                                                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                                                        <a href="{{ $evidence->url }}" class="text-sm font-medium text-zinc-950 underline-offset-4 hover:underline dark:text-white" target="_blank" rel="noreferrer">
                                                                            {{ $evidence->title }}
                                                                        </a>
                                                                        <span class="shrink-0 rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                                                            {{ $evidenceStatusLabels[$evidence->verification_status] ?? $evidence->verification_status }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    @foreach ($argument->children as $child)
                                                        <div class="mt-3 border-l-2 border-rose-200 pl-3 dark:border-rose-900">
                                                            <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700 dark:bg-rose-950 dark:text-rose-300">Entkräftung</span>
                                                            <p class="mt-2 text-sm text-zinc-700 dark:text-zinc-200">{{ $child->body }}</p>
                                                        </div>
                                                    @endforeach

                                                    @auth
                                                        @if ($discussion->isOpen())
                                                            <details class="mt-3">
                                                                <summary class="cursor-pointer text-sm font-medium text-zinc-600 dark:text-zinc-300">Argumentqualität bewerten</summary>
                                                                <form method="POST" action="{{ route('forum.arguments.quality.store', $argument) }}" class="mt-3 grid gap-2 sm:grid-cols-3">
                                                                    @csrf
                                                                    @foreach ([
                                                                        'clarity' => 'Klarheit',
                                                                        'relevance' => 'Relevanz',
                                                                        'logic' => 'Logik',
                                                                        'source_usage' => 'Quellen',
                                                                        'fairness' => 'Fairness',
                                                                        'rebuttal_strength' => 'Entkräftung',
                                                                    ] as $field => $label)
                                                                        <label class="text-sm text-zinc-600 dark:text-zinc-300">
                                                                            {{ $label }}
                                                                            <select name="{{ $field }}" class="mt-1 w-full rounded-md border-zinc-300 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-950">
                                                                                @for ($score = 1; $score <= 5; $score++)
                                                                                    <option value="{{ $score }}">{{ $score }}</option>
                                                                                @endfor
                                                                            </select>
                                                                        </label>
                                                                    @endforeach
                                                                    <textarea name="note" rows="2" placeholder="Notiz" class="sm:col-span-3 rounded-md border-zinc-300 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-950"></textarea>
                                                                    <button class="sm:col-span-3 rounded-md border border-zinc-300 px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                                                                        Qualität speichern
                                                                    </button>
                                                                </form>
                                                            </details>

                                                            <details class="mt-3">
                                                                <summary class="cursor-pointer text-sm font-medium text-zinc-600 dark:text-zinc-300">Entkräftung hinzufügen</summary>
                                                                <form method="POST" action="{{ route('forum.arguments.rebuttals.store', $argument) }}" class="mt-3 space-y-3">
                                                                    @csrf
                                                                    <textarea name="body" rows="3" class="w-full rounded-md border-zinc-300 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-950" required></textarea>
                                                                    <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200">
                                                                        Entkräftung speichern
                                                                    </button>
                                                                </form>
                                                            </details>
                                                        @endif
                                                    @endauth
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-zinc-500">Noch keine Claims für diese Position.</p>
                                @endforelse
                            </div>
                        </article>
                    @empty
                        <div class="rounded-lg border border-dashed border-zinc-300 bg-white p-6 text-sm text-zinc-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                            Noch keine Positionen. Formuliere die erste Position zur Kernfrage.
                        </div>
                    @endforelse
                </div>
            </section>

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
                        <dt>Governance</dt>
                        <dd>{{ $discussion->governance_profile }}</dd>
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
