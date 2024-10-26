<!DOCTYPE html>
<html lang="{{ \App\Enums\Locale::from(app()->getLocale())->code() }}">
    <head>
        <script>
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','{{ \App\Seo::gtmId() }}');
        </script>
        <link rel="alternate" hreflang="zh-Hant" href="{{ localizedRoute(\App\Enums\Locale::ZH) }}" />
        <link rel="alternate" hreflang="zh-Hans" href="{{ localizedRoute(\App\Enums\Locale::CN) }}" />
        <link rel="alternate" hreflang="x-default" href="{{ localizedRoute(\App\Enums\Locale::from(config('app.fallback_locale'))) }}" />
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="{{ \App\Seo::description() }}" />
        <meta name="keywords" content="{{ \App\Seo::keywords() }}" />
        <meta name="author" content="{{ \App\Seo::authors() }}">
        <meta property="og:url" content="{{ request()->url() }}" />
        <meta property="og:site_name" content="{{ \App\Seo::site() }}" />
        <meta property="og:title" content="{{ \App\Seo::title() }}" />
        <meta property="og:description" content="{{ \App\Seo::description() }}" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="{{ \App\Seo::image() }}" />
        <meta name="twitter:image" content="{{ \App\Seo::image() }}" />
        <meta name="twitter:image:alt" content="{{ \App\Seo::title() }}" />
        <meta name="twitter:title" content="{{ \App\Seo::title() }}" />
        <meta name="twitter:description" content="{{ \App\Seo::description() }}" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:site" content="{{ \App\Seo::twitter() }}" />
        <meta name="twitter:creator" content="{{ \App\Seo::twitter() }}" />
        <title>{{ \App\Seo::title() }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
        <link rel="icon" type="image/x-icon" href="{{ cdn('img/favicon.png') }}">
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

                return this.appendRecommendId(url);
            },
            chapterUrl(chapter) {
                let url = '{{ localizedRoute('chapters.view', ['chapter' => ':chapter']) }}'.replace(':chapter', chapter.id);

                if (chapter.recommend_id) {
                    url += `#${chapter.recommend_id}`;
                }

                return this.appendRecommendId(url);
            },
            appendRecommendId(url) {
                return url + (window.recommendId ? `#${window.recommendId}` : '');
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
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ \App\Seo::gtmId() }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
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
                            <img width="20" height="20" src="{{ cdn('img/flags/' . \App\Enums\Locale::current()->value . '.png') }}" alt="{{ \App\Enums\Locale::current()->label() }}">
                        </flux:navbar.item>
                    </flux:tooltip>
                    <flux:menu>
                        @foreach (\App\Enums\Locale::cases() as $locale)
                            <flux:menu.item :href="localizedRoute($locale)">
                                <img width="14" height="14" src="{{ cdn("img/flags/{$locale->value}.png") }}" alt="{{ $locale->label() }}" class="mr-2">
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
        {!! \App\Seo::jsonLdScript() !!}
    </body>
</html>
