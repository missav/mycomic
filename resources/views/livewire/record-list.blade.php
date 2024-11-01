<div class="flex items-stretch">
    <div class="w-3/4 grow">
        @if ($records->isNotEmpty())
            <flux:table>
                <flux:columns>
                    <flux:column class="w-full">{{ __('Comic') }}</flux:column>
                    <flux:column align="end">{{ __('Last read') }}</flux:column>
                </flux:columns>
                <flux:rows>
                    @foreach ($records as $record)
                        <flux:row :key="$record->id">
                            <flux:cell class="flex items-center gap-3">
                                <flux:avatar size="xs" src="{{ $record->comic->coverCdnUrl() }}" />
                                <div>
                                    [<a href="{{ $record->chapter->url() }}" class="text-amber-500 hover:underline underline-offset-4" wire:navigate>{{ $record->chapter->title }}</a>]
                                    <a href="{{ $record->comic->url() }}" class="hover:underline underline-offset-4" title="{{ $record->comic->name }}" wire:navigate>{{ $record->comic->name }}</a>
                                </div>
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
