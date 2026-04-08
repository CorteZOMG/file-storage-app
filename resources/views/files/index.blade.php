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

                    <form method="POST" action="{{ route('files.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- The Filepicker -->
                        <div class="mt-4">
                            <x-input-label for="file" :value="__('Select Image (Max 5MB)')" />
                            <input id="file" type="file" name="file"
                                class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2" required />
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
                            <x-text-input id="expires_at" type="datetime-local" name="expires_at"
                                :value="old('expires_at')"
                                min="{{ now()->setTimezone(config('app.timezone'))->format('Y-m-d\TH:i') }}"
                                class="block w-full mt-1" />
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-8">
            <h3 class="text-xl font-bold mb-4 text-gray-800">Your Files</h3>
            
            @if($files->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p>You haven't uploaded any files yet.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($files as $file)
                        <x-file-card :file="$file" />
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>