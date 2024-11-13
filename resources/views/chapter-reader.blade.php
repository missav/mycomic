<x-layout>
    <div
        x-data='{
            pages: @json($pages),
            loadedFirstPage: false,
            reachedEnd: false,
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
            showPage(number) {
                if (this.pages[number - 1]) {
                    this.pages[number - 1].show = true;
                }
            },
        }'
        x-init="$nextTick(() => {
            const waitForFirstPage = () => {
                const firstPage = document.getElementById('page_1');

                if (firstPage && firstPage.complete) {
                    loadedFirstPage = true;
                    return;
                }

                setTimeout(() => {
                    waitForFirstPage();
                }, 100);
            };

            showPage(1);
            showPage(2);
            showPage(3);

            waitForFirstPage();

            setTimeout(() => {
                axios.post('{{ route('chapters.sync', ['chapter' => $chapter]) }}').then(response => {
                    syncUserUuid(response.data.userUuid);
                });
            }, 2000);
        });"
        class="pb-20"
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
            <template x-for="page in pages" :key="`page_${page.number}`">
                <img
                    x-cloak
                    x-show="page.show"
                    :id="`page_${page.number}`"
                    :alt="'{{ __(':comic - :chapter: Page :page', ['comic' => $chapter->comic->name, 'chapter' => $chapter->title]) }}'.replace(':page', page.number)"
                    :src="page.show ? page.url : ''"
                    class="w-full mx-auto scroll-mt-16"
                    x-intersect:enter="() => {
                        if (loadedFirstPage) {
                            showPage(page.number + 1);
                            showPage(page.number + 2);
                        }

                        if (page.number === pages.length) {
                            reachedEnd = true;
                        }
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

        <div x-cloak x-show="reachedEnd" class="text-center py-6">
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
