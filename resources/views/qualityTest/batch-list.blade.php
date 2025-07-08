<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Completed Batches for Quality Testing') }}
        </h2>
    </x-slot>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('qualityTests', () => ({
                // Modal state
                showModal: false,
                isSubmitting: false,
                errors: {},
                formData: {
                    batch_id: '',
                    parameters_tested: [''],
                    final_pass_fail: '',
                    test_certificate: '',
                    remarks: ''
                },

                // Existing state
                openBatch: null,
                loadingTests: false,
                tests: {},
                loadedBatches: new Set(),
                error: null,
                currentBatch: null,
                batches: {!! json_encode(collect($batches->items())->map(function($batch) {
                    return [
                        'id' => $batch->id,
                        'batch_code' => $batch->batch_code ?? 'N/A',
                        'product_name' => $batch->relationLoaded('product') && $batch->product ? $batch->product->name : 'No Product',
                        'created_at' => $batch->created_at ? $batch->created_at->toDateTimeString() : now()->toDateTimeString(),
                        'updated_at' => $batch->updated_at ? $batch->updated_at->toDateTimeString() : now()->toDateTimeString()
                    ];
                })) !!},

                // Initialize Axios instance with default config
                axiosInstance: axios.create({
                    baseURL: '{{ url('/') }}',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                }),

                async loadTests(batchId) {
                    console.log('Loading tests for batch:', batchId);

                    // Don't reload if already loaded
                    if (this.loadedBatches.has(batchId)) {
                        console.log('Tests already loaded for batch', batchId);
                        return;
                    }

                    this.loadingTests = true;
                    this.error = null;

                    try {
                        const testsUrl = `{{ url('batches') }}/${batchId}/qualitytests`;
                        console.log('Fetching tests from URL:', testsUrl);

                        const response = await this.axiosInstance.get(testsUrl);
                        console.log('Axios response:', response);

                        if (!response.data) {
                            throw new Error('Empty response from server');
                        }

                        if (response.data.success === false) {
                            throw new Error(response.data.message || 'Failed to load tests');
                        }

                        if (!Array.isArray(response.data.data)) {
                            console.warn('Expected data to be an array, got:', typeof response.data.data);
                            this.tests = { ...this.tests, [batchId]: [] };
                        } else {
                            console.log(`Loaded ${response.data.data.length} tests for batch ${batchId}`);
                            this.tests = { ...this.tests, [batchId]: response.data.data };
                            this.loadedBatches = new Set([...this.loadedBatches, batchId]);
                        }
                    } catch (error) {
                        console.error('Error in loadTests:', {
                            error: error.message,
                            stack: error.stack,
                            batchId: batchId
                        });

                        this.error = 'Failed to load quality tests. ' + (error.message || 'Please try again.');
                        this.tests = { ...this.tests, [batchId]: [] };

                        // Show error in UI
                        const errorMessage = `Error loading tests: ${error.message}`;
                        alert(errorMessage);
                    } finally {
                        console.log('Finished loading tests for batch', batchId);
                        this.loadingTests = false;
                    }
                },

                // Open the new test modal for a specific batch
                openNewTestModal(batchId) {
                    try {
                        // Find the batch data from the batches array
                        const batch = this.batches.find(b => b.id == batchId);
                        if (batch) {
                            this.currentBatch = batch;

                            // Ensure the store is initialized
                            if (!window.Alpine.store('modal')) {
                                window.Alpine.store('modal', { batch: null });
                            }

                            // Update the Alpine store with a fresh copy of the batch data
                            const batchCopy = JSON.parse(JSON.stringify(batch));
                            window.Alpine.store('modal').batch = batchCopy;

                            // Show the modal
                            this.showModal = true;

                            // Ensure the form has time to initialize with the batch data
                            this.$nextTick(() => {
                                // Dispatch an event with the batch data
                                this.$dispatch('batch-selected', { batch: batchCopy });
                            });
                        } else {
                            console.error('Batch not found:', batchId);
                            this.error = 'Batch not found. Please try again.';
                        }
                    } catch (error) {
                        console.error('Error in openNewTestModal:', error);
                        this.error = 'Failed to open test form. Please try again.';
                    }
                },

                // Helper method to handle errors consistently
                handleError(error, context = '') {
                    let errorMessage = 'An error occurred';

                    if (error.response) {
                        // The request was made and the server responded with a status code
                        // that falls out of the range of 2xx
                        console.error('Response error:', {
                            status: error.response.status,
                            statusText: error.response.statusText,
                            data: error.response.data,
                            headers: error.response.headers,
                            context
                        });

                        errorMessage = error.response.data?.message ||
                                     error.response.statusText ||
                                     `HTTP error ${error.response.status}`;
                    } else if (error.request) {
                        // The request was made but no response was received
                        console.error('No response received:', error.request, context);
                        errorMessage = 'No response from server. Please check your connection.';
                    } else {
                        // Something happened in setting up the request that triggered an Error
                        console.error('Request setup error:', error.message, context);
                        errorMessage = error.message || 'Failed to send request';
                    }

                    this.error = errorMessage;
                    throw new Error(errorMessage);
                },
                async toggleBatch(batchId) {
                    if (this.openBatch === batchId) {
                        // Collapse if clicking the same batch
                        this.openBatch = null;
                    } else {
                        // Expand and load tests
                        this.openBatch = batchId;
                        await this.loadTests(batchId);
                    }
                },

                // Format date for display
                formatDate(dateString) {
                    if (!dateString) return 'N/A';
                    const date = new Date(dateString);
                    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
                },

                // Modal methods
                addParameter() {
                    this.formData.parameters_tested.push('');
                },

                removeParameter(index) {
                    this.formData.parameters_tested.splice(index, 1);
                },

                // Handle opening the new test modal
                openNewTestModal(batchId) {
                    // Dispatch an event to set the batch in the modal
                    this.$dispatch('set-batch', { batch: { id: batchId } });
                    this.showModal = true;
                },

                // Handle test creation from the modal
                handleTestCreated(data) {
                    if (this.openBatch) {
                        this.loadTests(this.openBatch);
                    }
                    // Show success message
                    alert('Quality test created successfully!');
                },
            }));
        });
    </script>

    <div class="py-12" x-data="qualityTests">
        <!-- New Test Button - Removed since we'll use the one in the batch row -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <p class="text-sm text-gray-500">Click on a batch row to view tests and create new ones.</p>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($batches as $batch)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $batch->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                            {{ $batch->batch_code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $batch->product->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($batch->source)
                                                Source#{{ is_object($batch->source) ? $batch->source->id : $batch->source }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="toggleBatch({{ $batch->id }})"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-3 focus:outline-none"
                                                    :disabled="loadingTests">
                                                <span x-text="openBatch === {{ $batch->id }} ? 'Hide Tests' : 'Quality Test'"></span>
                                                <svg x-show="!loadingTests" class="w-4 h-4 inline ml-1 transition-transform duration-200 transform"
                                                     :class="{ 'rotate-180': openBatch === {{ $batch->id }} }"
                                                     fill="none"
                                                     stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                                <svg x-show="loadingTests && openBatch === {{ $batch->id }}" class="animate-spin -ml-1 mr-2 h-4 w-4 text-indigo-600 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr x-show="openBatch === {{ $batch->id }}" x-collapse class="bg-gray-50">
                                        <td colspan="5" class="px-6 py-4">
                                            <div class="ml-8">
                                                <div class="flex justify-between items-center mb-2">
                                                    <h3 class="text-sm font-medium text-gray-900">Quality Tests</h3>
                                                    @can('create_quality_test')
                                                        <button @click="openNewTestModal({{ $batch->id }})"
                                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                        New Test
                                                    </button>
                                                    @endcan
                                                </div>
                                                <div x-show="loadingTests && openBatch === {{ $batch->id }}" class="text-center py-4">
                                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
                                                    <p class="mt-2 text-sm text-gray-500">Loading tests...</p>
                                                </div>

                                                <div x-show="!loadingTests && tests[{{ $batch->id }}] && tests[{{ $batch->id }}].length > 0" class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                                                    <table class="min-w-full divide-y divide-gray-300">
                                                        <thead class="bg-gray-100">
                                                            <tr>
                                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Parameter Tested</th>
                                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Result</th>
                                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200 bg-white">
                                                            <template x-for="test in tests[{{ $batch->id }}]" :key="test.id">
                                                                <tr>
                                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500" x-text="test.parameter_tested || 'N/A'"></td>
                                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500" x-text="test.result || 'N/A'"></td>
                                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                                        <span x-bind:class="{'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true, 'bg-green-100 text-green-800': test.result_status === 'pass', 'bg-red-100 text-red-800': test.result_status !== 'pass'}" x-text="test.result_status === 'pass' ? 'Passed' : test.result_status === 'fail' ? 'Failed' : test.result_status || 'N/A'">
                                                                        </span>
                                                                    </td>
                                                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                                                        <a x-bind:href="'{{ url('batches') }}/' + test.batch_id + '/quality-tests/' + test.id + '/edit'" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                                                        <a x-bind:href="'{{ url('batches') }}/' + test.batch_id + '/quality-tests/' + test.id" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                                                        @can('delete_quality_test')
                                                                        <button type="button"
                                                                                @click="if(confirm('Are you sure you want to delete this test?')) {
                                                                                    axiosInstance.delete(`/batches/${test.batch_id}/quality-tests/${test.id}`)
                                                                                        .then(response => {
                                                                                            if (response.data.success) {
                                                                                                // Remove the test from the UI
                                                                                                const index = tests[{{ $batch->id }}].findIndex(t => t.id === test.id);
                                                                                                if (index > -1) {
                                                                                                    tests[{{ $batch->id }}].splice(index, 1);
                                                                                                }
                                                                                            } else {
                                                                                                alert(response.data.message || 'Failed to delete test');
                                                                                            }
                                                                                        })
                                                                                        .catch(error => {
                                                                                            console.error('Error deleting test:', error);
                                                                                            alert(error.response?.data?.message || 'An error occurred while deleting the test');
                                                                                        });
                                                                                }"
                                                                                class="text-red-600 hover:text-red-900">
                                                                            Delete
                                                                        </button>
                                                                        @endcan
                                                                    </td>
                                                                </tr>
                                                            </template>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div x-show="!loadingTests && (!tests[{{ $batch->id }}] || tests[{{ $batch->id }}].length === 0)" class="text-sm text-gray-500">
                                                    No quality tests found for this batch.
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No completed batches found for quality testing.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($batches->hasPages())
                        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                            {{ $batches->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Initialize Alpine.js store for modal state -->
        <div x-data="{
            modal: {
                batch: null
            }
        }" x-init="
            // Initialize the store if it doesn't exist
            if (!window.Alpine.store('modal')) {
                window.Alpine.store('modal', { batch: null });
            }

            // Listen for batch selection
            $watch('$store.modal.batch', (batch) => {
                if (batch) {
                    this.modal.batch = batch;
                }
            });

            // Listen for the modal to open
            $watch('showModal', (isOpen) => {
                if (isOpen && this.currentBatch) {
                    window.Alpine.store('modal').batch = this.currentBatch;
                }
            });
        ">
            <!-- Modal -->
            <div x-show="showModal"
                 x-cloak
                 class="fixed inset-0 z-50 overflow-y-auto"
                 aria-labelledby="modal-title"
                 role="dialog"
                 aria-modal="true"
                 x-data="{}">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Background overlay -->
                    <div x-show="showModal"
                         @click="showModal = false"
                         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-from="opacity-0"
                         x-transition:enter-to="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-from="opacity-100"
                         x-transition:leave-to="opacity-0"
                         aria-hidden="true"></div>

                    <!-- Modal panel -->
                    <div x-show="showModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-to="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-from="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6">

                        <!-- Modal header -->
                        <div class="flex justify-between items-center pb-3">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Create New Quality Test
                                <template x-if="$store.modal.batch">
                                    <span class="text-sm text-gray-500">for Batch #<span x-text="$store.modal.batch.batch_code || $store.modal.batch.id"></span></span>
                    <!-- Modal content -->
                    <div class="mt-5">
                        <template x-if="currentBatch">
                            <div>
                                <div class="mb-4">
                                    <h4 class="text-lg font-medium text-gray-900">Create Quality Test for Batch <span x-text="currentBatch.batch_code"></span></h4>
                                    <p class="text-sm text-gray-500">Product: <span x-text="currentBatch.product_name"></span></p>
                                </div>
                                <template x-if="currentBatch">
                                    <div>
                                        <x-quality-test-form
                                            x-on:test-created="() => {
                                                showModal = false;
                                                batch = currentBatch;
                                                if (openBatch) {
                                                    loadTests(openBatch);
                                                }
                                            }"
                                            x-on:close-modal="showModal = false" />
                                    </div>
                                </template>
                            </div>
                        </template>
                        <div x-show="!currentBatch" class="text-center py-4">
                            <p class="text-gray-500">Loading batch information...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
