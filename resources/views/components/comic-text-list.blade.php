@props(['comics' => []])

<div class="space-y-2.5">
    @foreach ($comics as $comic)
        <div class="flex">
            <div class="truncate grow text-sm text-zinc-500 dark:text-zinc-300">
                [<a href="{{ $comic->recentChapterUrl() }}" class="text-orange-700 dark:text-amber-500 hover:underline underline-offset-4">{{ $comic->recentChapterTitle() }}</a>]
                <a href="{{ $comic->url() }}" class="hover:underline underline-offset-4" title="{{ $comic->name() }}">{{ $comic->name() }}</a>
            </div>
            <div class="flex-none text-sm text-zinc-500 dark:text-zinc-300 ml-2">
                {{ $comic->last_updated_on->format(__('n-j')) }}
            </div>
        </div>
    @endforeach
</div>
