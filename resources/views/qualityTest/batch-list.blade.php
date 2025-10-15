<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Completed Batches for Quality Testing') }}
        </h2>
    </x-slot>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('qualityTests', () => ({
                openBatch: null,
                loadingTests: false,
                tests: {},
                loadedBatches: new Set(),
                error: null,
                proceedingBatch: null,
                readyBatches: @json($readyBatchIds ?? []),

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

                areAllTestsPassed(batchId) {
                    const tests = this.tests[batchId] || [];
                    if (!tests.length) return false;
                    return tests.every(test => (test.result || '').toString().toLowerCase() === 'pass');
                },

                isBatchReady(batchId) {
                    return this.readyBatches.includes(batchId);
                },

                async proceedToNextStage(batchId) {
                    if (this.proceedingBatch === batchId) return;

                    this.proceedingBatch = batchId;
                    this.error = null;

                    try {
                        const url = `{{ url('batches') }}/${batchId}/quality-tests/ready`;
                        const response = await this.axiosInstance.post(url);

                        if (!response.data?.success) {
                            throw new Error(response.data?.message || 'Failed to mark batch ready for packaging.');
                        }

                        if (!this.readyBatches.includes(batchId)) {
                            this.readyBatches.push(batchId);
                        }

                        const Toast = Swal?.mixin ? Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1200,
                            timerProgressBar: true,
                            didOpen: toast => {
                                toast.addEventListener('mouseenter', Swal.stopTimer);
                                toast.addEventListener('mouseleave', Swal.resumeTimer);
                            }
                        }) : null;

                        if (Toast) {
                            Toast.fire({ icon: 'success', title: 'Batch is ready for packaging.' });
                        } else {
                            alert('Batch is ready for packaging.');
                        }

                        setTimeout(() => window.location.reload(), 1300);
                    } catch (error) {
                        console.error('Error marking batch ready:', error);
                        const message = error.response?.data?.message || error.message || 'Failed to proceed to next stage.';
                        this.error = message;
                        if (Swal?.fire) {
                            Swal.fire({ icon: 'error', title: 'Error', text: message });
                        } else {
                            alert(message);
                        }
                    } finally {
                        this.proceedingBatch = null;
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
            }));
        });
    </script>

    <div class="py-12" x-data="qualityTests()">

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
                                                    <a href="{{ route('quality-tests.create', ['batch' => $batch->id]) }}"
                                                       class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                        + New Test
                                                    </a>
                                                @endcan
                                                <div class="flex items-center space-x-3">
                                                    <template x-if="tests[{{ $batch->id }}] && tests[{{ $batch->id }}].length && areAllTestsPassed({{ $batch->id }}) && !isBatchReady({{ $batch->id }})">
                                                        <button @click="proceedToNextStage({{ $batch->id }})"
                                                                :disabled="proceedingBatch === {{ $batch->id }}"
                                                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                            <span x-show="proceedingBatch !== {{ $batch->id }}">Proceed to next stage</span>
                                                            <span x-show="proceedingBatch === {{ $batch->id }}" class="flex items-center">
                                                                <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                </svg>
                                                                Processing
                                                            </span>
                                                        </button>
                                                    </template>
                                                    <template x-if="tests[{{ $batch->id }}] && tests[{{ $batch->id }}].length && !areAllTestsPassed({{ $batch->id }})">
                                                        <span class="text-xs text-red-500">All tests must pass before proceeding.</span>
                                                    </template>
                                                    <template x-if="isBatchReady({{ $batch->id }})">
                                                        <span class="text-xs text-green-600 font-medium">Batch ready for packaging</span>
                                                    </template>
                                                </div>
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
                                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Result Status</th>
                                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-200 bg-white">
                                                    <template x-for="test in tests[{{ $batch->id }}]" :key="test.id">
                                                        <tr>
                                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                                <template x-if="test.parameter_tested">
        <span x-text="(() => {
            try {
                // Convert to array if it's a string
                let params = test.parameter_tested;
                if (typeof params === 'string') {
                    try {
                        params = JSON.parse(params);
                    } catch (e) {
                        // If it's not valid JSON, treat as a single value
                        params = [params];
                    }
                }

                // Ensure it's an array
                if (!Array.isArray(params)) {
                    params = [params];
                }

                // Process each parameter
                return params.map(param => {
                    if (!param) return '';
                    // Convert to string in case it's a number or other type
                    let str = String(param);
                    // Replace underscores and special characters with spaces
                    str = str.replace(/[^\w\s]/g, ' ').replace(/_/g, ' ');
                    // Capitalize first letter of each word
                    return str.toLowerCase()
                        .split(' ')
                        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                        .filter(word => word) // Remove empty strings from multiple spaces
                        .join(' ');
                }).filter(Boolean).join(', '); // Remove any empty strings and join with comma
            } catch (e) {
                console.error('Error processing parameters:', e);
                return 'Error';
            }
        })()"></span>
                                                                </template>
                                                                <template x-if="!test.parameter_tested">
                                                                    <span>N/A</span>
                                                                </template>
                                                            </td>
                                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500" x-text="test.result || 'N/A'"></td>
                                                            <td class="px-3 py-4 text-sm text-gray-500">
                                                                <template x-if="test.result_status">
                                                                    <div class="space-y-1">
                                                                        <template x-for="[key, value] in (() => {
                try {
                    let status = test.result_status;
                    if (typeof status === 'string') {
                        try {
                            status = JSON.parse(status);
                        } catch (e) {
                            // If it's not valid JSON, return as is
                            return [['Status', status]];
                        }
                    }
                    return Object.entries(status || {});
                } catch (e) {
                    console.error('Error processing status:', e);
                    return [];
                }
            })()" :key="key">
                                                                            <div class="whitespace-normal">
                    <span x-text="key
                        .replace(/_/g, ' ') // Replace underscores with spaces
                        .replace(/\b\w/g, l => l.toUpperCase()) // Capitalize first letter of each word
                        .replace(/\b(\d+)\b/g, ' $1 ') // Add space around numbers
                        .replace(/\s+/g, ' ') // Collapse multiple spaces
                        .trim()
                    "></span>:
                                                                                <span x-text="String(value)
                        .replace(/_/g, ' ') // Replace underscores with spaces
                        .replace(/\b\w/g, l => l.toUpperCase()) // Capitalize first letter of each word
                        .replace(/\b(\d+)\b/g, ' $1 ')" // Add space around numbers
                                                                                class="font-medium"
                                                                                ></span>
                                                                            </div>
                                                                        </template>
                                                                        <template x-if="!test.result_status || (() => {
                try {
                    let status = test.result_status;
                    if (typeof status === 'string') {
                        try { status = JSON.parse(status); } catch {}
                    }
                    return !status || Object.keys(status).length === 0;
                } catch { return true; }
            })()">
                                                                            <span class="text-gray-400">N/A</span>
                                                                        </template>
                                                                    </div>
                                                                </template>
                                                                <template x-if="!test.result_status">
                                                                    <span class="text-gray-400">N/A</span>
                                                                </template>
                                                            </td>
                                                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                                                @can('edit_quality_test')
                                                                    <a x-bind:href="'{{ url('batches') }}/' + test.batch_id + '/quality-tests/' + test.id + '/edit'" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                                                @endcan
                                                                {{--@can('view_quality_test')
                                                                    <a x-bind:href="'{{ url('batches') }}/' + test.batch_id + '/quality-tests/' + test.id" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                                                @endcan--}}
                                                                @can('delete_quality_test')
                                                                    <span x-data="{ confirming: false, deleting: false }">
                                                                        <button type="button"
                                                                                x-show="!confirming"
                                                                                @click="confirming = true"
                                                                                class="text-red-600 hover:text-red-900"
                                                                                :disabled="deleting">
                                                                            Delete
                                                                        </button>

                                                                        <span x-show="confirming" class="inline-flex space-x-2 items-center">
                                                                            <span class="text-sm text-gray-600">Are you sure?</span>
                                                                            <button type="button"
                                                                                    @click="
                                                                                        deleting = true;
                                                                                        axiosInstance.delete(`/batches/${test.batch_id}/quality-tests/${test.id}`)
                                                                                            .then(response => {
                                                                                                if (response.data.success) {
                                                                                                    // Remove the test from the UI
                                                                                                    const index = tests[{{ $batch->id }}].findIndex(t => t.id === test.id);
                                                                                                    if (index > -1) {
                                                                                                        tests[{{ $batch->id }}].splice(index, 1);
                                                                                                    }
                                                                                                    // Show success message
                                                                                                    const Toast = Swal.mixin({
                                                                                                        toast: true,
                                                                                                        position: 'top-end',
                                                                                                        showConfirmButton: false,
                                                                                                        timer: 1000,
                                                                                                        timerProgressBar: true,
                                                                                                        didOpen: (toast) => {
                                                                                                            toast.addEventListener('mouseenter', Swal.stopTimer);
                                                                                                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                                                                                                        }
                                                                                                    });
                                                                                                    Toast.fire({
                                                                                                        icon: 'success',
                                                                                                        title: 'Test deleted successfully!'
                                                                                                    });
                                                                                                } else {
                                                                                                    throw new Error(response.data.message || 'Failed to delete test');
                                                                                                }
                                                                                            })
                                                                                            .catch(error => {
                                                                                                console.error('Error deleting test:', error);
                                                                                                Swal.fire({
                                                                                                    icon: 'error',
                                                                                                    title: 'Error',
                                                                                                    text: error.response?.data?.message || 'An error occurred while deleting the test',
                                                                                                    confirmButtonText: 'OK',
                                                                                                    confirmButtonColor: '#3b82f6'
                                                                                                });
                                                                                            })
                                                                                            .finally(() => {
                                                                                                confirming = false;
                                                                                                deleting = false;
                                                                                            });"
                                                                                    class="text-sm text-red-600 hover:text-red-900 font-medium"
                                                                                    :disabled="deleting">
                                                                                <span x-show="!deleting">Yes, delete</span>
                                                                                <span x-show="deleting" class="inline-flex items-center">
                                                                                    <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                                    </svg>
                                                                                    Deleting...
                                                                                </span>
                                                                            </button>
                                                                            <button type="button"
                                                                                    @click="confirming = false"
                                                                                    class="text-sm text-gray-600 hover:text-gray-900"
                                                                                    :disabled="deleting">
                                                                                No, cancel
                                                                            </button>
                                                                        </span>
                                                                    </span>
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
    </div>
</x-app-layout>
