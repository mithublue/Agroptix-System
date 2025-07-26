@php
    $showDrawer = request()->has('create') || $errors->any();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Shipment List') }}
            </h2>
            <div class="flex space-x-2">
                @can('create_shipment')
                    <a href="{{ route('shipments.index', ['create' => 1]) }}" 
                       @if(!$showDrawer) 
                           x-data="{}" 
                           x-on:click.prevent="
                               $dispatch('open-drawer');
                               window.history.pushState({}, '', '{{ route('shipments.index', ['create' => 1]) }}');
                           "
                       @endif
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Add New Shipment') }}
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Filters -->
            <div class="mb-6 bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Filters</h3>
                <form method="GET" action="{{ route('shipments.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                        <!-- Origin Filter -->
                        <div>
                            <label for="origin" class="block text-sm font-medium text-gray-700 mb-1">Origin</label>
                            <input type="text" id="origin" name="origin" value="{{ request('origin') }}"
                                   class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                   placeholder="Filter by origin">
                        </div>

                        <!-- Destination Filter -->
                        <div>
                            <label for="destination" class="block text-sm font-medium text-gray-700 mb-1">Destination</label>
                            <input type="text" id="destination" name="destination" value="{{ request('destination') }}"
                                   class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                   placeholder="Filter by destination">
                        </div>

                        <!-- Vehicle Type Filter -->
                        <div>
                            <label for="vehicle_type" class="block text-sm font-medium text-gray-700 mb-1">Vehicle Type</label>
                            <select id="vehicle_type" name="vehicle_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">All Types</option>
                                @foreach(['Truck', 'Ship', 'Train', 'Air', 'Other'] as $type)
                                    <option value="{{ $type }}" {{ request('vehicle_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Mode Filter -->
                        <div>
                            <label for="mode" class="block text-sm font-medium text-gray-700 mb-1">Mode</label>
                            <select id="mode" name="mode" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">All Modes</option>
                                @foreach(['Road', 'Sea', 'Rail', 'Air', 'Multimodal'] as $mode)
                                    <option value="{{ $mode }}" {{ request('mode') == $mode ? 'selected' : '' }}>{{ $mode }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2">
                            <button type="submit" class="h-10 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Apply Filters
                            </button>
                            <a href="{{ route('shipments.index') }}" class="h-10 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if($shipments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origin</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Distance (km)</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departure</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($shipments as $shipment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $shipment->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($shipment->batch)
                                                <a href="{{ route('batches.show', $shipment->batch) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $shipment->batch->batch_code ?? 'Batch #' . $shipment->batch->id }}
                                                </a>
                                            @else
                                                <span class="text-gray-400">No batch</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $shipment->origin ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $shipment->destination ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $shipment->vehicle_type ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $shipment->mode ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $shipment->route_distance ? number_format($shipment->route_distance, 2) : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $shipment->departure_time ? \Carbon\Carbon::parse($shipment->departure_time)->format('M d, Y H:i') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                @can('view_shipment')
                                                    <a href="{{ route('shipments.show', $shipment) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </a>
                                                @endcan
                                                @can('edit_shipment')
                                                    <a href="{{ route('shipments.edit', $shipment) }}" class="text-yellow-600 hover:text-yellow-900">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </a>
                                                @endcan
                                                @can('delete_shipment')
                                                    <form action="{{ route('shipments.destroy', $shipment) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this shipment?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($shipments->hasPages())
                        <div class="px-6 py-4 bg-gray-50">
                            {{ $shipments->withQueryString()->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-6 text-center text-gray-500">
                        No shipments found.
                        @can('create_shipment')
                            <a href="{{ route('shipments.create') }}" class="text-indigo-600 hover:underline">Create one now</a>.
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Side Drawer for Creating/Editing Shipments -->
    <x-shipment.drawer :show="$showDrawer" :title="'Add New Shipment'" :formAction="route('shipments.store')">
        <x-shipment.form :batches="\App\Models\Batch::latest()->limit(100)->get()" />
    </x-shipment.drawer>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('shipmentDrawer', () => ({
                    show: @js($showDrawer),
                    init() {
                        // Listen for the open-drawer event
                        window.addEventListener('open-drawer', () => {
                            this.show = true;
                        });
                        
                        // Handle browser back/forward buttons
                        window.addEventListener('popstate', (event) => {
                            const urlParams = new URLSearchParams(window.location.search);
                            this.show = urlParams.has('create');
                        });
                    },
                    close() {
                        this.show = false;
                        // Update URL without page reload
                        window.history.pushState({}, '', '{{ route('shipments.index') }}');
                    }
                }));
            });

            // Handle successful form submission
            document.addEventListener('shipment-created', (event) => {
                // Close the drawer
                const drawer = document.querySelector('[x-data^="shipmentDrawer"]');
                if (drawer) {
                    drawer.__x.$data.show = false;
                }
                
                // Reload the page to show the new shipment
                window.location.reload();
            });
        </script>
    @endpush
</x-app-layout>
