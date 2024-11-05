<div x-cloak class="space-y-12">
    <div x-data='{ comics: @json(\App\Http\Resources\ComicResource::collection($this->featuredComics)) }'>
        <x-comic-thumbnails :half="false"></x-comic-thumbnails>
    </div>
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
                    <a href="{{ localizedRoute('comics.index', ['sort' => '-update']) }}" class="text-amber-500 hover:underline underline-offset-4" wire:navigate>{{ __('More') }}</a>
                </flux:column>
                <flux:column>
                    {{ __('Daily rank') }}
                    <flux:spacer />
                    <a href="{{ localizedRoute('rank') }}" class="text-amber-500 hover:underline underline-offset-4" wire:navigate>{{ __('More') }}</a>
                </flux:column>
                <flux:column>
                    {{ __('Weekly rank') }}
                    <flux:spacer />
                    <a href="{{ localizedRoute('rank', ['sort' => '-week']) }}" class="text-amber-500 hover:underline underline-offset-4" wire:navigate>{{ __('More') }}</a>
                </flux:column>
                <flux:column>
                    {{ __('All-time rank') }}
                    <flux:spacer />
                    <a href="{{ localizedRoute('rank', ['sort' => '-views']) }}" class="text-amber-500 hover:underline underline-offset-4" wire:navigate>{{ __('More') }}</a>
                </flux:column>
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
                        <x-comic-text-list :comics="$this->allTimeRankComics" />
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
