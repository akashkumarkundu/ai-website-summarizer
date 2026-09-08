<div class="w-full max-w-4xl mx-auto space-y-8" id="summarizer-app">
    {{-- Hero Section --}}
    <div class="text-center space-y-3">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 shadow-xs">
            <flux:icon name="sparkles" class="size-3.5 text-amber-500" />
            <span>Powered by OpenRouter AI</span>
        </div>

        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-zinc-900 dark:text-zinc-50">
            AI Website Summarizer
        </h1>

        <p class="text-base sm:text-lg text-zinc-600 dark:text-zinc-400 max-w-2xl mx-auto">
            Enter a website URL and get an AI-powered summary.
        </p>
    </div>

    {{-- Input Card --}}
    <div class="relative bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm p-5 sm:p-7">
        <form wire:submit="summarize" class="space-y-4">
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label for="url-input" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Website URL
                    </label>

                    {{-- Language Selector Toggle --}}
                    <div class="inline-flex items-center p-0.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-xs font-medium border border-zinc-200 dark:border-zinc-700">
                        <button
                            type="button"
                            wire:click="$set('language', 'bn')"
                            class="px-2.5 py-1 rounded-md transition cursor-pointer {{ $language === 'bn' ? 'bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-xs font-semibold' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200' }}"
                        >
                            🇧🇩 বাংলা
                        </button>
                        <button
                            type="button"
                            wire:click="$set('language', 'en')"
                            class="px-2.5 py-1 rounded-md transition cursor-pointer {{ $language === 'en' ? 'bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-xs font-semibold' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200' }}"
                        >
                            🇬🇧 English
                        </button>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                            <flux:icon name="globe-alt" class="size-5 text-zinc-400" />
                        </div>
                        <input
                            type="url"
                            id="url-input"
                            wire:model="url"
                            placeholder="https://example.com/article"
                            required
                            class="block w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-950 pl-10 pr-4 py-2.5 text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:border-zinc-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900/10 dark:focus:border-zinc-100 dark:focus:bg-zinc-900 transition"
                        />
                    </div>

                    <flux:button
                        variant="primary"
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="summarize"
                        class="sm:w-auto w-full justify-center px-6 py-2.5 font-medium shadow-xs"
                    >
                        <span wire:loading.remove wire:target="summarize" class="flex items-center gap-2">
                            <flux:icon name="sparkles" class="size-4" />
                            Summarize Website
                        </span>
                        <span wire:loading wire:target="summarize" class="flex items-center gap-2">
                            <svg class="animate-spin size-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Summarizing...
                        </span>
                    </flux:button>
                </div>

                @error('url')
                    <p class="text-xs font-medium text-red-600 dark:text-red-400 mt-1.5 flex items-center gap-1.5">
                        <flux:icon name="exclamation-circle" class="size-4 shrink-0" />
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Quick Sample URLs --}}
            <div class="pt-2 flex flex-wrap items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                <span class="font-medium">Try a sample:</span>
                <button
                    type="button"
                    wire:click="setSampleUrl('https://laravel.com')"
                    class="px-2.5 py-1 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition cursor-pointer"
                >
                    laravel.com
                </button>
                <button
                    type="button"
                    wire:click="setSampleUrl('https://en.wikipedia.org/wiki/Artificial_intelligence')"
                    class="px-2.5 py-1 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition cursor-pointer"
                >
                    Wikipedia (AI)
                </button>
                <button
                    type="button"
                    wire:click="setSampleUrl('https://news.ycombinator.com')"
                    class="px-2.5 py-1 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition cursor-pointer"
                >
                    Hacker News
                </button>
            </div>
        </form>
    </div>

    {{-- Loading Indicator --}}
    <div
        wire:loading
        wire:target="summarize"
        class="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm p-8 text-center space-y-4"
    >
        <div class="inline-flex items-center justify-center p-3 bg-zinc-100 dark:bg-zinc-800 rounded-full text-zinc-900 dark:text-zinc-100">
            <svg class="animate-spin size-8 text-zinc-900 dark:text-zinc-100" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <div class="space-y-1">
            <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                Fetching & Analyzing Webpage
            </h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-md mx-auto">
                Safely extracting readable content and generating an intelligent summary with OpenRouter AI...
            </p>
        </div>
        <div class="w-48 h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full mx-auto overflow-hidden">
            <div class="h-full bg-zinc-900 dark:bg-zinc-100 rounded-full animate-pulse"></div>
        </div>
    </div>

    {{-- Error Message Display --}}
    @if ($errorMessage && ! $summary)
        <div class="bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900/50 rounded-2xl p-5 sm:p-6 space-y-3">
            <div class="flex items-start gap-3">
                <div class="p-1 rounded-full bg-red-100 dark:bg-red-900/60 text-red-600 dark:text-red-400 mt-0.5">
                    <flux:icon name="exclamation-triangle" class="size-5" />
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-red-900 dark:text-red-200">
                        Unable to summarize website
                    </h3>
                    <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                        {{ $errorMessage }}
                    </p>
                </div>
                <button
                    type="button"
                    wire:click="$set('errorMessage', null)"
                    class="text-red-400 hover:text-red-600 dark:hover:text-red-300 cursor-pointer"
                    aria-label="Dismiss error"
                >
                    <flux:icon name="x-mark" class="size-5" />
                </button>
            </div>
        </div>
    @endif

    {{-- Summary Result Display --}}
    @if ($summary)
        <div
            x-data="{ copied: false }"
            wire:loading.remove
            wire:target="summarize"
            class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm overflow-hidden"
        >
            {{-- Result Header --}}
            <div class="p-5 sm:p-6 bg-zinc-50/70 dark:bg-zinc-950/50 border-b border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="space-y-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2.5 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800/60">
                            <flux:icon name="check-circle" class="size-3.5" />
                            {{ $language === 'bn' ? 'বাংলা সারসংক্ষেপ প্রস্তুত' : 'Summary Ready' }}
                        </span>
                        @if ($charCount)
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">
                                &bull; {{ number_format($charCount) }} chars analyzed
                            </span>
                        @endif
                    </div>

                    <h2 class="text-lg sm:text-xl font-bold text-zinc-900 dark:text-zinc-100 truncate" title="{{ $websiteTitle }}">
                        {{ $websiteTitle ?: 'Website Summary' }}
                    </h2>

                    @if ($summarizedUrl)
                        <a
                            href="{{ $summarizedUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1 text-xs text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 hover:underline truncate max-w-md"
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
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700/60 transition shadow-2xs cursor-pointer"
                    >
                        <template x-if="!copied">
                            <span class="inline-flex items-center gap-1.5">
                                <flux:icon name="clipboard-document" class="size-4 text-zinc-500" />
                                {{ $language === 'bn' ? 'সারসংক্ষেপ কপি করুন' : 'Copy Summary' }}
                            </span>
                        </template>
                        <template x-if="copied">
                            <span class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-semibold">
                                <flux:icon name="check" class="size-4" />
                                {{ $language === 'bn' ? 'কপি হয়েছে!' : 'Copied!' }}
                            </span>
                        </template>
                    </button>

                    <button
                        type="button"
                        wire:click="resetSummarizer"
                        class="p-2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer"
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
