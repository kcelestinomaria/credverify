@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-xl font-inter transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-jasiri-teal focus:border-jasiri-teal focus:bg-white dark:focus:bg-gray-600 shadow-sm']) }}>
