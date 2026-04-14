<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Quick Stats -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg border border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Overview</h3>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div>
                        <div class="text-3xl font-bold">{{ $totalFiles }}</div>
                        <div class="text-sm text-gray-500">Active Files</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold">{{ $deletedFiles }}</div>
                        <div class="text-sm text-gray-500">Deleted Files</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold">{{ $linksStats['public']['count'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Public Links</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold">{{ $linksStats['one_time']['count'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">One-Time Links</div>
                    </div>
                </div>
            </div>

            <!-- Link Performance -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg border border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Link Performance Breakdown</h3>
                
                <div class="max-w-md">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2">Share Type</th>
                                <th class="py-2 text-right">Total Generated</th>
                                <th class="py-2 text-right">Total Views</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr>
                                <td class="py-3 text-gray-700">Public</td>
                                <td class="py-3 text-right">{{ $linksStats['public']['count'] }}</td>
                                <td class="py-3 text-right font-medium">{{ $linksStats['public']['views'] }}</td>
                            </tr>
                            <tr>
                                <td class="py-3 text-gray-700">One-Time</td>
                                <td class="py-3 text-right">{{ $linksStats['one_time']['count'] }}</td>
                                <td class="py-3 text-right font-medium">{{ $linksStats['one_time']['views'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Links -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg border border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Top 5 Most Viewed Links</h3>
                
                @if($topLinks->isEmpty())
                    <p class="text-gray-500 text-sm">No links found.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead>
                                <tr class="text-gray-600 border-b">
                                    <th class="py-2 px-2">Filename</th>
                                    <th class="py-2 px-2">Type</th>
                                    <th class="py-2 px-2 text-right">Views</th>
                                    <th class="py-2 px-2">Valid</th>
                                    <th class="py-2 px-2 text-right">Link</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($topLinks as $link)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-2">
                                            @if($link->file)
                                                {{ Str::limit($link->file->name, 40) }}
                                            @else
                                                <span class="italic text-gray-500">Deleted File</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-2 capitalize text-gray-700">{{ $link->type }}</td>
                                        <td class="py-3 px-2 text-right font-bold">{{ $link->views }}</td>
                                        <td class="py-3 px-2">
                                            @if($link->isValid())
                                                Yes
                                            @else
                                                <span class="text-gray-400">No</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-2 text-right">
                                            @if($link->isValid())
                                                <a href="{{ route('shared.show', $link->token) }}" target="_blank" class="text-blue-600 hover:underline">Open</a>
                                            @else
                                                <span class="text-gray-400">&mdash;</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
