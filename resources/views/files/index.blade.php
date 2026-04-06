<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Files') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Upload a New File</h3>

                    {{-- CRITICAL: enctype="multipart/form-data" is required for file uploads --}}
                    <form method="POST" action="{{ route('files.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- The Filepicker -->
                        <div class="mt-4">
                            <x-input-label for="file" :value="__('Select Image (Max 5MB)')" />
                            {{-- Standard HTML file input stylized with Tailwind --}}
                            <input id="file" type="file" name="file"
                                class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2" required />
                            {{-- Validation Errors magically appear here --}}
                            <x-input-error :messages="$errors->get('file')" class="mt-2" />
                        </div>

                        <!-- The Comment Field -->
                        <div class="mt-4">
                            <x-input-label for="comment" :value="__('Comment (Optional)')" />
                            <x-text-input id="comment" type="text" name="comment" :value="old('comment')"
                                class="block w-full mt-1" />
                            <x-input-error :messages="$errors->get('comment')" class="mt-2" />
                        </div>

                        <!-- Date Field for Expiry -->
                        <div class="mt-4">
                            <x-input-label for="expires_at" :value="__('Expiration Date (Optional)')" />
                            {{-- type="datetime-local" gives you a nice native browser calendar picker --}}
                            <x-text-input id="expires_at" type="datetime-local" name="expires_at"
                                :value="old('expires_at')" class="block w-full mt-1" />
                            <x-input-error :messages="$errors->get('expires_at')" class="mt-2" />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Upload File') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>