@props(['title' => null, 'url' => null])

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
<div {{ $attributes->merge(['class' => 'grid grid-cols-2 gap-x-6 gap-y-10 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:gap-x-8']) }}>
    <template x-for="comic in comics">
        <div class="group relative">
            <div class="aspect-w-3 aspect-h-4 w-full overflow-hidden rounded-md bg-gray-200 group-hover:opacity-75 shadow-lg dark:shadow-gray-500/40">
                <a :href="comic.url" wire:navigate>
                    <img
                        :src="comic.coverCdnUrl"
                        :alt="comic.name"
                        class="h-full w-full object-cover object-center lg:h-full lg:w-full"
                    >
                </a>
            </div>
            <div class="mt-2 text-center">
                <flux:subheading class="truncate">
                    <a :href="comic.url" x-text="comic.name" wire:navigate></a>
                </flux:subheading>
            </div>
        </div>
    </template>
</div>
