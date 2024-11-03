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
                    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 space-x-0 sm:space-x-3">
                        <flux:button x-show="! $wire.recentChapterId" icon="arrow-right-start-on-rectangle" variant="danger" ::href="appendRecommendId('{{ $comic->chapters->first()->url() }}')" href id="start">{{ __('Start reading') }}</flux:button>
                        <flux:button x-cloak x-show="$wire.recentChapterId" icon="arrow-path" variant="danger" ::href="$wire.recentChapterId ? chapterUrl($wire.recentChapterId) : ''" href id="continue" class="!ml-0 !mt-0">{{ __('Continue reading') }} - <span id="recent-chapter-title"></span></flux:button>
                        <flux:button x-show="! $wire.isSynced" icon="bookmark" variant="filled" disabled>{{ __('Bookmark') }}</flux:button>
                        <flux:modal.trigger x-cloak x-show="$wire.isSynced && ! $wire.isLoggedIn" name="login">
                            <flux:button icon="bookmark" variant="filled" @click="$wire.actionAfterLogin = 'bookmark';">{{ __('Bookmark') }}</flux:button>
                        </flux:modal.trigger>
                        <flux:button x-cloak x-show="$wire.isLoggedIn && $wire.hasBookmarked" wire:click="unbookmark" icon="check" variant="primary">{{ __('Bookmarked') }}</flux:button>
                        <flux:button x-cloak x-show="$wire.isLoggedIn && ! $wire.hasBookmarked" wire:click="bookmark" icon="bookmark" variant="filled">{{ __('Bookmark') }}</flux:button>
                        <flux:dropdown position="bottom" align="start">
                            <flux:button icon="share" variant="ghost" class="w-full">{{ __('Share to friends') }}</flux:button>
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
        <div>
            <flux:heading size="lg">{{ __('Review') }}</flux:heading>
            <div class="mt-3 flex items-center">
                <div>
                    <div class="flex items-center">
                        @foreach (range(1, 5) as $rating)
                            <svg class="h-5 w-5 flex-shrink-0 {{ $rating <= $this->averageRatings ? 'text-yellow-400': 'text-gray-300' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" clip-rule="evenodd" />
                            </svg>
                        @endforeach
                    </div>
                    <p class="sr-only">{{ __(':current out of 5 stars') }}</p>
                </div>
                <p class="ml-2 text-sm text-gray-500 dark:text-white/80">{{ __('Based on :count reviews', ['count' => $this->ratings->sum()]) }}</p>
            </div>
            <div class="mt-6">
                <h3 class="sr-only">{{ __('Review data') }}</h3>
                <dl class="space-y-3">
                    @foreach (range(5, 1) as $rating)
                        <div class="flex items-center text-sm">
                            <dt class="flex flex-1 items-center">
                                <p class="w-3 font-medium text-gray-500 dark:text-white/80">{{ $rating }}<span class="sr-only"> {{ __('star reviews') }}</span></p>
                                <div aria-hidden="true" class="ml-1 flex flex-1 items-center">
                                    <svg class="h-5 w-5 flex-shrink-0 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" clip-rule="evenodd" />
                                    </svg>
                                    <div class="relative ml-3 flex-1">
                                        <div class="h-3 rounded-full border border-gray-200 bg-gray-100"></div>
                                        @if ($this->ratings->get($rating, 0) > 0)
                                            <div style="width: calc({{ $this->ratings->get($rating, 0) }} / {{ $this->ratings->sum() }} * 100%)" class="absolute inset-y-0 rounded-full border border-yellow-400 bg-yellow-400"></div>
                                        @endif
                                    </div>
                                </div>
                            </dt>
                            <dd class="ml-3 w-10 text-right text-sm tabular-nums text-gray-500 dark:text-white/80">{{ $this->ratings->sum() === 0 ? 0 : ceil($this->ratings->get($rating, 0) / $this->ratings->sum() * 100) }}%</dd>
                        </div>
                    @endforeach
                </dl>
                <div class="text-center mt-6">
                    <flux:modal.trigger name="review">
                        <flux:button size="sm">{{ __('Post review') }}</flux:button>
                    </flux:modal.trigger>
                    <flux:modal name="review" class="md:w-96 space-y-6 text-left">
                        <form wire:submit.prevent="review" class="space-y-6">
                            <flux:radio.group :label="__('Rating')" wire:model="rating">
                                <flux:radio value="5" label="★★★★★" />
                                <flux:radio value="4" label="★★★★" />
                                <flux:radio value="3" label="★★★" />
                                <flux:radio value="2" label="★★" />
                                <flux:radio value="1" label="★" />
                            </flux:radio.group>
                            <flux:textarea :label="__('Review text')" wire:model="text" />
                            <div class="flex">
                                <flux:spacer />
                                <flux:button type="submit" variant="primary">{{ __('Post review') }}</flux:button>
                            </div>
                        </form>
                    </flux:modal>
                </div>
            </div>
        </div>
        <x-comic-text-list title="Recent updates" :url="localizedRoute('comics.index', ['sort' => '-update'])" :comics="$this->recentUpdatedComics"></x-comic-text-list>
        <x-comic-text-list title="Recent published" :url="localizedRoute('comics.index', ['sort' => '-id'])" :comics="$this->recentPublishedComics"></x-comic-text-list>
    </div>
    <x-auth-modal></x-auth-modal>
</div>
