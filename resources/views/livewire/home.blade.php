<div x-cloak class="space-y-12">
    <div
        x-data='{ comics: placeholders(6) }'
        x-init="$nextTick(async () => comics = await getRecommendations('desktop-home-recommended', 6));"
    >
        <x-comic-section title="Recommended for you"></x-comic-section>
    </div>
    <div x-data='{ comics: @json(\App\Http\Resources\ComicResource::collection($recentUpdatedComics)) }'>
        <x-comic-section title="Recent update" :url="localizedRoute('comics.index', ['sort' => '-update'])" lozad></x-comic-section>
    </div>
</div>
