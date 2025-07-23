@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">QR Code: {{ $batch->batch_code }}</h2>
                    <div class="flex space-x-3">
                        <a href="{{ route('batches.show', $batch) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Back to Batch
                        </a>
                        <a href="{{ route('batches.timeline', $batch) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            View Timeline
                        </a>
                    </div>
                </div>

                <div class="text-center">
                    <div class="mx-auto w-64 h-64 bg-white p-4 rounded-lg shadow-md">
                        {!! $qrCode !!}
                    </div>
                    
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900">Batch: {{ $batch->batch_code }}</h3>
                        <p class="mt-1 text-sm text-gray-500">Scan this QR code to view batch details</p>
                        
                        <div class="mt-4 flex justify-center space-x-3">
                            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-1h12v1a2 2 0 01-2 2h-1a2 2 0 01-2-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                                </svg>
                                Print
                            </button>
                            <button onclick="downloadQR()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Download
                            </button>
                        </div>
                        
                        <div class="mt-6 p-4 bg-gray-50 rounded-md">
                            <h4 class="text-sm font-medium text-gray-900">Trace Code</h4>
                            <div class="mt-2 flex rounded-md shadow-sm">
                                <div class="relative flex items-stretch flex-grow focus-within:z-10">
                                    <input type="text" id="trace-code" readonly value="{{ $batch->trace_code }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300">
                                </div>
                                <button onclick="copyToClipboard()" class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                                        <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
                                    </svg>
                                    <span>Copy</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function downloadQR() {
        // Create a temporary link element
        const link = document.createElement('a');
        link.download = 'batch-{{ $batch->batch_code }}-qrcode.png';
        
        // Get the SVG element and convert it to a data URL
        const svg = document.querySelector('.qr-code-svg');
        const svgData = new XMLSerializer().serializeToString(svg);
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();
        
        img.onload = function() {
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);
            
            // Convert canvas to PNG and trigger download
            link.href = canvas.toDataURL('image/png');
            link.click();
        };
        
        img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
    }
    
    function copyToClipboard() {
        const copyText = document.getElementById('trace-code');
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand('copy');
        
        // Show a tooltip or notification
        const button = event.currentTarget;
        const originalText = button.innerHTML;
        button.innerHTML = '<span>Copied!</span>';
        
        setTimeout(() => {
            button.innerHTML = originalText;
        }, 2000);
    }
</script>
@endpush

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .print-content, .print-content * {
            visibility: visible;
        }
        .print-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
    }
</style>

<div class="print-content hidden">
    <div class="text-center p-8">
        <h1 class="text-2xl font-bold mb-2">{{ config('app.name') }}</h1>
        <h2 class="text-xl font-semibold mb-6">Batch QR Code: {{ $batch->batch_code }}</h2>
        <div class="flex justify-center mb-4">
            {!! $qrCode !!}
        </div>
        <p class="text-sm text-gray-600">Trace Code: {{ $batch->trace_code }}</p>
        <p class="text-xs text-gray-500 mt-4">Generated on {{ now()->format('M d, Y h:i A') }}</p>
    </div>
</div>
@endsection
