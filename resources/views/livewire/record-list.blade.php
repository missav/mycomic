<div class="flex items-stretch">
    <div class="w-3/4 grow">
        <flux:breadcrumbs class="mb-4">
            <flux:breadcrumbs.item :href="localizedRoute('home')" icon="home" />
            <flux:breadcrumbs.item>{{ \App\Seo::title(raw: true) }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        @if ($records->isNotEmpty())
            <flux:table>
                <flux:columns>
                    <flux:column>{{ __('Comic') }}</flux:column>
                    <flux:column>{{ __('Last updated') }}</flux:column>
                    <flux:column></flux:column>
                </flux:columns>
                <flux:rows>
                    @foreach ($records as $record)
                        <flux:row :key="$record->id">
                            <flux:cell class="flex items-center gap-3">
                                <flux:avatar size="xs" src="{{ $record->comic->coverCdnUrl() }}" />
                                <div>
                                    @if ($record->chapter)
                                        [<a href="{{ $record->chapter->url() }}" class="text-amber-500 hover:underline underline-offset-4" wire:navigate>{{ $record->chapter->title }}</a>]
                                    @endif
                                    <a href="{{ $record->comic->url() }}" class="hover:underline underline-offset-4" title="{{ $record->comic->name }}" wire:navigate>{{ $record->comic->name }}</a>
                                </div>
                            </flux:cell>
                            <flux:cell>
                                <a href="{{ $record->comic->recentChapter->url() }}" class="text-amber-500 hover:underline underline-offset-4" wire:navigate>{{ $record->comic->recentChapter->title }}</a>
                            </flux:cell>
                            <flux:cell align="end" class="whitespace-nowrap">{{ localized($record->updated_at->diffForHumans()) }}</flux:cell>
                        </flux:row>
                    @endforeach
                </flux:rows>
            </flux:table>
        @else
            <x-empty-state.list>
                <flux:button variant="primary" disabled>{{ __('No records') }}</flux:button>
            </x-empty-state.list>
        @endif
    </div>
    <div class="w-1/4 ml-8 hidden lg:block text-white space-y-8">
        <x-comic-text-list title="Recent updates" :url="localizedRoute('comics.index', ['sort' => '-update'])" :comics="$this->recentUpdatedComics"></x-comic-text-list>
        <x-comic-text-list title="Recent published" :url="localizedRoute('comics.index', ['sort' => '-id'])" :comics="$this->recentPublishedComics"></x-comic-text-list>
    </div>
</div>
