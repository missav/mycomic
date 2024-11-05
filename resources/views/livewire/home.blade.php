<div x-cloak class="space-y-12">
    <div
        x-data='{ comics: placeholders(12) }'
        x-init="$nextTick(async () => comics = await getRecommendations(prefixScenario('home-recommended'), 12));"
    >
        <x-comic-thumbnails title="Recommended for you"></x-comic-thumbnails>
    </div>
    <div x-data='{ comics: @json(\App\Http\Resources\ComicResource::collection($this->recentUpdatedComics)) }'>
        <x-comic-thumbnails title="Recent updates" :url="localizedRoute('comics.index', ['sort' => '-update'])" lozad></x-comic-thumbnails>
    </div>
    <div x-data='{ comics: @json(\App\Http\Resources\ComicResource::collection($this->recentPublishedComics)) }'>
        <x-comic-thumbnails title="Recent published" :url="localizedRoute('comics.index', ['sort' => '-id'])" lozad></x-comic-thumbnails>
    </div>
</div>
