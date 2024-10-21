<div
    x-data='{
        comics: @json(\App\Http\Resources\ComicResource::collection($comics)),
    }'
>
    <div class="lg:flex">
        <flux:navlist class="flex-none hidden lg:flex w-40 mr-8 space-y-6">
            <flux:navlist.group heading="{{ __('Region') }}" expandable>
                <flux:navlist.item
                    :href="route('comics.index', request()->append('filter.country', null))"
                    :current="! request('filter.country')"
                    wire:navigate
                >
                    {{ __('All') }}
                </flux:navlist.item>
                @foreach (\App\Enums\ComicCountry::cases() as $comicCountry)
                    <flux:navlist.item
                        :href="route('comics.index', request()->append('filter.country', $comicCountry->value))"
                        :current="request('filter.country') === $comicCountry->value"
                        wire:navigate
                    >
                        {{ $comicCountry->text() }}
                    </flux:navlist.item>
                @endforeach
            </flux:navlist.group>
            <flux:navlist.group heading="{{ __('Audience') }}" expandable>
                <flux:navlist.item
                    :href="route('comics.index', request()->append('filter.audience', null))"
                    :current="! request('filter.audience')"
                    wire:navigate
                >
                    {{ __('All') }}
                </flux:navlist.item>
                @foreach (\App\Enums\ComicAudience::cases() as $comicAudience)
                    <flux:navlist.item
                        :href="route('comics.index', request()->append('filter.audience', $comicAudience->value))"
                        :current="request('filter.audience') === $comicAudience->value"
                        wire:navigate
                    >
                        {{ $comicAudience->text() }}
                    </flux:navlist.item>
                @endforeach
            </flux:navlist.group>
            <flux:navlist.group heading="{{ __('Year') }}" expandable :expanded="(bool) request('filter.year')">
                <flux:navlist.item
                    :href="route('comics.index', request()->append('filter.year', null))"
                    :current="! request('filter.year')"
                    wire:navigate
                >
                    {{ __('All') }}
                </flux:navlist.item>
                @foreach (array_merge(range(now()->year, 2010), ['200x', '199x', '198x', '197x']) as $year)
                    <flux:navlist.item
                        :href="route('comics.index', request()->append('filter.year', $year))"
                        :current="request('filter.year') === (string) $year"
                        wire:navigate
                    >
                        {{ __($year) }}
                    </flux:navlist.item>
                @endforeach
            </flux:navlist.group>
            <flux:navlist.group heading="{{ __('Audience') }}" expandable>
                <flux:navlist.item
                    :href="route('comics.index', request()->append('filter.audience', null))"
                    :current="! request('filter.audience')"
                    wire:navigate
                >
                    {{ __('All') }}
                </flux:navlist.item>
                <flux:navlist.item
                    :href="route('comics.index', request()->append('filter.audience', $comicAudience->value))"
                    :current="request('filter.audience') === $comicAudience->value"
                    wire:navigate
                >
                    {{ $comicAudience->text() }}
                </flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>
        <div>
            <x-comic-section></x-comic-section>
            <div class="mt-8">
                {{ $comics->links() }}
            </div>
        </div>
    </div>
</div>
