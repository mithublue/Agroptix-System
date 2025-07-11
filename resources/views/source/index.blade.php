<x-app-layout>
    {{-- This optional header slot will go into the <header> of your layout --}}
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Sources') }}
            </h2>
            @can('create', \App\Models\Source::class)
                <a href="{{ route('sources.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Add New Source') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Filters -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Filters</h3>
                        <form method="GET" action="{{ route('sources.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <!-- Type Filter -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                <select id="type" name="type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    @foreach($types as $value => $label)
                                        <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Production Method Filter -->
                            <div>
                                <label for="production_method" class="block text-sm font-medium text-gray-700 mb-1">Production Method</label>
                                <select id="production_method" name="production_method" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    @foreach($productionMethods as $value => $label)
                                        <option value="{{ $value }}" {{ request('production_method') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    @foreach($statuses as $value => $label)
                                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Owner Filter -->
                            <div>
                                <label for="owner_id" class="block text-sm font-medium text-gray-700 mb-1">Owner</label>
                                <select id="owner_id" name="owner_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    @foreach($owners as $id => $name)
                                        <option value="{{ $id }}" {{ request('owner_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Buttons -->
                            <div class="flex flex-col justify-end">
                                <div class="flex space-x-2">
                                    <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 h-10">
                                        Apply
                                    </button>
                                    <a href="{{ route('sources.index') }}" class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 h-10">
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Production Method</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($sources as $source)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ config('at.type.' . $source->type, $source->type) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($source->gps_lat && $source->gps_long)
                                                <div class="text-sm text-gray-900">
                                                    {{ $source->gps_lat }}, {{ $source->gps_long }}
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-500">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900">{{ $source->production_method ?? 'N/A' }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900">{{ $source->area ?? 'N/A' }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statuses = config('at.source_status', []);
                                                $statusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'approved' => 'bg-green-100 text-green-800',
                                                    'rejected' => 'bg-red-100 text-red-800',
                                                    'active' => 'bg-blue-100 text-blue-800',
                                                    'inactive' => 'bg-gray-100 text-gray-800'
                                                ];
                                                $color = $statusColors[$source->status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            @can('manage_source')
                                                <div x-data="{
                                                    status: '{{ $source->status }}',
                                                    isUpdating: false,
                                                    statuses: {{ json_encode($statuses) }},
                                                    statusColors: {{ json_encode($statusColors) }},
                                                    updateStatus() {
                                                        this.isUpdating = true;
                                                        fetch('{{ route('sources.update.status', $source) }}', {
                                                            method: 'PATCH',
                                                            headers: {
                                                                'Content-Type': 'application/json',
                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                'Accept': 'application/json',
                                                                'X-Requested-With': 'XMLHttpRequest'
                                                            },
                                                            body: JSON.stringify({ status: this.status })
                                                        })
                                                        .then(response => response.json())
                                                        .then(data => {
                                                            if (data.success) {
                                                                this.status = data.status;
                                                                this.$dispatch('notify', {
                                                                    type: 'success',
                                                                    message: data.message || 'Status updated successfully'
                                                                });
                                                            } else {
                                                                this.$dispatch('notify', {
                                                                    type: 'error',
                                                                    message: data.message || 'Failed to update status.'
                                                                });
                                                            }
                                                        })
                                                        .catch(error => {
                                                            console.error('Error:', error);
                                                            this.$dispatch('notify', {
                                                                type: 'error',
                                                                message: 'An error occurred while updating status.'
                                                            });
                                                        })
                                                        .finally(() => {
                                                            this.isUpdating = false;
                                                        });
                                                    }
                                                }" class="relative">
                                                    <div class="relative">
                                                        <select x-model="status" 
                                                                @change="updateStatus"
                                                                :disabled="isUpdating"
                                                                class="appearance-none block w-full bg-white border border-gray-300 rounded-md py-1 px-2 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                                                                :class="statusColors[status] + ' cursor-pointer pr-8'">
                                                            @foreach($statuses as $value => $label)
                                                                <option value="{{ $value }}" class="bg-white text-gray-900">{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div x-show="isUpdating" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center">
                                                        <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                                    {{ $statuses[$source->status] ?? ucfirst($source->status) }}
                                                </span>
                                            @endcan
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $source->owner->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('sources.show', $source) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                            @can('edit', $source)
                                                <a href="{{ route('sources.edit', $source) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                            @endcan
                                            @can('delete', $source)
                                                <form action="{{ route('sources.destroy', $source) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                                            onclick="return confirm('Are you sure you want to delete this source?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No sources found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $sources->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
