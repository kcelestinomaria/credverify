<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-3xl font-poppins text-primary dark:text-white leading-tight">
                    Welcome back, {{ auth()->user()->name }}! 👋
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1 font-inter">
                    @if(auth()->user()->isAdmin())
                        Manage your institution's credentials and track verification activity
                    @else
                        Verify credentials and manage your verification history
                    @endif
                </p>
            </div>
            <div class="flex items-center space-x-3">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('credentials.create') }}" class="bg-primary hover:bg-secondary text-white px-6 py-3 rounded-xl font-semibold font-inter transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Issue Credential
                    </a>
                @else
                    <a href="{{ route('employer.verify') }}" class="bg-secondary hover:bg-primary text-white px-6 py-3 rounded-xl font-semibold font-inter transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Verify Credential
                    </a>
                @endif
                <a href="{{ route('verification.index') }}" class="bg-white hover:bg-gray-50 text-primary border-2 border-gray-200 hover:border-primary px-6 py-3 rounded-xl font-semibold font-inter transition-all duration-300 shadow-md hover:shadow-lg">
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
                    <div class="bg-primary rounded-xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-sm font-medium font-inter">Total Credentials</p>
                                <p class="text-3xl font-bold font-poppins">{{ $totalCredentials ?? 0 }}</p>
                                <p class="text-white/70 text-xs mt-1 font-inter">All time</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Verified Credentials -->
                    <div class="bg-secondary rounded-xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-sm font-medium font-inter">Verified</p>
                                <p class="text-3xl font-bold font-poppins">{{ $verifiedCredentials ?? 0 }}</p>
                                <p class="text-white/70 text-xs mt-1 font-inter">{{ $verificationRate ?? 0 }}% success rate</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- This Month -->
                    <div class="bg-accent rounded-xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-sm font-medium font-inter">This Month</p>
                                <p class="text-3xl font-bold font-poppins">{{ $thisMonthCredentials ?? 0 }}</p>
                                <p class="text-white/70 text-xs mt-1 font-inter">{{ date('F Y') }}</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Total Verifications -->
                    <div class="bg-gradient-to-br from-primary to-secondary rounded-xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-sm font-medium font-inter">Verifications</p>
                                <p class="text-3xl font-bold font-poppins">{{ $totalVerifications ?? 0 }}</p>
                                <p class="text-white/70 text-xs mt-1 font-inter">All time checks</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-xl font-bold font-poppins text-primary dark:text-white flex items-center">
                            <svg class="w-6 h-6 mr-3 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Quick Actions
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mt-1 font-inter">Manage your institution's credentials efficiently</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <a href="{{ route('credentials.create') }}" class="group bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-800/30 rounded-xl p-6 transition-all duration-300 border border-blue-200 dark:border-blue-700 hover:shadow-lg">
                                <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                                <h4 class="font-semibold font-poppins text-primary dark:text-white mb-2">Issue New Credential</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-inter">Create and issue a new digital credential with blockchain security</p>
                            </a>

                            <a href="{{ route('credentials.index') }}" class="group bg-green-50 hover:bg-green-100 dark:bg-green-900/20 dark:hover:bg-green-800/30 rounded-xl p-6 transition-all duration-300 border border-green-200 dark:border-green-700 hover:shadow-lg">
                                <div class="w-12 h-12 bg-secondary rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <h4 class="font-semibold font-poppins text-primary dark:text-white mb-2">Manage Credentials</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-inter">View, edit, and manage all issued credentials</p>
                            </a>

                            @can('manage-institutions')
                            <a href="{{ route('institutions.index') }}" class="group bg-yellow-50 hover:bg-yellow-100 dark:bg-yellow-900/20 dark:hover:bg-yellow-800/30 rounded-xl p-6 transition-all duration-300 border border-yellow-200 dark:border-yellow-700 hover:shadow-lg">
                                <div class="w-12 h-12 bg-accent rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <h4 class="font-semibold font-poppins text-primary dark:text-white mb-2">Manage Institutions</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-inter">Oversee and manage institutional settings</p>
                            </a>
                            @else
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                                <div class="w-12 h-12 bg-gray-400 rounded-xl flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h4 class="font-semibold font-poppins text-gray-500 dark:text-gray-400 mb-2">Institution Management</h4>
                                <p class="text-gray-400 dark:text-gray-500 text-sm font-inter">Contact your super admin for institution management access</p>
                            </div>
                            @endcan
                        </div>
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
                    <div class="bg-primary rounded-xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-sm font-medium font-inter">Total Verifications</p>
                                <p class="text-3xl font-bold font-poppins">{{ $totalVerifications ?? 0 }}</p>
                                <p class="text-white/70 text-xs mt-1 font-inter">All time</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- This Month -->
                    <div class="bg-secondary rounded-xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-sm font-medium font-inter">This Month</p>
                                <p class="text-3xl font-bold font-poppins">{{ $thisMonthVerifications ?? 0 }}</p>
                                <p class="text-white/70 text-xs mt-1 font-inter">{{ date('F Y') }}</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Today -->
                    <div class="bg-accent rounded-xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-sm font-medium font-inter">Today</p>
                                <p class="text-3xl font-bold font-poppins">{{ $todayVerifications ?? 0 }}</p>
                                <p class="text-white/70 text-xs mt-1 font-inter">{{ date('M j') }}</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Success Rate -->
                    <div class="bg-gradient-to-br from-primary to-secondary rounded-xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-sm font-medium font-inter">Success Rate</p>
                                <p class="text-3xl font-bold font-poppins">{{ $successRate ?? 0 }}%</p>
                                <p class="text-white/70 text-xs mt-1 font-inter">Valid credentials</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-xl font-bold font-poppins text-primary dark:text-white flex items-center">
                            <svg class="w-6 h-6 mr-3 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Quick Actions
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mt-1 font-inter">Verify credentials and manage your verification history</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <a href="{{ route('employer.verify') }}" class="group bg-green-50 hover:bg-green-100 dark:bg-green-900/20 dark:hover:bg-green-800/30 rounded-xl p-6 transition-all duration-300 border border-green-200 dark:border-green-700 hover:shadow-lg">
                                <div class="w-12 h-12 bg-secondary rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h4 class="font-semibold font-poppins text-primary dark:text-white mb-2">Verify Credential</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-inter">Instantly verify the authenticity of digital credentials</p>
                            </a>

                            <a href="{{ route('employer.history') }}" class="group bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-800/30 rounded-xl p-6 transition-all duration-300 border border-blue-200 dark:border-blue-700 hover:shadow-lg">
                                <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h4 class="font-semibold font-poppins text-primary dark:text-white mb-2">Verification History</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-inter">View your complete verification history and records</p>
                            </a>

                            <a href="{{ route('employer.bulk-verify') }}" class="group bg-yellow-50 hover:bg-yellow-100 dark:bg-yellow-900/20 dark:hover:bg-yellow-800/30 rounded-xl p-6 transition-all duration-300 border border-yellow-200 dark:border-yellow-700 hover:shadow-lg">
                                <div class="w-12 h-12 bg-accent rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                </div>
                                <h4 class="font-semibold font-poppins text-primary dark:text-white mb-2">Bulk Verify</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-inter">Verify multiple credentials at once for efficiency</p>
                            </a>
                        </div>
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
                        <a href="{{ route('verification.index') }}" class="inline-flex items-center bg-white text-primary px-6 py-3 rounded-xl font-semibold font-inter hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
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
