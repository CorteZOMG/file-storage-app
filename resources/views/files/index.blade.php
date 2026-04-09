<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Files') }}
        </h2>
    </x-slot>

    <x-upload-modal />

    <!-- Add file button -->
    <div x-data="" x-on:click.prevent="$dispatch('open-modal', 'upload-modal')"
         class="fixed flex items-center justify-center cursor-pointer w-14 h-14 bottom-8 right-8 bg-purple-500 hover:bg-purple-600 text-white rounded-full text-4xl shadow-lg transition pb-1 z-40">
        +
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-8">
        <h3 class="text-xl font-bold mb-4 text-gray-800">Your Files</h3>

        @if($files->isEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    aria-hidden="true">
                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
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