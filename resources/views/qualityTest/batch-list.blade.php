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
                async loadTests(batchId) {
                    if (this.tests[batchId]) return; // Already loaded
                    
                    this.loadingTests = true;
                    try {
                        // First, check if the batch exists by making a HEAD request
                        const batchUrl = `{{ url('batches/BATCH_ID') }}`.replace('BATCH_ID', batchId);
                        const batchCheck = await fetch(batchUrl, {
                            method: 'HEAD',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            credentials: 'same-origin'
                        });

                        if (!batchCheck.ok) {
                            throw new Error('Batch not found');
                        }

                        // If batch exists, fetch the tests
                        const url = `{{ url('batches/BATCH_ID/quality-tests') }}`.replace('BATCH_ID', batchId);
                        console.log('Fetching tests from URL:', url);
                        
                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            credentials: 'same-origin'
                        });
                        
                        console.log('Response status:', response.status);
                        
                        if (!response.ok) {
                            let errorMessage = 'Failed to load tests';
                            try {
                                const errorData = await response.json();
                                console.error('Error response:', errorData);
                                errorMessage = errorData.message || errorData.error || errorMessage;
                            } catch (e) {
                                const text = await response.text();
                                console.error('Failed to parse error response:', text);
                                errorMessage = `HTTP error! status: ${response.status}`;
                            }
                            throw new Error(errorMessage);
                        }
                        
                        const data = await response.json();
                        console.log('Tests loaded successfully:', data);
                        this.$set(this.tests, batchId, data);
                    } catch (error) {
                        console.error('Error loading tests:', error);
                        alert('Failed to load quality tests: ' + error.message);
                    } finally {
                        this.loadingTests = false;
                    }
                },
                toggleBatch(batchId) {
                    if (this.openBatch === batchId) {
                        this.openBatch = null;
                    } else {
                        this.openBatch = batchId;
                        this.loadTests(batchId);
                    }
                }
            }));
        });
    </script>

    <div class="py-12" x-data="qualityTests">

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
                                                </div>
                                                <div x-show="loadingTests && openBatch === {{ $batch->id }}" class="text-center py-4">
                                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
                                                    <p class="mt-2 text-sm text-gray-500">Loading tests...</p>
                                                </div>
                                                
                                                <div x-show="!loadingTests && tests[{{ $batch->id }}] && tests[{{ $batch->id }}].length > 0" class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                                                    <table class="min-w-full divide-y divide-gray-300">
                                                        <thead class="bg-gray-100">
                                                            <tr>
                                                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Test Date</th>
                                                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Parameter</th>
                                                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Result</th>
                                                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                                                <th class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                                                    <span class="sr-only">Actions</span>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200 bg-white">
                                                            <template x-for="test in tests[{{ $batch->id }}]" :key="test.id">
                                                                <tr>
                                                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900" x-text="new Date(test.test_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })"></td>
                                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500" x-text="test.test_parameter"></td>
                                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500" x-text="test.test_result + ' ' + (test.result_unit || '')"></td>
                                                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                                                        <span :class="{
                                                                            'bg-green-100 text-green-800': test.pass_fail === 'pass',
                                                                            'bg-red-100 text-red-800': test.pass_fail !== 'pass'
                                                                        }" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" x-text="test.pass_fail.charAt(0).toUpperCase() + test.pass_fail.slice(1)">
                                                                        </span>
                                                                    </td>
                                                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                                                        <a :href="`/batches/${test.batch_id}/quality-tests/${test.id}/edit`" class="text-indigo-600 hover:text-indigo-900">Edit</a>
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
