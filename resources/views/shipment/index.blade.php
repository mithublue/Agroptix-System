<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Shipment List') }}
            </h2>
            <div class="flex space-x-2">
                @can('create_shipment')
                    <button @click="$dispatch('shipment-form-drawer:show')"
                            class="add-shipment-btn inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Add New Shipment') }}
                    </button>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="shipmentIndex()">
        <!-- Shipment Form Drawer -->
        <x-shipment.shipment-form-drawer>
            <x-shipment.shipment-form :batches="\App\Models\Batch::latest()->take(50)->get()" />
        </x-shipment.shipment-form-drawer>
        
        <!-- Shipment Show Drawer -->
        <div x-show="showViewDrawer" 
             @click.away="showViewDrawer = false"
             class="fixed inset-0 overflow-hidden z-50"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                <div class="relative w-screen max-w-2xl">
                    <div class="h-full flex flex-col bg-white shadow-xl overflow-y-scroll">
                        <div class="flex-1 py-6 overflow-y-auto px-4 sm:px-6">
                            <div class="flex items-start justify-between">
                                <h2 class="text-lg font-medium text-gray-900" id="slide-over-title">
                                    Shipment Details
                                </h2>
                                <div class="ml-3 h-7 flex items-center">
                                    <button @click="showViewDrawer = false" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <span class="sr-only">Close panel</span>
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-8">
                                <div x-show="isLoading">
                                    <x-shipment.loading-state />
                                </div>
                                <div x-show="!isLoading" x-html="shipmentDetails"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                                                    <button @click="viewShipment({{ $shipment->id }})" class="text-indigo-600 hover:text-indigo-900">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </button>
                                                @endcan
                                                @can('edit_shipment')
                                                    <button type="button"
                                                            class="text-indigo-600 hover:text-indigo-900 edit-shipment"
                                                            data-id="{{ $shipment->id }}">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </button>
                                                @endcan
                                                @can('delete_shipment')
                                                    <div class="inline-flex items-center" x-data="{ showConfirm: false }">
                                                        <button x-show="!showConfirm" 
                                                                @click="showConfirm = true" 
                                                                class="text-red-600 hover:text-red-900"
                                                                type="button">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                        <div x-show="showConfirm" 
                                                             x-transition:enter="transition ease-out duration-100"
                                                             x-transition:enter-start="opacity-0 scale-95"
                                                             x-transition:enter-end="opacity-100 scale-100"
                                                             x-transition:leave="transition ease-in duration-75"
                                                             x-transition:leave-start="opacity-100 scale-100"
                                                             x-transition:leave-end="opacity-0 scale-95"
                                                             class="inline-flex items-center space-x-1">
                                                            <span class="text-xs text-gray-600">Are you sure?</span>
                                                            <form action="{{ route('shipments.destroy', $shipment) }}" method="POST" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-medium">
                                                                    Yes
                                                                </button>
                                                            </form>
                                                            <button @click="showConfirm = false" class="text-gray-600 hover:text-gray-900 text-xs font-medium">
                                                                No
                                                            </button>
                                                        </div>
                                                    </div>
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
                            <a href="javascript:void(0)" @click="$dispatch('shipment-form-drawer:show')" class="text-indigo-600 hover:underline">Create one now</a>.
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function shipmentIndex() {
            return {
                showViewDrawer: false,
                isLoading: false,
                shipmentDetails: '',
                currentShipment: null,
                
                async viewShipment(shipmentId) {
                    try {
                        // Reset state
                        this.isLoading = true;
                        this.shipmentDetails = '';
                        this.currentShipment = null;
                        
                        // Show the drawer immediately
                        this.showViewDrawer = true;
                        
                        // Fetch shipment data
                        const response = await fetch(`{{ route('shipments.show', '') }}/${shipmentId}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        
                        if (!response.ok) {
                            throw new Error('Failed to fetch shipment details');
                        }
                        
                        const { data: shipment } = await response.json();
                        this.currentShipment = shipment;
                        
                        // Render the shipment details using the Blade component
                        const responseHtml = await fetch('{{ route("shipments.render-details") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ shipment })
                        });
                        
                        if (!responseHtml.ok) {
                            throw new Error('Failed to render shipment details');
                        }
                        
                        this.shipmentDetails = await responseHtml.text();
                        
                    } catch (error) {
                        console.error('Error fetching shipment:', error);
                        this.shipmentDetails = `
                            <div class="rounded-md bg-red-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Error loading shipment details</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <p>${error.message || 'An unknown error occurred while loading the shipment details.'}</p>
                                        </div>
                                        <div class="mt-4">
                                            <button @click="viewShipment(${shipmentId})" class="rounded-md bg-red-50 px-2 py-1.5 text-sm font-medium text-red-800 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 focus:ring-offset-red-50">
                                                Try again
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    } finally {
                        this.isLoading = false;
                    }
                },
                
                resetForm() {
                    const form = document.getElementById('shipment-form');
                    if (form) {
                        // Reset form fields
                        form.reset();
                        
                        // Remove _method field if it exists
                        const methodInput = form.querySelector('input[name="_method"]');
                        if (methodInput) {
                            methodInput.remove();
                        }
                        
                        // Reset form action to create route
                        form.action = '{{ route('shipments.store') }}';
                    }
                },
                
                init() {
                    // Handle Add New Shipment button click
                    document.querySelectorAll('.add-shipment-btn').forEach(button => {
                        button.addEventListener('click', () => {
                            this.resetForm();
                        });
                    });
                    
                    // Also handle the "Create one now" link if it exists
                    const createLink = document.querySelector('a[onclick*="shipment-form-drawer:show"]');
                    if (createLink) {
                        createLink.addEventListener('click', () => {
                            this.resetForm();
                        });
                    }
                    // Listen for the form submission success event
                    window.addEventListener('shipment-created', (event) => {
                        // Show success message
                        const successEvent = new CustomEvent('notify', {
                            detail: {
                                type: 'success',
                                message: event.detail.message || 'Shipment created successfully!'
                            }
                        });
                        window.dispatchEvent(successEvent);

                        // Close the drawer
                        window.dispatchEvent(new CustomEvent('shipment-form-drawer:close'));

                        // Reload the page to show the new shipment
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    });

                    // Handle edit button clicks
                    document.querySelectorAll('.edit-shipment').forEach(button => {
                        button.addEventListener('click', (e) => {
                            e.preventDefault();
                            const shipmentId = button.dataset.id;
                            this.editShipment(shipmentId);
                        });
                    });
                },

                async editShipment(shipmentId) {
                    try {
                        // Show loading state
                        const response = await fetch(`{{ route('shipments.show', '') }}/${shipmentId}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Failed to fetch shipment data');
                        }

                        const { data: shipment } = await response.json();

                        // Update form action and method
                        const form = document.getElementById('shipment-form');
                        form.action = `{{ route('shipments.update', '') }}/${shipmentId}`;
                        
                        // Add or update _method field for PUT
                        let methodInput = form.querySelector('input[name="_method"]');
                        if (!methodInput) {
                            methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            form.appendChild(methodInput);
                        }
                        methodInput.value = 'PUT';

                        // Set form values
                        if (form) {
                            // Set basic fields
                            const fields = [
                                'batch_id', 'origin', 'destination', 'vehicle_type', 'mode',
                                'fuel_type', 'temperature', 'route_distance', 'notes'
                            ];

                            fields.forEach(field => {
                                const input = form.querySelector(`[name="${field}"]`);
                                if (input) {
                                    if (input.type === 'checkbox') {
                                        input.checked = shipment[field];
                                    } else {
                                        input.value = shipment[field] || '';
                                    }
                                }
                            });

                            // Set datetime fields
                            const dateFields = ['departure_time', 'arrival_time'];
                            dateFields.forEach(field => {
                                const input = form.querySelector(`[name="${field}"]`);
                                if (input && shipment[field]) {
                                    const date = new Date(shipment[field]);
                                    input.value = date.toISOString().slice(0, 16);
                                }
                            });

                            // Set status if exists
                            if (shipment.status) {
                                const statusSelect = form.querySelector('[name="status"]');
                                if (statusSelect) {
                                    statusSelect.value = shipment.status;
                                }
                            }
                        }

                        // Show the drawer
                        window.dispatchEvent(new CustomEvent('shipment-form-drawer:show'));

                    } catch (error) {
                        console.error('Error fetching shipment data:', error);
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: {
                                type: 'error',
                                message: 'Failed to load shipment data. Please try again.'
                            }
                        }));
                    }
                }
            };
        }
    </script>
</x-app-layout>
