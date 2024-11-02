@script
    <script>
        window.pushTimeout(() => {
            $wire.sync().then(() => {
                if ($wire.recentChapterId) {
                    document.getElementById('recent-chapter-title').innerText = document.getElementById('recent-chapter').innerText;
                }
            });
        }, 10);

        window.pushTimeout(() => {
            recombeeClient.send(new recombee.AddDetailView(window.userUuid, {{ $comic->id }}, {
                cascadeCreate: true,
                recommId: window.recommendId,
            }));
        }, 3000);
    </script>
@endscript

<div class="flex items-stretch">
    <div class="w-3/4 grow">
        <flux:breadcrumbs class="mb-4">
            <flux:breadcrumbs.item :href="localizedRoute('home')" icon="home" />
            <flux:breadcrumbs.item :href="localizedRoute('comics.index')">{{ __('Comic database') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $comic->name() }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:card class="flex flex-col sm:flex-row">
            <div class="sm:hidden aspect-w-2 aspect-h-1 sm:aspect-w-3 sm:aspect-h-4 overflow-hidden rounded-t-md shadow-lg dark:shadow-gray-500/40 -m-6 mb-6">
                <img src="{{ $comic->coverCdnUrl() }}" alt="{{ $comic->name() }}" class="object-cover object-top">
            </div>
            <div class="grow">
                <flux:subheading>{{ __(':year / :count chapters', ['year' => $comic->year, 'count' => $comic->chapters()->count()]) }}</flux:subheading>
                <flux:heading size="xl">
                    {{ $comic->name() }}
                </flux:heading>
                @if ($comic->is_ended)
                    <flux:badge color="lime" size="sm" class="mt-2">{{ __('Ended') }}</flux:badge>
                @else
                    <flux:badge color="blue" size="sm" class="mt-2">{{ __('Ongoing') }}</flux:badge>
                @endif
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1 my-4">
                    <div>
                        <label class="text-sm text-zinc-500 dark:text-white/50">{{ __('Author') }}:</label>
                        <span class="text-sm text-zinc-800 dark:text-white">
                            {!! $comic
                                ->authors
                                ->map(fn (\App\Models\Author $author) =>
                                    '<a href="' . $author->url() . '" class="hover:underline underline-offset-4">' . $author->name . '</a>'
                                )
                                ->implode(', ') !!}
                        </span>
                    </div>
                    <div>
                        <label class="text-sm text-zinc-500 dark:text-white/50">{{ __('Last updated') }}:</label>
                        <span class="text-sm text-zinc-800 dark:text-white">
                            <time datetime="{{ $comic->last_updated_on->toDateString() }}">{{ $comic->last_updated_on->toDateString() }}</time>
                        </span>
                    </div>
                    <div>
                        <label class="text-sm text-zinc-500 dark:text-white/50">{{ __('Genre') }}:</label>
                        <span class="text-sm text-zinc-800 dark:text-white">
                            {!! $comic
                                ->tags
                                ->map(fn (\App\Models\Tag $tag) =>
                                    '<a href="' . $tag->url() . '" class="hover:underline underline-offset-4">' . $tag->name . '</a>'
                                )
                                ->add('<a href="' . $comic->audienceUrl() . '" class="hover:underline underline-offset-4">' . $comic->audience->text() . '</a>')
                                ->implode(', ') !!}
                        </span>
                    </div>
                    <div>
                        <label class="text-sm text-zinc-500 dark:text-white/50">{{ __('Region') }}:</label>
                        <span class="text-sm text-zinc-800 dark:text-white">
                            {!! '<a href="' . $comic->countryUrl() . '" class="hover:underline underline-offset-4">' . $comic->country->text() . '</a>' !!}
                        </span>
                    </div>
                </div>
                <div class="md:w-4/5 text-zinc-800 dark:text-white">
                    @if (\Illuminate\Support\Str::length($comic->description()) > 150)
                        <div x-data="{ show: false }">
                            <div x-show="! show">
                                {{ \Illuminate\Support\Str::limit($comic->description(), 150) }}
                                <a href="#" @click.prevent="show = ! show" class="text-amber-500 hover:underline underline-offset-4">
                                    {{ __('Show all') }}
                                </a>
                            </div>
                            <div x-cloak x-show="show">
                                {{ $comic->description() }}
                            </div>
                        </div>
                    @else
                        {{ $comic->description() }}
                    @endif
                </div>
                <div class="mt-8">
                    <flux:button x-show="! $wire.recentChapterId" icon="arrow-right-start-on-rectangle" variant="danger" ::href="appendRecommendId('{{ $comic->chapters->first()->url() }}')" href id="start">{{ __('Start reading') }}</flux:button>
                    <flux:button x-cloak x-show="$wire.recentChapterId" icon="arrow-path" variant="danger" ::href="$wire.recentChapterId ? chapterUrl($wire.recentChapterId) : ''" href id="continue">{{ __('Continue reading') }} - <span id="recent-chapter-title"></span></flux:button>
                    <flux:button x-show="! $wire.isSynced" icon="bookmark" variant="filled" class="ml-2" disabled>{{ __('Bookmark') }}</flux:button>
                    <flux:modal.trigger x-cloak x-show="$wire.isSynced && ! $wire.isLoggedIn" name="login">
                        <flux:button icon="bookmark" variant="filled" class="ml-2" @click="$wire.actionAfterLogin = 'bookmark';">{{ __('Bookmark') }}</flux:button>
                    </flux:modal.trigger>
                    <flux:button x-cloak x-show="$wire.isLoggedIn && $wire.hasBookmarked" wire:click="unbookmark" icon="check" variant="primary" class="ml-2">{{ __('Bookmarked') }}</flux:button>
                    <flux:button x-cloak x-show="$wire.isLoggedIn && ! $wire.hasBookmarked" wire:click="bookmark" icon="bookmark" variant="filled" class="ml-2">{{ __('Bookmark') }}</flux:button>
                    <flux:dropdown position="bottom" align="start">
                        <flux:button icon="share" variant="ghost" class="ml-2">{{ __('Share to friends') }}</flux:button>
                        <flux:menu>
                            <flux:menu.item :href="$comic->shareUrl('whatsapp')" target="_blank">
                                {{ __('Share via :channel', ['channel' => 'Whatsapp']) }}
                            </flux:menu.item>
                            <flux:menu.item :href="$comic->shareUrl('telegram')" target="_blank">
                                {{ __('Share via :channel', ['channel' => 'Telegram']) }}
                            </flux:menu.item>
                            <flux:menu.item :href="$comic->shareUrl('twitter')" target="_blank">
                                {{ __('Share via :channel', ['channel' => 'X (Twitter)']) }}
                            </flux:menu.item>
                            <flux:menu.item :href="$comic->shareUrl('email')" target="_blank">
                                {{ __('Share via :channel', ['channel' => 'Email']) }}
                            </flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                </div>
            </div>
            <div class="hidden sm:block flex-none sm:w-40 mt-6 sm:mt-0 sm:ml-8">
                <div class="aspect-w-2 aspect-h-1 sm:aspect-w-3 sm:aspect-h-4 overflow-hidden rounded-md shadow-lg dark:shadow-gray-500/40">
                    <img src="{{ $comic->coverCdnUrl() }}" alt="{{ $comic->name }}" class="object-cover object-top">
                </div>
            </div>
        </flux:card>
        <div class="mt-8 mb-12">
            @foreach ($comic->chapters->reverse()->groupBy(fn (\App\Models\Chapter $chapter) => $chapter->type()) as $group => $chapters)
                <div
                    x-cloak
                    x-data='{
                        chapters: @json(\App\Http\Resources\ChapterResource::collection($chapters)),
                        decending: true,
                        toggleSorting() {
                            this.chapters = this.chapters.reverse();
                            this.decending = ! this.decending;
                        },
                    }'
                >
                    <flux:subheading size="xl" class="flex justify-between items-center mt-8 mb-4">
                        <div>{{ $group }}</div>
                        <flux:button x-show="decending" @click="toggleSorting" size="sm" variant="ghost" icon="arrow-down-right">{{ __('Decending') }}</flux:button>
                        <flux:button x-show="! decending" @click="toggleSorting" size="sm" variant="ghost" icon="arrow-up-right">{{ __('Ascending') }}</flux:button>
                    </flux:subheading>
                    <div class="grid grid-cols-3 gap-4">
                        <template x-for="chapter in chapters">
                            <flux:button ::href="chapterUrl(chapter)" href x-text="chapter.title" ::id="chapter.id === $wire.recentChapterId ? 'recent-chapter' : ''" wire:navigate>&nbsp;</flux:button>
                        </template>
                    </div>
                </div>
            @endforeach
        </div>
        <flux:separator class="my-8" text="{{ __('Recommended for you') }}" />
        <div
            x-data='{ comics: placeholders(12) }'
            x-init="$nextTick(async () => comics = await getRecommendations(prefixScenario('comic-list-recommended'), 12));"
        >
            <x-comic-section></x-comic-section>
        </div>
    </div>
    <div class="w-1/4 ml-8 hidden lg:block text-white space-y-8">
        <x-comic-text-list title="Recent updates" :url="localizedRoute('comics.index', ['sort' => '-update'])" :comics="$this->recentUpdatedComics"></x-comic-text-list>
        <x-comic-text-list title="Recent published" :url="localizedRoute('comics.index', ['sort' => '-id'])" :comics="$this->recentPublishedComics"></x-comic-text-list>
    </div>
    <x-auth-modal></x-auth-modal>
</div>
