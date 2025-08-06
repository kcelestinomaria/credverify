<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CredVerify') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        @stack('styles')
    </head>
    <body class="font-sans antialiased h-full bg-gradient-to-br from-jasiri-teal via-jasiri-blue to-jasiri-teal">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-gradient-to-br from-jasiri-teal/20 via-jasiri-blue/20 to-jasiri-teal/20"></div>
            
            <!-- Logo Section -->
            <div class="relative z-10 mb-8">
                <a href="/" class="flex items-center space-x-3 group">
                    <div class="w-16 h-16 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center shadow-2xl border border-white/20 group-hover:scale-110 transition-all duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div class="text-center">
                        <h1 class="text-2xl font-bold font-poppins text-white">CredVerify</h1>
                        <p class="text-sm font-inter text-white/80">Academic Credentials Platform</p>
                    </div>
                </a>
            </div>

            <!-- Main Content Card -->
            <div class="relative z-10 w-full sm:max-w-md px-6 py-8 bg-white/10 backdrop-blur-md shadow-2xl border border-white/20 rounded-2xl overflow-hidden">
                <!-- Card Background Pattern -->
                <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-white/10"></div>
                
                <!-- Content -->
                <div class="relative z-10">
                    {{ $slot }}
                </div>
            </div>
            
            <!-- Footer -->
            <div class="relative z-10 mt-8 text-center">
                <p class="text-sm font-inter text-white/60">
                    © {{ date('Y') }} CredVerify. All rights reserved.
                </p>
            </div>
        </div>
        
        <!-- Animated Background Elements -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-jasiri-teal/20 rounded-full blur-3xl animate-bounce-gentle"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-jasiri-blue/20 rounded-full blur-3xl animate-bounce-gentle" style="animation-delay: 1s;"></div>
        </div>
    </body>
</html>
