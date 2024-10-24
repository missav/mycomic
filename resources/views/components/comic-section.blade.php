@props([
    'title' => null,
    'url' => null,
    'lozad' => false,
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
<div {{ $attributes->merge(['class' => 'grid grid-cols-2 gap-x-6 gap-y-10 sm:grid-cols-3 lg:grid-cols-6 xl:gap-x-8']) }}>
    <template x-for="comic in comics">
        <div class="group relative">
            <div class="aspect-w-5 aspect-h-3 sm:aspect-w-1 sm:aspect-h-1 lg:aspect-w-3 lg:aspect-h-4 w-full overflow-hidden rounded-md bg-gray-200 group-hover:opacity-75">
                <a :href="comic.id ? comicUrl(comic) : '#'" wire:navigate>
                    <img
                        @if ($lozad)
                            :data-src="cdn(comic.cover_image_path)"
                            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mN09omrBwADNQFuUCqPAwAAAABJRU5ErkJggg=="
                        @else
                            :src="comic.cover_image_path ? cdn(comic.cover_image_path) : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mN09omrBwADNQFuUCqPAwAAAABJRU5ErkJggg=='"
                        @endif
                        :alt="comic.name"
                        class="lozad w-full h-full object-cover object-top lg:h-full lg:w-full"
                    >
                </a>
            </div>
            <div class="mt-2 text-center">
                <flux:subheading class="truncate">
                    <a :href="comicUrl(comic)" x-text="comic.name ? comic.name : '&nbsp;'" wire:navigate></a>
                </flux:subheading>
            </div>
        </div>
    </template>
</div>
