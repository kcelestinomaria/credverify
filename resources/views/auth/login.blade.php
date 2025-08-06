<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-jasiri-teal to-jasiri-blue rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-2xl">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold font-poppins text-white mb-2">Welcome Back</h2>
                <p class="text-white/80 font-inter">Sign in to your CredVerify account to continue</p>
            </div>

            <!-- Login Form -->
            <div class="bg-white/10 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 p-8">
                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-semibold font-inter text-white mb-2">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="username" required 
                                   class="block w-full pl-10 pr-3 py-3 border border-white/30 rounded-xl focus:ring-2 focus:ring-jasiri-teal focus:border-jasiri-teal transition-all duration-200 bg-white/10 focus:bg-white/20 text-white placeholder-white/60 font-inter" 
                                   placeholder="Enter your email address"
                                   value="{{ old('email') }}">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold font-inter text-white mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required 
                                   class="block w-full pl-10 pr-3 py-3 border border-white/30 rounded-xl focus:ring-2 focus:ring-jasiri-teal focus:border-jasiri-teal transition-all duration-200 bg-white/10 focus:bg-white/20 text-white placeholder-white/60 font-inter" 
                                   placeholder="Enter your password">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-white/30 text-jasiri-teal shadow-sm focus:ring-jasiri-teal bg-white/10" name="remember">
                            <span class="ms-2 text-sm text-white/80 font-inter">Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-jasiri-teal hover:text-jasiri-blue font-medium font-inter transition-colors" href="{{ route('password.request') }}">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" class="w-full bg-jasiri-teal hover:bg-jasiri-teal/90 text-white font-bold font-inter py-4 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-jasiri-teal/50">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                Sign In
                            </span>
                        </button>
                    </div>

                    <!-- Register Link -->
                    <div class="text-center pt-4 border-t border-white/20">
                        <p class="text-sm text-white/80 font-inter">
                            Don't have an account? 
                            <a href="{{ route('register') }}" class="font-semibold text-jasiri-teal hover:text-jasiri-blue transition-colors">
                                Create one here
                            </a>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Demo Credentials -->
            <div class="bg-gradient-to-r from-jasiri-teal/20 to-jasiri-blue/20 backdrop-blur-md rounded-xl p-6 text-white shadow-xl border border-white/20">
                <h3 class="text-lg font-bold font-poppins mb-4 text-center">Demo Credentials</h3>
                <div class="space-y-3 text-sm font-inter">
                    <div class="bg-white/10 rounded-lg p-3">
                        <p class="font-semibold text-jasiri-teal">Admin Account:</p>
                        <p class="text-white/80">Email: admin@jasiri.com</p>
                        <p class="text-white/80">Password: admin123</p>
                    </div>
                    <div class="bg-white/10 rounded-lg p-3">
                        <p class="font-semibold text-jasiri-blue">Employer Account:</p>
                        <p class="text-white/80">Email: employer@example.com</p>
                        <p class="text-white/80">Password: password</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
