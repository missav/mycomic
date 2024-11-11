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
        showBottomControl: false,
        selectedPage: window.currentPage,
        pages: @json($pages),
        jumpToPage(page) {
            this.pages[page - 1].show = true;

            this.markPage(page);
            this.$nextTick(() => {
                document.getElementById(`page_${page}`).scrollIntoView();
            });
        },
        markPage(page) {
            window.currentPage = page;
            this.selectedPage = page;

            history.pushState("", document.title, `${window.location.pathname}${window.location.search}#p${window.currentPage}`);
        },
        markCurrentPage() {
            const currentPage = this.pages.filter(page => page.viewable).sort((a, b) => b.number - a.number)[0];

            if (currentPage) {
                this.markPage(currentPage.number);
            }
        },
        prevPage() {
            if (window.currentPage <= 1) {
                return;
            }

            this.jumpToPage(parseInt(window.currentPage) - 1);
        },
        nextPage() {
            if (window.currentPage >= this.pages.length) {
                return;
            }

            this.jumpToPage(parseInt(window.currentPage) + 1);
        },
        showPage(index) {
            if (this.pages[index]) {
                this.pages[index].show = true;
            }
        },
    }'
    x-init="() => {
        if (window.currentPage) {
            jumpToPage(window.currentPage);

            setTimeout(() => {
                showPage(window.currentPage);
                showPage(parseInt(window.currentPage) + 1);
            }, 500);
        }
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
                    page.viewable = true;

                    markCurrentPage();

                    setTimeout(() => {
                        showPage(page.number);
                        showPage(parseInt(page.number) + 1);
                    }, 500);
                }"
                x-intersect:leave="() => {
                    page.viewable = false;

                    markCurrentPage();
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
