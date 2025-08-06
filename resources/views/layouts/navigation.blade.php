<nav x-data="{ open: false }" class="bg-white/95 backdrop-blur-md border-b border-neutral-200 shadow-sm sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-accent-500 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-all duration-300">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7v10c0 5.55 3.84 9.739 9 11 5.16-1.261 9-5.45 9-11V7l-10-5z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold font-poppins text-neutral-900 group-hover:text-primary-600 transition-colors duration-200">CredVerify</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="font-inter">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    @auth
                        @if(auth()->user()->isAdmin())
                            @can('manage-institutions')
                                <x-nav-link :href="route('institutions.index')" :active="request()->routeIs('institutions.*')" class="font-inter">
                                    {{ __('Institutions') }}
                                </x-nav-link>
                            @endcan
                            
                            @can('manage-credentials')
                                <x-nav-link :href="route('credentials.index')" :active="request()->routeIs('credentials.*')" class="font-inter">
                                    {{ __('Credentials') }}
                                </x-nav-link>
                            @endcan
                        @endif
                        
                        @if(auth()->user()->isEmployer())
                            <x-nav-link :href="route('employer.verify')" :active="request()->routeIs('employer.verify*')" class="font-inter">
                                {{ __('Verify Credential') }}
                            </x-nav-link>
                            
                            <x-nav-link :href="route('employer.history')" :active="request()->routeIs('employer.history')" class="font-inter">
                                {{ __('Verification History') }}
                            </x-nav-link>
                            
                            <x-nav-link :href="route('employer.bulk-verify')" :active="request()->routeIs('employer.bulk-verify*')" class="font-inter">
                                {{ __('Bulk Verify') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-neutral-600 bg-white hover:text-neutral-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150 font-inter">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-accent-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                                <span class="font-medium">{{ Auth::user()->name }}</span>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-neutral-200">
                            <p class="text-sm font-poppins text-neutral-900">{{ Auth::user()->name }}</p>
                            <p class="text-sm font-inter text-neutral-600">{{ Auth::user()->email }}</p>
                        </div>
                        
                        <x-dropdown-link :href="route('profile.edit')" class="font-inter">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')" class="font-inter"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-gray-400 dark:text-gray-500 hover:text-jasiri-teal dark:hover:text-jasiri-teal hover:bg-gray-100/50 dark:hover:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-jasiri-teal focus:ring-opacity-50 transition-all duration-200">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white/95 backdrop-blur-md border-t border-neutral-200">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="font-inter">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            @auth
                @if(auth()->user()->isAdmin())
                    @can('manage-institutions')
                        <x-responsive-nav-link :href="route('institutions.index')" :active="request()->routeIs('institutions.*')" class="font-inter">
                            {{ __('Institutions') }}
                        </x-responsive-nav-link>
                    @endcan
                    
                    @can('manage-credentials')
                        <x-responsive-nav-link :href="route('credentials.index')" :active="request()->routeIs('credentials.*')" class="font-inter">
                            {{ __('Credentials') }}
                        </x-responsive-nav-link>
                    @endcan
                @endif
                
                @if(auth()->user()->isEmployer())
                    <x-responsive-nav-link :href="route('employer.verify')" :active="request()->routeIs('employer.verify*')" class="font-inter">
                        {{ __('Verify Credential') }}
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link :href="route('employer.history')" :active="request()->routeIs('employer.history')" class="font-inter">
                        {{ __('Verification History') }}
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link :href="route('employer.bulk-verify')" :active="request()->routeIs('employer.bulk-verify*')" class="font-inter">
                        {{ __('Bulk Verify') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-neutral-200">
            <div class="px-4 py-3">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-accent-500 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <div class="font-medium text-base font-poppins text-neutral-900">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm font-inter text-neutral-600">{{ Auth::user()->email }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="font-inter">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')" class="font-inter"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
