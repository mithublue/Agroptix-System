<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Packaging') }}
            </h2>
            @can('create_packaging')
                <button @click="$store.drawer.open = true; $store.drawer.packagingData = null"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Add New Packaging
                </button>
            @endcan
        </div>
    </x-slot>

    <!-- Success Message -->
    <div x-data="{ show: {{ json_encode(session('success') ? true : false) }}, message: '{{ addslashes(session('success', '')) }}' }"
         x-show="show && message"
         x-init="if(show) { setTimeout(() => { show = false }, 5000) }"
         class="fixed top-4 right-4 z-50"
         style="display: none;">
        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded shadow-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800" x-text="message"></p>
                </div>
                <div class="ml-4">
                    <button @click="show = false" class="text-green-500 hover:text-green-600 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="py-6" x-data="packagingIndex()">
        <script>
            function packagingIndex() {
                return {
                    packages: @json($packages->items()),
                    pagination: {
                        currentPage: {{ $packages->currentPage() }},
                        lastPage: {{ $packages->lastPage() }},
                        perPage: {{ $packages->perPage() }},
                        total: {{ $packages->total() }},
                        links: @json($packages->linkCollection()->toArray())
                    },
                    listeners: {}, // Store listener references for cleanup

                    init() {
                        console.log('packagingIndex initialized'); // Debug

                        // Listener for packaging-created
                        this.listeners.packagingCreated = (event) => {
                            console.log('packaging-created event:', event); // Debug
                            const newPackaging = event.detail;
                            if (newPackaging && newPackaging.id) {
                                this.packages.unshift(newPackaging);
                                if (this.packages.length > this.pagination.perPage) {
                                    this.packages.pop();
                                    this.pagination.total++;
                                }
                                this.$dispatch('notify', {
                                    type: 'success',
                                    message: 'Packaging added to list',
                                    timeout: 3000
                                });
                            } else {
                                console.warn('Invalid packaging data:', newPackaging);
                                this.$dispatch('notify', {
                                    type: 'error',
                                    message: 'Failed to add packaging to list',
                                    timeout: 5000
                                });
                            }
                        };
                        window.addEventListener('packaging-created', this.listeners.packagingCreated);

                        // Listener for refresh-data
                        this.listeners.refreshData = async () => {
                            try {
                                console.log('Refreshing data...'); // Debug
                                const response = await fetch('{{ route("admin.packaging.index") }}');
                                const html = await response.text();
                                const temp = document.createElement('div');
                                temp.innerHTML = html;
                                const newTableBody = temp.querySelector('tbody');
                                const currentTableBody = this.$el.querySelector('tbody');
                                if (newTableBody && currentTableBody) {
                                    currentTableBody.innerHTML = newTableBody.innerHTML;
                                    const pagination = temp.querySelector('.pagination');
                                    if (pagination) {
                                        const currentPagination = this.$el.querySelector('.pagination');
                                        if (currentPagination) {
                                            currentPagination.outerHTML = pagination.outerHTML;
                                        }
                                    }
                                    this.$dispatch('notify', {
                                        type: 'success',
                                        message: 'Packaging list updated successfully',
                                        timeout: 3000
                                    });
                                } else {
                                    console.warn('Failed to parse new table data');
                                    window.location.reload();
                                }
                            } catch (error) {
                                console.error('Error refreshing data:', error);
                                this.$dispatch('notify', {
                                    type: 'error',
                                    message: 'Failed to refresh data. Please try again.',
                                    timeout: 5000
                                });
                            }
                        };
                        window.addEventListener('refresh-data', this.listeners.refreshData);

                        // Listener for notifications
                        let isShowingToast = false;
                        this.listeners.notify = (event) => {
                            if (isShowingToast) return;
                            isShowingToast = true;
                            const { type, message, timeout = 5000 } = event.detail;
                            console.log('Notify event:', { type, message, timeout }); // Debug
                            const toast = document.createElement('div');
                            toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-md shadow-lg ${
                                type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                            }`;
                            const closeSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                            closeSvg.setAttribute('class', 'h-5 w-5');
                            closeSvg.setAttribute('fill', 'none');
                            closeSvg.setAttribute('viewBox', '0 0 24 24');
                            closeSvg.setAttribute('stroke', 'currentColor');
                            closeSvg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
                            const closeButton = document.createElement('button');
                            closeButton.className = 'ml-4';
                            closeButton.appendChild(closeSvg);
                            const messageSpan = document.createElement('span');
                            messageSpan.className = 'mr-2';
                            messageSpan.textContent = message;
                            const flexDiv = document.createElement('div');
                            flexDiv.className = 'flex items-center';
                            flexDiv.appendChild(messageSpan);
                            flexDiv.appendChild(closeButton);
                            toast.appendChild(flexDiv);
                            document.body.appendChild(toast);
                            setTimeout(() => {
                                if (toast.parentNode) {
                                    toast.remove();
                                }
                                isShowingToast = false;
                            }, timeout);
                            closeButton.addEventListener('click', () => {
                                if (toast.parentNode) {
                                    toast.remove();
                                }
                                isShowingToast = false;
                            });
                        };
                        window.addEventListener('notify', this.listeners.notify);
                    },

                    destroy() {
                        // Cleanup event listeners
                        window.removeEventListener('packaging-created', this.listeners.packagingCreated);
                        window.removeEventListener('refresh-data', this.listeners.refreshData);
                        window.removeEventListener('notify', this.listeners.notify);
                        console.log('packagingIndex destroyed'); // Debug
                    }
                };
            }
        </script>
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Packaging Records
                    </h3>
                </div>

                <!-- Filters -->
                <div class="bg-white px-4 py-3 border-b border-gray-200 sm:px-6">
                    <form method="GET" action="{{ route('admin.packaging.index') }}" class="space-y-4 sm:space-y-0 sm:flex sm:space-x-4">
                        <!-- Batch Filter -->
                        <div class="w-full sm:w-1/4">
                            <label for="batch_id" class="block text-sm font-medium text-gray-700">Batch</label>
                            <select id="batch_id" name="batch_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">All Batches</option>
                                @foreach(\App\Models\Batch::all() as $batch)
                                    <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                                        {{ $batch->batch_code }} (ID: {{ $batch->id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Package Type Filter -->
                        <div class="w-full sm:w-1/4">
                            <label for="package_type" class="block text-sm font-medium text-gray-700">Package Type</label>
                            <input type="text" name="package_type" id="package_type" value="{{ request('package_type') }}"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   placeholder="Filter by type">
                        </div>

                        <!-- Material Type Filter -->
                        <div class="w-full sm:w-1/4">
                            <label for="material_type" class="block text-sm font-medium text-gray-700">Material Type</label>
                            <input type="text" name="material_type" id="material_type" value="{{ request('material_type') }}"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   placeholder="Filter by material">
                        </div>

                        <!-- Quantity Filter -->
                        <div class="w-full sm:w-1/4">
                            <label for="quantity_of_units" class="block text-sm font-medium text-gray-700">Quantity</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="number" name="quantity_of_units" id="quantity_of_units" value="{{ request('quantity_of_units') }}"
                                       class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       placeholder="Exact quantity">
                                <div class="flex items-center">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-r-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Filter
                                    </button>
                                    @if(request()->hasAny(['batch_id', 'package_type', 'material_type', 'quantity_of_units']))
                                        <a href="{{ route('admin.packaging.index') }}" class="ml-2 inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Reset
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Batch
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="(pkg, index) in packages" :key="pkg.id">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="pkg.id"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <template x-if="pkg.batch">
                                        <span x-text="pkg.batch.batch_code + ' (ID: ' + pkg.batch_id + ')'"></span>
                                    </template>
                                    <template x-if="!pkg.batch">
                                        <span>N/A</span>
                                    </template>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        @can('view_packaging')
                                            <a :href="'{{ route('admin.packaging.show', '') }}/' + pkg.id" class="text-indigo-600 hover:text-indigo-900">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        @endcan

                                        @can('edit_packaging')
                                            <button @click="$store.drawer.open = true; $store.drawer.packagingData = pkg" class="text-indigo-600 hover:text-indigo-900">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                        @endcan

                                        @can('delete_packaging')
                                            <form :action="'{{ route('admin.packaging.destroy', '') }}/' + pkg.id" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this packaging record?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 focus:outline-none" title="Delete Packaging">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <!-- Empty state -->
                        <tr x-show="!packages || packages.length === 0">
                            <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No packaging records found.
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                @if($packages->hasPages())
                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6 flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0">
                        <div class="flex-1">
                            {{ $packages->links() }}
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-700">Items per page:</span>
                            <form method="GET" action="{{ route('admin.packaging.index') }}" class="inline-flex" id="perPageForm">
                                @foreach(request()->except('page', 'per_page') as $key => $value)
                                    @if(is_array($value))
                                        @foreach($value as $item)
                                            <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                        @endforeach
                                    @else
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                    @endif
                                @endforeach
                                <select name="per_page" onchange="document.getElementById('perPageForm').submit()"
                                        class="block w-full pl-3 pr-10 py-1.5 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    @foreach([5, 10, 20, 30, 50] as $perPage)
                                        <option value="{{ $perPage }}" {{ request('per_page', 15) == $perPage ? 'selected' : '' }}>
                                            {{ $perPage }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Side Drawer -->
        <div x-data="drawer()"
             x-init="init()"
             @keydown.escape.window="if(open) close()"
             class="fixed inset-0 overflow-hidden z-50"
             style="display: none;"
             x-show="open"
             x-transition:opacity.300ms>
            <div class="absolute inset-0 overflow-hidden">
                <!-- Overlay -->
                <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     x-show="open"
                     x-transition:enter="ease-in-out duration-500"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in-out duration-500"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="close()"></div>

                <!-- Drawer Panel -->
                <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                    <div class="w-screen max-w-md"
                         x-show="open"
                         x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="translate-x-full">
                        <div class="h-full flex flex-col bg-white shadow-xl">
                            @php
                                $batches = \App\Models\Batch::with('product')->latest()->get();
                                $rpcUnits = \App\Models\RpcUnit::all();
                                $users = \App\Models\User::all();
                            @endphp
                            <x-packaging.form :batches="$batches" :rpcUnits="$rpcUnits" :users="$users" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function drawer() {
                return {
                    open: false,
                    packagingData: null,

                    init() {
                        console.log('drawer initialized'); // Debug
                        if (!window.Alpine.store('drawer')) {
                            window.Alpine.store('drawer', {
                                open: false,
                                packagingData: null
                            });
                        }
                        this.$watch('open', value => {
                            if (window.Alpine.store('drawer').open !== value) {
                                window.Alpine.store('drawer').open = value;
                            }
                        });
                        this.$watch('$store.drawer.open', value => {
                            if (this.open !== value) {
                                this.open = value;
                            }
                        });
                        this.$el.addEventListener('close-drawer', () => {
                            this.close();
                        });
                    },

                    close() {
                        this.open = false;
                    }
                };
            }
        </script>

        <script>
            document.addEventListener('alpine:init', () => {
                if (!window.Alpine.store('drawer')) {
                    window.Alpine.store('drawer', {
                        open: false,
                        packagingData: null
                    });
                }
            });
        </script>

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
                    // This script can be used for any additional client-side functionality
                });
            </script>
        @endpush
</x-app-layout>
