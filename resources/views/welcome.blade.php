<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        @include('partials.head', ['title' => __('AI Website Summarizer - Fast, Bilingual & Dual Engine')])
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 flex flex-col antialiased selection:bg-violet-600 selection:text-white relative overflow-x-hidden">
        {{-- Ambient Gradient Auras in Background --}}
        <div class="fixed inset-0 pointer-events-none -z-10 overflow-hidden">
            <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[900px] h-[500px] bg-gradient-to-tr from-violet-600/15 via-fuchsia-500/15 to-cyan-500/15 dark:from-violet-600/20 dark:via-fuchsia-500/20 dark:to-cyan-500/20 blur-[130px] rounded-full"></div>
            <div class="absolute top-[600px] -right-40 w-[600px] h-[400px] bg-cyan-500/10 dark:bg-cyan-500/15 blur-[120px] rounded-full"></div>
            <div class="absolute top-[800px] -left-40 w-[600px] h-[400px] bg-violet-500/10 dark:bg-violet-500/15 blur-[120px] rounded-full"></div>
        </div>

        {{-- Top Navigation Bar --}}
        <header class="w-full border-b border-zinc-200/80 dark:border-zinc-800/80 bg-white/70 dark:bg-zinc-900/70 backdrop-blur-xl sticky top-0 z-40 transition-colors">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                {{-- Brand Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="flex aspect-square size-10 items-center justify-center rounded-2xl bg-gradient-to-tr from-violet-600 via-fuchsia-600 to-cyan-500 text-white shadow-lg shadow-violet-500/30 transition-transform group-hover:scale-105">
                        <x-app-logo-icon class="size-5.5 fill-current" />
                    </div>
                    <div class="flex flex-col">
                        <span class="font-black text-base sm:text-lg text-zinc-900 dark:text-zinc-50 tracking-tight flex items-center gap-1.5">
                            WebSummarizer<span class="text-violet-600 dark:text-violet-400">.ai</span>
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 -mt-0.5">
                            Dual-Engine AI
                        </span>
                    </div>
                </a>

                {{-- Navigation Actions --}}
                <nav class="flex items-center gap-3">
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-zinc-100/80 dark:bg-zinc-800/80 border border-zinc-200/60 dark:border-zinc-700/60 text-zinc-600 dark:text-zinc-300">
                        <span class="flex size-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>Groq &amp; OpenRouter Active</span>
                    </div>

                    @if (Route::has('login'))
                        @auth
                            <a
                                href="{{ route('dashboard') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-bold rounded-xl bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all shadow-sm"
                            >
                                <flux:icon name="squares-2x2" class="size-4" />
                                <span>{{ __('Dashboard') }}</span>
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="px-3.5 py-1.5 text-xs sm:text-sm font-bold text-zinc-700 dark:text-zinc-300 hover:text-violet-600 dark:hover:text-violet-400 transition"
                            >
                                {{ __('Log in') }}
                            </a>

                            @if (Route::has('register'))
                                <a
                                    href="{{ route('register') }}"
                                    class="inline-flex items-center px-4 py-2 text-xs sm:text-sm font-bold rounded-xl border border-zinc-300/80 dark:border-zinc-700/80 bg-white/90 dark:bg-zinc-800/90 text-zinc-900 dark:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-all shadow-xs"
                                >
                                    {{ __('Register') }}
                                </a>
                            @endif
                        @endauth
                    @endif
                </nav>
            </div>
        </header>

        {{-- Main Hero & Summarizer Component --}}
        <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 space-y-16">
            <livewire:website-summarizer />

            {{-- Feature Grid: Professional & Colorful Highlights --}}
            <section class="space-y-8 pt-6 border-t border-zinc-200/60 dark:border-zinc-800/60">
                <div class="text-center space-y-2">
                    <h2 class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-zinc-50">
                        Engineered for Speed, Security &amp; Accuracy
                    </h2>
                    <p class="text-sm sm:text-base text-zinc-500 dark:text-zinc-400 max-w-xl mx-auto">
                        A robust, full-stack Laravel solution built with modern asynchronous inference and resilient safety layers.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    {{-- Card 1: Groq Speed --}}
                    <div class="relative group p-6 rounded-3xl bg-white/60 dark:bg-zinc-900/60 border border-zinc-200/80 dark:border-zinc-800/80 backdrop-blur-md shadow-xs hover:shadow-xl hover:shadow-amber-500/5 hover:border-amber-500/40 transition-all duration-300">
                        <div class="size-11 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-4 transition group-hover:scale-110">
                            <flux:icon name="bolt" class="size-6" />
                        </div>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100 mb-1.5">
                            ⚡ Groq LPU™ Inference
                        </h3>
                        <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            Ultra-fast response times reaching 300+ tokens per second for instantaneous summarization.
                        </p>
                    </div>

                    {{-- Card 2: OpenRouter Cloud --}}
                    <div class="relative group p-6 rounded-3xl bg-white/60 dark:bg-zinc-900/60 border border-zinc-200/80 dark:border-zinc-800/80 backdrop-blur-md shadow-xs hover:shadow-xl hover:shadow-blue-500/5 hover:border-blue-500/40 transition-all duration-300">
                        <div class="size-11 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-4 transition group-hover:scale-110">
                            <flux:icon name="globe-alt" class="size-6" />
                        </div>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100 mb-1.5">
                            🌐 OpenRouter Cloud
                        </h3>
                        <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            Powered by cutting-edge Gemini and Llama foundation models for high-comprehension insights.
                        </p>
                    </div>

                    {{-- Card 3: Bilingual Output --}}
                    <div class="relative group p-6 rounded-3xl bg-white/60 dark:bg-zinc-900/60 border border-zinc-200/80 dark:border-zinc-800/80 backdrop-blur-md shadow-xs hover:shadow-xl hover:shadow-emerald-500/5 hover:border-emerald-500/40 transition-all duration-300">
                        <div class="size-11 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-4 transition group-hover:scale-110">
                            <flux:icon name="language" class="size-6" />
                        </div>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100 mb-1.5">
                            🇧🇩 Native বাংলা &amp; English
                        </h3>
                        <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            Full bilingual support with culturally natural Bengali grammar and concise English overviews.
                        </p>
                    </div>

                    {{-- Card 4: SSRF Safe --}}
                    <div class="relative group p-6 rounded-3xl bg-white/60 dark:bg-zinc-900/60 border border-zinc-200/80 dark:border-zinc-800/80 backdrop-blur-md shadow-xs hover:shadow-xl hover:shadow-violet-500/5 hover:border-violet-500/40 transition-all duration-300">
                        <div class="size-11 rounded-2xl bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center mb-4 transition group-hover:scale-110">
                            <flux:icon name="shield-check" class="size-6" />
                        </div>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100 mb-1.5">
                            🛡️ Zero-Trust SSRF Defense
                        </h3>
                        <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            Blocks loopback, link-local, and private intranet ranges (10.x, 192.168.x) for total network safety.
                        </p>
                    </div>
                </div>
            </section>
        </main>

        {{-- Footer --}}
        <footer class="w-full border-t border-zinc-200/80 dark:border-zinc-800/80 py-8 text-center text-xs text-zinc-500 dark:text-zinc-400 bg-white/40 dark:bg-zinc-950/40 backdrop-blur-md">
            <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-zinc-800 dark:text-zinc-200">WebSummarizer.ai</span>
                    <span>&bull;</span>
                    <span>Built with Laravel 13, Livewire 4, Groq &amp; OpenRouter</span>
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold">
                    <span class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                        <span class="size-1.5 rounded-full bg-emerald-500"></span>
                        Status: Operational
                    </span>
                    <span>&bull;</span>
                    <span>Privacy First</span>
                </div>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>
