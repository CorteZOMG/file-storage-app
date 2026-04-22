@props(['file'])

<a href="{{ route('files.show', $file) }}" class="block h-full group cursor-pointer">
    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden border border-gray-100 flex flex-col h-full group-hover:shadow-lg group-hover:-translate-y-1 transition-all duration-200">
        
        <!-- Image Preview Area -->
        <div class="h-48 w-full bg-gray-50 flex items-center justify-center overflow-hidden border-b border-gray-100 relative">
            <img src="{{ route('files.preview', $file) }}" alt="{{ $file->name }}" class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105" loading="lazy">
        </div>

        <div class="p-6 flex flex-col flex-1 justify-between">
            <div>
            <!-- Card Header (Icon and Date) -->
            <div class="flex items-center justify-between mb-4">
                <div class="text-sm text-gray-500 flex items-center">
                    <!-- Generic File Icon -->
                    <x-icons.page class="w-4 h-4 mr-1 text-indigo-500" />
                    File
                </div>
                <div class="text-xs text-gray-400" 
                     x-data="{ d: new Date('{{ $file->created_at->toIso8601String() }}') }"
                     x-bind:title="d.toLocaleString([], {month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'})"
                     title="{{ $file->created_at->format('M d, Y H:i') }} UTC">
                    {{ $file->created_at->diffForHumans() }}
                </div>
            </div>

            <!-- File Name -->
            <h4 class="text-lg font-bold text-gray-900 border-b pb-2 mb-3 break-all" title="{{ $file->name }}">
                {{ Str::limit($file->name, 40) }}
            </h4>

            <!-- Comment -->
            @if($file->comment)
                <p class="text-sm text-gray-600 mb-4 line-clamp-3">
                    {{ $file->comment }}
                </p>
            @else
                <p class="text-sm text-gray-400 italic mb-4">
                    No comment added.
                </p>
            @endif
        </div>

        <!-- Expiration Status -->
        @if($file->expires_at)
            <div
                class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs font-semibold {{ $file->expires_at->isPast() ? 'text-red-500' : 'text-orange-500' }}">
                <span>{{ $file->expires_at->isPast() ? 'Expired' : 'Expires' }}</span>
                <span>{{ $file->expires_at->diffForHumans() }}</span>
            </div>
        @endif
        </div>
    </div>
</a>