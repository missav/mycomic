<div x-data="{ showBottomControl: false }" class="pb-20">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="localizedRoute('home')" icon="home" wire:navigate />
        <flux:breadcrumbs.item :href="localizedRoute('comics.view', ['comic' => $chapter->comic])" wire:navigate>{{ $chapter->comic->name }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $chapter->title }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <x-chapter-control :chapter="$chapter" x-intersect="showBottomControl = false" x-intersect:leave="showBottomControl = true" class="justify-center py-5" />

    <div>
        @for ($i = 1; $i <= $chapter->pages; $i++)
            <img
                wire:key="{{ $chapter->id }}-{{ $i }}"
                alt="{{ __(':comic - :chapter: Page :page', ['comic' => $chapter->comic->name, 'chapter' => $chapter->title, 'page' => $i]) }}"
                @if ($i <= 2)
                    src="{{ $chapter->pageCdnUrl($i) }}"
                    class="w-full min-h-64 mx-auto"
                @else
                    src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mN09omrBwADNQFuUCqPAwAAAABJRU5ErkJggg=="
                    data-src="{{ $chapter->pageCdnUrl($i) }}"
                    data-placeholder-background="#27272A"
                    class="lozad w-full min-h-64 mx-auto"
                @endif
                @if ($i === $chapter->pages)
                    x-intersect.once="() => {
                        recombeeClient.send(new recombee.AddPurchase(window.user_uuid, {{ $chapter->comic->id }}, {
                            cascadeCreate: true,
                            recommId: window.recommendId,
                        }));
                    }"
                @endif
            />
        @endfor
    </div>

    <x-chapter-control :chapter="$chapter" x-show="showBottomControl" class="fixed bottom-0 left-0 right-0 justify-center py-5 bg-zinc-50 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-700" />
</div>
