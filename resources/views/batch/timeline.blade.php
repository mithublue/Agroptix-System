<x-app-layout>
    <x-slot name="title">
        {{ $title }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Batch Timeline: {{ $batch->batch_code }}</h2>
                    <div class="flex space-x-3">
                        <a href="{{ route('batches.show', $batch) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Back to Batch
                        </a>
                        <a href="{{ route('batches.qr-code', $batch) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            View QR Code
                        </a>
                    </div>
                </div>

                <div class="flow-root">
                    <ul class="-mb-8">
                        @forelse($timeline as $event)
                            @include('partials.timeline-event', ['event' => $event, 'loop' => $loop])
                        @empty
                            <li class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No timeline events</h3>
                                <p class="mt-1 text-sm text-gray-500">This batch doesn't have any timeline events yet.</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
