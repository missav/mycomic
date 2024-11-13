@props(['chapter', 'previousUrl', 'nextUrl'])

<flux:button.group {{ $attributes }}>
    <flux:button size="sm" icon="chevron-double-left" :href="$previousUrl" :disabled="! $previousUrl">{{ __('Prev chapter') }}</flux:button>
    <flux:button size="sm" icon="chevron-left" @click="prevPage" ::disabled="currentPage === 1">{{ __('Prev page') }}</flux:button>
    <flux:select
        x-model="selectedPage"
        x-on:change="() => { jumpToPage(parseInt(selectedPage)); }"
        size="sm"
        class="!w-20"
    >
        @foreach (range(1, $chapter->pages) as $page)
            <flux:option>{{ $page }}</flux:option>
        @endforeach
    </flux:select>
    <flux:button size="sm" icon-trailing="chevron-right" @click="nextPage" ::disabled="currentPage >= pages.length">{{ __('Next page') }}</flux:button>
    <flux:button size="sm" icon-trailing="chevron-double-right" :href="$nextUrl" :disabled="! $nextUrl">{{ __('Next chapter') }}</flux:button>
</flux:button.group>
