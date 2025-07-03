<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $batch->display_name }}
            </h2>
            @can('update', $batch)
                <a href="{{ route('batches.edit', $batch) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Edit Batch') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">
                            {{ $batch->display_name }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Created: {{ $batch->created_at->format('M d, Y') }}
                        </p>
                    </div>
                    @can('update', $batch)
                        <a href="{{ route('batches.edit', $batch) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Edit Batch') }}
                        </a>
                    @endcan
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Batch Information</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Batch Code</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $batch->batch_code ?? 'N/A' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'processing' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $statusColor = $statusColors[$batch->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                        {{ $batch->status ? ucfirst($batch->status) : 'N/A' }}
                                    </span>
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Product</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $batch->product->name ?? 'N/A' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Source</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $batch->source->type ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Harvest Details</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Harvest Time</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $batch->harvest_time ? $batch->harvest_time->format('M d, Y H:i') : 'N/A' }}
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Weight</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $batch->weight ? $batch->weight . ' kg' : 'N/A' }}
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Grade</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $batch->grade ? \App\Models\Batch::GRADES[$batch->grade] ?? $batch->grade : 'N/A' }}
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Has Defect</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $batch->has_defect ? 'Yes' : 'No' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                @if($batch->remark)
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Remarks</h3>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <p class="text-sm text-gray-700">{{ $batch->remark }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Eco Processes Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Eco Processes</h3>
                    @can('create_batch')
                        <a href="{{ route('batches.eco-processes.create', $batch) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Add Eco Process') }}
                        </a>
                    @endcan
                </div>

                @if($batch->ecoProcesses->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stage</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($batch->ecoProcesses as $ecoProcess)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $ecoProcess->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $ecoProcess->stage }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'in_progress' => 'bg-blue-100 text-blue-800',
                                                    'completed' => 'bg-green-100 text-green-800',
                                                    'failed' => 'bg-red-100 text-red-800',
                                                ];
                                                $statusColor = $statusColors[$ecoProcess->status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                                {{ str_replace('_', ' ', ucfirst($ecoProcess->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $ecoProcess->start_time?->format('Y-m-d H:i') ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $ecoProcess->end_time?->format('Y-m-d H:i') ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-4">
                                                @if(Route::has('batches.eco-processes.show'))
                                                    <a href="{{ route('batches.eco-processes.show', [$batch, $ecoProcess]) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ __('View') }}
                                                    </a>
                                                @endif
                                                @can('update', $ecoProcess)
                                                    <a href="{{ route('batches.eco-processes.edit', [$batch, $ecoProcess]) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ __('Edit') }}
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No eco processes</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by adding a new eco process.</p>
                        @can('create_batch')
                            <div class="mt-6">
                                <a href="{{ route('batches.eco-processes.create', $batch) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    New Eco Process
                                </a>
                            </div>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
        </div>
    </div>
</x-app-layout>
