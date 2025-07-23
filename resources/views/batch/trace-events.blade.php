@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Trace Events: {{ $batch->batch_code }}</h2>
                    <div class="flex space-x-3">
                        <a href="{{ route('batches.show', $batch) }}" class="btn btn-secondary">
                            Back to Batch
                        </a>
                        <a href="{{ route('batches.timeline', $batch) }}" class="btn btn-primary">
                            View Timeline
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Event Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actor
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Timestamp
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Details
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($events as $event)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @php
                                                $icon = [
                                                    'harvest' => '🌱',
                                                    'processing' => '⚙️',
                                                    'packaging' => '📦',
                                                    'shipping' => '🚚',
                                                    'delivery' => '✅',
                                                    'inspection' => '🔍',
                                                    'quality_check' => '🔍',
                                                    'default' => '📝'
                                                ][$event['event_type'] ?? 'default'];
                                            @endphp
                                            <span class="text-xl mr-2">{{ $icon }}</span>
                                            <span class="capitalize">{{ str_replace('_', ' ', $event['event_type']) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $event['actor_name'] ?? 'System' }}</div>
                                        <div class="text-sm text-gray-500">{{ $event['actor_role'] ?? 'System' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($event['timestamp'])->format('M d, Y h:i A') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($event['timestamp'])->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $event['description'] ?? 'No details' }}</div>
                                        @if(isset($event['location']))
                                            <div class="text-xs text-gray-500">{{ $event['location'] }}</div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No trace events found for this batch.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($events, 'links'))
                    <div class="mt-4">
                        {{ $events->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .btn {
        @apply inline-flex items-center px-4 py-2 border text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2;
    }
    .btn-primary {
        @apply border-transparent text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500;
    }
    .btn-secondary {
        @apply border-gray-300 text-gray-700 bg-white hover:bg-gray-50 focus:ring-indigo-500;
    }
</style>
@endsection
