@props([
    'name' => null,
    'href' => '/',
])

@php
$classes = Flux::classes()
    ->add('h-10 flex items-center mr-4')
    ;
@endphp

<a href="{{ $href }}" {{ $attributes->class([ $classes, 'gap-2' ])->except('alt') }} data-flux-brand>
    <span class="text-xl text-zinc-900 dark:text-zinc-100 font-medium truncate font-sans"><span>MY</span><span class="text-red-500">COMIC</span></span>
</a>
