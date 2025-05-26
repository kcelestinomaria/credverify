<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CredVerify - Blockchain-Backed Credential Verification</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800|inter:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .font-poppins { font-family: 'Poppins', sans-serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
        .bg-primary { background-color: #1F3B73; }
        .bg-secondary { background-color: #2BAE66; }
        .bg-accent { background-color: #F5C542; }
        .text-primary { color: #1F3B73; }
        .text-secondary { color: #2BAE66; }
        .text-accent { color: #F5C542; }
        .border-primary { border-color: #1F3B73; }
        .border-secondary { border-color: #2BAE66; }
        .border-accent { border-color: #F5C542; }
        .gradient-primary { background: linear-gradient(135deg, #1F3B73 0%, #2BAE66 100%); }
        .gradient-accent { background: linear-gradient(135deg, #F5C542 0%, #2BAE66 100%); }
        .animate-float { animation: float 6s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-20px); } }
        .glass-effect { backdrop-filter: blur(16px); background: rgba(31, 59, 115, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); }
    </style>
</head>
<body class="antialiased bg-gray-50 font-inter">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 glass-effect">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold font-poppins text-primary">
                        CredVerify
                    </h1>
                </div>
                
                @if (Route::has('login'))
                    <div class="flex items-center space-x-6">
                        <a href="{{ route('verification.index') }}" class="text-primary hover:text-secondary font-medium font-inter transition-colors">
                            Verify Credential
                        </a>
                        @auth
                            <a href="{{ url('/dashboard') }}" class="bg-primary hover:bg-secondary text-white px-6 py-3 rounded-xl font-semibold font-inter transition-all duration-300 shadow-lg hover:shadow-xl">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-primary hover:text-secondary font-medium font-inter transition-colors">
                                Login
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-primary hover:bg-secondary text-white px-6 py-3 rounded-xl font-semibold font-inter transition-all duration-300 shadow-lg hover:shadow-xl">
                                    Get Started
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Background -->
        <div class="absolute inset-0 gradient-primary"></div>
        <div class="absolute inset-0 bg-black opacity-10"></div>
        
        <!-- Floating Elements -->
        <div class="absolute top-20 left-10 w-64 h-64 bg-accent rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float"></div>
        <div class="absolute top-40 right-10 w-64 h-64 bg-secondary rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-20 left-1/2 w-64 h-64 bg-white rounded-full mix-blend-multiply filter blur-xl opacity-10 animate-float" style="animation-delay: 4s;"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="space-y-8">
                <div class="space-y-6">
                    <h1 class="text-5xl md:text-7xl font-bold font-poppins text-white leading-tight">
                        Secure Digital
                        <span class="text-accent">
                            Credentials
                        </span>
                        <br>Made Simple
                    </h1>
                    <p class="text-xl md:text-2xl font-inter text-white/90 max-w-4xl mx-auto leading-relaxed">
                        Revolutionary blockchain-backed credential verification system that enables Kenyan institutions to issue tamper-proof digital credentials and allows employers to instantly verify authenticity.
                    </p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center pt-8">
                    <a href="{{ route('register') }}" class="group bg-white hover:bg-gray-50 text-primary px-8 py-4 rounded-xl font-bold font-inter text-lg transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:scale-105">
                        <span class="flex items-center space-x-2">
                            <span>Start Issuing Credentials</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </span>
                    </a>
                    <a href="{{ route('verification.index') }}" class="group glass-effect text-white border-2 border-white/30 hover:border-white/50 px-8 py-4 rounded-xl font-bold font-inter text-lg transition-all duration-300 hover:bg-white/10">
                        <span class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Verify Credential</span>
                        </span>
                    </a>
                </div>

                <!-- Trust Indicators -->
                <div class="pt-16">
                    <p class="text-white/80 text-sm font-medium font-inter mb-8">Trusted by leading Kenyan institutions</p>
                    <div class="flex justify-center items-center space-x-12 opacity-80">
                        <div class="text-white font-semibold font-inter">Strathmore University</div>
                        <div class="text-white font-semibold font-inter">University of Nairobi</div>
                        <div class="text-white font-semibold font-inter">KCA University</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-4xl md:text-5xl font-bold font-poppins text-primary mb-6">
                    Why Choose CredVerify?
                </h2>
                <p class="text-xl font-inter text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Built with cutting-edge blockchain technology to ensure maximum security, trust, and efficiency in credential management for Kenyan institutions.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    <div class="w-16 h-16 bg-primary rounded-xl flex items-center justify-center mb-6 mx-auto">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold font-poppins text-primary mb-4 text-center">Blockchain Security</h3>
                    <p class="text-gray-600 font-inter text-center leading-relaxed">
                        Every credential is secured with SHA-256 hashing and blockchain technology, making forgery impossible and ensuring complete data integrity.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    <div class="w-16 h-16 bg-secondary rounded-xl flex items-center justify-center mb-6 mx-auto">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold font-poppins text-primary mb-4 text-center">Instant Verification</h3>
                    <p class="text-gray-600 font-inter text-center leading-relaxed">
                        Verify credentials in seconds using QR codes or verification codes. No more waiting for manual checks or lengthy verification processes.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    <div class="w-16 h-16 bg-accent rounded-xl flex items-center justify-center mb-6 mx-auto">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold font-poppins text-primary mb-4 text-center">Multi-Institution</h3>
                    <p class="text-gray-600 font-inter text-center leading-relaxed">
                        Support for multiple Kenyan institutions with independent credential management, verification, and complete administrative control.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-4xl md:text-5xl font-bold font-poppins text-primary mb-6">
                    How It Works
                </h2>
                <p class="text-xl font-inter text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Simple, secure, and efficient credential management in three easy steps.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="relative mb-8">
                        <div class="w-24 h-24 bg-primary rounded-full flex items-center justify-center mx-auto shadow-xl">
                            <span class="text-3xl font-bold font-poppins text-white">1</span>
                        </div>
                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-accent rounded-full"></div>
                    </div>
                    <h3 class="text-2xl font-bold font-poppins text-primary mb-4">Issue Credentials</h3>
                    <p class="text-gray-600 font-inter leading-relaxed">
                        Institutions upload and issue digital credentials with automatic blockchain verification and QR code generation for seamless sharing.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="relative mb-8">
                        <div class="w-24 h-24 bg-secondary rounded-full flex items-center justify-center mx-auto shadow-xl">
                            <span class="text-3xl font-bold font-poppins text-white">2</span>
                        </div>
                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-accent rounded-full"></div>
                    </div>
                    <h3 class="text-2xl font-bold font-poppins text-primary mb-4">Share Securely</h3>
                    <p class="text-gray-600 font-inter leading-relaxed">
                        Credential holders receive QR codes and verification codes to share with employers or other parties with complete security.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="relative mb-8">
                        <div class="w-24 h-24 bg-accent rounded-full flex items-center justify-center mx-auto shadow-xl">
                            <span class="text-3xl font-bold font-poppins text-white">3</span>
                        </div>
                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-secondary rounded-full"></div>
                    </div>
                    <h3 class="text-2xl font-bold font-poppins text-primary mb-4">Verify Instantly</h3>
                    <p class="text-gray-600 font-inter leading-relaxed">
                        Employers scan QR codes or enter verification codes to instantly confirm credential authenticity with blockchain verification.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 gradient-primary relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl md:text-5xl font-bold font-poppins text-white mb-6">
                Ready to Transform Your Credentials?
            </h2>
            <p class="text-xl font-inter text-white/90 mb-12 max-w-3xl mx-auto leading-relaxed">
                Join leading Kenyan institutions and employers who trust CredVerify for secure, efficient, and reliable credential management.
            </p>
            <div class="flex flex-col sm:flex-row gap-6 justify-center">
                <a href="{{ route('register') }}" class="group bg-white hover:bg-gray-50 text-primary px-10 py-5 rounded-xl font-bold font-inter text-lg transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:scale-105">
                    <span class="flex items-center justify-center space-x-2">
                        <span>Register Your Institution</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </span>
                </a>
                <a href="{{ route('verification.index') }}" class="group glass-effect text-white border-2 border-white/30 hover:border-white/50 px-10 py-5 rounded-xl font-bold font-inter text-lg transition-all duration-300 hover:bg-white/10">
                    <span class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Verify Credential Now</span>
                    </span>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-primary text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="w-12 h-12 bg-secondary rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold font-poppins">CredVerify</h3>
                    </div>
                    <p class="text-white/80 font-inter mb-6 leading-relaxed max-w-md">
                        Secure, blockchain-backed credential verification for the digital age. 
                        Trusted by Kenyan institutions and employers worldwide for reliable credential management.
                    </p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold font-poppins mb-6">Quick Links</h4>
                    <ul class="space-y-3 font-inter">
                        <li><a href="{{ route('verification.index') }}" class="text-white/80 hover:text-white transition-colors">Verify Credential</a></li>
                        <li><a href="{{ route('login') }}" class="text-white/80 hover:text-white transition-colors">Login</a></li>
                        <li><a href="{{ route('register') }}" class="text-white/80 hover:text-white transition-colors">Register</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold font-poppins mb-6">Support</h4>
                    <ul class="space-y-3 font-inter">
                        <li><a href="#" class="text-white/80 hover:text-white transition-colors">Documentation</a></li>
                        <li><a href="#" class="text-white/80 hover:text-white transition-colors">Contact Us</a></li>
                        <li><a href="#" class="text-white/80 hover:text-white transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/20 mt-12 pt-8 text-center">
                <p class="text-white/80 font-inter">
                    © {{ date('Y') }} CredVerify. All rights reserved. Built with security and trust in mind for Kenya.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
