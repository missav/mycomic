<div
    x-data='{
        comics: @json(\App\Http\Resources\ComicResource::collection($comics)),
    }'
>
    <div class="md:flex">
        <flux:navlist class="flex-none hidden md:flex w-32 lg:w-40 mr-6 lg:mr-8 space-y-4">
            @if (request()->has('filter'))
                <flux:button size="sm" :href="route('comics.index', request()->append('filter', null))">{{ __('Reset filters') }}</flux:button>
            @endif
            <flux:navlist.group heading="{{ __('Region') }}" expandable>
                <flux:navlist.item
                    :href="route('comics.index', request()->append('filter.country', null))"
                    :current="! request()->has('filter.country')"
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
            <flux:navlist.group heading="{{ __('Genre') }}" expandable :expanded="request()->has('filter.tag')">
                <flux:navlist.item
                    :href="route('comics.index', request()->append('filter.tag', null))"
                    :current="! request()->has('filter.tag')"
                    wire:navigate
                >
                    {{ __('All') }}
                </flux:navlist.item>
                @foreach (\App\Models\Tag::cached() as $slug => $name)
                    <flux:navlist.item
                        :href="route('comics.index', request()->append('filter.tag', $slug))"
                        :current="request('filter.tag') === $slug"
                        wire:navigate
                    >
                        {{ $name }}
                    </flux:navlist.item>
                @endforeach
            </flux:navlist.group>
            <flux:navlist.group heading="{{ __('Audience') }}" expandable :expanded="request()->has('filter.audience')">
                <flux:navlist.item
                    :href="route('comics.index', request()->append('filter.audience', null))"
                    :current="! request()->has('filter.audience')"
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
            <flux:navlist.group heading="{{ __('Year') }}" expandable :expanded="request()->has('filter.year')">
                <flux:navlist.item
                    :href="route('comics.index', request()->append('filter.year', null))"
                    :current="! request()->has('filter.year')"
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
            <flux:navlist.group heading="{{ __('Progress') }}" expandable :expanded="request()->has('filter.end')">
                <flux:navlist.item
                    :href="route('comics.index', request()->append('filter.end', null))"
                    :current="! request()->has('filter.end')"
                    wire:navigate
                >
                    {{ __('All') }}
                </flux:navlist.item>
                <flux:navlist.item
                    :href="route('comics.index', request()->append('filter.end', '0'))"
                    :current="request('filter.end') === '0'"
                    wire:navigate
                >
                    {{ __('Ongoing') }}
                </flux:navlist.item>
                <flux:navlist.item
                    :href="route('comics.index', request()->append('filter.end', '1'))"
                    :current="request('filter.end') === '1'"
                    wire:navigate
                >
                    {{ __('Ended') }}
                </flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>
        <div>
            <flux:navbar class="mb-6 pt-0 justify-end">
                <flux:navbar.item
                    icon="megaphone"
                    :href="route('comics.index', request()->append('sort', null))"
                    :current="! request()->has('sort')"
                    wire:navigate
                >
                    {{ __('Recent published') }}
                </flux:navbar.item>
                <flux:navbar.item
                    icon="clock"
                    :href="route('comics.index', request()->append('sort', '-update'))"
                    :current="request()->get('sort') === '-update'"
                    wire:navigate
                >
                    {{ __('Recent updates') }}
                </flux:navbar.item>
                <flux:navbar.item
                    icon="fire"
                    :href="route('comics.index', request()->append('sort', '-views'))"
                    :current="request()->get('sort') === '-views'"
                    wire:navigate
                >
                    {{ __('Most views') }}
                </flux:navbar.item>
            </flux:navbar>
            <x-comic-section></x-comic-section>
            <div class="mt-8">
                {{ $comics->links() }}
            </div>
        </div>
    </div>
</div>
