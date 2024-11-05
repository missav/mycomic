<div
    x-data='{
        comics: @json(\App\Http\Resources\ComicResource::collection($comics)),
    }'
>
    <div class="md:flex">
        <x-comic-sidebar class="flex-none hidden md:flex">
            <flux:navlist.group heading="{{ __('Sort') }}" expandable>
                <flux:navlist.item
                    :href="localizedRoute('comics.index', request()->append('sort', null))"
                    :current="! request('sort')"
                    wire:navigate
                >
                    {{ __('Recent published') }}
                </flux:navlist.item>
                <flux:navlist.item
                    :href="localizedRoute('comics.index', request()->append('sort', '-update'))"
                    :current="request('sort') === '-update'"
                    wire:navigate
                >
                    {{ __('Recent updates') }}
                </flux:navlist.item>
                <flux:navlist.item
                    :href="localizedRoute('comics.index', request()->append('sort', '-views'))"
                    :current="request('sort') === '-views'"
                    wire:navigate
                >
                    {{ __('Most views') }}
                </flux:navlist.item>
            </flux:navlist.group>
        </x-comic-sidebar>
        <div class="grow">
            <div class="flex md:hidden justify-between mb-6">
                <flux:modal.trigger name="filters">
                    <flux:button icon="adjustments-horizontal">{{ __('Filter') }}</flux:button>
                </flux:modal.trigger>
                <flux:modal.menu name="filters" variant="flyout" class="space-y-6">
                    <x-comic-sidebar class="flex"></x-comic-sidebar>
                </flux:modal.menu>
                <flux:dropdown position="bottom" align="end">
                    @if (request()->get('sort') === '-update')
                        <flux:button icon="megaphone" icon-trailing="chevron-down">{{ __('Recent updates') }}</flux:button>
                    @elseif (request()->get('sort') === '-views')
                        <flux:button icon="clock" icon-trailing="chevron-down">{{ __('Most views') }}</flux:button>
                    @else
                        <flux:button icon="fire" icon-trailing="chevron-down">{{ __('Recent published') }}</flux:button>
                    @endif
                    <flux:menu>
                        <flux:menu.item icon="megaphone" :href="localizedRoute('comics.index', request()->append('sort', null))">
                            {{ __('Recent published') }}
                        </flux:menu.item>
                        <flux:menu.item icon="clock" :href="localizedRoute('comics.index', request()->append('sort', '-update'))">
                            {{ __('Recent updates') }}
                        </flux:menu.item>
                        <flux:menu.item icon="fire" :href="localizedRoute('comics.index', request()->append('sort', '-views'))">
                            {{ __('Most views') }}
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </div>
            @if (request('q'))
                <a href="{{ localizedRoute('comics.index', request()->append('q', null)) }}" wire:navigate>
                    <flux:badge class="mb-6" size="lg" color="amber">
                        {{ __('Keyword: :keyword', ['keyword' => request('q')]) }}
                        <flux:badge.close />
                    </flux:badge>
                </a>
            @endif
            <x-comic-thumbnails class="mb-8"></x-comic-thumbnails>
            <div>
                {{ $comics->links() }}
            </div>
        </div>
    </div>
</div>
