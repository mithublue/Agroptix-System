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
                                                $statuses = [
                                                    'pending' => 'Pending',
                                                    'in_progress' => 'In Progress',
                                                    'completed' => 'Completed',
                                                    'failed' => 'Failed'
                                                ];
                                                $statusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'in_progress' => 'bg-blue-100 text-blue-800',
                                                    'completed' => 'bg-green-100 text-green-800',
                                                    'failed' => 'bg-red-100 text-red-800',
                                                ];
                                                $statusColor = $statusColors[$ecoProcess->status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            @can('create_batch')
                                                <div x-data="{
                                                    status: '{{ $ecoProcess->status }}',
                                                    isUpdating: false,
                                                    statuses: {{ json_encode($statuses) }},
                                                    statusColors: {{ json_encode($statusColors) }},
                                                    updateStatus() {
                                                        this.isUpdating = true;
                                                        fetch('{{ route('batches.eco-processes.status.update', [$batch, $ecoProcess]) }}', {
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
                                                                // Update the status display
                                                                this.status = data.data.status;
                                                                // You can also update the timestamps if needed
                                                                const startTimeElement = this.$el.closest('tr').querySelector('.start-time');
                                                                const endTimeElement = this.$el.closest('tr').querySelector('.end-time');
                                                                if (startTimeElement && data.data.start_time) {
                                                                    startTimeElement.textContent = data.data.start_time;
                                                                }
                                                                if (endTimeElement && data.data.end_time) {
                                                                    endTimeElement.textContent = data.data.end_time;
                                                                }
                                                            } else {
                                                                alert('Error: ' + data.message);
                                                            }
                                                        })
                                                        .catch(error => {
                                                            console.error('Error:', error);
                                                            alert('An error occurred while updating the status.');
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
                                                    {{ str_replace('_', ' ', ucfirst($ecoProcess->status)) }}
                                                </span>
                                            @endcan
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 start-time">
                                            {{ $ecoProcess->start_time?->format('Y-m-d H:i') ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 end-time">
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
