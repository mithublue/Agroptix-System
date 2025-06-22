<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Source Details') }}
            </h2>
            <div class="flex space-x-2">
                @can('update', $source)
                <a href="{{ route('sources.edit', $source) }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Edit') }}
                </a>
                @endcan
                <a href="{{ route('sources.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                            <dl class="divide-y divide-gray-200">
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Type</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ config('at.type.' . $source->type, $source->type) }}
                                    </dd>
                                </div>
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Production Method</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $source->production_method ?? 'N/A' }}
                                    </dd>
                                </div>
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Area</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $source->area ?? 'N/A' }}
                                    </dd>
                                </div>
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'approved' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                                'active' => 'bg-blue-100 text-blue-800',
                                                'inactive' => 'bg-gray-100 text-gray-800'
                                            ];
                                            $color = $statusColors[$source->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                            {{ ucfirst($source->status) }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Location Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Location</h3>
                            <dl class="divide-y divide-gray-200">
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">GPS Coordinates</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        @if($source->gps_lat && $source->gps_long)
                                            {{ $source->gps_lat }}, {{ $source->gps_long }}
                                            <a href="https://www.google.com/maps?q={{ $source->gps_lat }},{{ $source->gps_long }}" 
                                               target="_blank" 
                                               class="ml-2 text-indigo-600 hover:text-indigo-900 text-sm">
                                                (View on Map)
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Owner Information -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Owner Information</h3>
                        <div class="mt-4">
                            <dl class="divide-y divide-gray-200">
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Owner</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $source->owner->name }}
                                    </dd>
                                </div>
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $source->owner->email }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="text-sm text-gray-500">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <span class="font-medium">Created:</span>
                                    <span>{{ $source->created_at->format('M d, Y H:i') }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Last Updated:</span>
                                    <span>{{ $source->updated_at->format('M d, Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>