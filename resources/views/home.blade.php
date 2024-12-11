<x-layout>
    <div class="space-y-12">
        <x-comic-thumbnails :comics="$featuredComics" :half="false" preload="12"></x-comic-thumbnails>
        <div
            x-data='{ comics: placeholders(12) }'
            x-init="$nextTick(async () => {
                console.log('a');
                comics = await getRecommendations(prefixScenario('home-recommended'), 12);
                console.log('b');
            });"
        >
            <x-comic-thumbnails :comics="\App\Models\Comic::placeholders(12)" title="Recommended for you"></x-comic-thumbnails>
        </div>
        <div class="overflow-x-auto lg:overflow-x-visible">
            <flux:table class="w-auto lg:w-full">
                <flux:columns>
                    <flux:column>
                        {{ __('Recent updates') }}
                        <flux:spacer />
                        <a href="{{ localizedRoute('comics.index', ['sort' => '-update']) }}" class="text-orange-700 dark:text-amber-500 hover:underline underline-offset-4">{{ __('More') }}</a>
                    </flux:column>
                    <flux:column>
                        {{ __('Daily rank') }}
                        <flux:spacer />
                        <a href="{{ localizedRoute('rank') }}" class="text-orange-700 dark:text-amber-500 hover:underline underline-offset-4">{{ __('More') }}</a>
                    </flux:column>
                    <flux:column>
                        {{ __('Weekly rank') }}
                        <flux:spacer />
                        <a href="{{ localizedRoute('rank', ['sort' => '-week']) }}" class="text-orange-700 dark:text-amber-500 hover:underline underline-offset-4">{{ __('More') }}</a>
                    </flux:column>
                    <flux:column>
                        {{ __('All-time rank') }}
                        <flux:spacer />
                        <a href="{{ localizedRoute('rank', ['sort' => '-views']) }}" class="text-orange-700 dark:text-amber-500 hover:underline underline-offset-4">{{ __('More') }}</a>
                    </flux:column>
                </flux:columns>
                <flux:rows>
                    <flux:row>
                        <flux:cell>
                            <x-comic-text-list :comics="$recentUpdatedComics" />
                        </flux:cell>
                        <flux:cell>
                            <x-comic-text-list :comics="$dailyRankComics" />
                        </flux:cell>
                        <flux:cell>
                            <x-comic-text-list :comics="$weeklyRankComics" />
                        </flux:cell>
                        <flux:cell>
                            <x-comic-text-list :comics="$allTimeRankComics" />
                        </flux:cell>
                    </flux:row>
                </flux:rows>
            </flux:table>
        </div>
        <x-comic-thumbnails :comics="$recentUpdatedComics" title="Recent updates" :url="localizedRoute('comics.index', ['sort' => '-update'])"></x-comic-thumbnails>
        <x-comic-thumbnails :comics="$recentPublishedComics" title="Recent published" :url="localizedRoute('comics.index', ['sort' => '-id'])"></x-comic-thumbnails>
    </div>
</x-layout>
