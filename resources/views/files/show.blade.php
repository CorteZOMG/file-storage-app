<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('files.index') }}" class="text-gray-500 hover:text-indigo-600 mr-3 transition-colors"
                title="Back to Files">
                <x-icons.arrow-left class="w-6 h-6 inline-block" />
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
                            <x-icons.document class="w-5 h-5 mr-1.5" />
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
                                    <x-icons.eye class="w-5 h-5 mr-2 opacity-75" />
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
                                    <x-icons.clock class="w-5 h-5 mr-2" />
                                    {{ $file->expires_at->format('M d, Y \a\t H:i') }}
                                    <span class="text-sm ml-2 font-normal opacity-80 border-l border-current pl-2">
                                        {{ $file->expires_at->isPast() ? 'Expired' : 'Expires ' . $file->expires_at->diffForHumans() }}
                                    </span>
                                </p>
                            @else
                                <p class="text-base text-green-600 font-medium flex items-center">
                                    <x-icons.check-circle class="w-5 h-5 mr-2" />
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
                                        <x-icons.trash class="w-4 h-4 mr-2" />
                                        Delete File
                                    </button>
                                </form>

                                <!-- Download button -->
                                <a href="{{ route('files.download', $file) }}"
                                    class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition ease-in-out duration-150 shadow-sm"
                                    download>
                                    <x-icons.download class="w-4 h-4 mr-2" />
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