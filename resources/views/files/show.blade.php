<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('files.index') }}" class="text-gray-500 hover:text-indigo-600 mr-3 transition-colors"
                title="Back to Files">
                <svg class="w-6 h-6 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('File Details') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <!-- Header Section -->
                <div
                    class="bg-gray-50 border-b border-gray-100 p-6 sm:px-10 flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <div
                            class="flex items-center text-sm text-indigo-600 font-semibold mb-2 uppercase tracking-wider">
                            <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            File Document
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 break-all leading-tight">
                            {{ $file->name }}
                        </h1>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="p-6 sm:p-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        <!-- Upload Date -->
                        <div class="flex flex-col">
                            <h3 class="text-sm font-bold text-gray-500 mb-1 uppercase tracking-wide">Upload Date</h3>
                            <p class="text-base text-gray-900 font-medium">
                                {{ $file->created_at->format('M d, Y \a\t H:i') }}
                            </p>
                            <p class="text-sm text-gray-400 mt-0.5">
                                {{ $file->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <!-- View Count -->
                        <div class="flex flex-col">
                            <h3 class="text-sm font-bold text-gray-500 mb-2 uppercase tracking-wide">Total Views</h3>
                            <div class="flex items-center">
                                <span
                                    class="inline-flex items-center bg-indigo-50 text-indigo-700 text-lg font-bold px-4 py-1.5 rounded-full ring-1 ring-indigo-200">
                                    <svg class="w-5 h-5 mr-2 opacity-75" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    {{ number_format($file->view_count) }} {{ Str::plural('View', $file->view_count) }}
                                </span>
                            </div>
                        </div>

                        <!-- Expiration Status -->
                        <div class="flex flex-col md:col-span-2">
                            <h3 class="text-sm font-bold text-gray-500 mb-1 uppercase tracking-wide">Expiration Status
                            </h3>
                            @if($file->expires_at)
                                <p
                                    class="text-base font-medium flex items-center {{ $file->expires_at->isPast() ? 'text-red-600' : 'text-orange-600' }}">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $file->expires_at->format('M d, Y \a\t H:i') }}
                                    <span class="text-sm ml-2 font-normal opacity-80 border-l border-current pl-2">
                                        {{ $file->expires_at->isPast() ? 'Expired' : 'Expires ' . $file->expires_at->diffForHumans() }}
                                    </span>
                                </p>
                            @else
                                <p class="text-base text-green-600 font-medium flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Never Expires
                                </p>
                            @endif
                        </div>

                        <!-- Image Preview -->
                        <div class="col-span-1 md:col-span-2 pt-4 border-t border-gray-100 mt-2">
                            <h3 class="text-sm font-bold text-gray-500 mb-4 mt-2 uppercase tracking-wide">Image Preview
                            </h3>
                            <div
                                class="bg-gray-100 rounded-xl border border-gray-200 overflow-hidden flex items-center justify-center p-2 sm:p-4">
                                <img src="{{ route('files.download', $file) }}" alt="{{ $file->name }}"
                                    class="max-h-[500px] w-auto object-contain rounded shadow-sm">
                            </div>

                            <!-- Delete button -->
                            <div class="mt-4 flex flex-col sm:flex-row justify-end gap-3">
                                <form action="{{ route('files.destroy', $file) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this file? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 shadow-sm transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                        Delete File
                                    </button>
                                </form>

                                <!-- Download button -->
                                <a href="{{ route('files.download', $file) }}"
                                    class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition ease-in-out duration-150 shadow-sm"
                                    download>
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Download Image
                                </a>
                            </div>
                        </div>

                    </div>

                    <!-- Comment Section -->
                    <div class="mt-8 pt-8 border-t border-gray-100">
                        <h3 class="text-sm font-bold text-gray-500 mb-3 flex items-center uppercase tracking-wide">
                            Comment & Description
                        </h3>
                        @if($file->comment)
                            <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                                <p class="text-gray-700 leading-relaxed text-sm sm:text-base">
                                    {{ $file->comment }}
                                </p>
                            </div>
                        @else
                            <div
                                class="bg-gray-50/50 rounded-xl p-6 border border-dashed border-gray-300 flex flex-col items-center justify-center text-center">
                                <p class="text-gray-400 italic text-sm">No additional details or comments were provided.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Share Links Manager Component -->
                    <x-files.share-manager :file="$file" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>