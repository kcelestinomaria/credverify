<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-3xl font-poppins text-neutral-900 leading-tight">
                    Admin Dashboard
                </h2>
                <p class="text-neutral-600 mt-1 font-inter">
                    Manage your institution's credentials and users
                </p>
            </div>
            <div class="flex items-center space-x-3">
                @can('manage-credentials')
                    <a href="{{ route('credentials.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-xl font-semibold font-inter transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Issue Credential
                    </a>
                @endcan
                
                @can('manage-users')
                    <a href="{{ route('admin.users.index') }}" class="bg-accent-600 hover:bg-accent-700 text-white px-6 py-3 rounded-xl font-semibold font-inter transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Manage Users
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Credentials -->
                <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-sm font-medium font-inter">Total Credentials</p>
                            <p class="text-3xl font-bold font-poppins">{{ $stats['total_credentials'] ?? 0 }}</p>
                            <p class="text-white/70 text-xs mt-1 font-inter">All time</p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Verified Credentials -->
                <div class="bg-gradient-to-br from-accent-500 to-accent-600 rounded-xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-sm font-medium font-inter">Verified</p>
                            <p class="text-3xl font-bold font-poppins">{{ $stats['verified_credentials'] ?? 0 }}</p>
                            <p class="text-white/70 text-xs mt-1 font-inter">{{ $stats['verification_rate'] ?? 0 }}% success rate</p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- This Month -->
                <div class="bg-gradient-to-br from-warning-500 to-warning-600 rounded-xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-sm font-medium font-inter">This Month</p>
                            <p class="text-3xl font-bold font-poppins">{{ $stats['this_month_credentials'] ?? 0 }}</p>
                            <p class="text-white/70 text-xs mt-1 font-inter">{{ date('F Y') }}</p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Verifications -->
                <div class="bg-gradient-to-br from-primary-500 to-accent-500 rounded-xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-sm font-medium font-inter">Total Verifications</p>
                            <p class="text-3xl font-bold font-poppins">{{ $stats['total_verifications'] ?? 0 }}</p>
                            <p class="text-white/70 text-xs mt-1 font-inter">All time</p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @can('manage-credentials')
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-primary-500/10 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold font-poppins text-neutral-900 dark:text-neutral-100">Issue Credential</h3>
                                <p class="text-neutral-600 dark:text-neutral-400 text-sm font-inter">Create and issue new credentials</p>
                            </div>
                        </div>
                        <a href="{{ route('credentials.create') }}" class="mt-4 inline-flex items-center text-primary-500 hover:text-primary-600 font-medium font-inter transition-colors">
                            Get Started
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @endcan

                @can('manage-users')
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-accent-500/10 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold font-poppins text-neutral-900 dark:text-neutral-100">Manage Users</h3>
                                <p class="text-neutral-600 dark:text-neutral-400 text-sm font-inter">Add and manage user accounts</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="mt-4 inline-flex items-center text-accent-500 hover:text-accent-600 font-medium font-inter transition-colors">
                            View Users
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @endcan

                @can('view-reports')
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-warning-500/10 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold font-poppins text-neutral-900 dark:text-neutral-100">View Reports</h3>
                                <p class="text-neutral-600 dark:text-neutral-400 text-sm font-inter">Analytics and verification reports</p>
                            </div>
                        </div>
                        <a href="#" class="mt-4 inline-flex items-center text-warning-500 hover:text-warning-600 font-medium font-inter transition-colors">
                            View Reports
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @endcan
            </div>

            <!-- System Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold font-poppins text-neutral-900 dark:text-neutral-100">System Information</h3>
                    <p class="text-neutral-600 dark:text-neutral-400 text-sm font-inter mt-1">Current user permissions and system status</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold font-poppins text-neutral-900 dark:text-neutral-100 mb-3">User Information</h4>
                            <div class="space-y-2 text-sm font-inter">
                                <div class="flex justify-between">
                                    <span class="text-neutral-600 dark:text-neutral-400">Name:</span>
                                    <span class="text-neutral-900 dark:text-neutral-100">{{ auth()->user()->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-600 dark:text-neutral-400">Email:</span>
                                    <span class="text-neutral-900 dark:text-neutral-100">{{ auth()->user()->email }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-600 dark:text-neutral-400">Role:</span>
                                    <span class="text-neutral-900 dark:text-neutral-100 capitalize">{{ auth()->user()->role }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-600 dark:text-neutral-400">Status:</span>
                                    <span class="text-neutral-900 dark:text-neutral-100">
                                        @if(auth()->user()->isActive())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent-100 text-accent-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger-100 text-danger-800">
                                                Inactive
                                            </span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold font-poppins text-neutral-900 dark:text-neutral-100 mb-3">Permissions</h4>
                            <div class="space-y-2">
                                @foreach(auth()->user()->getPermissions() as $permission)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-accent-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm font-inter text-neutral-600 dark:text-neutral-400">{{ ucwords(str_replace('-', ' ', $permission)) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 