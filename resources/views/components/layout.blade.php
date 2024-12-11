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
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
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
        <link rel="preconnect" href="{{ cdn() }}">
        <link rel="preload" as="font" href="/fonts/inter-latin-400-normal.woff2" type="font/woff2" crossorigin />
        <link rel="preload" as="font" href="/fonts/inter-latin-500-normal.woff2" type="font/woff2" crossorigin />
        <style>
            @font-face {
                font-family: 'Inter';
                font-style: normal;
                font-weight: 400;
                font-display: swap;
                src: local(''),
                    url('/fonts/inter-latin-400-normal.woff2') format('woff2'),
                    url('/fonts/inter-latin-400-normal.woff') format('woff');
            }

            @font-face {
                font-family: 'Inter';
                font-style: normal;
                font-weight: 500;
                font-display: swap;
                src: local(''),
                url('/fonts/inter-latin-500-normal.woff2') format('woff2'),
                url('/fonts/inter-latin-500-normal.woff') format('woff');
            }
        </style>
        <link rel="icon" type="image/x-icon" href="{{ cdn('img/favicon.png') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body
        x-cloak
        x-data="{
            locale: '{{ app()->getLocale() }}',
            cdnUrl: '{{ config('app.cdn_url') }}',
            maxComicId: {{ \App\Models\Comic::maxId() }},
            loginAction: null,
            cdn(path) {
                return this.cdnUrl + path;
            },
            isDesktop() {
                return document.documentElement.clientWidth >= 1024;
            },
            prefixScenario(scenario) {
                return (this.isDesktop() ? 'desktop' : 'mobile') + '-' + scenario;
            },
            comicUrl(comic) {
                let url = '{{ localizedRoute('comics.view', ['comic' => ':comic']) }}'.replace(':comic', comic.id);

                if (comic.recommend_id) {
                    url += `#${comic.recommend_id}`;
                }

                return this.appendRecommendId(url);
            },
            chapterUrl(chapter) {
                if (! chapter.id) {
                    chapter = { id: chapter };
                }

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
            syncUserUuid(userUuid) {
                if (userUuid) {
                    window.userUuid = userUuid;

                    Cookies.set('user_uuid', window.userUuid, { expires: 365 });
                }
            },
            getRecommendations(scenario, count, comicId) {
                console.log('c');
                const data = {
                    scenario: scenario,
                    cascadeCreate: true,
                    returnProperties: true,
                    includedProperties: [
                        this.locale === 'zh' ? 'name' : 'name_cn',
                        'recent_chapter_id',
                        this.locale === 'zh' ? 'recent_chapter_title' : 'recent_chapter_title_cn',
                        'recent_chapter_title_cn',
                        'cover_image_path',
                    ],
                };
                console.log('d');

                const transformResponse = response => response.recomms.map(item => {
                    item.values.id = item.id;
                    item.values.recommend_id = response.recommId;

                    if (this.locale === 'cn') {
                        item.values.name = item.values.name_cn;
                        item.values.recent_chapter_title = item.values.recent_chapter_title_cn;
                    }

                    return item.values;
                });
                console.log('e');

                if (comicId) {
                    return new Promise(resolve => {
                        if (window.userUuid) {
                            recombeeClient.send(new recombee.RecommendItemsToItem(comicId, window.userUuid, count, data)).then(response => {
                                resolve(transformResponse(response));
                            });
                        }
                    });
                }

                console.log('f');

                return new Promise(resolve => {
                    console.log('g');
                    if (window.userUuid) {
                        console.log('h');
                        recombeeClient.send(new recombee.RecommendItemsToUser(window.userUuid, count, data)).then(response => {
                            resolve(transformResponse(response));
                        });
                    }
                });
            },
        }"
        x-init="$nextTick(() => {
            lozadObserve();
        });"
        class="relative min-h-screen bg-slate-50 dark:bg-zinc-800 dark"
    >
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ \App\Seo::gtmId() }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <flux:header container class="fixed top-0 left-0 right-0 bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
            <flux:sidebar.toggle class="lg:hidden mr-2" icon="bars-3" inset="left" />

            <x-logo />

            <flux:navbar class="-mb-px max-lg:hidden">
                @foreach (\App\Menu::main() as $item)
                    <flux:navbar.item icon="{{ $item['icon'] }}" :href="localizedRoute($item['route'])" :current="request()->routeIs('*.' . $item['route'])">
                        {{ __($item['text']) }}
                    </flux:navbar.item>
                @endforeach
                <flux:navbar.item icon="arrow-path" ::href="comicUrl({ id: Math.floor(Math.random() * maxComicId) })" href>
                    {{ __('Random comic') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar>
                <form action="{{ localizedRoute('comics.index') }}" method="get" class="hidden md:block">
                    <flux:input icon="magnifying-glass" placeholder="{{ __('Search') }}..." size="sm" name="q" :value="request('q')" />
                </form>
                <flux:tooltip content="{{ __('Search') }}" position="bottom">
                    <flux:navbar.item
                        class="md:hidden"
                        icon="magnifying-glass"
                        icon-variant="solid"
                        @click="document.body.hasAttribute('data-show-stashed-sidebar') ? document.body.removeAttribute('data-show-stashed-sidebar') : document.body.setAttribute('data-show-stashed-sidebar', ''); document.getElementById('sidebar-search').focus();"
                        :aria-label="__('Search')"
                    />
                </flux:tooltip>
                <flux:tooltip content="{{ __('Toggle dark mode') }}" position="bottom">
                    <flux:navbar.item class="hidden md:flex" icon="moon" icon-variant="solid" x-data @click.prevent="$store.darkMode.toggle()" :aria-label="__('Toggle dark mode')" />
                </flux:tooltip>
                <flux:dropdown position="bottom" align="end">
                    <flux:tooltip content="{{ __('Switch language') }}" position="bottom">
                        <flux:navbar.item>
                            <img width="20" height="20" src="{{ \App\Enums\Locale::current()->flagUrl() }}" alt="{{ \App\Enums\Locale::current()->label() }}">
                        </flux:navbar.item>
                    </flux:tooltip>
                    <flux:menu>
                        @foreach (\App\Enums\Locale::cases() as $locale)
                            <flux:menu.item :href="localizedRoute($locale)">
                                <img width="14" height="14" src="{{ $locale->flagUrl() }}" alt="{{ $locale->label() }}" class="mr-2">
                                {{ $locale->label() }}
                            </flux:menu.item>
                        @endforeach
                    </flux:menu>
                </flux:dropdown>
            </flux:navbar>
        </flux:header>

        <flux:sidebar stashable sticky class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <form action="{{ localizedRoute('comics.index') }}" method="get">
                <flux:input icon="magnifying-glass" placeholder="{{ __('Search') }}..." size="sm" name="q" :value="request('q')" id="sidebar-search" />
            </form>

            <flux:navlist variant="outline">
                <flux:navlist.item icon="home" :href="localizedRoute('home')" :current="request()->routeIs('*.home')">
                    {{ __('Home') }}
                </flux:navlist.item>
                @foreach (\App\Menu::main() as $item)
                    <flux:navlist.item icon="{{ $item['icon'] }}" :href="localizedRoute($item['route'])" :current="request()->routeIs('*.' . $item['route'])">
                        {{ __($item['text']) }}
                    </flux:navlist.item>
                @endforeach
                <flux:navlist.item icon="arrow-path" ::href="comicUrl({ id: Math.floor(Math.random() * maxComicId) })" href>
                    {{ __('Random comic') }}
                </flux:navlist.item>
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="moon" icon-variant="solid" x-on:click.prevent="$store.darkMode.toggle()">{{ __('Toggle dark mode') }}</flux:navlist.item>
            </flux:navlist>
        </flux:sidebar>

        <flux:main container class="mt-14">
            {{ $slot }}

            @if (! request()->routeIs('*.chapters.view'))
                <footer>
                    <div class="text-center py-16">
                        <flux:tooltip :content="__('Back to the top')">
                            <flux:button variant="subtle" icon="arrow-up-circle" square @click="window.scrollTo({ top: 0, behavior: 'smooth' });" :aria-label="__('Back to the top')" />
                        </flux:tooltip>
                    </div>
                    <div>
                        <div class="xl:grid xl:grid-cols-3 xl:gap-8">
                            <div>
                                <div class="mb-4">
                                    <x-logo />
                                </div>
                                <flux:subheading class="text-balance">{{ \App\Seo::about() }}</flux:subheading>
                            </div>
                            <div class="mt-16 grid grid-cols-2 gap-8 xl:col-span-2 xl:mt-0">
                                <div class="md:grid md:grid-cols-2 md:gap-8">
                                    <div>
                                        <flux:heading size="lg">{{ __('Sort') }}</flux:heading>
                                        <ul role="list" class="mt-6 space-y-4">
                                            <li><a href="{{ localizedRoute('comics.index', request()->append('sort', null)) }}" class="text-sm/6 hover:text-gray-700 dark:text-gray-400 hover:dark:text-white">{{ __('Recent published') }}</a></li>
                                            <li><a href="{{ localizedRoute('comics.index', request()->append('sort', '-update')) }}" class="text-sm/6 hover:text-gray-700 dark:text-gray-400 hover:dark:text-white">{{ __('Recent updates') }}</a></li>
                                            <li><a href="{{ localizedRoute('comics.index', request()->append('sort', '-views')) }}" class="text-sm/6 hover:text-gray-700 dark:text-gray-400 hover:dark:text-white">{{ __('Most views') }}</a></li>
                                        </ul>
                                    </div>
                                    <div class="mt-10 md:mt-0">
                                        <flux:heading size="lg">{{ __('Audience') }}</flux:heading>
                                        <ul role="list" class="mt-6 space-y-4">
                                            @foreach (\App\Enums\ComicAudience::cases() as $comicAudience)
                                                <li><a href="{{ localizedRoute('comics.index', request()->append('filter.audience', $comicAudience->value)) }}" class="text-sm/6 hover:text-gray-700 dark:text-gray-400 hover:dark:text-white">{{ $comicAudience->text() }}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="md:grid md:grid-cols-2 md:gap-8">
                                    <div>
                                        <flux:heading size="lg">{{ __('Region') }}</flux:heading>
                                        <ul role="list" class="mt-6 space-y-4">
                                            @foreach (\App\Enums\ComicCountry::cases() as $comicCountry)
                                                <li><a href="{{ localizedRoute('comics.index', request()->append('filter.country', $comicCountry->value)) }}" class="text-sm/6 hover:text-gray-700 dark:text-gray-400 hover:dark:text-white">{{ $comicCountry->text() }}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="mt-10 md:mt-0">
                                        <flux:heading size="lg">{{ __('Progress') }}</flux:heading>
                                        <ul role="list" class="mt-6 space-y-4">
                                            <li><a href="{{ localizedRoute('comics.index', request()->append('filter.end', '0')) }}" class="text-sm/6 hover:text-gray-700 dark:text-gray-400 hover:dark:text-white">{{ __('Ongoing') }}</a></li>
                                            <li><a href="{{ localizedRoute('comics.index', request()->append('filter.end', '1')) }}" class="text-sm/6 hover:text-gray-700 dark:text-gray-400 hover:dark:text-white">{{ __('Ended') }}</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-16 border-t border-white/10 pt-8 sm:mt-20 lg:mt-24">
                            <p class="text-sm/6 text-gray-500">&copy; {{ now()->year }} {{ \App\Seo::site() }}</p>
                        </div>
                    </div>
                </footer>
            @endif
        </flux:main>
        {!! \App\Seo::jsonLdScript() !!}
    </body>
</html>
