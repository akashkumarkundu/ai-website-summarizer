<div class="w-full max-w-4xl mx-auto space-y-8" id="summarizer-app">
    {{-- Hero Section with Colorful Badges --}}
    <div class="text-center space-y-4">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-gradient-to-r from-violet-500/10 via-fuchsia-500/10 to-cyan-500/10 dark:from-violet-500/20 dark:via-fuchsia-500/20 dark:to-cyan-500/20 text-violet-700 dark:text-violet-300 border border-violet-200/60 dark:border-violet-800/60 shadow-sm backdrop-blur-md animate-pulse">
            <span class="flex h-2 w-2 rounded-full bg-emerald-500"></span>
            <span class="tracking-wide">Dual-Engine AI: Ultra-Fast Groq &amp; OpenRouter</span>
        </div>

        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-zinc-900 dark:text-zinc-50">
            AI Website <span class="bg-gradient-to-r from-violet-600 via-fuchsia-600 to-cyan-500 bg-clip-text text-transparent">Summarizer</span>
        </h1>

        <p class="text-base sm:text-lg text-zinc-600 dark:text-zinc-300 max-w-2xl mx-auto leading-relaxed">
            Enter any public website URL to extract readable content and generate instant, intelligent summaries in seconds.
        </p>
    </div>

    {{-- Main Input Card with Glassmorphism & Gradient Border --}}
    <div class="relative bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/80 dark:border-zinc-800/80 rounded-3xl shadow-xl shadow-violet-500/5 dark:shadow-black/40 p-5 sm:p-8 transition-all hover:border-zinc-300 dark:hover:border-zinc-700">
        {{-- Gradient Top Accent Bar --}}
        <div class="absolute inset-x-8 -top-px h-px bg-gradient-to-r from-transparent via-violet-500 to-transparent"></div>

        <form wire:submit="summarize" class="space-y-6">
            {{-- Dual Controls: AI Provider & Language Selector --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-2xl bg-zinc-50/80 dark:bg-zinc-950/60 border border-zinc-200/60 dark:border-zinc-800/60">
                {{-- AI Engine Selector --}}
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">AI Engine:</span>
                    <div class="inline-flex items-center p-1 rounded-xl bg-zinc-200/70 dark:bg-zinc-800/80 text-xs font-medium border border-zinc-300/60 dark:border-zinc-700/60">
                        <button
                            type="button"
                            wire:click="$set('provider', 'openrouter')"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition cursor-pointer {{ $provider === 'openrouter' ? 'bg-white dark:bg-zinc-900 text-blue-600 dark:text-blue-400 font-bold shadow-xs' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-200' }}"
                        >
                            <flux:icon name="globe-alt" class="size-3.5 text-blue-500" />
                            <span>OpenRouter</span>
                        </button>

                        <button
                            type="button"
                            wire:click="$set('provider', 'groq')"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition cursor-pointer {{ $provider === 'groq' ? 'bg-white dark:bg-zinc-900 text-amber-600 dark:text-amber-400 font-bold shadow-xs' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-200' }}"
                        >
                            <flux:icon name="bolt" class="size-3.5 text-amber-500" />
                            <span>Groq LPU™</span>
                            <span class="text-[10px] uppercase tracking-wider px-1.5 py-0.2 rounded bg-amber-500/10 text-amber-600 dark:text-amber-400 font-bold">Fast</span>
                        </button>
                    </div>
                </div>

                {{-- Language Toggle --}}
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Language:</span>
                    <div class="inline-flex items-center p-1 rounded-xl bg-zinc-200/70 dark:bg-zinc-800/80 text-xs font-medium border border-zinc-300/60 dark:border-zinc-700/60">
                        <button
                            type="button"
                            wire:click="$set('language', 'bn')"
                            class="px-3 py-1.5 rounded-lg transition cursor-pointer {{ $language === 'bn' ? 'bg-white dark:bg-zinc-900 text-emerald-600 dark:text-emerald-400 font-bold shadow-xs' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-200' }}"
                        >
                            🇧🇩 বাংলা
                        </button>
                        <button
                            type="button"
                            wire:click="$set('language', 'en')"
                            class="px-3 py-1.5 rounded-lg transition cursor-pointer {{ $language === 'en' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 font-bold shadow-xs' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-200' }}"
                        >
                            🇬🇧 English
                        </button>
                    </div>
                </div>
            </div>

            {{-- URL Input Box & Summarize Action --}}
            <div class="space-y-2">
                <label for="url-input" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                    Website URL to Summarize
                </label>

                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1 group">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <flux:icon name="globe-alt" class="size-5 text-violet-500 transition group-focus-within:text-fuchsia-500" />
                        </div>
                        <input
                            type="url"
                            id="url-input"
                            wire:model="url"
                            placeholder="https://hstu.ac.bd/admission/index"
                            required
                            class="block w-full rounded-xl border border-zinc-300/80 dark:border-zinc-700/80 bg-zinc-50/70 dark:bg-zinc-950/80 pl-11 pr-4 py-3 text-sm sm:text-base text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:border-violet-500 focus:bg-white focus:outline-hidden focus:ring-4 focus:ring-violet-500/15 dark:focus:border-violet-400 dark:focus:bg-zinc-900 transition shadow-inner"
                        />
                    </div>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="summarize"
                        class="sm:w-auto w-full inline-flex items-center justify-center gap-2 px-7 py-3 rounded-xl font-bold text-sm sm:text-base text-white bg-gradient-to-r from-violet-600 via-fuchsia-600 to-cyan-600 hover:from-violet-500 hover:via-fuchsia-500 hover:to-cyan-500 active:scale-[0.98] transition-all duration-200 shadow-lg shadow-violet-500/25 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                        <span wire:loading.remove wire:target="summarize" class="flex items-center gap-2">
                            <flux:icon name="sparkles" class="size-4" />
                            <span>Summarize Website</span>
                        </span>
                        <span wire:loading wire:target="summarize" class="flex items-center gap-2">
                            <svg class="animate-spin size-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Analyzing...</span>
                        </span>
                    </button>
                </div>

                @error('url')
                    <p class="text-xs font-semibold text-red-600 dark:text-red-400 mt-1.5 flex items-center gap-1.5">
                        <flux:icon name="exclamation-circle" class="size-4 shrink-0" />
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Quick Sample URLs --}}
            <div class="pt-1 flex flex-wrap items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                <span class="font-semibold text-zinc-700 dark:text-zinc-300">Try popular sites:</span>
                <button
                    type="button"
                    wire:click="setSampleUrl('https://hstu.ac.bd/admission/index')"
                    class="px-2.5 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800/80 text-zinc-700 dark:text-zinc-300 hover:bg-violet-100 hover:text-violet-700 dark:hover:bg-violet-900/30 dark:hover:text-violet-300 transition cursor-pointer border border-zinc-200/50 dark:border-zinc-700/50"
                >
                    🎓 HSTU Admission
                </button>
                <button
                    type="button"
                    wire:click="setSampleUrl('https://laravel.com')"
                    class="px-2.5 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800/80 text-zinc-700 dark:text-zinc-300 hover:bg-violet-100 hover:text-violet-700 dark:hover:bg-violet-900/30 dark:hover:text-violet-300 transition cursor-pointer border border-zinc-200/50 dark:border-zinc-700/50"
                >
                    🚀 Laravel.com
                </button>
                <button
                    type="button"
                    wire:click="setSampleUrl('https://en.wikipedia.org/wiki/Artificial_intelligence')"
                    class="px-2.5 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800/80 text-zinc-700 dark:text-zinc-300 hover:bg-violet-100 hover:text-violet-700 dark:hover:bg-violet-900/30 dark:hover:text-violet-300 transition cursor-pointer border border-zinc-200/50 dark:border-zinc-700/50"
                >
                    📚 Wikipedia (AI)
                </button>
            </div>
        </form>
    </div>

    {{-- Vibrant Animated Loading Indicator --}}
    <div
        wire:loading
        wire:target="summarize"
        class="w-full relative overflow-hidden bg-white/90 dark:bg-zinc-900/90 border border-violet-500/20 dark:border-violet-500/30 rounded-3xl shadow-xl p-8 sm:p-10 text-center space-y-5 backdrop-blur-md"
    >
        {{-- Background Glow --}}
        <div class="absolute -top-20 -left-20 size-40 bg-violet-500/15 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -right-20 size-40 bg-fuchsia-500/15 rounded-full blur-3xl"></div>

        <div class="relative inline-flex items-center justify-center p-4 bg-gradient-to-tr from-violet-600 to-fuchsia-600 text-white rounded-2xl shadow-lg shadow-violet-500/30">
            <svg class="animate-spin size-8" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <div class="relative space-y-1.5">
            <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">
                Fetching &amp; Synthesizing Webpage
            </h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-md mx-auto">
                Extracting clean readable text and running AI analysis via
                <span class="font-bold text-violet-600 dark:text-violet-400">
                    {{ $provider === 'groq' ? 'Groq LPU™ Engine' : 'OpenRouter Cloud' }}
                </span>
                in
                <span class="font-semibold text-zinc-700 dark:text-zinc-300">
                    {{ $language === 'bn' ? 'বাংলা' : 'English' }}
                </span>...
            </p>
        </div>

        <div class="relative w-56 h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full mx-auto overflow-hidden p-0.5">
            <div class="h-full bg-gradient-to-r from-violet-600 via-fuchsia-600 to-cyan-500 rounded-full animate-pulse"></div>
        </div>
    </div>

    {{-- Error Message Display --}}
    @if ($errorMessage && ! $summary)
        <div class="bg-red-50/90 dark:bg-red-950/40 border border-red-200 dark:border-red-900/50 rounded-2xl p-5 sm:p-6 space-y-3 shadow-sm backdrop-blur-md">
            <div class="flex items-start gap-3">
                <div class="p-2 rounded-xl bg-red-100 dark:bg-red-900/60 text-red-600 dark:text-red-400 mt-0.5 shrink-0">
                    <flux:icon name="exclamation-triangle" class="size-5" />
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-bold text-red-900 dark:text-red-200">
                        Unable to summarize website
                    </h3>
                    <p class="text-sm text-red-700 dark:text-red-300 mt-1 leading-relaxed">
                        {{ $errorMessage }}
                    </p>
                    @if (str_contains($errorMessage, 'Groq API key'))
                        <div class="mt-3">
                            <button
                                type="button"
                                wire:click="$set('provider', 'openrouter')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200 hover:bg-red-200 transition cursor-pointer"
                            >
                                <flux:icon name="arrow-path" class="size-3.5" />
                                <span>Switch back to OpenRouter</span>
                            </button>
                        </div>
                    @endif
                </div>
                <button
                    type="button"
                    wire:click="$set('errorMessage', null)"
                    class="text-red-400 hover:text-red-600 dark:hover:text-red-300 cursor-pointer p-1"
                    aria-label="Dismiss error"
                >
                    <flux:icon name="x-mark" class="size-5" />
                </button>
            </div>
        </div>
    @endif

    {{-- Summary Result Card --}}
    @if ($summary)
        <div
            x-data="{ copied: false }"
            wire:loading.remove
            wire:target="summarize"
            class="relative bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800/90 rounded-3xl shadow-xl shadow-violet-500/5 dark:shadow-black/50 overflow-hidden"
        >
            {{-- Top Accent Gradient Bar --}}
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-violet-600 via-fuchsia-600 to-cyan-500"></div>

            {{-- Result Header --}}
            <div class="p-6 sm:p-7 bg-zinc-50/70 dark:bg-zinc-950/60 border-b border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="space-y-1.5 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        {{-- Status Badge --}}
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-500/10 dark:bg-emerald-500/20 px-2.5 py-0.5 rounded-full border border-emerald-500/20">
                            <flux:icon name="check-circle" class="size-3.5 text-emerald-500" />
                            {{ $language === 'bn' ? 'সারসংক্ষেপ সম্পন্ন' : 'Summary Ready' }}
                        </span>

                        {{-- Provider Badge --}}
                        <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-0.5 rounded-full border {{ str_contains(strtolower($activeProviderName ?? ''), 'groq') ? 'bg-amber-500/10 text-amber-700 dark:text-amber-300 border-amber-500/20' : 'bg-blue-500/10 text-blue-700 dark:text-blue-300 border-blue-500/20' }}">
                            @if(str_contains(strtolower($activeProviderName ?? ''), 'groq'))
                                <flux:icon name="bolt" class="size-3 text-amber-500" />
                            @else
                                <flux:icon name="globe-alt" class="size-3 text-blue-500" />
                            @endif
                            {{ $activeProviderName ?? ($provider === 'groq' ? 'Groq LPU' : 'OpenRouter') }}
                        </span>

                        {{-- Speed Badge --}}
                        @if ($executionTime)
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-violet-700 dark:text-violet-300 bg-violet-500/10 px-2.5 py-0.5 rounded-full border border-violet-500/20">
                                ⏱️ {{ $executionTime }}s
                            </span>
                        @endif

                        {{-- Content Stats --}}
                        @if ($charCount)
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">
                                &bull; {{ number_format($charCount) }} chars
                            </span>
                        @endif
                    </div>

                    <h2 class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-zinc-50 truncate" title="{{ $websiteTitle }}">
                        {{ $websiteTitle ?: 'Website Summary' }}
                    </h2>

                    @if ($summarizedUrl)
                        <a
                            href="{{ $summarizedUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1 text-xs font-medium text-violet-600 dark:text-violet-400 hover:underline truncate max-w-md"
                        >
                            <span>{{ $summarizedUrl }}</span>
                            <flux:icon name="arrow-top-right-on-square" class="size-3" />
                        </a>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2 shrink-0">
                    <button
                        type="button"
                        @click="navigator.clipboard.writeText($refs.summaryMarkdown.innerText.trim()); copied = true; setTimeout(() => copied = false, 2500)"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-bold rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-100 hover:bg-zinc-50 dark:hover:bg-zinc-700/80 transition-all shadow-sm active:scale-95 cursor-pointer"
                    >
                        <template x-if="!copied">
                            <span class="inline-flex items-center gap-1.5">
                                <flux:icon name="clipboard-document" class="size-4 text-violet-500" />
                                <span>{{ $language === 'bn' ? 'সারসংক্ষেপ কপি করুন' : 'Copy Summary' }}</span>
                            </span>
                        </template>
                        <template x-if="copied">
                            <span class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                                <flux:icon name="check" class="size-4" />
                                <span>{{ $language === 'bn' ? 'কপি হয়েছে!' : 'Copied!' }}</span>
                            </span>
                        </template>
                    </button>

                    <button
                        type="button"
                        wire:click="resetSummarizer"
                        class="p-2.5 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer"
                        title="Clear and summarize another URL"
                    >
                        <flux:icon name="arrow-path" class="size-4" />
                    </button>
                </div>
            </div>

            {{-- Summary Content Body --}}
            <div class="p-6 sm:p-8">
                <div
                    x-ref="summaryMarkdown"
                    class="prose dark:prose-invert max-w-none text-zinc-800 dark:text-zinc-200 text-sm sm:text-base leading-relaxed space-y-4"
                >
                    {!! Illuminate\Support\Str::markdown($summary) !!}
                </div>
            </div>
        </div>
    @endif
</div>
