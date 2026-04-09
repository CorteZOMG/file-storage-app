<x-modal name="upload-modal" :show="$errors->isNotEmpty()" focusable>
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">
            {{ __('Upload a New File') }}
        </h2>

        <form method="POST" action="{{ route('files.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- The Filepicker -->
            <div class="mt-4">
                <x-input-label for="file" :value="__('Select File (Max 5MB)')" />
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
                <x-text-input id="expires_at" type="datetime-local" name="expires_at" :value="old('expires_at')"
                    min="{{ now()->setTimezone(config('app.timezone'))->format('Y-m-d\TH:i') }}"
                    class="block w-full mt-1" />
                <x-input-error :messages="$errors->get('expires_at')" class="mt-2" />
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end mt-6">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __('Upload File') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>