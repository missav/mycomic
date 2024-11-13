@props(['route' => 'comics.index'])

<flux:navlist {{ $attributes->merge(['class' => 'w-32 lg:w-40 mr-6 lg:mr-8 space-y-4']) }}>
    @if (request()->has('filter'))
        <flux:button size="sm" :href="localizedRoute($route, request()->append('filter', null))">
            {{ __('Reset filters') }}
        </flux:button>
    @endif
    {{ $slot }}
    <flux:navlist.group heading="{{ __('Region') }}" expandable>
        <flux:navlist.item
            :href="localizedRoute($route, request()->append('filter.country', null))"
            :current="! request()->has('filter.country')"
        >
            {{ __('All') }}
        </flux:navlist.item>
        @foreach (\App\Enums\ComicCountry::cases() as $comicCountry)
            <flux:navlist.item
                :href="localizedRoute($route, request()->append('filter.country', $comicCountry->value))"
                :current="request('filter.country') === $comicCountry->value"
            >
                {{ $comicCountry->text() }}
            </flux:navlist.item>
        @endforeach
    </flux:navlist.group>
    <flux:navlist.group heading="{{ __('Genre') }}" expandable :expanded="request()->has('filter.tag')">
        <flux:navlist.item
            :href="localizedRoute($route, request()->append('filter.tag', null))"
            :current="! request()->has('filter.tag')"
        >
            {{ __('All') }}
        </flux:navlist.item>
        @foreach (\App\Models\Tag::cached() as $slug => $name)
            <flux:navlist.item
                :href="localizedRoute($route, request()->append('filter.tag', $slug))"
                :current="request('filter.tag') === $slug"
            >
                {{ $name }}
            </flux:navlist.item>
        @endforeach
    </flux:navlist.group>
    <flux:navlist.group heading="{{ __('Audience') }}" expandable :expanded="request()->has('filter.audience')">
        <flux:navlist.item
            :href="localizedRoute($route, request()->append('filter.audience', null))"
            :current="! request()->has('filter.audience')"
        >
            {{ __('All') }}
        </flux:navlist.item>
        @foreach (\App\Enums\ComicAudience::cases() as $comicAudience)
            <flux:navlist.item
                :href="localizedRoute($route, request()->append('filter.audience', $comicAudience->value))"
                :current="request('filter.audience') === $comicAudience->value"
            >
                {{ $comicAudience->text() }}
            </flux:navlist.item>
        @endforeach
    </flux:navlist.group>
    <flux:navlist.group heading="{{ __('Year') }}" expandable :expanded="request()->has('filter.year')">
        <flux:navlist.item
            :href="localizedRoute($route, request()->append('filter.year', null))"
            :current="! request()->has('filter.year')"
        >
            {{ __('All') }}
        </flux:navlist.item>
        @foreach (array_merge(range(now()->year, 2010), ['200x', '199x', '198x', '197x']) as $year)
            <flux:navlist.item
                :href="localizedRoute($route, request()->append('filter.year', $year))"
                :current="request('filter.year') === (string) $year"
            >
                {{ __($year) }}
            </flux:navlist.item>
        @endforeach
    </flux:navlist.group>
    <flux:navlist.group heading="{{ __('Progress') }}" expandable :expanded="request()->has('filter.end')">
        <flux:navlist.item
            :href="localizedRoute($route, request()->append('filter.end', null))"
            :current="! request()->has('filter.end')"
        >
            {{ __('All') }}
        </flux:navlist.item>
        <flux:navlist.item
            :href="localizedRoute($route, request()->append('filter.end', '0'))"
            :current="request('filter.end') === '0'"
        >
            {{ __('Ongoing') }}
        </flux:navlist.item>
        <flux:navlist.item
            :href="localizedRoute($route, request()->append('filter.end', '1'))"
            :current="request('filter.end') === '1'"
        >
            {{ __('Ended') }}
        </flux:navlist.item>
    </flux:navlist.group>
</flux:navlist>
