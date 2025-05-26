<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Verification Result</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-gray-900">CredVerify</h1>
                    </div>
                    <nav class="space-x-4">
                        <a href="{{ route('verification.index') }}" class="text-gray-600 hover:text-gray-900">Verify Another</a>
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Login</a>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                @if($found)
                    <!-- Credential Found -->
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <!-- Success Header -->
                        <div class="bg-green-50 px-6 py-4 border-b border-green-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-lg font-medium text-green-800">Credential Verified</h3>
                                    <p class="text-sm text-green-600">This credential is authentic and valid.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Credential Details -->
                        <div class="px-6 py-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Left Column -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Full Name</label>
                                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $credential->full_name }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Credential Type</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $credential->credential_type }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Issued By</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $credential->issued_by }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Institution</label>
                                        <div class="mt-1 flex items-center">
                                            @if($credential->institution->logo_url)
                                                <img src="{{ $credential->institution->logo_url }}" alt="{{ $credential->institution->name }}" class="h-8 w-8 rounded mr-2">
                                            @endif
                                            <p class="text-sm text-gray-900">{{ $credential->institution->name }}</p>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Date Issued</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $credential->issued_on->format('F d, Y') }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Status</label>
                                        <div class="mt-1">
                                            @if($credential->isVerified())
                                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                                    ✓ Verified
                                                </span>
                                            @else
                                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                                    ✗ Revoked
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="space-y-4">
                                    @if(isset($qrPath) && $qrPath)
                                        <div class="text-center">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">QR Code</label>
                                            <img src="{{ $qrPath }}" alt="QR Code" class="mx-auto border rounded">
                                        </div>
                                    @endif

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Verification Code</label>
                                        <p class="mt-1 text-sm font-mono bg-gray-100 p-2 rounded">{{ $credential->verification_code }}</p>
                                    </div>

                                    @if($credential->json_path)
                                        <div>
                                            <a href="{{ route('credential.json', $credential) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                                Download Blockcerts JSON
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Credential Not Found -->
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <!-- Error Header -->
                        <div class="bg-red-50 px-6 py-4 border-b border-red-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-lg font-medium text-red-800">Credential Not Found</h3>
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="px-6 py-6 text-center">
                            <p class="text-gray-600 mb-4">Please check the verification code and try again.</p>
                            <a href="{{ route('verification.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Try Again
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Additional Actions -->
                <div class="mt-8 text-center">
                    <a href="{{ route('verification.index') }}" class="text-blue-600 hover:text-blue-800">
                        ← Verify Another Credential
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <p class="text-center text-sm text-gray-600">
                    © {{ date('Y') }} CredVerify. Secure credential verification system.
                </p>
            </div>
        </footer>
    </div>
</body>
</html> 