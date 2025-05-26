<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-3xl font-poppins text-primary dark:text-white leading-tight">
                    Institutions Management
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1 font-inter">
                    Manage and oversee all educational institutions in the system
                </p>
            </div>
            <a href="{{ route('institutions.create') }}" class="bg-primary hover:bg-secondary text-white px-6 py-3 rounded-xl font-semibold font-inter transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Institution
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-8 bg-green-50 border-l-4 border-secondary p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700 font-medium font-inter">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Statistics Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-primary rounded-xl p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-sm font-medium font-inter">Total Institutions</p>
                            <p class="text-3xl font-bold font-poppins">{{ $institutions->total() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-secondary rounded-xl p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-sm font-medium font-inter">Active Credentials</p>
                            <p class="text-3xl font-bold font-poppins">{{ $institutions->sum(function($institution) { return $institution->credentials()->where('status', 'verified')->count(); }) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-accent rounded-xl p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-sm font-medium font-inter">Admin Users</p>
                            <p class="text-3xl font-bold font-poppins">{{ $institutions->sum(function($institution) { return $institution->users()->where('role', 'admin')->count(); }) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-primary to-secondary rounded-xl p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-sm font-medium font-inter">Total Verifications</p>
                            <p class="text-3xl font-bold font-poppins">{{ $institutions->sum(function($institution) { return \App\Models\EmployerVerification::whereHas('credential', function($q) use ($institution) { $q->where('institution_id', $institution->id); })->count(); }) }}</p>
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

            <!-- Institutions Grid -->
            @if($institutions->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($institutions as $institution)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                            <!-- Institution Header -->
                            <div class="relative bg-gradient-to-r from-primary to-secondary p-6 text-white">
                                <div class="flex items-center space-x-4">
                                    @if($institution->logo_url)
                                        <img src="{{ $institution->logo_url }}" alt="{{ $institution->name }}" class="w-16 h-16 rounded-xl object-cover border-2 border-white/20">
                                    @else
                                        <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center border-2 border-white/20">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-xl font-bold font-poppins text-white truncate">{{ $institution->name }}</h3>
                                        <p class="text-white/80 text-sm font-inter">{{ $institution->contact_email }}</p>
                                    </div>
                                </div>
                                <div class="absolute top-4 right-4">
                                    <span class="bg-white/20 text-white text-xs px-2 py-1 rounded-full font-medium font-inter">
                                        {{ $institution->slug }}
                                    </span>
                                </div>
                            </div>

                            <!-- Institution Content -->
                            <div class="p-6">
                                @if($institution->description)
                                    <p class="text-gray-600 dark:text-gray-400 text-sm font-inter mb-6 line-clamp-3">
                                        {{ $institution->description }}
                                    </p>
                                @else
                                    <p class="text-gray-400 dark:text-gray-500 text-sm font-inter mb-6 italic">
                                        No description available
                                    </p>
                                @endif

                                <!-- Statistics -->
                                <div class="grid grid-cols-3 gap-4 mb-6">
                                    <div class="text-center">
                                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mx-auto mb-2">
                                            <svg class="w-6 h-6 text-primary dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-2xl font-bold font-poppins text-primary dark:text-white">{{ $institution->credentials()->count() }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-inter">Credentials</p>
                                    </div>
                                    <div class="text-center">
                                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mx-auto mb-2">
                                            <svg class="w-6 h-6 text-secondary dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-2xl font-bold font-poppins text-primary dark:text-white">{{ $institution->credentials()->where('status', 'verified')->count() }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-inter">Verified</p>
                                    </div>
                                    <div class="text-center">
                                        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center mx-auto mb-2">
                                            <svg class="w-6 h-6 text-accent dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-2xl font-bold font-poppins text-primary dark:text-white">{{ $institution->users()->where('role', 'admin')->count() }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-inter">Admins</p>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex space-x-3">
                                    <a href="{{ route('institutions.show', $institution) }}" class="flex-1 bg-primary hover:bg-secondary text-white text-center py-3 px-4 rounded-xl font-semibold font-inter transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </a>
                                    <a href="{{ route('institutions.edit', $institution) }}" class="flex-1 bg-accent hover:bg-yellow-600 text-white text-center py-3 px-4 rounded-xl font-semibold font-inter transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('institutions.destroy', $institution) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this institution? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white py-3 px-4 rounded-xl font-semibold font-inter transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($institutions->hasPages())
                    <div class="mt-12">
                        {{ $institutions->links() }}
                    </div>
                @endif

            @else
                <!-- Empty State -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 p-12 text-center">
                    <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold font-poppins text-primary dark:text-white mb-4">No Institutions Found</h3>
                    <p class="text-gray-600 dark:text-gray-400 font-inter mb-8 max-w-md mx-auto">
                        Get started by adding your first educational institution to the system. You can manage credentials, users, and verification settings.
                    </p>
                    <a href="{{ route('institutions.create') }}" class="inline-flex items-center bg-primary hover:bg-secondary text-white px-8 py-4 rounded-xl font-semibold font-inter transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Your First Institution
                    </a>
                </div>
            @endif
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
</x-app-layout> 