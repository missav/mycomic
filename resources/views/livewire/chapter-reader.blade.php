@script
    <script>
        window.pushTimeout(() => {
            $wire.sync();
        }, 2000);
    </script>
@endscript

<div
    wire:ignore
    x-data='{
        pages: @json($pages),
        currentPage: 1,
        selectedPage: 1,
        showBottomControl: false,
        jumpToPage(page) {
            this.pages[page - 1].show = true;
            this.currentPage = page;
            this.selectedPage = page;
            this.$nextTick(() => {
                document.getElementById(`page_${page}`).scrollIntoView();
            });
        },
        prevPage() {
            if (this.currentPage <= 1) {
                return;
            }

            this.jumpToPage(this.currentPage - 1);
        },
        nextPage() {
            if (this.currentPage >= this.pages.length) {
                return;
            }

            this.jumpToPage(this.currentPage + 1);
        },
        showPage(index) {
            if (this.pages[index]) {
                this.pages[index].show = true;
            }
        },
    }'
    x-init="() => {
        showPage(currentPage - 1);
        showPage(currentPage);
        showPage(currentPage + 1);
    }"
    class="pb-20"
>
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="localizedRoute('home')" icon="home" wire:navigate />
        <flux:breadcrumbs.item :href="localizedRoute('comics.view', ['comic' => $chapter->comic])" class="whitespace-nowrap" wire:navigate>{{ \Illuminate\Support\Str::limit($chapter->comic->name, 20) }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item><div class="truncate whitespace-nowrap">{{ \Illuminate\Support\Str::limit($chapter->title, 10) }}</div></flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <x-chapter-control
        :chapter="$chapter"
        x-intersect.margin.-100px="showBottomControl = false;"
        x-intersect:leave.margin.-100px="showBottomControl = true;"
        class="justify-center py-5"
    />

    <div class="-mx-6 sm:mx-0">
        <template x-for="page in pages" :key="`page_${page.number}`">
            <img
                x-cloak
                x-show="page.show"
                :id="`page_${page.number}`"
                :alt="'{{ __(':comic - :chapter: Page :page', ['comic' => $chapter->comic->name, 'chapter' => $chapter->title]) }}'.replace(':page', page.number)"
                :src="page.show ? page.url : ''"
                class="w-full mx-auto scroll-mt-16"
                x-intersect:enter="() => {
                    showPage(page.number);
                    showPage(page.number + 1);
                }"
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

    <div class="text-center py-6">
        <flux:badge color="blue" size="lg" icon="hand-thumb-up">{{ __('Reached the bottom') }}</flux:badge>
    </div>

    <x-chapter-control
        x-cloak
        :chapter="$chapter"
        x-show="showBottomControl"
        class="fixed bottom-0 left-0 right-0 justify-center py-5 bg-zinc-50 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-700"
    />
</div>
