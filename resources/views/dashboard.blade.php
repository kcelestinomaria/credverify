<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-3xl font-poppins text-neutral-900 leading-tight">
                    Welcome back, {{ auth()->user()->name }}! 👋
                </h2>
                <p class="text-neutral-600 mt-1 font-inter">
                    @if(auth()->user()->isAdmin())
                        Manage your institution's credentials and track verification activity
                    @else
                        Verify credentials and manage your verification history
                    @endif
                </p>
            </div>
            <div class="flex items-center space-x-3">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('credentials.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-xl font-semibold font-inter transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Issue Credential
                    </a>
                @else
                    <a href="{{ route('employer.verify') }}" class="bg-accent-600 hover:bg-accent-700 text-white px-6 py-3 rounded-xl font-semibold font-inter transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Verify Credential
                    </a>
                @endif
                <a href="{{ route('credential.verification.index') }}" class="bg-white hover:bg-neutral-50 text-primary-600 border-2 border-neutral-300 hover:border-primary-500 px-6 py-3 rounded-xl font-semibold font-inter transition-all duration-300 shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Public Verification
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @if(auth()->user()->isAdmin())
                <!-- Admin Dashboard -->
                
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Credentials -->
                    <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-sm font-medium font-inter">Total Credentials</p>
                                <p class="text-3xl font-bold font-poppins">{{ $totalCredentials ?? 0 }}</p>
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
                                <p class="text-3xl font-bold font-poppins">{{ $verifiedCredentials ?? 0 }}</p>
                                <p class="text-white/70 text-xs mt-1 font-inter">{{ $verificationRate ?? 0 }}% success rate</p>
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
                                <p class="text-3xl font-bold font-poppins">{{ $thisMonthCredentials ?? 0 }}</p>
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
                                <p class="text-3xl font-bold font-poppins">{{ $totalVerifications ?? 0 }}</p>
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
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-jasiri-teal/10 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-jasiri-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold font-poppins text-gray-900 dark:text-gray-100">Issue Credential</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-inter">Create and issue new credentials</p>
                            </div>
                        </div>
                        <a href="{{ route('credentials.create') }}" class="mt-4 inline-flex items-center text-jasiri-teal hover:text-jasiri-blue font-medium font-inter transition-colors">
                            Get Started
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-jasiri-blue/10 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-jasiri-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold font-poppins text-gray-900 dark:text-gray-100">View Reports</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-inter">Analytics and verification reports</p>
                            </div>
                        </div>
                        <a href="#" class="mt-4 inline-flex items-center text-jasiri-blue hover:text-jasiri-teal font-medium font-inter transition-colors">
                            View Reports
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-jasiri-amber-400/10 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-jasiri-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold font-poppins text-gray-900 dark:text-gray-100">Settings</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-inter">Configure your institution</p>
                            </div>
                        </div>
                        <a href="#" class="mt-4 inline-flex items-center text-jasiri-amber-400 hover:text-jasiri-amber-500 font-medium font-inter transition-colors">
                            Manage Settings
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Recent Credentials -->
                @if(isset($recentCredentials) && $recentCredentials->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-xl font-bold font-poppins text-primary dark:text-white flex items-center">
                                    <svg class="w-6 h-6 mr-3 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Recent Credentials
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 mt-1 font-inter">Latest credentials issued by your institution</p>
                            </div>
                            <a href="{{ route('credentials.index') }}" class="text-primary hover:text-secondary dark:text-blue-400 dark:hover:text-blue-300 font-medium text-sm font-inter">
                                View All →
                            </a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold font-inter text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold font-inter text-gray-500 dark:text-gray-300 uppercase tracking-wider">Credential</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold font-inter text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold font-inter text-gray-500 dark:text-gray-300 uppercase tracking-wider">Issued</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold font-inter text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($recentCredentials as $credential)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center mr-3">
                                                <span class="text-white font-semibold font-poppins text-sm">{{ substr($credential->student_name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium font-inter text-gray-900 dark:text-white">{{ $credential->student_name }}</div>
                                                <div class="text-sm font-inter text-gray-500 dark:text-gray-400">{{ $credential->student_email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium font-inter text-gray-900 dark:text-white">{{ $credential->credential_type }}</div>
                                        <div class="text-sm font-inter text-gray-500 dark:text-gray-400">{{ $credential->issued_by }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold font-inter rounded-full {{ $credential->status === 'verified' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                            {{ ucfirst($credential->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-inter text-gray-500 dark:text-gray-400">
                                        {{ $credential->created_at->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('credentials.show', $credential) }}" class="text-primary hover:text-secondary dark:text-blue-400 dark:hover:text-blue-300 font-medium text-sm font-inter">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

            @else
                <!-- Employer Dashboard -->
                
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Verifications -->
                    <div class="bg-gradient-to-br from-jasiri-blue to-jasiri-blue/80 rounded-xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-sm font-medium font-inter">Total Verifications</p>
                                <p class="text-3xl font-bold font-poppins">{{ $totalVerifications ?? 0 }}</p>
                                <p class="text-white/70 text-xs mt-1 font-inter">All time</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- This Month -->
                    <div class="bg-gradient-to-br from-jasiri-teal to-jasiri-teal/80 rounded-xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-sm font-medium font-inter">This Month</p>
                                <p class="text-3xl font-bold font-poppins">{{ $thisMonthVerifications ?? 0 }}</p>
                                <p class="text-white/70 text-xs mt-1 font-inter">{{ date('F Y') }}</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Success Rate -->
                    <div class="bg-gradient-to-br from-jasiri-amber-400 to-jasiri-amber-500 rounded-xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-sm font-medium font-inter">Success Rate</p>
                                <p class="text-3xl font-bold font-poppins">{{ $successRate ?? 0 }}%</p>
                                <p class="text-white/70 text-xs mt-1 font-inter">Valid credentials</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-gradient-to-br from-jasiri-teal to-jasiri-blue rounded-xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-sm font-medium font-inter">Recent Activity</p>
                                <p class="text-3xl font-bold font-poppins">{{ $recentActivity ?? 0 }}</p>
                                <p class="text-white/70 text-xs mt-1 font-inter">Last 7 days</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-jasiri-blue/10 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-jasiri-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold font-poppins text-gray-900 dark:text-gray-100">Verify Credential</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-inter">Verify a single credential</p>
                            </div>
                        </div>
                        <a href="{{ route('employer.verify') }}" class="mt-4 inline-flex items-center text-jasiri-blue hover:text-jasiri-teal font-medium font-inter transition-colors">
                            Start Verification
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-jasiri-teal/10 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-jasiri-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold font-poppins text-gray-900 dark:text-gray-100">Bulk Verify</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-inter">Verify multiple credentials</p>
                            </div>
                        </div>
                        <a href="{{ route('employer.bulk-verify') }}" class="mt-4 inline-flex items-center text-jasiri-teal hover:text-jasiri-blue font-medium font-inter transition-colors">
                            Bulk Verification
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-jasiri-amber-400/10 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-jasiri-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold font-poppins text-gray-900 dark:text-gray-100">History</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-inter">View verification history</p>
                            </div>
                        </div>
                        <a href="{{ route('employer.history') }}" class="mt-4 inline-flex items-center text-jasiri-amber-400 hover:text-jasiri-amber-500 font-medium font-inter transition-colors">
                            View History
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Recent Verifications -->
                @if(isset($recentVerifications) && $recentVerifications->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-xl font-bold font-poppins text-primary dark:text-white flex items-center">
                                    <svg class="w-6 h-6 mr-3 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Recent Verifications
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 mt-1 font-inter">Your latest credential verification activities</p>
                            </div>
                            <a href="{{ route('employer.history') }}" class="text-primary hover:text-secondary dark:text-blue-400 dark:hover:text-blue-300 font-medium text-sm font-inter">
                                View All →
                            </a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold font-inter text-gray-500 dark:text-gray-300 uppercase tracking-wider">Credential</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold font-inter text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold font-inter text-gray-500 dark:text-gray-300 uppercase tracking-wider">Institution</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold font-inter text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold font-inter text-gray-500 dark:text-gray-300 uppercase tracking-wider">Verified</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($recentVerifications as $verification)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium font-inter text-gray-900 dark:text-white">{{ $verification->verification_code }}</div>
                                        <div class="text-sm font-inter text-gray-500 dark:text-gray-400">{{ $verification->credential_type ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium font-inter text-gray-900 dark:text-white">{{ $verification->student_name ?? 'N/A' }}</div>
                                        <div class="text-sm font-inter text-gray-500 dark:text-gray-400">{{ $verification->student_email ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-inter text-gray-900 dark:text-white">
                                        {{ $verification->institution_name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold font-inter rounded-full {{ $verification->is_valid ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                            {{ $verification->is_valid ? 'Valid' : 'Invalid' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-inter text-gray-500 dark:text-gray-400">
                                        {{ $verification->created_at->format('M j, Y') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            @endif

            <!-- Public Verification Portal -->
            <div class="bg-gradient-to-r from-primary to-secondary rounded-xl p-8 text-white shadow-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold font-poppins mb-2">Public Verification Portal</h3>
                        <p class="text-white/90 mb-4 font-inter">Allow anyone to verify credentials instantly without requiring an account</p>
                        <a href="{{ route('credential.verification.index') }}" class="inline-flex items-center bg-white text-primary px-6 py-3 rounded-xl font-semibold font-inter hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Access Portal
                        </a>
                    </div>
                    <div class="hidden md:block">
                        <div class="w-24 h-24 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
