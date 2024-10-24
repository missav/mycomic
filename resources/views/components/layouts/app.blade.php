<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? config('app.name') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @fluxStyles
    </head>
    <body
        x-data="{
            cdnUrl: '{{ config('app.cdn_url') }}',
            cdn(path) {
                return this.cdnUrl + path;
            },
            comicUrl(comic) {
                let url = '{{ localizedRoute('comics.view', ['comic' => ':comic']) }}'.replace(':comic', comic.id);

                if (comic.recommend_id) {
                    url += `#${comic.recommend_id}`;
                }

                return url;
            },
            placeholders(count) {
                let i, placeholders = [];

                for (i = 0; i < count; i++) {
                    placeholders.push([]);
                }

                return placeholders;
            },
            lozadObserve() {
                window.lozad('.lozad', {
                    rootMargin: '0px 0px 500px 0px',
                }).observe();
            },
            getRecommendations(scenario, count) {
                return new Promise(resolve => {
                    recombeeClient.send(new recombee.RecommendItemsToUser(window.user_uuid, count, {
                        scenario: scenario,
                        cascadeCreate: true,
                        returnProperties: true,
                        includedProperties: [
                            'name',
                            'cover_image_path',
                        ],
                    })).then(response => {
                        const recommendations = response.recomms.map(item => {
                            item.values.id = item.id;
                            item.values.recommend_id = response.recommId;

                            return item.values;
                        });

                        resolve(recommendations);
                    });
                });
            },
        }"
        x-init="$nextTick(() => {
            lozadObserve();
        })"
        class="relative min-h-screen bg-white dark:bg-zinc-800"
    >
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
                <flux:tooltip content="{{ __('Toggle dark mode') }}" position="bottom">
                    <flux:navbar.item class="hidden md:flex" icon="moon" icon-variant="solid" label="{{ __('Toggle dark mode') }}" x-data x-on:click.prevent="$store.darkMode.toggle()" />
                </flux:tooltip>
                <flux:dropdown position="bottom" align="end">
                    <flux:tooltip content="{{ __('Switch language') }}" position="bottom">
                        <flux:navbar.item class="hidden md:flex" :square="true">
                            <img width="20" height="20" src="{{ asset('img/flags/' . \App\Enums\Locale::current()->value . '.png') }}" alt="{{ \App\Enums\Locale::current()->label() }}">
                        </flux:navbar.item>
                    </flux:tooltip>
                    <flux:menu>
                        @foreach (\App\Enums\Locale::cases() as $locale)
                            <flux:menu.item :href="localizedRoute($locale)">
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

            <flux:spacer />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="moon" icon-variant="solid" x-on:click.prevent="$store.darkMode.toggle()">{{ __('Toggle dark mode') }}</flux:navlist.item>
            </flux:navlist>
        </flux:sidebar>

        <flux:main container class="mt-14">
            {{ $slot }}
        </flux:main>

        @livewireScripts
        @fluxScripts

        <script>
            window.recommendId = window.location.hash.slice(1);
            window.timeouts = [];
        </script>

        <script data-navigate-once>
            window.pushTimeout = (callback, ms) => {
                window.timeouts.push(setTimeout(callback, ms));
            };

            document.addEventListener('livewire:init', () => {
                window.user_uuid = Cookies.get('user_uuid');

                if (! window.user_uuid) {
                    if (window.crypto && window.crypto.randomUUID) {
                        window.user_uuid = window.crypto.randomUUID();
                    } else {
                        const generateUUID = () => {
                            let d = new Date().getTime();
                            let d2 = ((typeof performance !== 'undefined') && performance.now && (performance.now() * 1000)) || 0;

                            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
                                let r = Math.random() * 16;

                                if (d > 0) {
                                    r = (d + r) % 16 | 0;
                                    d = Math.floor(d / 16);
                                } else {
                                    r = (d2 + r) % 16 | 0;
                                    d2 = Math.floor(d2 / 16);
                                }

                                return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
                            });
                        }

                        window.user_uuid = generateUUID();
                    }

                    Cookies.set('user_uuid', window.user_uuid, { expires: 365 });
                }
            });

            document.addEventListener('livewire:navigate', () => {
                window.timeouts.forEach(timeout => {
                    clearTimeout(timeout);
                });
            });

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
