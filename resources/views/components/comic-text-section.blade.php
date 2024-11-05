@props([
    'title' => null,
    'url' => null,
    'comics' => [],
])

<div class="space-y-2.5">
    @if ($title)
        <flux:separator :text="__($title)" />
    @endif
    <x-comic-text-list :comics="$comics" />
    <div class="text-center pt-2">
        <flux:button size="xs" :href="$url" wire:navigate>{{ __('View more') }}</flux:button>
    </div>
</div>
