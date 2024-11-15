@props([
    'comics' => collect(),
    'title' => null,
    'url' => null,
    'lozad' => false,
    'half' => true,
])

@if ($title || $url)
    <div class="flex items-center justify-between mb-6">
        @if ($title)
            <flux:heading size="xl">{{ __($title) }}</flux:heading>
        @endif
        @if ($url)
            <flux:button variant="filled" href="{{ $url }}" icon-trailing="arrow-up-right">{{ __('View more') }}</flux:button>
        @endif
    </div>
@endif
<div {{ $attributes->merge(['class' => 'grid grid-cols-3 md:grid-cols-6 gap-x-2 lg:gap-x-4 xl:gap-x-6 gap-y-6 lg:gap-y-8']) }}>
    @foreach ($comics as $comic)
        <div class="group relative">
            <div class="aspect-w-3 aspect-h-4 w-full overflow-hidden rounded-md bg-gray-200 group-hover:opacity-75 relative">
                <a href="{{ $comic->url() }}">
                    <img
                        src="{{ $comic->coverCdnUrl() }}"
                        alt="{{ $comic->name() }}"
                        class="lozad w-full h-full object-cover object-top lg:h-full lg:w-full -z-10"
                    >
                    <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-t from-black to-30% flex items-end justify-center px-3">
                        <div class="text-white text-sm pb-3 truncate">
                            {{ $comic->recentChapterTitle() }}
                            @if ($comic->is_ended)
                                [{{ __('End') }}]
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            <div class="mt-2 text-center">
                <flux:subheading class="truncate">
                    {{ $comic->name() }}
                </flux:subheading>
            </div>
        </div>
    @endforeach
    <template x-for="(comic, index) in comics">
        <div class="group relative" @if ($half) :class="index >= (comics.length + {{ $comics->count() }}) / 2 ? 'hidden md:block' : ''" @endif>
            <div class="aspect-w-3 aspect-h-4 w-full overflow-hidden rounded-md bg-gray-200 group-hover:opacity-75 relative">
                <a :href="comic.id ? comicUrl(comic) : '#'">
                    <img
                        @if ($lozad)
                            :data-src="cdn(comic.cover_image_path)"
                            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mN09omrBwADNQFuUCqPAwAAAABJRU5ErkJggg=="
                        @else
                            :src="comic.cover_image_path ? cdn(comic.cover_image_path) : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mN09omrBwADNQFuUCqPAwAAAABJRU5ErkJggg=='"
                        @endif
                        :alt="comic.name"
                        class="lozad w-full h-full object-cover object-top lg:h-full lg:w-full -z-10"
                    >
                    <div x-cloak x-show="comic.recent_chapter_title" class="absolute top-0 left-0 w-full h-full bg-gradient-to-t from-black to-30% flex items-end justify-center px-3">
                        <div x-text="comic.recent_chapter_title + (comic.is_ended ? ' [{{ __('End') }}]' : '')" class="text-white text-sm pb-3 truncate"></div>
                    </div>
                </a>
            </div>
            <div class="mt-2 text-center">
                <flux:subheading class="truncate">
                    <a :href="comicUrl(comic)" x-text="comic.name ? comic.name : '&nbsp;'"></a>
                </flux:subheading>
            </div>
        </div>
    </template>
</div>
