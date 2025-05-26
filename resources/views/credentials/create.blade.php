<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Upload New Credential') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('credentials.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Full Name -->
                        <div>
                            <x-input-label for="full_name" :value="__('Full Name')" />
                            <x-text-input id="full_name" class="block mt-1 w-full" type="text" name="full_name" :value="old('full_name')" required autofocus />
                            <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
                        </div>

                        <!-- Credential Type -->
                        <div>
                            <x-input-label for="credential_type" :value="__('Credential Type')" />
                            <x-text-input id="credential_type" class="block mt-1 w-full" type="text" name="credential_type" :value="old('credential_type')" required placeholder="e.g., Bachelor of Science, Certificate in Web Development" />
                            <x-input-error :messages="$errors->get('credential_type')" class="mt-2" />
                        </div>

                        <!-- Issued By -->
                        <div>
                            <x-input-label for="issued_by" :value="__('Issued By')" />
                            <x-text-input id="issued_by" class="block mt-1 w-full" type="text" name="issued_by" :value="old('issued_by')" required placeholder="e.g., Department of Computer Science" />
                            <x-input-error :messages="$errors->get('issued_by')" class="mt-2" />
                        </div>

                        <!-- Issued On -->
                        <div>
                            <x-input-label for="issued_on" :value="__('Issued On')" />
                            <x-text-input id="issued_on" class="block mt-1 w-full" type="date" name="issued_on" :value="old('issued_on')" required />
                            <x-input-error :messages="$errors->get('issued_on')" class="mt-2" />
                        </div>

                        <!-- Credential File -->
                        <div>
                            <x-input-label for="credential_file" :value="__('Credential File')" />
                            <input id="credential_file" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" type="file" name="credential_file" required accept=".pdf,.jpg,.jpeg,.png">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">PDF, JPG, JPEG, or PNG (Max: 10MB)</p>
                            <x-input-error :messages="$errors->get('credential_file')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('credentials.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Upload Credential') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 