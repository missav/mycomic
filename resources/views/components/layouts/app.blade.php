<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? config('app.name') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
        @vite('resources/css/app.css')
        @livewireStyles
        @fluxStyles
    </head>
    <body class="relative min-h-screen bg-white dark:bg-zinc-800">
        <flux:header container class="fixed top-0 left-0 right-0 bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
            <flux:sidebar.toggle class="lg:hidden mr-2" icon="bars-2" inset="left" />

            <flux:brand :href="localizedRoute('home')" class="dark:hidden" wire:navigate />
            <flux:brand :href="localizedRoute('home')" class="hidden dark:flex" wire:navigate />

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="book-open" :href="localizedRoute('comics.index')" :current="request()->routeIs('comics.index')" wire:navigate>
                    {{ __('Comic database') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="gap-0 md:gap-2">
                <form action="{{ localizedRoute('comics.index') }}" method="get">
                    <flux:input icon="magnifying-glass" placeholder="{{ __('Search') }}..." size="sm" name="q" :value="request('q')" />
                </form>
                <flux:tooltip content="{{ __('Toggle dark mode') }}" position="bottom" x-data x-on:keydown.d.window="if (document.activeElement.localName === 'body') $store.darkMode.toggle()">
                    <flux:navbar.item class="hidden md:flex" icon="moon" icon-variant="solid" href="#" label="{{ __('Toggle dark mode') }}" x-on:click.prevent="$store.darkMode.toggle()" />
                </flux:tooltip>
                <flux:dropdown position="bottom" align="end">
                    <flux:tooltip content="{{ __('Switch language') }}" position="bottom">
                        <flux:navbar.item class="hidden md:flex" :square="true">
                            <img width="20" height="20" src="{{ asset('img/flags/' . \App\Enums\Locale::current()->value . '.png') }}" alt="{{ \App\Enums\Locale::current()->label() }}">
                        </flux:navbar.item>
                    </flux:tooltip>
                    <flux:menu>
                        @foreach (\App\Enums\Locale::cases() as $locale)
                            <flux:menu.item :href="localizedRoute($locale)" wire:navigate>
                                <img width="14" height="14" src="{{ asset("img/flags/{$locale->value}.png") }}" alt="{{ $locale->label() }}" class="mr-2">
                                {{ $locale->label() }}
                            </flux:menu.item>
                        @endforeach
                    </flux:menu>
                </flux:dropdown>
            </flux:navbar>
        </flux:header>

        <flux:sidebar stashable sticky class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="home" :href="localizedRoute('home')" :current="request()->routeIs('home')" wire:navigate>
                    {{ __('Home') }}
                </flux:navlist.item>
                <flux:navlist.item icon="book-open" :href="localizedRoute('comics.index')" :current="request()->routeIs('comics.index')" wire:navigate>
                    {{ __('Comic database') }}
                </flux:navlist.item>
            </flux:navlist>
        </flux:sidebar>

        <flux:main container class="mt-14">
            {{ $slot }}
        </flux:main>

        @livewireScripts
        @fluxScripts

        <script data-navigate-once>
            document.addEventListener('livewire:navigated', () => {
                Alpine.store('darkMode').applyToBody();
            });

            Alpine.store('darkMode', {
                on: false,

                toggle() {
                    this.on = ! this.on;
                },

                init() {
                    this.on = this.wantsDarkMode();

                    Alpine.effect(() => {
                        document.dispatchEvent(new CustomEvent('dark-mode-toggled', { detail: { isDark: this.on }, bubbles: true }));

                        this.applyToBody();
                    })

                    let media = window.matchMedia('(prefers-color-scheme: dark)');

                    media.addEventListener('change', e => {
                        this.on = media.matches;
                    })
                },

                wantsDarkMode() {
                    let media = window.matchMedia('(prefers-color-scheme: dark)');

                    if (window.localStorage.getItem('darkMode') === '') {
                        return media.matches;
                    } else {
                        return JSON.parse(window.localStorage.getItem('darkMode'));
                    }
                },

                applyToBody() {
                    let state = this.on;

                    window.localStorage.setItem('darkMode', JSON.stringify(state));

                    state ? document.body.classList.add('dark') : document.body.classList.remove('dark');
                },
            });
        </script>
    </body>
</html>
