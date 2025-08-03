<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Batch History - {{ $data['batch']['trace_code'] }}</title>
    <style>
        @page {
            margin: 20px 25px;
            font-family: 'DejaVu Sans', sans-serif;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #2c3e50;
            font-size: 18pt;
            margin: 0 0 5px 0;
        }
        .header .subtitle {
            color: #7f8c8d;
            font-size: 12pt;
            margin: 0;
        }
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .section-header {
            background-color: #f8f9fa;
            color: #2c3e50;
            padding: 5px 10px;
            font-weight: bold;
            border-left: 4px solid #3498db;
            margin-bottom: 10px;
            font-size: 11pt;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }
        .info-item {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #7f8c8d;
            font-size: 9pt;
            margin-bottom: 2px;
        }
        .info-value {
            color: #2c3e50;
            word-break: break-word;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9pt;
            page-break-inside: auto;
        }
        th {
            background-color: #f8f9fa;
            color: #2c3e50;
            text-align: left;
            padding: 8px;
            border: 1px solid #dee2e6;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border: 1px solid #dee2e6;
            vertical-align: top;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
            font-size: 8pt;
            color: #7f8c8d;
        }
        .page-break {
            page-break-after: always;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .badge-primary {
            background-color: #cce5ff;
            color: #004085;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-muted {
            color: #6c757d;
        }
        .mt-3 {
            margin-top: 1rem;
        }
        .mb-3 {
            margin-bottom: 1rem;
        }
        .p-3 {
            padding: 1rem;
        }
        .bg-light {
            background-color: #f8f9fa;
        }
        .border {
            border: 1px solid #dee2e6;
        }
        .rounded {
            border-radius: 0.25rem;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Batch History Report</h1>
        <p class="subtitle">{{ $data['batch']['name'] ?? 'Untitled Batch' }}</p>
        <p class="text-muted">Trace Code: {{ $data['batch']['trace_code'] }} | Generated on: {{ \Carbon\Carbon::parse($data['exported_at'])->format('M d, Y \a\t h:i A') }}</p>
    </div>

    <!-- Batch Information -->
    <div class="section">
        <div class="section-header">Batch Information</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Batch Name</div>
                <div class="info-value">{{ $data['batch']['name'] ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Status</div>
                <div class="info-value">
                    @php
                        $statusClass = [
                            'created' => 'badge-info',
                            'harvested' => 'badge-success',
                            'processing' => 'badge-warning',
                            'qc_pending' => 'badge-warning',
                            'qc_approved' => 'badge-success',
                            'qc_rejected' => 'badge-danger',
                            'packaged' => 'badge-primary',
                            'shipped' => 'badge-primary',
                            'delivered' => 'badge-success',
                            'returned' => 'badge-danger',
                            'destroyed' => 'badge-danger',
                        ][$data['batch']['status']] ?? 'badge-secondary';
                    @endphp
                    <span class="badge {{ $statusClass }}">
                        {{ $data['batch']['status_label'] ?? ucfirst(str_replace('_', ' ', $data['batch']['status'])) }}
                    </span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Created</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($data['batch']['created_at'])->format('M d, Y') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Last Updated</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($data['batch']['updated_at'])->format('M d, Y') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Quantity</div>
                <div class="info-value">
                    {{ number_format($data['batch']['quantity'], 2) }} {{ $data['batch']['unit'] ?? '' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Location</div>
                <div class="info-value">{{ $data['batch']['location'] ?? 'N/A' }}</div>
            </div>
            @if(!empty($data['batch']['harvest_date']))
            <div class="info-item">
                <div class="info-label">Harvest Date</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($data['batch']['harvest_date'])->format('M d, Y') }}</div>
            </div>
            @endif
            @if(!empty($data['batch']['expiry_date']))
            <div class="info-item">
                <div class="info-label">Expiry Date</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($data['batch']['expiry_date'])->format('M d, Y') }}</div>
            </div>
            @endif
        </div>
        
        @if(!empty($data['batch']['description']))
        <div class="info-item mt-3">
            <div class="info-label">Description</div>
            <div class="info-value">{!! nl2br(e($data['batch']['description'])) !!}</div>
        </div>
        @endif
        
        @if(!empty($data['batch']['notes']))
        <div class="info-item mt-3">
            <div class="info-label">Notes</div>
            <div class="info-value">{!! nl2br(e($data['batch']['notes'])) !!}</div>
        </div>
        @endif
    </div>

    <!-- Farm Information -->
    @if($data['farm'])
    <div class="section">
        <div class="section-header">Farm Information</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Farm Name</div>
                <div class="info-value">{{ $data['farm']['name'] ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Location</div>
                <div class="info-value">{{ $data['farm']['location'] ?? 'N/A' }}</div>
            </div>
            @if(!empty($data['farm']['size']))
            <div class="info-item">
                <div class="info-label">Size</div>
                <div class="info-value">{{ $data['farm']['size'] }}</div>
            </div>
            @endif
            @if(!empty($data['farm']['certification']))
            <div class="info-item">
                <div class="info-label">Certification</div>
                <div class="info-value">{{ $data['farm']['certification'] }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Product Information -->
    @if($data['product'])
    <div class="section">
        <div class="section-header">Product Information</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Product Name</div>
                <div class="info-value">{{ $data['product']['name'] ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">SKU</div>
                <div class="info-value">{{ $data['product']['sku'] ?? 'N/A' }}</div>
            </div>
            @if(!empty($data['product']['category']))
            <div class="info-item">
                <div class="info-label">Category</div>
                <div class="info-value">{{ $data['product']['category'] }}</div>
            </div>
            @endif
        </div>
        
        @if(!empty($data['product']['description']))
        <div class="info-item mt-3">
            <div class="info-label">Description</div>
            <div class="info-value">{!! nl2br(e($data['product']['description'])) !!}</div>
        </div>
        @endif
    </div>
    @endif

    <!-- Quality Tests -->
    @if($data['quality_tests']->isNotEmpty())
    <div class="section">
        <div class="section-header">Quality Tests</div>
        <table>
            <thead>
                <tr>
                    <th>Test Type</th>
                    <th>Test Date</th>
                    <th>Tested By</th>
                    <th>Result</th>
                    <th>Passed</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['quality_tests'] as $test)
                <tr>
                    <td>{{ $test['test_type'] ?? 'N/A' }}</td>
                    <td>{{ $test['test_date'] ? \Carbon\Carbon::parse($test['test_date'])->format('M d, Y') : 'N/A' }}</td>
                    <td>{{ $test['tested_by'] ?? 'N/A' }}</td>
                    <td>{{ $test['result'] ?? 'N/A' }}</td>
                    <td>
                        @if(isset($test['passed']))
                            @if($test['passed'])
                                <span class="badge badge-success">Passed</span>
                            @else
                                <span class="badge badge-danger">Failed</span>
                            @endif
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Packaging Information -->
    @if($data['packaging'])
    <div class="section">
        <div class="section-header">Packaging Information</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Type</div>
                <div class="info-value">{{ $data['packaging']['type'] ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Material</div>
                <div class="info-value">{{ $data['packaging']['material'] ?? 'N/A' }}</div>
            </div>
            @if(!empty($data['packaging']['weight']))
            <div class="info-item">
                <div class="info-label">Weight</div>
                <div class="info-value">
                    {{ number_format($data['packaging']['weight'], 2) }} {{ $data['packaging']['weight_unit'] ?? '' }}
                </div>
            </div>
            @endif
            @if(!empty($data['packaging']['dimensions']))
            <div class="info-item">
                <div class="info-label">Dimensions</div>
                <div class="info-value">{{ $data['packaging']['dimensions'] }}</div>
            </div>
            @endif
            @if(!empty($data['packaging']['barcode']))
            <div class="info-item">
                <div class="info-label">Barcode</div>
                <div class="info-value">{{ $data['packaging']['barcode'] }}</div>
            </div>
            @endif
            @if(!empty($data['packaging']['packaged_at']))
            <div class="info-item">
                <div class="info-label">Packaged At</div>
                <div class="info-value">
                    {{ \Carbon\Carbon::parse($data['packaging']['packaged_at'])->format('M d, Y H:i') }}
                    @if(!empty($data['packaging']['packaged_by']))
                        by {{ $data['packaging']['packaged_by'] }}
                    @endif
                </div>
            </div>
            @endif
        </div>
        
        @if(!empty($data['packaging']['notes']))
        <div class="info-item mt-3">
            <div class="info-label">Packaging Notes</div>
            <div class="info-value">{!! nl2br(e($data['packaging']['notes'])) !!}</div>
        </div>
        @endif
    </div>
    @endif

    <!-- Shipments -->
    @if($data['shipments']->isNotEmpty())
    <div class="section">
        <div class="section-header">Shipments</div>
        @foreach($data['shipments'] as $shipment)
        <div class="mb-4 p-3 border rounded">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Tracking Number</div>
                    <div class="info-value">{{ $shipment['tracking_number'] ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Carrier</div>
                    <div class="info-value">{{ $shipment['carrier'] ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        @php
                            $statusClass = [
                                'pending' => 'badge-warning',
                                'in_transit' => 'badge-info',
                                'out_for_delivery' => 'badge-primary',
                                'delivered' => 'badge-success',
                                'failed' => 'badge-danger',
                                'returned' => 'badge-danger',
                            ][$shipment['status']] ?? 'badge-secondary';
                        @endphp
                        <span class="badge {{ $statusClass }}">
                            {{ ucfirst(str_replace('_', ' ', $shipment['status'])) }}
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Shipped At</div>
                    <div class="info-value">
                        {{ $shipment['shipped_at'] ? \Carbon\Carbon::parse($shipment['shipped_at'])->format('M d, Y H:i') : 'N/A' }}
                    </div>
                </div>
                @if(!empty($shipment['estimated_delivery']))
                <div class="info-item">
                    <div class="info-label">Estimated Delivery</div>
                    <div class="info-value">
                        {{ \Carbon\Carbon::parse($shipment['estimated_delivery'])->format('M d, Y') }}
                    </div>
                </div>
                @endif
                @if(!empty($shipment['actual_delivery']))
                <div class="info-item">
                    <div class="info-label">Actual Delivery</div>
                    <div class="info-value">
                        {{ \Carbon\Carbon::parse($shipment['actual_delivery'])->format('M d, Y H:i') }}
                    </div>
                </div>
                @endif
                @if(!empty($shipment['origin']))
                <div class="info-item">
                    <div class="info-label">Origin</div>
                    <div class="info-value">{{ $shipment['origin'] }}</div>
                </div>
                @endif
                @if(!empty($shipment['destination']))
                <div class="info-item">
                    <div class="info-label">Destination</div>
                    <div class="info-value">{{ $shipment['destination'] }}</div>
                </div>
                @endif
            </div>
            
            @if(!empty($shipment['notes']))
            <div class="info-item mt-3">
                <div class="info-label">Notes</div>
                <div class="info-value">{!! nl2br(e($shipment['notes'])) !!}</div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <!-- Trace Events -->
    @if($data['trace_events']->isNotEmpty())
    <div class="section">
        <div class="section-header">Trace Events</div>
        <table>
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Event Type</th>
                    <th>Description</th>
                    <th>Actor</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['trace_events'] as $event)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($event['timestamp'])->format('M d, Y H:i') }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $event['event_type'])) }}</td>
                    <td>{{ $event['description'] ?? 'N/A' }}</td>
                    <td>
                        @if($event['actor'])
                            {{ $event['actor']['name'] ?? 'System' }}
                        @else
                            System
                        @endif
                    </td>
                    <td>{{ $event['location'] ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>
            This report was generated on {{ \Carbon\Carbon::parse($data['exported_at'])->format('F j, Y \a\t h:i A') }}
            @if($data['exported_by'])
                by {{ $data['exported_by']['name'] }} ({{ $data['exported_by']['email'] }})
            @endif
        </p>
        <p>Trace Code: {{ $data['batch']['trace_code'] }} | Page <span class="page-number"></span> of <span class="page-count"></span></p>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $size = 8;
            $font = $fontMetrics->getFont("DejaVu Sans");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 20;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>
