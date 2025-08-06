<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-3 bg-jasiri-teal border border-transparent rounded-xl font-semibold font-inter text-sm text-white uppercase tracking-wider hover:bg-jasiri-teal/90 focus:bg-jasiri-teal/90 active:bg-jasiri-teal/95 focus:outline-none focus:ring-2 focus:ring-jasiri-teal focus:ring-opacity-50 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 ease-in-out']) }}>
    {{ $slot }}
</button>
