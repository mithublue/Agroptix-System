@extends('layouts.app')

@section('title', $title)

@section('content')
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
                        @forelse($timeline as $index => $event)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            @switch($event['event_type'])
                                                @case('harvest')
                                                    <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 001.5 1.937v1.626c0 .17.013.34.039.506l1.524 5.062a1 1 0 00.497.72 11.98 11.98 0 01-5.56-1.475.75.75 0 10-.682 1.34 13.5 13.5 0 006.25 1.586 13.5 13.5 0 006.25-1.586.75.75 0 00-.682-1.34 11.98 11.98 0 01-5.56 1.475l1.5-5.062a2 2 0 00.04-.506V9.937A2 2 0 0016 8v-.5A1.5 1.5 0 0014.5 6c-.525 0-.987.27-1.256.32a6.012 6.012 0 01-1.912-2.705c.145-.03.293-.05.443-.06a.75.75 0 00.725-.75V3a.75.75 0 00-.75-.75h-1.5a.75.75 0 00-.75.75v.19c0 .414.336.75.75.75.15.01.298.03.443.06a6.012 6.012 0 01-4.488 0c.145-.03.293-.05.443-.06a.75.75 0 00.725-.75V3a.75.75 0 00-.75-.75h-1.5a.75.75 0 00-.75.75v.19c0 .414.336.75.75.75.15.01.298.03.443.06a6.012 6.012 0 01-1.912 2.705c-.03-.145-.05-.293-.06-.443a.75.75 0 00-.75-.725h-1.5a.75.75 0 00-.75.75v1.5c0 .414.336.75.75.75.15 0 .298.01.443.03z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                    @break
                                                @case('processing')
                                                    <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M2.25 13.5a8.23 8.23 0 007.5-4.317 8.25 8.25 0 007.5 4.317 8.25 8.25 0 01-15-7.5 8.25 8.25 0 0115-7.5 8.25 8.25 0 01-7.5 4.317 8.25 8.25 0 01-7.5-4.317 8.25 8.25 0 00-7.5 4.317 8.25 8.25 0 007.5 12.183 8.25 8.25 0 007.5-4.317 8.25 8.25 0 017.5 4.317 8.25 8.25 0 01-15 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                    @break
                                                @case('packaging')
                                                    <span class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M10.75 10.818v2.614A3.13 3.13 0 0011.888 13c.482-.315.612-.648.612-.875 0-.227-.13-.56-.612-.875a3.13 3.13 0 01-1.138-.732zM8.33 8.62c.053.055.115.11.184.164.208.16.46.284.736.363V6.603a2.45 2.45 0 01-.92.243 2.27 2.27 0 01-.9-.174 1.5 1.5 0 01.9-2.833z" />
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-6a.75.75 0 01.75.75v.316a3.78 3.78 0 011.653.713c.426.33.744.74.925 1.2a.75.75 0 01-1.395.55 1.35 1.35 0 00-.447-.563 2.187 2.187 0 00-.736-.363V9.3c.698.093 1.383.32 1.959.696.787.514 1.29 1.27 1.29 2.13 0 .86-.503 1.616-1.29 2.13-.576.377-1.261.603-1.96.696v.299a.75.75 0 11-1.5 0v-.3c-.697-.092-1.382-.318-1.958-.695-.482-.315-.857-.717-1.078-1.188a.75.75 0 111.359-.635c.08.173.187.32.316.433.15.12.31.216.49.291.18.075.37.114.558.122a.75.75 0 01.75.75v.316c-.697.092-1.383.318-1.958.695-.482.315-.857.717-1.078 1.188a.75.75 0 11-1.359-.635c.08-.173.187-.32.316-.433.15-.12.31-.216.49-.291.18-.075.37-.114.558-.122a.75.75 0 01.75.75v.316c0 .23-.11.449-.29.583-.18.133-.4.21-.63.21a2.19 2.19 0 01-1.487-.66 3.75 3.75 0 01-.693-1.48c-.12-.48-.12-.986 0-1.466a3.75 3.75 0 01.693-1.48 3.75 3.75 0 011.48-.693c.48-.12.986-.12 1.466 0a3.75 3.75 0 011.48.693c.33.33.58.73.725 1.165a.75.75 0 11-1.4.524c-.09-.24-.22-.455-.38-.633a2.25 2.25 0 00-.9-.563 2.25 2.25 0 00-1.1 0 2.25 2.25 0 00-.9.563 2.25 2.25 0 000 3.183c.24.24.51.42.8.54.29.12.6.18.91.18.31 0 .62-.06.91-.18.29-.12.56-.3.8-.54a2.25 2.25 0 00.38-.633.75.75 0 01.8-.447 3.75 3.75 0 00.725-1.165 3.75 3.75 0 00.693-1.48c.12-.48.12-.986 0-1.466a3.75 3.75 0 00-.693-1.48 3.75 3.75 0 00-1.48-.693c-.48-.12-.986-.12-1.466 0a3.75 3.75 0 00-1.48.693 3.75 3.75 0 00-.693 1.48c-.12.48-.12.986 0 1.466.12.48.32.93.6 1.33.15.2.31.39.49.56.1.1.2.2.31.29.05.04.1.08.16.11.06.03.12.06.19.09.07.03.14.05.22.07.07.02.15.03.23.04.04 0 .07.01.11.01z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                    @break
                                                @case('shipping')
                                                    <span class="h-8 w-8 rounded-full bg-purple-500 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                                                        </svg>
                                                    </span>
                                                    @break
                                                @case('delivery')
                                                    <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="h-8 w-8 rounded-full bg-gray-500 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                            @endswitch
                                        </div>
                                        <div class="flex min-w-0 flex-1 justify-between pt-1.5">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    {{ $event['description'] }}
                                                    <span class="font-medium text-gray-900">{{ $event['location'] ?? 'Unknown Location' }}</span>
                                                </p>
                                            </div>
                                            <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                                <time datetime="{{ $event['timestamp'] }}">{{ \Carbon\Carbon::parse($event['timestamp'])->diffForHumans() }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No timeline events</h3>
                                <p class="mt-1 text-sm text-gray-500">There are no events to display for this batch yet.</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
