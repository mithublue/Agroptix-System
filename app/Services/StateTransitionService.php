<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\TraceEvent;
use Illuminate\Support\Facades\Log;

class StateTransitionService
{
    /**
     * Map of valid state transitions
     * Each key is a status, and the value is an array of valid next statuses
     */
    protected array $validTransitions = [
        // Initial state
        Batch::STATUS_CREATED => [
            Batch::STATUS_HARVESTED,
        ],
        
        // Harvesting
        Batch::STATUS_HARVESTED => [
            Batch::STATUS_PROCESSING,
        ],
        
        // Processing
        Batch::STATUS_PROCESSING => [
            Batch::STATUS_QC_PENDING,
            Batch::STATUS_QC_REJECTED, // In case of immediate rejection
        ],
        
        // Quality Control
        Batch::STATUS_QC_PENDING => [
            Batch::STATUS_QC_APPROVED,
            Batch::STATUS_QC_REJECTED,
        ],
        
        Batch::STATUS_QC_APPROVED => [
            Batch::STATUS_PACKAGED,
        ],
        
        Batch::STATUS_QC_REJECTED => [
            Batch::STATUS_PROCESSING, // After rework
        ],
        
        // Packaging
        Batch::STATUS_PACKAGED => [
            Batch::STATUS_SHIPPED,
        ],
        
        // Shipping
        Batch::STATUS_SHIPPED => [
            Batch::STATUS_DELIVERED,
            Batch::STATUS_RETURNED,
        ],
        
        // Final states (no further transitions allowed)
        Batch::STATUS_DELIVERED => [],
        Batch::STATUS_RETURNED => [
            Batch::STATUS_DESTROYED,
            Batch::STATUS_REPROCESSED => Batch::STATUS_PROCESSING,
        ],
        Batch::STATUS_DESTROYED => [],
    ];
    
    /**
     * Check if a state transition is valid
     *
     * @param string $currentStatus
     * @param string $newStatus
     * @return bool
     */
    public function isValidTransition(string $currentStatus, string $newStatus): bool
    {
        // If the status hasn't changed, it's always valid
        if ($currentStatus === $newStatus) {
            return true;
        }
        
        // Check if the current status exists in our transitions
        if (!array_key_exists($currentStatus, $this->validTransitions)) {
            Log::warning("Invalid current status: {$currentStatus}");
            return false;
        }
        
        // Check if the new status is in the list of valid transitions
        $allowedTransitions = $this->validTransitions[$currentStatus];
        
        // If the new status is in the allowed transitions, it's valid
        if (in_array($newStatus, $allowedTransitions)) {
            return true;
        }
        
        // Check for special case of reprocessing
        if ($currentStatus === Batch::STATUS_RETURNED && str_starts_with($newStatus, 'reprocess_')) {
            $targetStatus = substr($newStatus, 10); // Remove 'reprocess_' prefix
            return isset($allowedTransitions[$newStatus]) && $allowedTransitions[$newStatus] === $targetStatus;
        }
        
        return false;
    }
    
    /**
     * Validate a batch status transition with detailed error information
     *
     * @param Batch $batch
     * @param string $newStatus
     * @return array
     */
    public function validateTransition(Batch $batch, string $newStatus): array
    {
        $currentStatus = $batch->status;
        
        // If the status hasn't changed, it's always valid
        if ($currentStatus === $newStatus) {
            return [
                'is_valid' => true,
                'message' => 'Status unchanged',
            ];
        }
        
        // Check if the transition is valid
        if ($this->isValidTransition($currentStatus, $newStatus)) {
            return [
                'is_valid' => true,
                'message' => 'Valid transition',
            ];
        }
        
        // If we get here, the transition is invalid
        $allowedTransitions = $this->validTransitions[$currentStatus] ?? [];
        
        // Format the allowed transitions for the error message
        $allowedList = implode(', ', array_map(function($status) {
            return "'{$status}'";
        }, $allowedTransitions));
        
        return [
            'is_valid' => false,
            'message' => "Invalid status transition from '{$currentStatus}' to '{$newStatus}'",
            'current_status' => $currentStatus,
            'attempted_status' => $newStatus,
            'allowed_transitions' => $allowedTransitions,
            'error' => "Cannot change status from '{$currentStatus}' to '{$newStatus}'. " .
                      "Valid next statuses: {$allowedList}",
        ];
    }
    
    /**
     * Get the next possible statuses for a batch
     *
     * @param Batch $batch
     * @return array
     */
    public function getNextPossibleStatuses(Batch $batch): array
    {
        $currentStatus = $batch->status;
        
        if (!array_key_exists($currentStatus, $this->validTransitions)) {
            return [];
        }
        
        return $this->validTransitions[$currentStatus];
    }
    
    /**
     * Check if a batch can be transitioned to a new status
     *
     * @param Batch $batch
     * @param string $newStatus
     * @return bool
     */
    public function canTransitionTo(Batch $batch, string $newStatus): bool
    {
        return $this->isValidTransition($batch->status, $newStatus);
    }
    
    /**
     * Get the workflow status for a batch
     *
     * @param Batch $batch
     * @return array
     */
    public function getWorkflowStatus(Batch $batch): array
    {
        $currentStatus = $batch->status;
        $nextStatuses = $this->getNextPossibleStatuses($batch);
        
        $workflow = [
            'current_status' => $currentStatus,
            'next_possible_statuses' => $nextStatuses,
            'is_final' => empty($nextStatuses),
            'can_proceed' => !empty($nextStatuses),
        ];
        
        // Add human-readable status information
        $workflow['status_label'] = $this->getStatusLabel($currentStatus);
        $workflow['next_status_labels'] = array_map(
            fn($status) => $this->getStatusLabel($status),
            $nextStatuses
        );
        
        return $workflow;
    }
    
    /**
     * Get a human-readable label for a status
     *
     * @param string $status
     * @return string
     */
    protected function getStatusLabel(string $status): string
    {
        $labels = [
            Batch::STATUS_CREATED => 'Created',
            Batch::STATUS_HARVESTED => 'Harvested',
            Batch::STATUS_PROCESSING => 'Processing',
            Batch::STATUS_QC_PENDING => 'Pending QC',
            Batch::STATUS_QC_APPROVED => 'QC Approved',
            Batch::STATUS_QC_REJECTED => 'QC Rejected',
            Batch::STATUS_PACKAGED => 'Packaged',
            Batch::STATUS_SHIPPED => 'Shipped',
            Batch::STATUS_DELIVERED => 'Delivered',
            Batch::STATUS_RETURNED => 'Returned',
            Batch::STATUS_DESTROYED => 'Destroyed',
        ];
        
        return $labels[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }
    
    /**
     * Get the workflow history for a batch
     *
     * @param Batch $batch
     * @return array
     */
    public function getWorkflowHistory(Batch $batch): array
    {
        return $batch->traceEvents()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($event) {
                return [
                    'event_id' => $event->id,
                    'event_type' => $event->event_type,
                    'status' => $event->data['status'] ?? null,
                    'timestamp' => $event->created_at->toDateTimeString(),
                    'actor' => $event->actor ? [
                        'id' => $event->actor->id,
                        'name' => $event->actor->name,
                        'email' => $event->actor->email,
                    ] : null,
                    'data' => $event->data,
                ];
            })
            ->toArray();
    }
}
