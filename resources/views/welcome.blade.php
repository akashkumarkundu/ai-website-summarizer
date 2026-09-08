<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => __('AI Website Summarizer')])
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 flex flex-col antialiased selection:bg-zinc-900 selection:text-white dark:selection:bg-zinc-100 dark:selection:text-zinc-900">
        {{-- Navigation Bar --}}
        <header class="w-full border-b border-zinc-200/80 dark:border-zinc-800/80 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md sticky top-0 z-30">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="flex aspect-square size-9 items-center justify-center rounded-xl bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 shadow-sm transition group-hover:scale-105">
                        <x-app-logo-icon class="size-5 fill-current" />
                    </div>
                    <span class="font-bold text-base sm:text-lg text-zinc-900 dark:text-zinc-50 tracking-tight">
                        WebSummarizer
                    </span>
                </a>

                <nav class="flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a
                                href="{{ route('dashboard') }}"
                                class="inline-flex items-center gap-2 px-3.5 py-1.5 text-sm font-medium rounded-lg bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-200 transition shadow-xs"
                            >
                                <flux:icon name="squares-2x2" class="size-4" />
                                <span>{{ __('Dashboard') }}</span>
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="px-3.5 py-1.5 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
                            >
                                {{ __('Log in') }}
                            </a>

                            @if (Route::has('register'))
                                <a
                                    href="{{ route('register') }}"
                                    class="inline-flex items-center px-3.5 py-1.5 text-sm font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition shadow-xs"
                                >
                                    {{ __('Register') }}
                                </a>
                            @endif
                        @endauth
                    @endif
                </nav>
            </div>
        </header>

        {{-- Main Summarizer Application --}}
        <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
            <livewire:website-summarizer />
        </main>

        {{-- Footer --}}
        <footer class="w-full border-t border-zinc-200/80 dark:border-zinc-800/80 py-6 text-center text-xs text-zinc-500 dark:text-zinc-400">
            <div class="max-w-5xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p>&copy; {{ date('Y') }} AI Website Summarizer. Powered by Laravel &amp; OpenRouter.</p>
                <div class="flex items-center gap-4">
                    <span>Safe SSRF Protection</span>
                    <span>&bull;</span>
                    <span>Structured Markdown Summary</span>
                </div>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>
