<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-3xl font-poppins text-primary dark:text-white leading-tight">
                    Credential Details
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1 font-inter">
                    View and manage credential information
                </p>
            </div>
            <a href="{{ route('credentials.index') }}" class="bg-primary hover:bg-secondary text-white px-6 py-3 rounded-xl font-semibold font-inter transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Credentials
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Credential Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Credential Information</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $credential->full_name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Credential Type</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $credential->credential_type }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Issued By</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $credential->issued_by }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Institution</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $credential->institution->name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Issued On</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $credential->issued_on->format('F d, Y') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Verification Code</label>
                                <p class="mt-1 text-sm font-mono text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-700 p-2 rounded">{{ $credential->verification_code }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <div class="mt-1">
                                    @if($credential->isVerified())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Verified
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Revoked
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hash</label>
                                <p class="mt-1 text-xs font-mono text-gray-600 dark:text-gray-400 break-all">{{ $credential->hash }}</p>
                            </div>
                        </div>

                        <!-- QR Code and Actions -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Verification</h3>
                            
                            @if($credential->qr_code_path && file_exists(storage_path('app/public/' . $credential->qr_code_path)))
                                <div class="text-center">
                                    <img src="{{ asset('storage/' . $credential->qr_code_path) }}" alt="QR Code" class="mx-auto border rounded">
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Scan to verify credential</p>
                                </div>
                            @endif

                            <div class="space-y-2">
                                <a href="{{ route('credential.verification.show', $credential->verification_code) }}" target="_blank" class="block w-full text-center bg-primary hover:bg-secondary text-white font-bold py-2 px-4 rounded-xl font-inter transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Public Verification Link
                                </a>

                                @if($credential->json_path && file_exists(storage_path('app/public/' . $credential->json_path)))
                                    <a href="{{ route('credential.json', $credential) }}" class="block w-full text-center bg-secondary hover:bg-accent text-white font-bold py-2 px-4 rounded-xl font-inter transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Download Blockcerts JSON
                                    </a>
                                @endif

                                @if($credential->isVerified())
                                    <form method="POST" action="{{ route('credentials.revoke', $credential) }}" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-xl font-inter transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105" onclick="return confirm('Are you sure you want to revoke this credential?')">
                                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                            </svg>
                                            Revoke Credential
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Credential File -->
                    @if($credential->credential_file_path && file_exists(storage_path('app/public/' . $credential->credential_file_path)))
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Credential File</h3>
                            <div class="border rounded-lg p-4">
                                @php
                                    $fileExtension = pathinfo($credential->credential_file_path, PATHINFO_EXTENSION);
                                @endphp
                                
                                @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ asset('storage/' . $credential->credential_file_path) }}" alt="Credential" class="max-w-full h-auto rounded">
                                @elseif(strtolower($fileExtension) === 'pdf')
                                    <embed src="{{ asset('storage/' . $credential->credential_file_path) }}" type="application/pdf" width="100%" height="600px" class="rounded">
                                @endif
                            </div>
                        </div>
                    @endif
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
    </style>
</x-app-layout> 