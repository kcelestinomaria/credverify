@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 py-2 text-sm font-medium font-inter rounded-lg text-jasiri-teal dark:text-jasiri-teal bg-jasiri-teal/10 dark:bg-jasiri-teal/20 border border-jasiri-teal/20 dark:border-jasiri-teal/30 focus:outline-none focus:ring-2 focus:ring-jasiri-teal focus:ring-opacity-50 transition-all duration-200 ease-in-out'
            : 'inline-flex items-center px-3 py-2 text-sm font-medium font-inter rounded-lg text-gray-600 dark:text-gray-400 hover:text-jasiri-teal dark:hover:text-jasiri-teal hover:bg-jasiri-teal/10 dark:hover:bg-jasiri-teal/20 focus:outline-none focus:ring-2 focus:ring-jasiri-teal focus:ring-opacity-50 transition-all duration-200 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
