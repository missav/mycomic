<x-layout>
    <div
        x-data='{
            pages: @json($pages),
            preload: 3,
            reachedEnd: false,
            showBottomControl: false,
        }'
        x-init="$nextTick(() => {
            setTimeout(() => {
                axios.post('{{ route('chapters.sync', ['chapter' => $chapter]) }}').then(response => {
                    syncUserUuid(response.data.userUuid);
                });
            }, 2000);
        });"
        class="pb-16"
    >
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="localizedRoute('home')" icon="home" />
            <flux:breadcrumbs.item :href="localizedRoute('comics.view', ['comic' => $chapter->comic])" class="whitespace-nowrap">{{ \Illuminate\Support\Str::limit($chapter->comic->name, 20) }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item><div class="truncate whitespace-nowrap">{{ \Illuminate\Support\Str::limit($chapter->title, 10) }}</div></flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <x-chapter-control
            :chapter="$chapter"
            :previous-url="$previouUrl"
            :next-url="$nextUrl"
            x-intersect.margin.-100px="showBottomControl = false;"
            x-intersect:leave.margin.-100px="showBottomControl = true;"
            class="justify-center py-5"
        />

        <div class="-mx-6 sm:mx-0">
            <div x-data="{ loaded: false }" x-show="! loaded" x-init="setTimeout(() => loaded = true, 1000)" class="w-full h-screen"></div>
            <template x-for="page in pages" :key="`page_${page.number}`">
                <img
                    :src="page.number <= preload ? page.url : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mN09omrBwADNQFuUCqPAwAAAABJRU5ErkJggg=='"
                    :data-src="page.url"
                    class="w-full mx-auto scroll-mt-16"
                    :class="page.number <= preload ? '' : 'lozad'"
                    :alt="'{{ __(':comic - :chapter: Page :page', ['comic' => $chapter->comic->name, 'chapter' => $chapter->title]) }}'.replace(':page', page.number)"
                    x-intersect.once="() => {
                        if (page.number === pages.length) {
                            recombeeClient.send(new recombee.AddPurchase(window.userUuid, {{ $chapter->comic->id }}, {
                                cascadeCreate: true,
                                recommId: window.recommendId,
                            }));
                        }
                    }"
                />
            </template>
        </div>

        <div class="text-center py-8">
            <flux:badge color="blue" size="lg" icon="hand-thumb-up">{{ __('The end of this chapter') }}</flux:badge>
        </div>

        <x-chapter-control
            x-cloak
            :chapter="$chapter"
            :previous-url="$previouUrl"
            :next-url="$nextUrl"
            x-show="showBottomControl"
            class="fixed bottom-0 left-0 right-0 justify-center py-5 bg-zinc-50 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-700"
        />
    </div>
</x-layout>
