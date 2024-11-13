<x-layout>
    <div class="grid place-content-center px-4">
        <div class="text-center">
            <h1 class="text-9xl font-black text-gray-200 dark:text-gray-700">@yield('code')</h1>
            <p class="text-2xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">
                {{ __('Uh-oh!') }}
            </p>
            <p class="mt-4 text-gray-500 dark:text-gray-400">@yield('message')</p>
            <flux:button variant="primary" :href="localizedRoute('home')" class="mt-8">
                {{ __('Back to home') }}
            </flux:button>
        </div>
    </div>
</x-layout>
