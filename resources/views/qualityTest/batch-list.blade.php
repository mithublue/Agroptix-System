<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Completed Batches for Quality Testing') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ openBatch: null }">
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
                                            <button @click="openBatch = openBatch === {{ $batch->id }} ? null : {{ $batch->id }}"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-3 focus:outline-none">
                                                Quality Test
                                                <svg class="w-4 h-4 inline ml-1 transition-transform duration-200 transform"
                                                     :class="{ 'rotate-180': openBatch === {{ $batch->id }} }"
                                                     fill="none"
                                                     stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
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
                                                @if($batch->qualityTests && $batch->qualityTests->count() > 0)
                                                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
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
                                                                @foreach($batch->qualityTests as $test)
                                                                    <tr>
                                                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900">
                                                                            {{ $test->test_date->format('M d, Y') }}
                                                                        </td>
                                                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                                            {{ $test->test_parameter }}
                                                                        </td>
                                                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                                            {{ $test->test_result }} {{ $test->result_unit }}
                                                                        </td>
                                                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $test->pass_fail === 'pass' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                                                {{ ucfirst($test->pass_fail) }}
                                                                            </span>
                                                                        </td>
                                                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                                                            <a href="{{ route('quality-tests.edit', ['batch' => $batch->id, 'quality_test' => $test->id]) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <p class="text-sm text-gray-500">No quality tests found for this batch.</p>
                                                @endif
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
