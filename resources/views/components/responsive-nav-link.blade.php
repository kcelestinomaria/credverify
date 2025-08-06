@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-jasiri-teal text-start text-base font-medium font-inter text-jasiri-teal dark:text-jasiri-teal bg-jasiri-teal/10 dark:bg-jasiri-teal/20 focus:outline-none focus:text-jasiri-teal dark:focus:text-jasiri-teal focus:bg-jasiri-teal/20 dark:focus:bg-jasiri-teal/30 focus:border-jasiri-teal dark:focus:border-jasiri-teal transition-all duration-200 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium font-inter text-gray-600 dark:text-gray-400 hover:text-jasiri-teal dark:hover:text-jasiri-teal hover:bg-jasiri-teal/10 dark:hover:bg-jasiri-teal/20 hover:border-jasiri-teal/50 dark:hover:border-jasiri-teal/50 focus:outline-none focus:text-jasiri-teal dark:focus:text-jasiri-teal focus:bg-jasiri-teal/10 dark:focus:bg-jasiri-teal/20 focus:border-jasiri-teal/50 dark:focus:border-jasiri-teal/50 transition-all duration-200 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
