<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="w-16 h-16 bg-primary rounded-xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold font-poppins text-primary mb-2">Welcome Back</h2>
                <p class="text-gray-600 font-inter">Sign in to your CredVerify account to continue</p>
            </div>

            <!-- Login Form -->
            <div class="bg-white rounded-xl shadow-2xl border border-gray-100 p-8">
                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-semibold font-inter text-primary mb-2">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="username" required 
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 bg-gray-50 focus:bg-white font-inter" 
                                   placeholder="Enter your email address"
                                   value="{{ old('email') }}">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold font-inter text-primary mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required 
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 bg-gray-50 focus:bg-white font-inter" 
                                   placeholder="Enter your password">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary" name="remember">
                            <span class="ms-2 text-sm text-gray-600 font-inter">Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-primary hover:text-secondary font-medium font-inter transition-colors" href="{{ route('password.request') }}">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" class="w-full bg-primary hover:bg-secondary text-white font-bold font-inter py-4 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                Sign In
                            </span>
                        </button>
                    </div>

                    <!-- Register Link -->
                    <div class="text-center pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600 font-inter">
                            Don't have an account? 
                            <a href="{{ route('register') }}" class="font-semibold text-primary hover:text-secondary transition-colors">
                                Create one here
                            </a>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Demo Credentials -->
            <div class="bg-gradient-to-r from-primary to-secondary rounded-xl p-6 text-white shadow-xl">
                <h3 class="text-lg font-bold font-poppins mb-4 text-center">Demo Credentials</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm font-inter">
                    <div class="bg-white/10 rounded-lg p-3">
                        <p class="font-semibold text-white/90 mb-1">Institution Admin</p>
                        <p class="text-white/80">admin@strathmore.edu</p>
                        <p class="text-white/80">password</p>
                    </div>
                    <div class="bg-white/10 rounded-lg p-3">
                        <p class="font-semibold text-white/90 mb-1">Employer</p>
                        <p class="text-white/80">hr@safaricom.co.ke</p>
                        <p class="text-white/80">password</p>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="text-center">
                <p class="text-xs text-gray-500 font-inter mb-4">Trusted by leading Kenyan institutions worldwide</p>
                <div class="flex justify-center items-center space-x-6 text-xs text-gray-400 font-inter">
                    <span>🔒 Blockchain Security</span>
                    <span>⚡ Instant Verification</span>
                    <span>🏛️ Multi-Institution</span>
                </div>
            </div>
        </div>
    </div>

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
        .focus\:ring-primary:focus { --tw-ring-color: #1F3B73; }
        .focus\:border-primary:focus { border-color: #1F3B73; }
    </style>
</x-guest-layout>
