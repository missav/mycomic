@props(['comics' => []])

<div class="space-y-2.5">
    @foreach ($comics as $comic)
        <div class="flex">
            <div class="truncate grow text-sm text-zinc-500 dark:text-zinc-300">
                [<a href="{{ $comic->recentChapter->url() }}" class="text-amber-500 hover:underline underline-offset-4">{{ $comic->recentChapter->title() }}</a>]
                <a href="{{ $comic->url() }}" class="hover:underline underline-offset-4" title="{{ $comic->name() }}">{{ $comic->name() }}</a>
            </div>
            <div class="flex-none text-sm text-zinc-500 dark:text-zinc-300 ml-2">
                {{ $comic->last_updated_on->format(__('m-d')) }}
            </div>
        </div>
    @endforeach
</div>
