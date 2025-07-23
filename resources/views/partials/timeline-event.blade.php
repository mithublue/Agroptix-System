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

<li>
    <div class="relative pb-8">
        @if (!$loop->last)
            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
        @endif

        <div class="relative flex space-x-3">
            <div>
                <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white text-lg">
                    {{ $icon }}
                </span>
            </div>
            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                <div>
                    <p class="text-sm text-gray-500">
                        <span class="font-medium text-gray-900">{{ $event->description ?? $eventType }}</span>
                        @if($event->location)
                            <span class="text-xs text-gray-400 ml-2">at {{ $event->location }}</span>
                        @endif
                    </p>
                    @if($event->details)
                        <div class="mt-1 text-sm text-gray-600 bg-gray-50 p-2 rounded-md">
                            {{ $event->details }}
                        </div>
                    @endif
                    @if($event->reference_document)
                        <div class="mt-1">
                            <a href="{{ Storage::url($event->reference_document) }}" target="_blank" class="inline-flex items-center text-xs text-indigo-600 hover:text-indigo-800">
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                View Document
                            </a>
                        </div>
                    @endif
                </div>
                <div class="text-right text-sm whitespace-nowrap text-gray-500" title="{{ $timestamp }}">
                    <time datetime="{{ $event->created_at->toIso8601String() }}">{{ $timeAgo }}</time>
                </div>
            </div>
        </div>

        @if($event->actor)
            <div class="mt-2 ml-11 text-xs text-gray-500">
                Recorded by {{ $event->actor->name ?? 'System' }}
            </div>
        @endif
    </div>
</li>
