<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 border-2 border-jasiri-teal rounded-xl font-semibold font-inter text-sm text-jasiri-teal dark:text-jasiri-teal uppercase tracking-wider shadow-lg hover:shadow-xl hover:bg-jasiri-teal hover:text-white dark:hover:bg-jasiri-teal dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-jasiri-teal focus:ring-opacity-50 transform hover:scale-105 disabled:opacity-25 transition-all duration-300 ease-in-out']) }}>
    {{ $slot }}
</button>
