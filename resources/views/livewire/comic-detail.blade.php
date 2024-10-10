<div class="flex items-stretch">
    <div class="grow">
        <flux:card class="flex">
            <div class="grow">
                <flux:subheading>{{ __(':year / :count chapters', ['year' => $comic->year, 'count' => $comic->chapters()->count()]) }}</flux:subheading>
                <flux:heading size="xl">{{ $comic->name }}</flux:heading>
                <div class="space-y-1 my-4">
                    <div>
                        <label class="text-sm text-zinc-500 dark:text-white/50">{{ __('Author') }}:</label>
                        <span class="text-sm text-zinc-800 dark:text-white">
                            {!! $comic
                                ->authors
                                ->map(fn (\App\Models\Author $author) =>
                                    '<a href="' . $author->url() . '" class="hover:underline underline-offset-4">' . $author->name . '</a>'
                                )
                                ->implode(', ') !!}
                        </span>
                    </div>
                    <div>
                        <label class="text-sm text-zinc-500 dark:text-white/50">{{ __('Genre') }}:</label>
                        <span class="text-sm text-zinc-800 dark:text-white">
                            {!! $comic
                                ->tags
                                ->map(fn (\App\Models\Tag $tag) =>
                                    '<a href="' . $tag->url() . '" class="hover:underline underline-offset-4">' . $tag->name . '</a>'
                                )
                                ->add('<a href="' . $comic->audienceUrl() . '" class="hover:underline underline-offset-4">' . $comic->audience . '</a>')
                                ->implode(', ') !!}
                        </span>
                    </div>
                    <div>
                        <label class="text-sm text-zinc-500 dark:text-white/50">{{ __('Region') }}:</label>
                        <span class="text-sm text-zinc-800 dark:text-white">
                            {!! '<a href="' . $comic->countryUrl() . '" class="hover:underline underline-offset-4">' . $comic->country . '</a>' !!}
                        </span>
                    </div>
                </div>
                <div>
                    <span class="text-zinc-800 dark:text-white">{{ $comic->description }}</span>
                </div>
            </div>
            <div class="rounded-xl bg-cover w-2/5 md:w-1/5" style="background-image: url({{ $comic->coverCdnUrl() }});"></div>
        </flux:card>
        <div class="mt-8">
            @foreach ($comic->chapters->reverse()->groupBy('type') as $group => $chapters)
                <flux:subheading size="xl" class="mb-4">{{ $group }}</flux:subheading>
                <div class="grid grid-cols-3 gap-4">
                    @foreach ($chapters as $chapter)
                        <flux:button :href="$chapter->url()">{{ $chapter->title }}</flux:button>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
    <div class="w-1/4 ml-4 hidden lg:flex">
    </div>
</div>
