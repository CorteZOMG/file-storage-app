<x-guest-layout>
    <div class="w-full sm:max-w-4xl mx-auto px-6 py-12">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <!-- Header bar -->
            <div class="bg-indigo-600 px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <h2 class="text-white font-bold text-lg leading-tight tracking-widest flex items-center">
                    <svg class="w-5 h-5 mr-2 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    Shared File Secure Viewer
                </h2>
                <div class="text-indigo-200 text-sm font-medium bg-indigo-700/50 px-3 py-1 rounded-full uppercase tracking-wide">
                    {{ $link->type === 'one-time' ? 'One-Time Access Link' : 'Public Access Link' }}
                </div>
            </div>

            <!-- Image View -->
            <div class="bg-gray-100 p-4 sm:p-8 border-b border-gray-200 flex justify-center items-center min-h-[300px]">
                <img src="{{ $imageUrl }}" alt="Shared Image" class="max-h-[600px] w-auto object-contain rounded-lg shadow-sm border border-gray-200">
            </div>

            <!-- Description Area -->
            <div class="p-6 sm:p-8">
                <h3 class="text-xl sm:text-2xl font-bold text-gray-800 break-all">
                    {{ $link->file->name }}
                </h3>
                
                <div class="mt-2 text-sm text-gray-500 font-medium">
                    Uploaded {{ $link->file->created_at->format('M j, Y') }}
                </div>
                
                @if($link->file->comment)
                    <div class="mt-6 p-5 bg-gray-50 rounded-xl border border-gray-200">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Uploader's Comment</h4>
                        <p class="text-gray-700 leading-relaxed text-sm sm:text-base">
                            {{ $link->file->comment }}
                        </p>
                    </div>
                @endif
                
                <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                    <a href="{{ $imageUrl }}" download class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 shadow-md transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download Image
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
