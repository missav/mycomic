@props([
    'comics' => collect(),
    'title' => null,
    'url' => null,
    'preload' => 0,
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
    @foreach ($comics as $index => $comic)
        <div class="@if ($half && $index >= $comics->count() / 2) hidden md:block @endif group relative">
            @if ($comic->exists)
                <div class="aspect-w-3 aspect-h-4 w-full overflow-hidden rounded-md bg-gray-200 group-hover:opacity-75 relative">
                    <a href="{{ $comic->url() }}">
                        <img
                            @if ($index < $preload)
                                src="{{ $comic->coverCdnUrl() }}"
                            @else
                                src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mN09omrBwADNQFuUCqPAwAAAABJRU5ErkJggg=="
                                data-src="{{ $comic->coverCdnUrl() }}"
                            @endif
                            @if ($preload > 0 && $loop->first)
                                fetchpriority="high"
                            @endif
                            alt="{{ $comic->name() }}"
                            class="@if ($index >= $preload) lozad @endif w-full h-full object-cover object-top lg:h-full lg:w-full -z-10"
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
            @else
                <div class="aspect-w-3 aspect-h-4 w-full overflow-hidden rounded-md bg-gray-200 group-hover:opacity-75 relative">
                    <a :href="comics[{{ $index }}].id ? comicUrl(comics[{{ $index }}]) : '#'">
                        <img
                            :src="comics[{{ $index }}].id ? cdn(comics[{{ $index }}].cover_image_path) : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mN09omrBwADNQFuUCqPAwAAAABJRU5ErkJggg=='"
                            :alt="comics[{{ $index }}].id ? comics[{{ $index }}].name : ''"
                            class="w-full h-full object-cover object-top lg:h-full lg:w-full -z-10"
                        >
                        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-t from-black to-30% flex items-end justify-center px-3">
                            <div
                                x-text="comics[{{ $index }}].id ? (comics[{{ $index }}].recent_chapter_title + (comics[{{ $index }}].is_ended ? ' [{{ __('End') }}]' : '')) : ''"
                                class="text-white text-sm pb-3 truncate"
                            ></div>
                        </div>
                    </a>
                </div>
                <div class="mt-2 text-center">
                    <flux:subheading x-text="comics[{{ $index }}].id ? comics[{{ $index }}].name : '&nbsp;'" class="truncate">&nbsp;</flux:subheading>
                </div>
            @endif
        </div>
    @endforeach
</div>
