<div
    x-data='{
        comics: @json(\App\Http\Resources\ComicResource::collection($recentUpdatedComics)),
    }'
>
    <x-comic-section title="Recent update" :url="route('comics.index')"></x-comic-section>
</div>
