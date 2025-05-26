<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold font-poppins text-primary dark:text-white">CredVerify</span>
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
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium font-inter rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-primary dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
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
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-primary dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-primary dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
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
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base font-poppins text-primary dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm font-inter text-gray-500">{{ Auth::user()->email }}</div>
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
    </style>
</nav>
