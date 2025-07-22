<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('RPC Units') }}
            </h2>
        </div>
    </x-slot>

    <script>
        document.addEventListener('alpine:init', () => {
            // Create a store for the drawer state
            Alpine.store('drawer', {
                open: false,
                toggle() {
                    this.open = !this.open;
                    document.body.classList.toggle('overflow-hidden', this.open);
                },
                close() {
                    this.open = false;
                    document.body.classList.remove('overflow-hidden');
                }
            });

            // Close drawer on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && Alpine.store('drawer').open) {
                    Alpine.store('drawer').close();
                }
            });
        });

        // Global function to load RPC units via AJAX
        async function loadRpcUnits() {
            try {
                const response = await axios.get('{{ route("rpcunit.index") }}');
                const parser = new DOMParser();
                const html = parser.parseFromString(response.data, 'text/html');
                const table = html.querySelector('table');
                if (table) {
                    const container = document.querySelector('#rpc-units-table');
                    if (container) {
                        container.innerHTML = table.outerHTML;
                    }
                }
            } catch (error) {
                console.error('Error loading RPC units:', error);
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: {
                        message: 'Failed to refresh RPC units list',
                        type: 'error'
                    }
                }));
            }
        }

        // Listen for rpc-unit-created event to refresh the table
        document.addEventListener('rpc-unit-created', async (e) => {
            // Close the drawer
            const drawer = Alpine.store('drawer');
            if (drawer) {
                drawer.close();
            }

            // Show success message
            window.dispatchEvent(new CustomEvent('show-toast', {
                detail: {
                    message: 'RPC Unit created successfully!',
                    type: 'success'
                }
            }));

            // Refresh the table
            await loadRpcUnits();
        });
    </script>

    <!-- View Modal -->
    <div x-data="{
        show: false,
        unit: {},
        init() {
            // Listen for the open-rpc-modal event
            window.addEventListener('open-rpc-modal', (event) => {
                this.unit = event.detail;
                this.show = true;
                document.body.classList.add('overflow-hidden');
            });

            // Close modal on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.show) {
                    this.close();
                }
            });
        },
        close() {
            this.show = false;
            document.body.classList.remove('overflow-hidden');
        }
    }"
    x-show="show"
    x-init="init()"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                 @click="close()"
                 x-show="show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
            </div>

            <!-- Modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
                 x-show="show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="'RPC Unit: ' + (unit ? unit.rpc_identifier : '')"></h3>
                            <div class="mt-4">
                                <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-2">
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">ID</dt>
                                        <dd class="mt-1 text-sm text-gray-900" x-text="unit ? unit.id : ''"></dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Capacity (kg)</dt>
                                        <dd class="mt-1 text-sm text-gray-900" x-text="unit ? (unit.capacity_kg ? unit.capacity_kg + ' kg' : 'N/A') : ''"></dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Material Type</dt>
                                        <dd class="mt-1 text-sm text-gray-900" x-text="unit ? (unit.material_type || 'N/A') : ''"></dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                                        <dd class="mt-1">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                  :class="{
                                                      'bg-green-100 text-green-800': unit && unit.status === 'active',
                                                      'bg-gray-100 text-gray-800': unit && unit.status === 'inactive',
                                                      'bg-yellow-100 text-yellow-800': unit && unit.status === 'maintenance',
                                                      'bg-red-100 text-red-800': unit && unit.status === 'retired'
                                                  }"
                                                  x-text="unit ? (unit.status ? unit.status.charAt(0).toUpperCase() + unit.status.slice(1) : 'N/A') : ''">
                                            </span>
                                        </dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Total Wash Cycles</dt>
                                        <dd class="mt-1 text-sm text-gray-900" x-text="unit ? (unit.total_wash_cycles || '0') : ''"></dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Total Reuse Count</dt>
                                        <dd class="mt-1 text-sm text-gray-900" x-text="unit ? (unit.total_reuse_count || '0') : ''"></dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Initial Purchase Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900" x-text="unit && unit.initial_purchase_date ? new Date(unit.initial_purchase_date).toLocaleDateString() : 'N/A'"></dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Last Washed Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900" x-text="unit && unit.last_washed_date ? new Date(unit.last_washed_date).toLocaleString() : 'Never'"></dd>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Current Location</dt>
                                        <dd class="mt-1 text-sm text-gray-900" x-text="unit ? (unit.current_location || 'N/A') : ''"></dd>
                                    </div>
                                    <div class="sm:col-span-2" x-show="unit && unit.notes">
                                        <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                        <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line" x-text="unit.notes"></dd>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button"
                            @click="close()"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Side Drawer for Creating New RPC Unit -->
    <div x-data x-init="$watch('$store.drawer.open', value => {
        if (value) {
            document.body.classList.add('overflow-hidden');
        } else {
            document.body.classList.remove('overflow-hidden');
        }
    })">
        <!-- Overlay -->
        <div x-show="$store.drawer.open"
             x-transition:enter="transition-opacity ease-in-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in-out duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 z-40"
             @click="$store.drawer.close()">
        </div>

        <!-- Drawer Panel -->
        <div x-show="$store.drawer.open"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="fixed inset-y-0 right-0 w-full max-w-md bg-white shadow-xl z-50 overflow-y-auto">
            <div class="px-6 py-6">
                <x-rpc.form :submitText="__('Create RPC Unit')" @success="$store.drawer.close(); window.location.reload();" />
            </div>
        </div>
    </div>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filter Form -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:p-6">
                    <form method="GET" action="{{ route('rpcunit.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <!-- RPC Identifier -->
                            <div class="lg:col-span-1">
                                <label for="rpc_identifier" class="block text-sm font-medium text-gray-700">RPC ID</label>
                                <input type="text" name="rpc_identifier" id="rpc_identifier"
                                       value="{{ request('rpc_identifier') }}"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <!-- Material Type -->
                            <div class="lg:col-span-1">
                                <label for="material_type" class="block text-sm font-medium text-gray-700">Material Type</label>
                                <select id="material_type" name="material_type" class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">All Types</option>
                                    @foreach(['plastic', 'metal', 'wood', 'other'] as $type)
                                        <option value="{{ $type }}" {{ request('material_type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status -->
                            <div class="lg:col-span-1">
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="status" name="status" class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">All Statuses</option>
                                    @foreach(['available', 'in_use', 'maintenance', 'retired'] as $status)
                                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Capacity (kg) -->
                            <div class="lg:col-span-1">
                                <label for="capacity_kg" class="block text-sm font-medium text-gray-700">Capacity (kg)</label>
                                <input type="number" name="capacity_kg" id="capacity_kg"
                                       value="{{ request('capacity_kg') }}" step="0.01" min="0"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <!-- Buttons -->
                            <div class="lg:col-span-1 flex items-end space-x-2">
                                <button type="submit" class="inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex-1">
                                    Apply
                                </button>
                                <a href="{{ route('rpcunit.index') }}" class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex-1">
                                    Reset
                                </a>
                            </div>
                    </form>
                </div>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                RPC Units List
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                Manage your Reusable Packaging Containers
                            </p>
                        </div>
                        <div>
                            <button type="button" @click="$store.drawer.open = true" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Add New Unit
                            </button>
                        </div>
                    </div>
                </div>

                @if (session('success'))
                    <div class="bg-green-50 border-l-4 border-green-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">
                                    {{ session('success') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="overflow-x-auto" id="rpc-units-table">
                    <div class="align-middle inline-block min-w-full">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-b-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        RPC Identifier
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Capacity (L)
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Material Type
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Wash Cycles
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Reuse Count
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($rpcUnits as $unit)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $unit->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $unit->rpc_identifier ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $unit->capacity ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $unit->material_type ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $unit->total_wash_cycle ?? '0' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $unit->total_reuse_count ?? '0' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusClasses = [
                                                    'active' => 'bg-green-100 text-green-800',
                                                    'inactive' => 'bg-gray-100 text-gray-800',
                                                    'maintenance' => 'bg-yellow-100 text-yellow-800',
                                                    'retired' => 'bg-red-100 text-red-800',
                                                ];
                                                $status = $unit->status ?? 'inactive';
                                                $statusClass = $statusClasses[$status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                    {{ ucfirst($status) }}
                                                </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @can('create_packaging')
                                                <div class="flex justify-end space-x-3">
                                                    <button @click="window.dispatchEvent(new CustomEvent('open-rpc-modal', { detail: {
                                                            id: '{{ $unit->id }}',
                                                            rpc_identifier: '{{ $unit->rpc_identifier }}',
                                                            capacity_kg: '{{ $unit->capacity_kg }}',
                                                            material_type: '{{ $unit->material_type }}',
                                                            status: '{{ $unit->status }}',
                                                            total_wash_cycles: '{{ $unit->total_wash_cycles }}',
                                                            total_reuse_count: '{{ $unit->total_reuse_count }}',
                                                            initial_purchase_date: '{{ $unit->initial_purchase_date }}',
                                                            last_washed_date: '{{ $unit->last_washed_date }}',
                                                            current_location: '{{ $unit->current_location }}',
                                                            notes: '{{ addslashes($unit->notes) }}'
                                                          }}))"
                                                            class="text-indigo-600 hover:text-indigo-900 focus:outline-none"
                                                            aria-label="View details">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </button>
                                                    <a href="{{ route('rpcunit.edit', $unit) }}" class="text-yellow-600 hover:text-yellow-900">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>
                                                    <div class="inline-flex items-center space-x-1 delete-container">
                                                        <button
                                                            type="button"
                                                            class="delete-btn text-red-600 hover:text-red-900 focus:outline-none"
                                                            data-id="{{ $unit->id }}"
                                                            data-url="{{ route('rpcunit.destroy', $unit) }}"
                                                            title="Delete RPC Unit">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                        <div class="confirmation-buttons hidden items-center space-x-1">
                                                            <span class="text-xs text-gray-600 mr-1">Are you sure?</span>
                                                            <button type="button" class="confirm-delete px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 focus:outline-none">
                                                                Yes
                                                            </button>
                                                            <button type="button" class="cancel-delete px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300 focus:outline-none">
                                                                No
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No RPC units found.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>

                            <!-- Table Footer with Pagination -->
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="8" class="px-6 py-4">
                                        <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                                            <div class="text-sm text-gray-700 px-6">
                                                Showing <span class="font-medium">{{ $rpcUnits->firstItem() }}</span> to
                                                <span class="font-medium">{{ $rpcUnits->lastItem() }}</span> of
                                                <span class="font-medium">{{ $rpcUnits->total() }}</span> results
                                            </div>

                                            <div class="flex items-center space-x-4">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-sm text-gray-700">Items per page:</span>
                                                    <form method="GET" action="{{ route('rpcunit.index') }}" class="inline-block">
                                                        <!-- Include all current filters as hidden inputs -->
                                                        @foreach(request()->except('per_page', 'page') as $key => $value)
                                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                                        @endforeach
                                                        <select name="per_page" onchange="this.form.submit()"
                                                                class="block w-20 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                                            @foreach([5, 10, 20, 30, 50] as $perPage)
                                                                <option value="{{ $perPage }}" {{ request('per_page', 20) == $perPage ? 'selected' : '' }}>
                                                                    {{ $perPage }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </form>
                                                </div>

                                                @if($rpcUnits->hasPages())
                                                    <div class="pagination">
                                                        {{ $rpcUnits->withQueryString()->links() }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@push('styles')
<style>
    .delete-container {
        position: relative;
        display: inline-flex;
        vertical-align: middle;
    }

    .confirmation-buttons {
        position: absolute;
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
        background: white;
        padding: 4px 8px;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        white-space: nowrap;
        z-index: 10;
        margin-left: 8px;
        border: 1px solid #e5e7eb;
    }

    .confirmation-buttons:before {
        content: '';
        position: absolute;
        right: 100%;
        top: 50%;
        transform: translateY(-50%);
        border-width: 6px;
        border-style: solid;
        border-color: transparent #e5e7eb transparent transparent;
    }

    .confirmation-buttons:after {
        content: '';
        position: absolute;
        right: 100%;
        top: 50%;
        transform: translateY(-50%);
        border-width: 5px;
        border-style: solid;
        border-color: transparent white transparent transparent;
        margin-right: -1px;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete button clicks
    document.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.delete-btn');
        const confirmBtn = e.target.closest('.confirm-delete');
        const cancelBtn = e.target.closest('.cancel-delete');

        // Show confirmation buttons when delete is clicked
        if (deleteBtn) {
            e.preventDefault();
            e.stopPropagation();

            // Hide all other open confirmations
            document.querySelectorAll('.confirmation-buttons').forEach(el => {
                el.classList.add('hidden');
            });

            // Show this confirmation
            const container = deleteBtn.closest('.delete-container');
            const confirmation = container.querySelector('.confirmation-buttons');
            confirmation.classList.remove('hidden');

            // Close on outside click
            const clickHandler = function(event) {
                if (!container.contains(event.target)) {
                    confirmation.classList.add('hidden');
                    document.removeEventListener('click', clickHandler);
                }
            };

            // Add a small delay to prevent immediate close
            setTimeout(() => {
                document.addEventListener('click', clickHandler);
            }, 100);
        }
        // Handle confirm delete
        else if (confirmBtn) {
            e.preventDefault();
            e.stopPropagation();

            const container = confirmBtn.closest('.delete-container');
            const deleteBtn = container.querySelector('.delete-btn');
            const confirmation = container.querySelector('.confirmation-buttons');
            const id = deleteBtn.getAttribute('data-id');
            const url = deleteBtn.getAttribute('data-url');

            // Show loading state
            confirmation.innerHTML = `
                <span class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Deleting...
                </span>
            `;

            // Get CSRF token from meta tag
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Send DELETE request
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                // Show success message
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: {
                        message: data.message || 'RPC Unit deleted successfully',
                        type: 'success'
                    }
                }));

                // Remove the row from the table
                const row = container.closest('tr');
                if (row) {
                    row.style.opacity = '0';
                    setTimeout(() => row.remove(), 300);
                }
            })
            .catch(error => {
                console.error('Error:', error);

                // Show error message
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: {
                        message: error.message || 'Failed to delete RPC Unit',
                        type: 'error'
                    }
                }));

                // Reset confirmation UI
                confirmation.classList.add('hidden');
            });
        }
        // Handle cancel delete
        else if (cancelBtn) {
            e.preventDefault();
            e.stopPropagation();

            const container = cancelBtn.closest('.delete-container');
            const confirmation = container.querySelector('.confirmation-buttons');
            confirmation.classList.add('hidden');
        }
    });
});
</script>
@endpush

</x-app-layout>
