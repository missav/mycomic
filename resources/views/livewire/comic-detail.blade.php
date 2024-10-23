@script
<script>
    $wire.view();
</script>
@endscript

<div class="flex items-stretch">
    <div class="w-3/4 grow">
        <flux:card class="flex flex-col sm:flex-row">
            <div class="grow">
                <flux:subheading>{{ __(':year / :count chapters', ['year' => $comic->year, 'count' => $comic->chapters()->count()]) }}</flux:subheading>
                <flux:heading size="xl">
                    {{ $comic->name }}
                </flux:heading>
                @if ($comic->is_ended)
                    <flux:badge color="lime" size="sm" class="mt-2">{{ __('Ended') }}</flux:badge>
                @else
                    <flux:badge color="blue" size="sm" class="mt-2">{{ __('Ongoing') }}</flux:badge>
                @endif
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
                                ->add('<a href="' . $comic->audienceUrl() . '" class="hover:underline underline-offset-4">' . $comic->audience->text() . '</a>')
                                ->implode(', ') !!}
                        </span>
                    </div>
                    <div>
                        <label class="text-sm text-zinc-500 dark:text-white/50">{{ __('Region') }}:</label>
                        <span class="text-sm text-zinc-800 dark:text-white">
                            {!! '<a href="' . $comic->countryUrl() . '" class="hover:underline underline-offset-4">' . $comic->country->text() . '</a>' !!}
                        </span>
                    </div>
                </div>
                <div class="md:w-4/5 text-zinc-800 dark:text-white">
                    @if (\Illuminate\Support\Str::length($comic->description) > 150)
                        <div x-data="{ show: false }">
                            <div x-show="! show">
                                {{ \Illuminate\Support\Str::limit($comic->description, 150) }}
                                <a href="#" @click.prevent="show = ! show" class="text-amber-500 hover:underline underline-offset-4">
                                    {{ __('Show all') }}
                                </a>
                            </div>
                            <div x-cloak x-show="show">
                                {{ $comic->description }}
                            </div>
                        </div>
                    @else
                        {{ $comic->description }}
                    @endif
                </div>
            </div>
            <div class="flex-none sm:w-40 mt-6 sm:mt-0 sm:ml-8">
                <div class="aspect-w-2 aspect-h-1 sm:aspect-w-3 sm:aspect-h-4 overflow-hidden rounded-md shadow-lg dark:shadow-gray-500/40">
                    <img src="{{ $comic->coverCdnUrl() }}" alt="{{ $comic->name }}" class="object-cover object-top">
                </div>
            </div>
        </flux:card>
        <div class="mt-8">
            @foreach ($comic->chapters->reverse()->groupBy('type') as $group => $chapters)
                <flux:subheading size="xl" class="mt-8 mb-4">{{ $group }}</flux:subheading>
                <div class="grid grid-cols-3 gap-4">
                    @foreach ($chapters as $chapter)
                        <flux:button :href="$chapter->url()">{{ $chapter->title }}</flux:button>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
    <div class="w-1/4 ml-4 hidden lg:flex text-white">
    </div>
</div>
