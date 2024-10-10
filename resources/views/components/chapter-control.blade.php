<flux:button.group {{ $attributes }}>
    @if ($this->previousUrl)
        <flux:button icon="arrow-left" :href="$this->previousUrl" wire:navigate>{{ __('Prev') }}</flux:button>
    @endif
    <flux:button icon="list-bullet" :href="$chapter->comic->url()" wire:navigate>{{ __('Index') }}</flux:button>
    @if ($this->nextUrl)
        <flux:button icon-trailing="arrow-right" :href="$this->nextUrl" wire:navigate>{{ __('Next') }}</flux:button>
    @endif
</flux:button.group>
