<div
    x-data='{
        comics: @json(\App\Http\Resources\ComicResource::collection($comics)),
    }'
>
    <div class="md:flex">
        <x-comic-sidebar route="rank" class="flex-none hidden md:flex">
            <flux:navlist.group heading="{{ __('Sort') }}" expandable>
                <flux:navlist.item
                    :href="localizedRoute('rank', request()->append('sort', null))"
                    :current="! request('sort')"
                    wire:navigate
                >
                    {{ __('Daily rank') }}
                </flux:navlist.item>
                <flux:navlist.item
                    :href="localizedRoute('rank', request()->append('sort', '-week'))"
                    :current="request('sort') === '-week'"
                    wire:navigate
                >
                    {{ __('Weekly rank') }}
                </flux:navlist.item>
                <flux:navlist.item
                    :href="localizedRoute('rank', request()->append('sort', '-month'))"
                    :current="request('sort') === '-month'"
                    wire:navigate
                >
                    {{ __('Monthly rank') }}
                </flux:navlist.item>
                <flux:navlist.item
                    :href="localizedRoute('rank', request()->append('sort', '-views'))"
                    :current="request('sort') === '-views'"
                    wire:navigate
                >
                    {{ __('All-time rank') }}
                </flux:navlist.item>
            </flux:navlist.group>
        </x-comic-sidebar>
        <div class="grow">
            <div class="flex md:hidden justify-between mb-6">
                <flux:modal.trigger name="filters">
                    <flux:button icon="adjustments-horizontal">{{ __('Filter') }}</flux:button>
                </flux:modal.trigger>
                <flux:modal.menu name="filters" variant="flyout" class="space-y-6">
                    <x-comic-sidebar route="rank" class="flex"></x-comic-sidebar>
                </flux:modal.menu>
                <flux:dropdown position="bottom" align="end">
                    @if (request()->get('sort') === '-week')
                        <flux:button icon="chart-bar" icon-trailing="chevron-down">{{ __('Weekly rank') }}</flux:button>
                    @elseif (request()->get('sort') === '-week')
                        <flux:button icon="chart-bar" icon-trailing="chevron-down">{{ __('Weekly rank') }}</flux:button>
                    @elseif (request()->get('sort') === '-month')
                        <flux:button icon="chart-bar" icon-trailing="chevron-down">{{ __('Monthly rank') }}</flux:button>
                    @else
                        <flux:button icon="chart-bar" icon-trailing="chevron-down">{{ __('Daily rank') }}</flux:button>
                    @endif
                    <flux:menu>
                        <flux:menu.item :href="localizedRoute('rank', request()->append('sort', null))">
                            {{ __('Daily rank') }}
                        </flux:menu.item>
                        <flux:menu.item :href="localizedRoute('rank', request()->append('sort', '-week'))">
                            {{ __('Weekly rank') }}
                        </flux:menu.item>
                        <flux:menu.item :href="localizedRoute('rank', request()->append('sort', '-month'))">
                            {{ __('Monthly rank') }}
                        </flux:menu.item>
                        <flux:menu.item :href="localizedRoute('rank', request()->append('sort', '-views'))">
                            {{ __('All-time rank') }}
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </div>
            <x-comic-section class="mb-8"></x-comic-section>
        </div>
    </div>
</div>
