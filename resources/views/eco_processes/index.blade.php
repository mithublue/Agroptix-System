<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Eco Processes for Batch #:batch', ['batch' => $batch->id]) }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('batches.show', $batch) }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Back to Batch') }}
                </a>
                @can('create_batch')
                    <a href="{{ route('batches.eco-processes.create', $batch) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Add Eco Process') }}
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if($ecoProcesses->count() > 0)
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
                                @foreach($ecoProcesses as $ecoProcess)
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
                                                <a href="{{ route('batches.eco-processes.edit', [$batch, $ecoProcess]) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ __('Edit') }}
                                                </a>
                                                <form action="{{ route('batches.eco-processes.destroy', [$batch, $ecoProcess]) }}" method="POST" class="inline" x-data="{ showConfirm: false }" @click.away="showConfirm = false">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button 
                                                        type="button"
                                                        @click="showConfirm = true"
                                                        x-show="!showConfirm"
                                                        class="text-red-600 hover:text-red-900 focus:outline-none"
                                                    >
                                                        {{ __('Delete') }}
                                                    </button>
                                                    <div x-show="showConfirm" class="inline-flex items-center space-x-2">
                                                        <span class="text-sm text-gray-600">Are you sure?</span>
                                                        <button 
                                                            type="submit" 
                                                            class="text-red-600 hover:text-red-900 font-medium focus:outline-none"
                                                        >
                                                            {{ __('Yes, delete') }}
                                                        </button>
                                                        <button 
                                                            type="button" 
                                                            @click="showConfirm = false"
                                                            class="text-gray-600 hover:text-gray-900 focus:outline-none"
                                                        >
                                                            {{ __('No, cancel') }}
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($ecoProcesses->hasPages())
                        <div class="px-6 py-4 bg-gray-50">
                            {{ $ecoProcesses->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-6 text-center text-gray-500">
                        {{ __('No eco processes found.') }}
                        @can('create_batch')
                            <a href="{{ route('batches.eco-processes.create', $batch) }}" class="text-blue-600 hover:underline">
                                {{ __('Create one now') }}
                            </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
