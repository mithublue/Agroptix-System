@php
    $eventIcons = [
        'harvest' => '🌱',
        'processing' => '⚙️',
        'quality_check' => '✅',
        'packaging' => '📦',
        'shipping' => '🚚',
        'delivery' => '🏁',
        'status_change' => '🔄',
        'note' => '📝',
        'other' => '🔍',
    ];

    $eventColors = [
        'harvest' => 'bg-green-100 text-green-800',
        'processing' => 'bg-blue-100 text-blue-800',
        'quality_check' => 'bg-purple-100 text-purple-800',
        'packaging' => 'bg-yellow-100 text-yellow-800',
        'shipping' => 'bg-indigo-100 text-indigo-800',
        'delivery' => 'bg-teal-100 text-teal-800',
        'status_change' => 'bg-gray-100 text-gray-800',
        'note' => 'bg-gray-50 text-gray-700',
        'other' => 'bg-gray-50 text-gray-700',
    ];

    $icon = $eventIcons[$event->event_type] ?? $eventIcons['other'];
    $color = $eventColors[$event->event_type] ?? $eventColors['other'];
    $eventType = str_replace('_', ' ', ucfirst($event->event_type));
    $timestamp = $event->created_at->timezone(config('app.timezone'))->format('M j, Y g:i A');
    $timeAgo = $event->created_at->diffForHumans();
@endphp

<li class="px-4 py-4 sm:px-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <span class="text-lg mr-3">{{ $icon }}</span>
            <div>
                <p class="text-sm font-medium text-gray-900 truncate">
                    {{ $event->description ?? $eventType }}
                </p>
                @if($event->location)
                    <p class="text-xs text-gray-500">
                        {{ $event->location }}
                    </p>
                @endif
            </div>
        </div>
        <div class="ml-2 flex-shrink-0 flex">
            <p class="text-sm text-gray-500 whitespace-nowrap ml-2" title="{{ $timestamp }}">
                {{ $timeAgo }}
            </p>
        </div>
    </div>
    
    @if($event->details)
        <div class="mt-2 text-sm text-gray-600">
            {{ $event->details }}
        </div>
    @endif
    
    <div class="mt-2 flex justify-between items-center">
        <div class="flex items-center">
            @if($event->reference_document)
                <a href="{{ Storage::url($event->reference_document) }}" target="_blank" class="inline-flex items-center text-xs text-indigo-600 hover:text-indigo-800">
                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    View Document
                </a>
            @endif
        </div>
        
        <div class="text-xs text-gray-500">
            @if($event->actor)
                Recorded by {{ $event->actor->name ?? 'System' }}
            @endif
        </div>
    </div>
</li>
