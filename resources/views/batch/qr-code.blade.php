<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $title }}
            </h2>
            @if(isset($batch->product))
            <span class="text-sm text-gray-500">
                Product: {{ $batch->product->name }}
            </span>
            @endif
        </div>
        <div class="py-6">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 mb-6">
                    <a href="{{ route('batches.show', $batch) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Back to Batch
                    </a>
                    <a href="{{ route('batches.timeline', $batch) }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        View Timeline
                    </a>
                </div>

                <!-- QR Code Card -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <!-- QR Code Display -->
                        <div class="text-center mb-8">
                            <div class="p-4 bg-white rounded-lg shadow-md mb-6 border border-gray-200 qr-code-print">
                                {!! $qrCode !!}
                            </div>

                            <!-- Batch Info -->
                            <div class="text-center mb-8">
                                <h3 class="text-lg font-medium text-gray-900">{{ $batch->batch_code }}</h3>
                                @if($batch->production_date)
                                    <p class="mt-1 text-sm text-gray-500">
                                        Produced on {{ \Carbon\Carbon::parse($batch->production_date)->format('F j, Y') }}
                                    </p>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row gap-3 w-full max-w-md mx-auto">
                                <button onclick="downloadQR()"
                                        class="flex-1 inline-flex justify-center items-center px-4 py-3 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    Download QR Code
                                </button>

                                <button onclick="printQR()"
                                        class="flex-1 inline-flex justify-center items-center px-4 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v2h6v-2z" clip-rule="evenodd" />
                                    </svg>
                                    Print QR Code
                                </button>
                            </div>

                            <!-- Trace Code (if available) -->
                            @if(isset($batch->trace_code))
                                <div class="mt-8 w-full max-w-md mx-auto">
                                    <label for="trace-code" class="block text-sm font-medium text-gray-700 mb-1">Trace Code</label>
                                    <div class="flex rounded-md shadow-sm">
                                        <input type="text"
                                               id="trace-code"
                                               readonly
                                               value="{{ $batch->trace_code }}"
                                               class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        >
                                        <button onclick="copyToClipboard()"
                                                class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                                                <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
                                            </svg>
                                            <span class="ml-1 text-sm font-medium">Copy</span>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    @push('scripts')
    <script>
        function printQR() {
            // Get the QR code SVG element
            const qrCodeElement = document.querySelector('.qr-code-print');
            
            // Create a new window for printing
            const printWindow = window.open('', '', 'width=800,height=600');
            
            // Create the print content with proper styling
            const printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>QR Code - {{ $batch->batch_code }}</title>
                    <style>
                        @page { size: auto; margin: 0mm; }
                        body { 
                            font-family: Arial, sans-serif;
                            text-align: center;
                            padding: 20px;
                        }
                        .print-container { 
                            max-width: 100%;
                            margin: 0 auto;
                        }
                        .qr-code {
                            margin: 20px auto;
                            padding: 20px;
                            max-width: 300px;
                        }
                        .batch-info {
                            margin: 20px 0;
                        }
                        .batch-code {
                            font-size: 24px;
                            font-weight: bold;
                            margin-bottom: 10px;
                        }
                        .print-date {
                            color: #666;
                            margin-top: 20px;
                            font-size: 14px;
                        }
                    </style>
                </head>
                <body>
                    <div class="print-container">
                        <div class="batch-info">
                            <div class="batch-code">{{ $batch->batch_code }}</div>
                            @if($batch->production_date)
                                <div>Produced on {{ \Carbon\Carbon::parse($batch->production_date)->format('F j, Y') }}</div>
                            @endif
                        </div>
                        <div class="qr-code">
                            {!! $qrCode !!}
                        </div>
                        @if(isset($batch->trace_code))
                            <div class="trace-code">
                                <div>Trace Code: <strong>{{ $batch->trace_code }}</strong></div>
                            </div>
                        @endif
                        <div class="print-date">
                            Printed on {{ now()->format('F j, Y \a\t g:i A') }}
                        </div>
                    </div>
                    <script>
                        // Auto-print when the print window loads
                        window.onload = function() {
                            setTimeout(function() {
                                window.print();
                                window.onafterprint = function() {
                                    window.close();
                                };
                            }, 200);
                        };
                    <\/script>
                </body>
                </html>
            `;
            
            // Write the content to the new window
            printWindow.document.open();
            printWindow.document.write(printContent);
            printWindow.document.close();
        }
        
        function downloadQR() {
            // Get the SVG element
            const svg = document.querySelector('svg');
            if (!svg) return;
            
            // Serialize the SVG to a string
            const serializer = new XMLSerializer();
            let svgString = serializer.serializeToString(svg);
            
            // Add XML namespace if not present
            if(!svgString.match(/^<svg[^>]+xmlns="http\:\/\/www\.w3\.org\/2000\/svg"/)){
                svgString = svgString.replace(/^<svg/, '<svg xmlns="http://www.w3.org/2000/svg"');
            }
            
            // Add XML declaration
            svgString = '<?xml version="1.0" standalone="no"?>\r\n' + svgString;
            
            // Convert SVG string to data URL
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();
            
            img.onload = function() {
                canvas.width = img.width;
                canvas.height = img.height;
                ctx.drawImage(img, 0, 0);
                
                // Create download link
                const a = document.createElement('a');
                a.download = '{{ $batch->batch_code }}_qrcode.png';
                a.href = canvas.toDataURL('image/png');
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            };
            
            img.src = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svgString);
        }
        
        function copyToClipboard() {
            const copyText = document.getElementById('trace-code');
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices
            
            document.execCommand('copy');
            
            // Change button text temporarily
            const button = copyText.nextElementSibling;
            const originalText = button.innerHTML;
            button.innerHTML = '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg><span class="ml-1 text-sm font-medium">Copied!</span>';
            
            setTimeout(() => {
                button.innerHTML = originalText;
            }, 2000);
        }
        // Download QR Code as PNG
        function downloadQR() {
            const svg = document.querySelector('svg');
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const data = new XMLSerializer().serializeToString(svg);

            const img = new Image();
            const svgBlob = new Blob([data], {type: 'image/svg+xml;charset=utf-8'});
            const url = URL.createObjectURL(svgBlob);

            img.onload = function() {
                canvas.width = img.width * 2; // Higher resolution
                canvas.height = img.height * 2;
                ctx.scale(2, 2);
                ctx.drawImage(img, 0, 0);

                const pngUrl = canvas.toDataURL('image/png');
                const downloadLink = document.createElement('a');
                downloadLink.href = pngUrl;
                downloadLink.download = '{{ $batch->batch_code }}-qrcode.png';
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
                URL.revokeObjectURL(url);
            };

            img.src = url;
        }
    </script>
    <style>
        /* QR code container */
        .qr-container {
            background: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .flex-col-on-mobile {
                flex-direction: column;
            }
            .w-full-on-mobile {
                width: 100%;
            }
        }
    </style>
</x-app-layout>
