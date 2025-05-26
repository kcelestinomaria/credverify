<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Credential Details') }}
            </h2>
            <a href="{{ route('credentials.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                {{ __('Back to Credentials') }}
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
                            
                            @php
                                $qrPath = 'credentials/qr/qr_' . $credential->verification_code . '.png';
                            @endphp
                            
                            @if(file_exists(storage_path('app/public/' . $qrPath)))
                                <div class="text-center">
                                    <img src="{{ asset('storage/' . $qrPath) }}" alt="QR Code" class="mx-auto border rounded">
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Scan to verify credential</p>
                                </div>
                            @endif

                            <div class="space-y-2">
                                <a href="{{ route('verification.show', $credential->verification_code) }}" target="_blank" class="block w-full text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    {{ __('Public Verification Link') }}
                                </a>

                                @if($credential->json_path && file_exists(storage_path('app/public/' . $credential->json_path)))
                                    <a href="{{ route('credential.json', $credential) }}" class="block w-full text-center bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        {{ __('Download Blockcerts JSON') }}
                                    </a>
                                @endif

                                @if($credential->isVerified())
                                    <form method="POST" action="{{ route('credentials.revoke', $credential) }}" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to revoke this credential?')">
                                            {{ __('Revoke Credential') }}
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
</x-app-layout> 