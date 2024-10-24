<div x-cloak class="space-y-12">
    <div
        x-data='{ comics: placeholders(6) }'
        x-init="$nextTick(async () => comics = await getRecommendations('desktop-home-recommended', 12));"
    >
        <x-comic-section title="Recommended for you"></x-comic-section>
    </div>
    <div x-data='{ comics: @json(\App\Http\Resources\ComicResource::collection($this->recentUpdatedComics)) }'>
        <x-comic-section title="Recent updates" :url="localizedRoute('comics.index', ['sort' => '-update'])" lozad></x-comic-section>
    </div>
    <div x-data='{ comics: @json(\App\Http\Resources\ComicResource::collection($this->recentPublishedComics)) }'>
        <x-comic-section title="Recent published" :url="localizedRoute('comics.index', ['sort' => '-id'])" lozad></x-comic-section>
    </div>
</div>
