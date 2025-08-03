<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\TraceEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TraceabilityService
{
    /**
     * Log a new trace event for a batch.
     *
     * @param Batch $batch
     * @param string $eventType
     * @param User $actor
     * @param array $data
     * @param UploadedFile|null $document
     * @param bool $isCorrectiveAction
     * @param int|null $parentEventId
     * @return TraceEvent
     * @throws \Exception
     */
    public function logEvent(
        Batch $batch,
        string $eventType,
        User $actor,
        array $data = [],
        ?UploadedFile $document = null,
        bool $isCorrectiveAction = false,
        ?int $parentEventId = null
    ): TraceEvent {
        return DB::transaction(function () use (
            $batch,
            $eventType,
            $actor,
            $data,
            $document,
            $isCorrectiveAction,
            $parentEventId
        ) {
            try {
                // Get the previous event for this batch
                $previousEvent = TraceEvent::where('batch_id', $batch->id)
                    ->latest('id')
                    ->first();

                // Handle document upload if provided
                $documentPath = null;
                if ($document) {
                    $documentPath = $this->storeDocument($document, $batch, $eventType);
                }

                // Create the trace event
                $event = new TraceEvent([
                    'batch_id' => $batch->id,
                    'event_type' => $eventType,
                    'actor_id' => $actor->id,
                    'location' => $data['location'] ?? null,
                    'reference_document' => $documentPath,
                    'data' => $data,
                    'previous_event_hash' => $previousEvent ? $previousEvent->current_hash : null,
                    'is_corrective_action' => $isCorrectiveAction,
                    'parent_event_id' => $parentEventId,
                    'device_id' => request()->header('X-Device-ID'),
                    'ip_address' => request()->ip(),
                ]);

                $event->save();

                // Update batch status if needed
                $this->updateBatchStatus($batch, $eventType);

                return $event;
            } catch (\Exception $e) {
                Log::error('Failed to log trace event', [
                    'batch_id' => $batch->id,
                    'event_type' => $eventType,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });
    }

    /**
     * Store a document related to a trace event.
     */
    protected function storeDocument(UploadedFile $file, Batch $batch, string $eventType): string
    {
        $path = sprintf(
            'trace-documents/%s/%s/%s_%s.%s',
            $batch->id,
            now()->format('Y-m-d'),
            Str::slug($eventType),
            Str::random(8),
            $file->getClientOriginalExtension()
        );

        return Storage::disk('public')->putFileAs(
            dirname($path),
            $file,
            basename($path)
        ) ? $path : '';
    }

    /**
     * Update the batch status based on the event type.
     */
    protected function updateBatchStatus(Batch $batch, string $eventType): void
    {
        $statusMap = [
            TraceEvent::TYPE_HARVEST => 'harvested',
            TraceEvent::TYPE_PROCESSING => 'processing',
            TraceEvent::TYPE_QC_PENDING => 'qc_pending',
            TraceEvent::TYPE_QC_APPROVED => 'qc_approved',
            TraceEvent::TYPE_QC_REJECTED => 'qc_rejected',
            TraceEvent::TYPE_PACKAGING => 'packaging',
            TraceEvent::TYPE_PACKAGED => 'packaged',
            TraceEvent::TYPE_SHIPPED => 'shipped',
            TraceEvent::TYPE_DELIVERED => 'delivered',
            TraceEvent::TYPE_QUARANTINE => 'quarantined',
            TraceEvent::TYPE_DISPOSED => 'disposed',
        ];

        if (array_key_exists($eventType, $statusMap)) {
            $batch->update(['status' => $statusMap[$eventType]]);
        }
    }

    /**
     * Get the event history for a batch.
     */
    public function getBatchTimeline(Batch $batch)
    {
        return TraceEvent::with(['actor', 'parentEvent'])
            ->where('batch_id', $batch->id)
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Verify the integrity of a batch's event chain.
     */
    public function verifyBatchChain(Batch $batch): array
    {
        $events = $this->getBatchTimeline($batch);
        $results = [
            'is_valid' => true,
            'events_checked' => 0,
            'invalid_events' => [],
        ];

        $previousHash = null;

        foreach ($events as $event) {
            $results['events_checked']++;
            
            // Verify hash chain
            if ($previousHash !== null && $event->previous_event_hash !== $previousHash) {
                $results['is_valid'] = false;
                $results['invalid_events'][] = [
                    'event_id' => $event->id,
                    'issue' => 'Hash mismatch',
                    'expected' => $previousHash,
                    'actual' => $event->previous_event_hash,
                ];
            }

            // Verify current hash
            if ($event->current_hash !== $event->generateHash()) {
                $results['is_valid'] = false;
                $results['invalid_events'][] = [
                    'event_id' => $event->id,
                    'issue' => 'Invalid hash',
                    'expected' => $event->generateHash(),
                    'actual' => $event->current_hash,
                ];
            }

            $previousHash = $event->current_hash;
        }

        return $results;
    }

    /**
     * Generate a Merkle proof for an event.
     * This can be used to verify that an event is part of the chain without revealing the entire chain.
     *
     * @param TraceEvent $event
     * @return array
     */
    public function generateMerkleProof(TraceEvent $event): array
    {
        $events = $event->batch->traceEvents()
            ->orderBy('id', 'asc')
            ->pluck('current_hash')
            ->toArray();
            
        $index = array_search($event->current_hash, $events);
        
        if ($index === false) {
            throw new Exception('Event not found in the batch event chain');
        }
        
        $proof = [];
        $currentIndex = $index;
        
        // Build the Merkle proof by hashing pairs of hashes
        while (count($events) > 1) {
            // If the current index is even, the sibling is at index + 1
            // If the current index is odd, the sibling is at index - 1
            $siblingIndex = ($currentIndex % 2 === 0) ? $currentIndex + 1 : $currentIndex - 1;
            
            // If the sibling exists, add it to the proof
            if (isset($events[$siblingIndex])) {
                $proof[] = [
                    'position' => $siblingIndex < $currentIndex ? 'left' : 'right',
                    'hash' => $events[$siblingIndex],
                ];
            }
            
            // Move up one level in the tree
            $currentIndex = (int)floor($currentIndex / 2);
            
            // Calculate the next level of hashes
            $nextLevel = [];
            for ($i = 0; $i < count($events); $i += 2) {
                $left = $events[$i];
                $right = $i + 1 < count($events) ? $events[$i + 1] : $events[$i];
                $nextLevel[] = hash('sha256', $left . $right);
            }
            
            $events = $nextLevel;
        }
        
        return [
            'event_id' => $event->id,
            'event_hash' => $event->current_hash,
            'merkle_root' => $events[0] ?? null,
            'proof' => $proof,
            'batch_id' => $event->batch_id,
            'trace_code' => $event->batch->trace_code,
            'generated_at' => now()->toIso8601String(),
        ];
    }
    
    /**
     * Verify a Merkle proof for an event.
     *
     * @param string $eventHash
     * @param array $proof
     * @param string $merkleRoot
     * @return bool
     */
    public function verifyMerkleProof(string $eventHash, array $proof, string $merkleRoot): bool
    {
        $currentHash = $eventHash;
        
        foreach ($proof as $node) {
            $hash = $node['hash'];
            
            if ($node['position'] === 'left') {
                $currentHash = hash('sha256', $hash . $currentHash);
            } else {
                $currentHash = hash('sha256', $currentHash . $hash);
            }
        }
        
        return $currentHash === $merkleRoot;
    }

    /**
     * Get the current status of a batch based on its events.
     */
    public function getBatchStatus(Batch $batch): string
    {
        $lastEvent = TraceEvent::where('batch_id', $batch->id)
            ->latest('id')
            ->first();

        return $lastEvent ? $lastEvent->event_type : 'unknown';
    }
}
