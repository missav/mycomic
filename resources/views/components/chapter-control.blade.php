@props(['chapter'])

<flux:button.group {{ $attributes }}>
    <flux:button size="sm" icon="chevron-double-left" :href="$this->previousUrl" :disabled="! $this->previousUrl" wire:navigate>{{ __('Prev chapter') }}</flux:button>
    <flux:button size="sm" icon="chevron-left" @click="prevPage">{{ __('Prev page') }}</flux:button>
    <flux:select
        x-model="selectedPage"
        x-on:change="() => { jumpToPage(parseInt(selectedPage)); }"
        size="sm"
        variant="listbox"
        searchable
        class="!w-20"
    >
        <x-slot name="search">
            <flux:select.search class="px-4" :placeholder="__('Page')" />
        </x-slot>
        @foreach (range(1, $chapter->pages) as $page)
            <flux:option>{{ $page }}</flux:option>
        @endforeach
    </flux:select>
    <flux:button size="sm" icon-trailing="chevron-right" @click="nextPage">{{ __('Next page') }}</flux:button>
    <flux:button size="sm" icon-trailing="chevron-double-right" :href="$this->nextUrl" :disabled="! $this->nextUrl" wire:navigate>{{ __('Next chapter') }}</flux:button>
</flux:button.group>
