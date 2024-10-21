<div
    x-data='{
        comics: @json(\App\Http\Resources\ComicResource::collection($comics)),
    }'
>
    <div class="lg:flex">
        <flux:navlist class="hidden lg:flex w-64 mr-8">
            <flux:navlist.group heading="{{ __('Region') }}" expandable>
                @foreach (\App\Enums\ComicCountry::cases() as $comicCountry)
                    <flux:navlist.item href="#" >{{ $comicCountry->text() }}</flux:navlist.item>
                @endforeach
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
