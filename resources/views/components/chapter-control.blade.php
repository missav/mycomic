@props(['chapter', 'previousUrl', 'nextUrl'])

<flux:button.group {{ $attributes }}>
    <flux:button icon="chevron-double-left" :href="$previousUrl" :disabled="! $previousUrl">{{ __('Prev chapter') }}</flux:button>
    <flux:button :href="$chapter->comic->url()">{{ __('Back to index') }}</flux:button>
    <flux:button icon-trailing="chevron-double-right" :href="$nextUrl" :disabled="! $nextUrl">{{ __('Next chapter') }}</flux:button>
</flux:button.group>
