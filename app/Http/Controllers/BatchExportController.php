<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\TraceEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BatchExportController extends Controller
{
    /**
     * Export batch history in the requested format
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $traceCode
     * @param  string  $format
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request, string $traceCode, string $format = 'pdf')
    {
        // Find the batch by trace code
        $batch = Batch::where('trace_code', $traceCode)->firstOrFail();
        
        // Authorize that the user can view this batch
        $this->authorize('view', $batch);
        
        // Load relationships
        $batch->load([
            'farm',
            'product',
            'qualityTests',
            'packaging',
            'shipments',
            'traceEvents' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'traceEvents.actor',
        ]);
        
        // Prepare the data for export
        $data = $this->prepareExportData($batch);
        
        // Generate the appropriate export based on format
        switch (strtolower($format)) {
            case 'csv':
                return $this->exportToCsv($data, $batch);
                
            case 'json':
                return $this->exportToJson($data, $batch);
                
            case 'pdf':
            default:
                return $this->exportToPdf($data, $batch);
        }
    }
    
    /**
     * Prepare the data for export
     *
     * @param  \App\Models\Batch  $batch
     * @return array
     */
    protected function prepareExportData(Batch $batch): array
    {
        // Basic batch information
        $data = [
            'batch' => [
                'id' => $batch->id,
                'trace_code' => $batch->trace_code,
                'name' => $batch->name,
                'description' => $batch->description,
                'status' => $batch->status,
                'status_label' => $this->getStatusLabel($batch->status),
                'created_at' => $batch->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $batch->updated_at->format('Y-m-d H:i:s'),
                'harvest_date' => $batch->harvest_date?->format('Y-m-d'),
                'expiry_date' => $batch->expiry_date?->format('Y-m-d'),
                'quantity' => $batch->quantity,
                'unit' => $batch->unit,
                'location' => $batch->location,
                'notes' => $batch->notes,
            ],
            'farm' => $batch->farm ? [
                'id' => $batch->farm->id,
                'name' => $batch->farm->name,
                'location' => $batch->farm->location,
                'size' => $batch->farm->size,
                'certification' => $batch->farm->certification,
            ] : null,
            'product' => $batch->product ? [
                'id' => $batch->product->id,
                'name' => $batch->product->name,
                'sku' => $batch->product->sku,
                'description' => $batch->product->description,
                'category' => $batch->product->category,
            ] : null,
            'quality_tests' => $batch->qualityTests->map(function ($test) {
                return [
                    'id' => $test->id,
                    'test_type' => $test->test_type,
                    'tested_by' => $test->tested_by,
                    'test_date' => $test->test_date?->format('Y-m-d H:i:s'),
                    'result' => $test->result,
                    'details' => $test->details,
                    'passed' => $test->passed,
                    'notes' => $test->notes,
                    'created_at' => $test->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $test->updated_at->format('Y-m-d H:i:s'),
                ];
            }),
            'packaging' => $batch->packaging ? [
                'id' => $batch->packaging->id,
                'type' => $batch->packaging->type,
                'material' => $batch->packaging->material,
                'weight' => $batch->packaging->weight,
                'weight_unit' => $batch->packaging->weight_unit,
                'dimensions' => $batch->packaging->dimensions,
                'barcode' => $batch->packaging->barcode,
                'packaged_by' => $batch->packaging->packaged_by,
                'packaged_at' => $batch->packaging->packaged_at?->format('Y-m-d H:i:s'),
                'notes' => $batch->packaging->notes,
            ] : null,
            'shipments' => $batch->shipments->map(function ($shipment) {
                return [
                    'id' => $shipment->id,
                    'tracking_number' => $shipment->tracking_number,
                    'carrier' => $shipment->carrier,
                    'shipping_method' => $shipment->shipping_method,
                    'shipped_at' => $shipment->shipped_at?->format('Y-m-d H:i:s'),
                    'estimated_delivery' => $shipment->estimated_delivery?->format('Y-m-d'),
                    'actual_delivery' => $shipment->actual_delivery?->format('Y-m-d H:i:s'),
                    'status' => $shipment->status,
                    'origin' => $shipment->origin,
                    'destination' => $shipment->destination,
                    'notes' => $shipment->notes,
                    'created_at' => $shipment->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $shipment->updated_at->format('Y-m-d H:i:s'),
                ];
            }),
            'trace_events' => $batch->traceEvents->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event_type' => $event->event_type,
                    'description' => $event->description,
                    'timestamp' => $event->created_at->format('Y-m-d H:i:s'),
                    'location' => $event->location,
                    'ip_address' => $event->ip_address,
                    'user_agent' => $event->user_agent,
                    'actor' => $event->actor ? [
                        'id' => $event->actor->id,
                        'name' => $event->actor->name,
                        'email' => $event->actor->email,
                    ] : null,
                    'data' => $event->data,
                ];
            }),
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'exported_by' => $request->user() ? [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ] : null,
        ];
        
        return $data;
    }
    
    /**
     * Export data to PDF
     *
     * @param  array  $data
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    protected function exportToPdf(array $data, Batch $batch)
    {
        $pdf = Pdf::loadView('exports.batch-history-pdf', [
            'data' => $data,
            'batch' => $batch,
        ]);
        
        $filename = 'batch-history-' . $batch->trace_code . '-' . now()->format('YmdHis') . '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * Export data to CSV
     *
     * @param  array  $data
     * @param  \App\Models\Batch  $batch
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    protected function exportToCsv(array $data, Batch $batch)
    {
        $filename = 'batch-history-' . $batch->trace_code . '-' . now()->format('YmdHis') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel compatibility with UTF-8
            fputs($file, "\xEF\xBB\xBF");
            
            // Batch Information
            fputcsv($file, ['Batch Information']);
            fputcsv($file, ['Field', 'Value']);
            foreach ($data['batch'] as $key => $value) {
                if (is_array($value) || is_object($value)) continue;
                fputcsv($file, [
                    ucwords(str_replace('_', ' ', $key)),
                    $this->formatValueForCsv($value)
                ]);
            }
            fputcsv($file, []);
            
            // Farm Information
            if ($data['farm']) {
                fputcsv($file, ['Farm Information']);
                fputcsv($file, ['Field', 'Value']);
                foreach ($data['farm'] as $key => $value) {
                    if (is_array($value) || is_object($value)) continue;
                    fputcsv($file, [
                        ucwords(str_replace('_', ' ', $key)),
                        $this->formatValueForCsv($value)
                    ]);
                }
                fputcsv($file, []);
            }
            
            // Product Information
            if ($data['product']) {
                fputcsv($file, ['Product Information']);
                fputcsv($file, ['Field', 'Value']);
                foreach ($data['product'] as $key => $value) {
                    if (is_array($value) || is_object($value)) continue;
                    fputcsv($file, [
                        ucwords(str_replace('_', ' ', $key)),
                        $this->formatValueForCsv($value)
                    ]);
                }
                fputcsv($file, []);
            }
            
            // Quality Tests
            if ($data['quality_tests']->isNotEmpty()) {
                fputcsv($file, ['Quality Tests']);
                $first = true;
                foreach ($data['quality_tests'] as $test) {
                    if ($first) {
                        fputcsv($file, array_keys($test));
                        $first = false;
                    }
                    fputcsv($file, array_map([$this, 'formatValueForCsv'], $test));
                }
                fputcsv($file, []);
            }
            
            // Packaging Information
            if ($data['packaging']) {
                fputcsv($file, ['Packaging Information']);
                fputcsv($file, ['Field', 'Value']);
                foreach ($data['packaging'] as $key => $value) {
                    if (is_array($value) || is_object($value)) continue;
                    fputcsv($file, [
                        ucwords(str_replace('_', ' ', $key)),
                        $this->formatValueForCsv($value)
                    ]);
                }
                fputcsv($file, []);
            }
            
            // Shipments
            if ($data['shipments']->isNotEmpty()) {
                fputcsv($file, ['Shipments']);
                $first = true;
                foreach ($data['shipments'] as $shipment) {
                    if ($first) {
                        fputcsv($file, array_keys($shipment));
                        $first = false;
                    }
                    fputcsv($file, array_map([$this, 'formatValueForCsv'], $shipment));
                }
                fputcsv($file, []);
            }
            
            // Trace Events
            if ($data['trace_events']->isNotEmpty()) {
                fputcsv($file, ['Trace Events']);
                $first = true;
                
                // Flatten the trace events data for CSV
                $flattenedEvents = [];
                foreach ($data['trace_events'] as $event) {
                    $flattened = [];
                    foreach ($event as $key => $value) {
                        if ($key === 'actor' && is_array($value)) {
                            foreach ($value as $actorKey => $actorValue) {
                                $flattened['actor_' . $actorKey] = $actorValue;
                            }
                        } elseif ($key === 'data' && is_array($value)) {
                            foreach ($value as $dataKey => $dataValue) {
                                $flattened['data_' . $dataKey] = is_array($dataValue) ? json_encode($dataValue) : $dataValue;
                            }
                        } else {
                            $flattened[$key] = is_array($value) ? json_encode($value) : $value;
                        }
                    }
                    $flattenedEvents[] = $flattened;
                }
                
                // Output headers
                if (!empty($flattenedEvents)) {
                    fputcsv($file, array_keys($flattenedEvents[0]));
                    
                    // Output data
                    foreach ($flattenedEvents as $event) {
                        fputcsv($file, array_map([$this, 'formatValueForCsv'], $event));
                    }
                }
            }
            
            // Export metadata
            fputcsv($file, ['']);
            fputcsv($file, ['Export Information']);
            fputcsv($file, ['Exported At', $data['exported_at']]);
            if ($data['exported_by']) {
                fputcsv($file, ['Exported By', $data['exported_by']['name'] . ' (' . $data['exported_by']['email'] . ')']);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export data to JSON
     *
     * @param  array  $data
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    protected function exportToJson(array $data, Batch $batch)
    {
        $filename = 'batch-history-' . $batch->trace_code . '-' . now()->format('YmdHis') . '.json';
        
        return response()->json($data, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    /**
     * Format a value for CSV export
     *
     * @param  mixed  $value
     * @return string
     */
    protected function formatValueForCsv($value)
    {
        if (is_null($value)) {
            return '';
        }
        
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }
        
        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }
        
        return (string) $value;
    }
    
    /**
     * Get a human-readable status label
     *
     * @param  string  $status
     * @return string
     */
    protected function getStatusLabel(string $status): string
    {
        $labels = [
            'created' => 'Created',
            'harvested' => 'Harvested',
            'processing' => 'Processing',
            'qc_pending' => 'QC Pending',
            'qc_approved' => 'QC Approved',
            'qc_rejected' => 'QC Rejected',
            'packaged' => 'Packaged',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'returned' => 'Returned',
            'destroyed' => 'Destroyed',
        ];
        
        return $labels[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }
}
