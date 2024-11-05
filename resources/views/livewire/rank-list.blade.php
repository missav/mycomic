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
        <flux:table class="mb-8">
            <flux:columns>
                <flux:column></flux:column>
                <flux:column>{{ __('Comic') }}</flux:column>
                <flux:column class="hidden lg:table-cell">{{ __('Author') }}</flux:column>
                <flux:column class="hidden lg:table-cell">{{ __('Recent chapter') }}</flux:column>
                <flux:column class="table-cell lg:hidden" align="right">{{ __('Recent chapter') }}</flux:column>
                <flux:column class="hidden lg:table-cell">{{ __('Last updated') }}</flux:column>
                <flux:column class="hidden lg:table-cell" align="right">{{ __('Rating') }}</flux:column>
            </flux:columns>
            <flux:rows>
                @foreach ($comics as $comic)
                    <flux:row>
                        <flux:cell class="font-mono">
                            @if ($loop->iteration <= 3)
                                <flux:badge color="red" variant="solid" size="sm" inset="top bottom">{{ $loop->iteration }}</flux:badge>
                            @else
                                {{ $loop->iteration }}
                            @endif
                        </flux:cell>
                        <flux:cell>
                            <a href="{{ $comic->url() }}" class="hover:underline underline-offset-4" wire:navigate>
                                {{ \Illuminate\Support\Str::limit($comic->name(), 25) }}
                            </a>
                        </flux:cell>
                        <flux:cell class="hidden lg:table-cell">
                            {!! $comic
                                ->authors
                                ->map(fn (\App\Models\Author $author) =>
                                    '<a href="' . $author->url() . '" class="hover:underline underline-offset-4" wire:navigate>' . $author->name . '</a>'
                                )
                                ->implode(', ') !!}
                        </flux:cell>
                        <flux:cell class="hidden lg:table-cell">
                            <a href="{{ $comic->recentChapter->url() }}" class="text-amber-500 hover:underline underline-offset-4" wire:navigate>
                                {{ \Illuminate\Support\Str::limit($comic->recentChapter->title(), 8) }}
                            </a>
                        </flux:cell>
                        <flux:cell class="table-cell lg:hidden" align="end">
                            <a href="{{ $comic->recentChapter->url() }}" class="text-amber-500 hover:underline underline-offset-4" wire:navigate>
                                {{ \Illuminate\Support\Str::limit($comic->recentChapter->title(), 8) }}
                            </a>
                        </flux:cell>
                        <flux:cell class="hidden lg:table-cell font-mono">{{ $comic->last_updated_on->toDateString() }}</flux:cell>
                        <flux:cell class="hidden lg:table-cell" align="end" variant="strong">{{ $comic->average_rating }}</flux:cell>
                    </flux:row>
                @endforeach
            </flux:rows>
        </flux:table>
    </div>
</div>
