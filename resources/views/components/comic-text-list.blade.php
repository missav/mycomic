@props([
    'title' => null,
    'url' => null,
    'comics' => [],
])

<div class="space-y-2.5">
    @if ($title)
        <flux:separator :text="__($title)" />
    @endif
    @foreach ($comics as $comic)
        <div class="flex">
            <div class="truncate grow text-sm text-zinc-500 dark:text-zinc-300">
                [<a href="{{ $comic->recentChapter->url() }}" class="text-amber-500 hover:underline underline-offset-4" wire:navigate>{{ $comic->recentChapter->title() }}</a>]
                <a href="{{ $comic->url() }}" class="hover:underline underline-offset-4" title="{{ $comic->name() }}" wire:navigate>{{ $comic->name() }}</a>
            </div>
            <div class="flex-none text-sm text-zinc-500 dark:text-zinc-300 ml-2">
                {{ $comic->last_updated_on->format(__('m-d')) }}
            </div>
        </div>
    @endforeach
    <div class="text-center pt-2">
        <flux:button size="xs" :href="$url" wire:navigate>{{ __('View more') }}</flux:button>
    </div>
</div>
