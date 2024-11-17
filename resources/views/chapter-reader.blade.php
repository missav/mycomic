<x-layout>
    <div
        x-data='{
            reachedEnd: false,
            shouldShowBottomControl: false,
            showBottomControl() {
                clearTimeout(window.bottomControlTimeout);

                window.bottomControlTimeout = setTimeout(() => {
                    this.shouldShowBottomControl = true;
                }, 300);
            },
            hideBottomControl() {
                clearTimeout(window.bottomControlTimeout);

                window.bottomControlTimeout = setTimeout(() => {
                    this.shouldShowBottomControl = false;
                }, 300);
            },
            addPurchaseRecommendation() {
                recombeeClient.send(new recombee.AddPurchase(window.userUuid, {{ $chapter->comic->id }}, {
                    cascadeCreate: true,
                    recommId: window.recommendId,
                }));
            }
        }'
        x-init="$nextTick(() => {
            window.bottomControlTimeout = null;

            setTimeout(() => {
                axios.post('{{ route('chapters.sync', ['chapter' => $chapter]) }}').then(response => {
                    syncUserUuid(response.data.userUuid);
                });
            }, 2000);
        });"
        class="pb-16"
    >
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="localizedRoute('home')" icon="home" aria-label="{{ __('Home') }}" />
            <flux:breadcrumbs.item :href="localizedRoute('comics.view', ['comic' => $chapter->comic])" class="whitespace-nowrap">{{ \Illuminate\Support\Str::limit($chapter->comic->name, 20) }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item><div class="truncate whitespace-nowrap">{{ \Illuminate\Support\Str::limit($chapter->title, 15) }}</div></flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <x-chapter-control
            :chapter="$chapter"
            :previous-url="$previouUrl"
            :next-url="$nextUrl"
            x-intersect.margin.-100px="hideBottomControl"
            x-intersect:leave.margin.-100px="showBottomControl"
            class="justify-center py-5"
        />

        <div class="pages -mx-6 sm:mx-0">
            <div x-data="{ loaded: false }" x-show="! loaded" x-init="setTimeout(() => loaded = true, 300)" class="w-full h-screen"></div>

            @foreach ($pages as $page)
                <img
                    @if ($page['number'] <= 3)
                        src="{{ $page['url'] }}"
                        class="w-full mx-auto"
                    @else
                        data-src="{{ $page['url'] }}"
                        class="@if (! $page['width']) h-screen @endif lozad w-full mx-auto"
                    @endif
                    alt="{{ __(':comic - :chapter: Page :page', ['comic' => $chapter->comic->name, 'chapter' => $chapter->title, 'page' => $page['number']]) }}"
                    @if ($loop->last)
                        x-intersect.once="addPurchaseRecommendation"
                    @endif
                    @if ($page['width'])
                        style="aspect-ratio: {{ $page['width'] }} / {{ $page['height'] }};"
                    @endif
                />
            @endforeach
        </div>

        <div class="text-center py-8">
            <flux:badge color="blue" size="lg" icon="hand-thumb-up">{{ __('The end of this chapter') }}</flux:badge>
        </div>

        <x-chapter-control
            x-cloak
            x-show="shouldShowBottomControl"
            :chapter="$chapter"
            :previous-url="$previouUrl"
            :next-url="$nextUrl"
            class="fixed bottom-0 left-0 right-0 justify-center py-5 bg-zinc-50 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-700"
        />
    </div>
</x-layout>
