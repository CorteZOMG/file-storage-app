@props(['file'])

<div class="mt-8 pt-8 border-t border-gray-100">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-4">
        <h3 class="text-sm font-bold text-gray-500 flex items-center uppercase tracking-wide">
            <x-icons.link class="w-4 h-4 mr-2" />
            Active Share Links
        </h3>
        
        <div class="flex gap-2">
            <!-- Generate Public Link Form -->
            <form action="{{ route('files.share', $file) }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="public">
                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Public Link
                </button>
            </form>

            <!-- Generate One-Time Link Form -->
            <form action="{{ route('files.share', $file) }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="one-time">
                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 border border-indigo-200 rounded-md font-semibold text-xs text-indigo-700 uppercase tracking-widest shadow-sm hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    One-Time Link
                </button>
            </form>
        </div>
    </div>

    @if($file->shareLinks->isNotEmpty())
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
            <ul class="divide-y divide-gray-200">
                @foreach($file->shareLinks->sortByDesc('created_at') as $link)
                    <li class="p-4 hover:bg-gray-50 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center flex-wrap gap-3">
                            <!-- Type Badge -->
                            @if($link->isPublic())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Public
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    One-Time
                                </span>
                            @endif

                            <!-- Status Badge -->
                            @if(!$link->isValid())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                    Exhausted
                                </span>
                            @endif

                            <!-- URL Display -->
                            <div class="text-sm font-mono text-gray-600 truncate max-w-[200px] sm:max-w-xs {{ !$link->isValid() ? 'line-through opacity-50' : '' }}">
                                {{ route('shared.show', $link->token) }}
                            </div>
                            
                            <!-- Copy Button -->
                            @if($link->isValid())
                            <button onclick="navigator.clipboard.writeText('{{ route('shared.show', $link->token) }}'); alert('Link copied to clipboard!');"
                                class="text-gray-400 hover:text-indigo-600 focus:outline-none transition-colors" title="Copy Link">
                                <x-icons.clipboard class="w-5 h-5" />
                            </button>
                            @endif
                        </div>

                        <div class="flex items-center text-sm text-gray-500 whitespace-nowrap">
                            <x-icons.eye class="w-4 h-4 mr-1.5 opacity-75" />
                            {{ $link->views }} views
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="bg-gray-50 rounded-lg p-6 border border-dashed border-gray-300 text-center">
            <p class="text-gray-500 text-sm">No links generated yet. Create one using the buttons above.</p>
        </div>
    @endif
</div>
