<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\TraceEvent;
use App\Services\TraceabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TraceabilityController extends Controller
{
    /**
     * The traceability service instance.
     */
    protected $traceabilityService;

    /**
     * Create a new controller instance.
     */
    public function __construct(TraceabilityService $traceabilityService)
    {
        $this->middleware('auth:api');
        $this->traceabilityService = $traceabilityService;
    }

    /**
     * Get the trace events for a specific batch.
     *
     * @param string $traceCode
     * @return JsonResponse
     */
    public function getBatchTimeline(string $traceCode): JsonResponse
    {
        $batch = Batch::where('trace_code', $traceCode)->firstOrFail();
        
        // Check if user has permission to view this batch
        $this->authorize('view', $batch);
        
        $timeline = $this->traceabilityService->getBatchTimeline($batch);
        
        return response()->json([
            'success' => true,
            'data' => $timeline
        ]);
    }

    /**
     * Log a new trace event for a batch.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logEvent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'batch_trace_code' => 'required|string|exists:batches,trace_code',
            'event_type' => ['required', 'string', Rule::in(array_values(TraceEvent::getEventTypes()))],
            'location' => 'required|string|max:255',
            'data' => 'nullable|array',
            'document' => 'nullable|file|max:10240', // Max 10MB
            'is_corrective_action' => 'sometimes|boolean',
            'parent_event_id' => 'nullable|exists:trace_events,id',
            'device_id' => 'nullable|string|max:255',
            'ip_address' => 'nullable|ip',
        ]);

        try {
            $batch = Batch::where('trace_code', $validated['batch_trace_code'])->firstOrFail();
            
            // Check if user has permission to log events for this batch
            $this->authorize('update', $batch);
            
            // Create the trace event
            $traceEvent = $this->traceabilityService->logEvent(
                batch: $batch,
                eventType: $validated['event_type'],
                actor: $request->user(),
                data: $validated['data'] ?? [],
                document: $request->file('document'),
                isCorrectiveAction: $validated['is_corrective_action'] ?? false,
                parentEventId: $validated['parent_event_id'] ?? null,
                deviceId: $validated['device_id'] ?? null,
                ipAddress: $validated['ip_address'] ?? $request->ip()
            );
            
            // Update batch status based on event type if needed
            if (in_array($validated['event_type'], [
                TraceEvent::TYPE_HARVEST,
                TraceEvent::TYPE_PROCESSING,
                TraceEvent::TYPE_QC,
                TraceEvent::TYPE_PACKAGING,
                TraceEvent::TYPE_SHIPPING,
                TraceEvent::TYPE_DELIVERY,
            ])) {
                $statusMap = [
                    TraceEvent::TYPE_HARVEST => Batch::STATUS_HARVESTED,
                    TraceEvent::TYPE_PROCESSING => Batch::STATUS_PROCESSING,
                    TraceEvent::TYPE_QC => Batch::STATUS_QC_PENDING,
                    TraceEvent::TYPE_PACKAGING => Batch::STATUS_PACKAGING,
                    TraceEvent::TYPE_SHIPPING => Batch::STATUS_SHIPPED,
                    TraceEvent::TYPE_DELIVERY => Batch::STATUS_DELIVERED,
                ];
                
                if (isset($statusMap[$validated['event_type']])) {
                    $batch->status = $statusMap[$validated['event_type']];
                    $batch->save();
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Trace event logged successfully',
                'data' => $traceEvent->load('actor')
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Failed to log trace event: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to log trace event: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a QR code for a batch's traceability URL.
     *
     * @param string $traceCode
     * @return \Illuminate\Http\Response
     */
    public function getQrCode(string $traceCode)
    {
        $batch = Batch::where('trace_code', $traceCode)->firstOrFail();
        
        // Check if user has permission to view this batch
        $this->authorize('view', $batch);
        
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->generate($batch->getTraceabilityUrl());
            
        return response($qrCode, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'inline; filename="qr-code-' . $batch->trace_code . '.svg"'
        ]);
    }

    /**
     * Verify the integrity of a batch's trace events.
     *
     * @param string $traceCode
     * @return JsonResponse
     */
    public function verifyIntegrity(string $traceCode): JsonResponse
    {
        $batch = Batch::where('trace_code', $traceCode)->firstOrFail();
        
        // Check if user has permission to view this batch
        $this->authorize('view', $batch);
        
        $verification = $this->traceabilityService->verifyBatchIntegrity($batch);
        
        return response()->json([
            'success' => true,
            'data' => [
                'batch_id' => $batch->id,
                'trace_code' => $batch->trace_code,
                'is_integrity_verified' => $verification['is_valid'],
                'verification_errors' => $verification['errors'],
                'verified_at' => now()->toDateTimeString(),
            ]
        ]);
    }

    /**
     * Handle QR/barcode scan and trigger appropriate event.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handleScan(Request $request): JsonResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'scan_data' => 'required|string',
            'scan_type' => 'required|string|in:qr,barcode',
            'event_type' => 'required|string',
            'location' => 'required|string|max:255',
            'device_id' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
            'timestamp' => 'nullable|date',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $validated = $validator->validated();
        $scanData = $validated['scan_data'];
        $eventType = $validated['event_type'];
        $location = $validated['location'];
        
        try {
            // Try to find batch by trace code (QR code)
            $batch = Batch::where('trace_code', $scanData)->first();
            
            // If not found by trace code, try to find by barcode (assuming barcode is the batch ID)
            if (!$batch && $validated['scan_type'] === 'barcode') {
                $batch = Batch::find($scanData);
            }
            
            if (!$batch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Batch not found',
                    'scan_data' => $scanData
                ], 404);
            }
            
            // Check if user has permission to log events for this batch
            $this->authorize('update', $batch);
            
            // Prepare event data
            $eventData = [
                'location' => $location,
                'device_id' => $validated['device_id'] ?? null,
                'scan_type' => $validated['scan_type'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => $validated['metadata'] ?? [],
            ];
            
            // Log the scan event
            $event = $this->traceabilityService->logEvent(
                batch: $batch,
                eventType: $eventType,
                actor: Auth::user(),
                data: $eventData,
                location: $location,
                ipAddress: $request->ip(),
                timestamp: $validated['timestamp'] ?? null
            );
            
            // Update batch status based on event type if needed
            $statusUpdate = $this->updateBatchStatusOnScan($batch, $eventType);
            
            return response()->json([
                'success' => true,
                'message' => 'Scan processed successfully',
                'data' => [
                    'event_id' => $event->id,
                    'batch_id' => $batch->id,
                    'trace_code' => $batch->trace_code,
                    'event_type' => $event->event_type,
                    'status_updated' => $statusUpdate['updated'],
                    'new_status' => $statusUpdate['new_status'] ?? null,
                    'timestamp' => $event->created_at->toDateTimeString(),
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error processing scan: ' . $e->getMessage(), [
                'scan_data' => $scanData,
                'event_type' => $eventType,
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process scan: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Update batch status based on scan event type.
     *
     * @param Batch $batch
     * @param string $eventType
     * @return array
     */
    protected function updateBatchStatusOnScan(Batch $batch, string $eventType): array
    {
        $statusMap = [
            TraceEvent::TYPE_HARVEST => Batch::STATUS_HARVESTED,
            TraceEvent::TYPE_PROCESSING => Batch::STATUS_PROCESSING,
            TraceEvent::TYPE_QC => Batch::STATUS_QC_PENDING,
            TraceEvent::TYPE_QC_APPROVAL => Batch::STATUS_QC_APPROVED,
            TraceEvent::TYPE_PACKAGED => Batch::STATUS_PACKAGED,
            TraceEvent::TYPE_SHIPPING => Batch::STATUS_SHIPPED,
            TraceEvent::TYPE_DELIVERY => Batch::STATUS_DELIVERED,
        ];
        
        if (isset($statusMap[$eventType])) {
            $newStatus = $statusMap[$eventType];
            
            // Only update if the status is different
            if ($batch->status !== $newStatus) {
                $oldStatus = $batch->status;
                $batch->status = $newStatus;
                $batch->save();
                
                // Log the status change
                $this->traceabilityService->logEvent(
                    batch: $batch,
                    eventType: 'status_changed',
                    actor: Auth::user(),
                    data: [
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'triggered_by' => 'scan_event',
                        'event_type' => $eventType,
                    ],
                    location: 'System',
                    ipAddress: request()->ip()
                );
                
                return [
                    'updated' => true,
                    'new_status' => $newStatus,
                    'old_status' => $oldStatus,
                ];
            }
        }
        
        return [
            'updated' => false,
            'current_status' => $batch->status,
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
