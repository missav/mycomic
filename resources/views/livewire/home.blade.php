<div x-cloak class="space-y-12">
    <div
        x-data='{ comics: placeholders(12) }'
        x-init="$nextTick(async () => comics = await getRecommendations(prefixScenario('home-recommended'), 12));"
    >
        <x-comic-thumbnails title="Recommended for you"></x-comic-thumbnails>
    </div>
    <div class="overflow-x-auto lg:overflow-x-visible">
        <flux:table class="w-auto lg:w-full">
            <flux:columns>
                <flux:column>
                    {{ __('Recent updates') }}
                    <flux:spacer />
                    {{ __('More') }}
                </flux:column>
                <flux:column>{{ __('Daily rank') }}</flux:column>
                <flux:column>{{ __('Weekly rank') }}</flux:column>
                <flux:column>{{ __('Monthly rank') }}</flux:column>
            </flux:columns>
            <flux:rows>
                <flux:row>
                    <flux:cell>
                        <x-comic-text-list :comics="$this->recentUpdatedComics" />
                    </flux:cell>
                    <flux:cell>
                        <x-comic-text-list :comics="$this->dailyRankComics" />
                    </flux:cell>
                    <flux:cell>
                        <x-comic-text-list :comics="$this->weeklyRankComics" />
                    </flux:cell>
                    <flux:cell>
                        <x-comic-text-list :comics="$this->monthlyRankComics" />
                    </flux:cell>
                </flux:row>
            </flux:rows>
        </flux:table>
    </div>
    <div x-data='{ comics: @json(\App\Http\Resources\ComicResource::collection($this->recentUpdatedComics)) }'>
        <x-comic-thumbnails title="Recent updates" :url="localizedRoute('comics.index', ['sort' => '-update'])" lozad></x-comic-thumbnails>
    </div>
    <div x-data='{ comics: @json(\App\Http\Resources\ComicResource::collection($this->recentPublishedComics)) }'>
        <x-comic-thumbnails title="Recent published" :url="localizedRoute('comics.index', ['sort' => '-id'])" lozad></x-comic-thumbnails>
    </div>
</div>
