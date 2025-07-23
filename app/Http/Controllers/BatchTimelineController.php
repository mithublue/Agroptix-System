<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Services\StateTransitionService;
use App\Services\TraceabilityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BatchTimelineController extends Controller
{
    /**
     * The traceability service instance.
     *
     * @var TraceabilityService
     */
    protected $traceabilityService;
    
    /**
     * The state transition service instance.
     *
     * @var StateTransitionService
     */
    protected $stateTransitionService;

    /**
     * Create a new controller instance.
     *
     * @param TraceabilityService $traceabilityService
     * @param StateTransitionService $stateTransitionService
     */
    public function __construct(
        TraceabilityService $traceabilityService,
        StateTransitionService $stateTransitionService
    ) {
        $this->middleware('auth');
        $this->traceabilityService = $traceabilityService;
        $this->stateTransitionService = $stateTransitionService;
    }

    /**
     * Display the batch timeline.
     *
     * @param Request $request
     * @param string $traceCode
     * @return Response
     */
    public function show(Request $request, string $traceCode): Response
    {
        // Find the batch by trace code
        $batch = Batch::where('trace_code', $traceCode)->firstOrFail();
        
        // Authorize that the user can view this batch
        $this->authorize('view', $batch);
        
        // Get the timeline data
        $timeline = $this->traceabilityService->getBatchTimeline($batch);
        
        // Get workflow information
        $workflow = $this->stateTransitionService->getWorkflowStatus($batch);
        
        // Get the batch with its relationships
        $batch->load([
            'farm',
            'product',
            'qualityTests',
            'packaging',
            'shipments',
            'traceEvents' => function ($query) {
                $query->with('actor')->latest();
            },
        ]);
        
        // Format the events for the timeline
        $events = $batch->traceEvents->map(function ($event) {
            return [
                'id' => $event->id,
                'type' => $event->event_type,
                'title' => $this->getEventTitle($event->event_type),
                'description' => $this->getEventDescription($event),
                'timestamp' => $event->created_at->toIso8601String(),
                'formatted_date' => $event->created_at->format('M j, Y'),
                'formatted_time' => $event->created_at->format('g:i A'),
                'actor' => $event->actor ? [
                    'id' => $event->actor->id,
                    'name' => $event->actor->name,
                    'email' => $event->actor->email,
                ] : null,
                'data' => $event->data,
                'icon' => $this->getEventIcon($event->event_type),
                'color' => $this->getEventColor($event->event_type),
            ];
        });
        
        // Group events by date for the timeline view
        $groupedEvents = $events->groupBy('formatted_date');
        
        return Inertia::render('BatchTimeline/Show', [
            'batch' => [
                'id' => $batch->id,
                'trace_code' => $batch->trace_code,
                'name' => $batch->name,
                'status' => $batch->status,
                'status_label' => $this->stateTransitionService->getStatusLabel($batch->status),
                'created_at' => $batch->created_at->toIso8601String(),
                'product' => $batch->product ? [
                    'id' => $batch->product->id,
                    'name' => $batch->product->name,
                    'sku' => $batch->product->sku,
                ] : null,
                'farm' => $batch->farm ? [
                    'id' => $batch->farm->id,
                    'name' => $batch->farm->name,
                    'location' => $batch->farm->location,
                ] : null,
            ],
            'timeline' => $groupedEvents,
            'workflow' => $workflow,
            'integrity_check' => $timeline['integrity_check'] ?? null,
            'can' => [
                'update' => $request->user()->can('update', $batch),
                'delete' => $request->user()->can('delete', $batch),
            ],
        ]);
    }
    
    /**
     * Get a human-readable title for an event type.
     *
     * @param string $eventType
     * @return string
     */
    protected function getEventTitle(string $eventType): string
    {
        $titles = [
            'batch_created' => 'Batch Created',
            'batch_updated' => 'Batch Updated',
            'batch_deleted' => 'Batch Deleted',
            'harvest' => 'Harvest',
            'processing' => 'Processing',
            'qc_test' => 'Quality Test',
            'qc_approval' => 'QC Approval',
            'qc_rejection' => 'QC Rejection',
            'packaging' => 'Packaging',
            'shipping' => 'Shipping',
            'delivery' => 'Delivery',
            'status_changed' => 'Status Changed',
            'corrective_action' => 'Corrective Action',
            'document_uploaded' => 'Document Uploaded',
            'note_added' => 'Note Added',
            'location_changed' => 'Location Changed',
            'temperature_reading' => 'Temperature Reading',
            'humidity_reading' => 'Humidity Reading',
            'inspection' => 'Inspection',
            'maintenance' => 'Maintenance',
            'batch_split' => 'Batch Split',
            'batch_merged' => 'Batch Merged',
            'sample_taken' => 'Sample Taken',
            'test_result' => 'Test Result',
            'certification' => 'Certification',
            'recall' => 'Recall',
            'disposal' => 'Disposal',
            'return' => 'Return',
            'damage' => 'Damage',
            'theft' => 'Theft',
            'loss' => 'Loss',
            'other' => 'Event',
        ];
        
        return $titles[$eventType] ?? ucwords(str_replace('_', ' ', $eventType));
    }
    
    /**
     * Get a description for an event.
     *
     * @param \App\Models\TraceEvent $event
     * @return string
     */
    protected function getEventDescription($event): string
    {
        $data = $event->data;
        $actorName = $event->actor ? $event->actor->name : 'System';
        
        switch ($event->event_type) {
            case 'status_changed':
                $from = $data['old_status'] ?? 'unknown';
                $to = $data['new_status'] ?? 'unknown';
                return "Status changed from {$from} to {$to}";
                
            case 'harvest':
                $date = $data['harvest_date'] ?? 'unknown date';
                $quantity = $data['quantity'] ?? 'unknown quantity';
                $unit = $data['unit'] ?? 'units';
                return "Harvested on {$date}: {$quantity} {$unit}";
                
            case 'processing':
                $type = $data['process_type'] ?? 'processing';
                $notes = $data['notes'] ?? '';
                return "Processed with {$type}" . ($notes ? ": {$notes}" : '');
                
            case 'qc_test':
                $result = $data['result'] ?? 'unknown';
                $testType = $data['test_type'] ?? 'test';
                return "QC {$testType} performed: {$result}";
                
            case 'qc_approval':
                return "QC Approved by {$actorName}";
                
            case 'qc_rejection':
                $reason = $data['reason'] ?? 'No reason provided';
                return "QC Rejected by {$actorName}: {$reason}";
                
            case 'shipping':
                $carrier = $data['carrier'] ?? 'unknown carrier';
                $tracking = $data['tracking_number'] ?? 'no tracking';
                return "Shipped via {$carrier}, Tracking: {$tracking}";
                
            case 'delivery':
                $recipient = $data['recipient'] ?? 'unknown recipient';
                $location = $data['location'] ?? 'unknown location';
                return "Delivered to {$recipient} at {$location}";
                
            default:
                if (isset($data['notes'])) {
                    return $data['notes'];
                }
                
                return ucfirst(str_replace('_', ' ', $event->event_type));
        }
    }
    
    /**
     * Get an icon for an event type.
     *
     * @param string $eventType
     * @return string
     */
    protected function getEventIcon(string $eventType): string
    {
        $icons = [
            'batch_created' => 'mdi-package-variant-closed',
            'batch_updated' => 'mdi-pencil',
            'batch_deleted' => 'mdi-delete',
            'harvest' => 'mdi-sprout',
            'processing' => 'mdi-factory',
            'qc_test' => 'mdi-clipboard-check',
            'qc_approval' => 'mdi-check-circle',
            'qc_rejection' => 'mdi-close-circle',
            'packaging' => 'mdi-package',
            'shipping' => 'mdi-truck-delivery',
            'delivery' => 'mdi-package-variant',
            'status_changed' => 'mdi-swap-horizontal',
            'corrective_action' => 'mdi-alert-circle',
            'document_uploaded' => 'mdi-file-document',
            'note_added' => 'mdi-note-text',
            'location_changed' => 'mdi-map-marker',
            'temperature_reading' => 'mdi-thermometer',
            'humidity_reading' => 'mdi-water-percent',
            'inspection' => 'mdi-magnify',
            'maintenance' => 'mdi-wrench',
            'batch_split' => 'mdi-arrow-split',
            'batch_merged' => 'mdi-arrow-merge',
            'sample_taken' => 'mdi-test-tube',
            'test_result' => 'mdi-clipboard-text',
            'certification' => 'mdi-certificate',
            'recall' => 'mdi-alert',
            'disposal' => 'mdi-delete-forever',
            'return' => 'mdi-undo',
            'damage' => 'mdi-alert-octagon',
            'theft' => 'mdi-shield-alert',
            'loss' => 'mdi-alert-circle',
        ];
        
        return $icons[$eventType] ?? 'mdi-information';
    }
    
    /**
     * Get a color for an event type.
     *
     * @param string $eventType
     * @return string
     */
    protected function getEventColor(string $eventType): string
    {
        // Default colors for different types of events
        if (str_contains($eventType, 'approval') || $eventType === 'delivery') {
            return 'success';
        }
        
        if (str_contains($eventType, 'rejection') || 
            in_array($eventType, ['recall', 'damage', 'theft', 'loss'])) {
            return 'error';
        }
        
        if (in_array($eventType, ['harvest', 'processing', 'packaging', 'shipping'])) {
            return 'info';
        }
        
        if (in_array($eventType, ['qc_test', 'inspection', 'test_result'])) {
            return 'warning';
        }
        
        return 'primary';
    }
}
