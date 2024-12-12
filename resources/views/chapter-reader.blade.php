<x-layout>
    <div
        x-data='{
            sendRecombeeAddPurchase() {
                if (window.userUuid) {
                    recombeeClient.send(new recombee.AddPurchase(window.userUuid, {{ $chapter->comic->id }}, {
                        cascadeCreate: true,
                        recommId: window.recommendId,
                    }));
                }
            }
        }'
        x-init="$nextTick(() => {
            setTimeout(() => {
                axios.post('{{ route('chapters.sync', ['chapter' => $chapter]) }}').then(response => {
                    syncUserUuid(response.data.userUuid);
                });
            }, 2000);
        });"
        @if ($previouUrl)
            @keyup.left.window="() => {
                window.location.href = '{{ $previouUrl }}';
            }"
        @endif
        @if ($nextUrl)
            @keyup.right.window="() => {
                window.location.href = '{{ $nextUrl }}';
            }"
        @endif
        class="pb-16"
    >
        <flux:breadcrumbs class="mb-4">
            <flux:breadcrumbs.item :href="localizedRoute('home')" icon="home" aria-label="{{ __('Home') }}" />
            <flux:breadcrumbs.item :href="localizedRoute('comics.view', ['comic' => $chapter->comic])" class="whitespace-nowrap">{{ \Illuminate\Support\Str::limit($chapter->comic->name, 20) }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item><div class="truncate whitespace-nowrap">{{ \Illuminate\Support\Str::limit($chapter->title, 15) }}</div></flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="-mx-6 sm:mx-0">
            @foreach ($pages as $page)
                <img
                    @if ($page['number'] <= 3)
                        src="{{ $page['url'] }}"
                        class="w-full mx-auto"
                    @else
                        data-src="{{ $page['url'] }}"
                        class="lozad w-full mx-auto"
                    @endif
                    alt="{{ __(':comic - :chapter: Page :page', ['comic' => $chapter->comic->name, 'chapter' => $chapter->title, 'page' => $page['number']]) }}"
                    @if ($loop->last)
                        x-intersect.once="sendRecombeeAddPurchase"
                    @endif
                    @if ($page['width'])
                        width="{{ $page['width'] }}"
                        height="{{ $page['height'] }}"
                    @endif
                />
            @endforeach
        </div>

        <div class="text-center py-8">
            @if ($nextUrl)
                <flux:badge color="blue" size="lg" icon="hand-thumb-up">{{ __('The end of this chapter') }}</flux:badge>
            @elseif ($chapter->comic->is_ended)
                <flux:badge color="blue" size="lg" icon="hand-thumb-up">{{ __('The end of this comic') }}</flux:badge>
            @else
                <flux:badge color="blue" size="lg" icon="hand-thumb-up">{{ __('To be continued') }}</flux:badge>
            @endif
        </div>

        @if (! $nextUrl)
            <flux:separator class="my-8" text="{{ __('Recommended for you') }}" />
            <div
                x-data='{ comics: placeholders(6) }'
                x-init="$nextTick(async () => comics = await getRecommendations(prefixScenario('watch-next'), 6, {{ $chapter->comic->id }}));"
                class="mb-8"
            >
                <x-comic-thumbnails :comics="\App\Models\Comic::placeholders(6)"></x-comic-thumbnails>
            </div>
        @endif

        <x-chapter-control
            :chapter="$chapter"
            :previous-url="$previouUrl"
            :next-url="$nextUrl"
            class="fixed bottom-0 left-0 right-0 justify-center py-5 bg-zinc-50 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-700"
        />
    </div>
</x-layout>
