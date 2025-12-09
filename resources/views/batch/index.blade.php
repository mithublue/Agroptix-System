<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Batch List') }}
            </h2>
            <div class="flex space-x-2">
                @can('create_batch')
                    <a href="{{ route('batches.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Add New Batch') }}
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
                <form method="GET" action="{{ route('batches.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <!-- Search -->
                    <div>
                        <label for="q" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" id="q" name="q"
                               value="{{ request('q') }}"
                               class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                               placeholder="Search batch code, trace code, status, product/source, or exact weight/id">
                    </div>
                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status" name="status" data-tom-select data-placeholder="All Statuses"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Source Filter -->
                    <div>
                        <label for="source_id" class="block text-sm font-medium text-gray-700 mb-1">Source</label>
                        <select id="source_id" name="source_id" data-tom-select data-placeholder="All Sources"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Sources</option>
                            @foreach($sources as $id => $name)
                                <option value="{{ $id }}" {{ request('source_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Product Filter -->
                    <div>
                        <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                        <select id="product_id" name="product_id" data-tom-select data-placeholder="All Products"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Products</option>
                            @foreach($products as $id => $name)
                                <option value="{{ $id }}" {{ request('product_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="flex flex-col justify-end">
                        <div class="flex space-x-2">
                            <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 h-10">
                                Apply
                            </button>
                            <a href="{{ route('batches.index') }}" class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 h-10">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if($batches->count() > 0)
                    @can('delete_batch')
                        <form id="bulk-delete-batches" method="POST" action="{{ route('batches.bulk-destroy') }}" class="hidden">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="redirect" value="{{ url()->full() }}">
                        </form>
                    @endcan
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    @can('delete_batch')
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" id="select-all-batches" class="rounded border-gray-300">
                                    </th>
                                    @endcan
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harvest Time</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eco Processes</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($batches as $batch)
                                    <tr>
                                        @can('delete_batch')
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <input type="checkbox" name="ids[]" value="{{ $batch->id }}" class="batch-checkbox rounded border-gray-300" data-batch-id="{{ $batch->id }}">
                                        </td>
                                        @endcan
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $batch->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $batch->harvest_time ? \Carbon\Carbon::parse($batch->harvest_time)->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statuses = [
                                                    'pending' => 'Pending',
                                                    'processing' => 'Processing',
                                                    'completed' => 'Completed',
                                                    'cancelled' => 'Cancelled'
                                                ];
                                                $statusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'processing' => 'bg-blue-100 text-blue-800',
                                                    'completed' => 'bg-green-100 text-green-800',
                                                    'cancelled' => 'bg-red-100 text-red-800',
                                                ];
                                                $statusColor = $statusColors[$batch->status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            @can('manage_batch')
                                                <div x-data="{
                                                    status: '{{ $batch->status }}',
                                                    isUpdating: false,
                                                    statuses: {{ json_encode($statuses) }},
                                                    statusColors: {{ json_encode($statusColors) }},
                                                    updateStatus() {
                                                        this.isUpdating = true;
                                                        fetch('{{ route('batches.status.update', $batch) }}', {
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
                                                                this.status = data.data.status;
                                                                this.$dispatch('notify', {
                                                                    type: 'success',
                                                                    message: data.message
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
                                                                message: 'An error occurred while updating the status.'
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
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                                    {{ $statuses[$batch->status] ?? ucfirst($batch->status) }}
                                                </span>
                                            @endcan
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($batch->source)
                                                {{ $batch->source->name ?? 'Source #' . $batch->source->id }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $batch->product->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            @php
                                                $ecoStatusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'in_progress' => 'bg-blue-100 text-blue-800',
                                                    'completed' => 'bg-green-100 text-green-800',
                                                    'failed' => 'bg-red-100 text-red-800',
                                                ];
                                            @endphp
                                            @if($batch->ecoProcesses->isEmpty())
                                                <span class="text-xs text-gray-400">None</span>
                                            @else
                                                <div class="flex flex-col space-y-1">
                                                    @foreach($batch->ecoProcesses as $ecoProcess)
                                                        @php
                                                            $statusColor = $ecoStatusColors[$ecoProcess->status] ?? 'bg-gray-100 text-gray-800';
                                                            $formattedStatus = str_replace('_', ' ', ucfirst($ecoProcess->status));
                                                            $formattedStage = str_replace('_', ' ', $ecoProcess->stage ?? 'Stage');
                                                        @endphp
                                                        <div class="flex items-center space-x-2">
                                                            <span class="text-xs text-gray-500">{{ $formattedStage }}</span>
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                                                {{ $formattedStatus }}
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                @can('create_batch')
                                                    <a href="{{ route('batches.eco-processes.index', $batch) }}" class="text-yellow-600 hover:text-blue-900">{{ __('Eco Process') }}</a>
                                                @endcan
                                                @can('view_batch')
                                                    <a href="{{ route('batches.show', $batch) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                                @endcan

                                                @can('edit_batch')
                                                    <a href="{{ route('batches.edit', $batch) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                @endcan

                                                @can('delete_batch')
                                                    <div x-data="{ showDeleteConfirm: false }" class="relative">
                                                        <button type="button" @click="showDeleteConfirm = true"
                                                                class="text-red-600 hover:text-red-900 focus:outline-none">
                                                            Delete
                                                        </button>

                                                        <!-- Delete Confirmation Dropdown -->
                                                        <div x-show="showDeleteConfirm"
                                                             @click.away="showDeleteConfirm = false"
                                                             x-transition:enter="transition ease-out duration-100"
                                                             x-transition:enter-start="transform opacity-0 scale-95"
                                                             x-transition:enter-end="transform opacity-100 scale-100"
                                                             x-transition:leave="transition ease-in duration-75"
                                                             x-transition:leave-start="transform opacity-100 scale-100"
                                                             x-transition:leave-end="transform opacity-0 scale-95"
                                                             class="absolute right-0 z-10 mt-1 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                                                             style="display: none;">
                                                            <div class="py-1">
                                                                <p class="px-4 py-2 text-sm text-gray-700">
                                                                    Delete this batch?
                                                                </p>
                                                                <div class="border-t border-gray-100"></div>
                                                                <div class="flex justify-end px-4 py-2 space-x-2">
                                                                    <button type="button" @click="showDeleteConfirm = false"
                                                                            class="text-sm text-gray-700 hover:bg-gray-100 px-2 py-1 rounded">
                                                                        Cancel
                                                                    </button>
                                                                    <button type="button"
                                                                            @click="document.getElementById('delete-batch-form-{{ $batch->id }}').submit()"
                                                                            class="text-sm text-white bg-red-600 hover:bg-red-700 px-2 py-1 rounded">
                                                                        Delete
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <form id="delete-batch-form-{{ $batch->id }}" action="{{ route('batches.destroy', $batch) }}" method="POST" class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @can('delete_batch')
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div>
                            <span id="selected-count" class="text-sm text-gray-600">0 selected</span>
                        </div>
                        <div>
                            <button type="button" id="delete-selected-btn" disabled class="px-4 py-2 bg-red-600 text-white text-sm rounded disabled:opacity-50 hover:bg-red-700">Delete Selected</button>
                        </div>
                    </div>
                    @endcan

                    @if($batches->hasPages())
                        <div class="px-6 py-4 bg-gray-50">
                            {{ $batches->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-6 text-center text-gray-500">
                        No batches found.
                        @can('create_batch')
                            <a href="{{ route('batches.create') }}" class="text-blue-600 hover:underline">Create one now</a>.
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>  
    alert('qwewq');
    (function() {
        'use strict';
        
        // Wait for DOM to be fully loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
        function init() {
            const selectAll = document.getElementById('select-all-batches');
            const deleteBtn = document.getElementById('delete-selected-btn');
            const countSpan = document.getElementById('selected-count');
            const bulkForm = document.getElementById('bulk-delete-batches');
            
            if (!selectAll || !deleteBtn) {
                console.log('Batch checkboxes not found - bulk delete may be disabled');
                return;
            }
            
            // Get all batch checkboxes
            function getCheckboxes() {
                return document.querySelectorAll('.batch-checkbox');
            }
            
            // Update UI based on selection
            function updateUI() {
                const checkboxes = getCheckboxes();
                const checked = Array.from(checkboxes).filter(cb => cb.checked);
                const count = checked.length;
                const total = checkboxes.length;
                
                // Update count display
                if (countSpan) {
                    countSpan.textContent = count + ' selected';
                }
                
                // Enable/disable delete button
                deleteBtn.disabled = count === 0;
                
                // Update select-all checkbox state
                if (total > 0) {
                    selectAll.checked = count === total;
                    selectAll.indeterminate = count > 0 && count < total;
                }
            }
            
            // Handle select all click
            selectAll.addEventListener('change', function() {
                const checkboxes = getCheckboxes();
                const isChecked = this.checked;
                
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = isChecked;
                });
                
                updateUI();
            });
            
            // Handle individual checkbox changes
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('batch-checkbox')) {
                    updateUI();
                }
            });
            
            // Handle delete button click
            deleteBtn.addEventListener('click', function() {
                const checkboxes = Array.from(getCheckboxes()).filter(cb => cb.checked);
                
                if (checkboxes.length === 0) {
                    return;
                }
                
                if (!confirm('Are you sure you want to delete ' + checkboxes.length + ' batch(es)?')) {
                    return;
                }
                
                // Clear existing hidden inputs
                const existingInputs = bulkForm.querySelectorAll('input[name="ids[]"]');
                existingInputs.forEach(input => input.remove());
                
                // Add selected IDs to form
                checkboxes.forEach(function(checkbox) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = checkbox.dataset.batchId;
                    bulkForm.appendChild(input);
                });
                
                // Submit the form
                bulkForm.submit();
            });
            
            // Initial update
            updateUI();
            
            // Initialize TomSelect if available
            if (window.initTomSelectCollection) {
                window.initTomSelectCollection(document.querySelectorAll('[data-tom-select]'));
            }
        }
    })();
    </script>
    @endpush
</x-app-layout>
