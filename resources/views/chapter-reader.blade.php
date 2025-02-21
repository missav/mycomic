<x-layout>
    <div
        x-data='{
            currentPage: 0,
            enterPage(page) {
                this.currentPage = page;
                this.$refs[`page-selector-${page}`].scrollIntoView();
            },
            scrollToPage(page) {
                this.currentPage = page;
                this.$refs[`page-${page}`].scrollIntoView();
            },
            reachedBottomCallback() {
                if (window.userUuid) {
                    recombeeClient.send(new recombee.AddPurchase(window.userUuid, {{ $chapter->comic->id }}, {
                        cascadeCreate: false,
                        recommId: window.recommendId,
                    }));

                    window.dataLayer.push({
                        event: "comicRead",
                        item: {
                            comic_id: "{{ $chapter->comic->dvd_id }}",
                        },
                    });
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
                    x-ref="page-{{ $page['number'] }}"
                    x-intersect:enter.threshold.20="enterPage({{ $page['number'] }});"
                    @if ($page['number'] <= 3)
                        src="{{ $page['url'] }}"
                        class="page w-full mx-auto"
                    @else
                        data-src="{{ $page['url'] }}"
                        class="lozad page w-full mx-auto"
                    @endif
                    alt="{{ __(':comic - :chapter: Page :page', ['comic' => $chapter->comic->name, 'chapter' => $chapter->title, 'page' => $page['number']]) }}"
                    @if ($loop->last)
                        x-intersect.once="reachedBottomCallback"
                    @endif
                    @if ($page['width'])
                        width="{{ $page['width'] }}"
                        height="{{ $page['height'] }}"
                    @endif
                />
            @endforeach
        </div>

        <div class="text-center pt-8 pb-16">
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

        <div class="fixed bottom-0 left-0 right-0 border-t border-zinc-200 dark:border-zinc-700">
            <div class="-mx-4 sm:mx-0">
                <div class="px-6 lg:px-8 [[data-flux-container]_&]:px-0 mx-auto w-full [:where(&)]:max-w-7xl text-white bg-zinc-100 dark:bg-zinc-800">
                    <div class="overflow-auto whitespace-nowrap" style="scrollbar-width: none;">
                        @for ($i = 1; $i <= $chapter->pages; $i++)
                            <button
                                x-ref="page-selector-{{ $i }}"
                                @click="scrollToPage({{ $i }});"
                                :class="{
                                    'text-red-600': currentPage === {{ $i }},
                                    'dark:text-red-500': currentPage === {{ $i }},
                                }"
                                class="page-selector p-2"
                            >
                                {{ $i }}
                            </button>
                        @endfor
                    </div>
                </div>
            </div>
            <div class="flex justify-center bg-zinc-50 dark:bg-zinc-900 py-4">
                <x-chapter-control
                    :chapter="$chapter"
                    :previous-url="$previouUrl"
                    :next-url="$nextUrl"
                />
            </div>
        </div>
    </div>
</x-layout>
