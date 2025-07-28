<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeliveryStoreRequest;
use App\Http\Requests\DeliveryUpdateRequest;
use App\Models\Batch;
use App\Models\Delivery;
use App\Services\TraceabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeliveryController extends Controller
{
    protected $traceabilityService;

    public function __construct(TraceabilityService $traceabilityService)
    {
        $this->middleware('auth');
        
        // Use can: middleware instead of permission:
        $this->middleware('can:view_deliveries')->only(['index', 'show']);
        $this->middleware('can:create_deliveries')->only(['create', 'store']);
        $this->middleware('can:edit_deliveries')->only(['edit', 'update']);
        $this->middleware('can:delete_deliveries')->only(['destroy']);
        $this->middleware('can:update_delivery_status')->only(['updateStatus']);

        $this->traceabilityService = $traceabilityService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Delivery::class);

        $user = auth()->user();
        $query = Delivery::with(['batch']);

        // If user doesn't have permission to view all deliveries, show only their own
        if (!$user->hasRole(['admin', 'logistics_manager'])) {
            $query->whereHas('batch', function($q) use ($user) {
                $q->where('created_by', $user->id);
            });
        }

        // Apply filters
        if ($request->has('status')) {
            $query->where('delivery_status', $request->input('status'));
        }

        if ($request->has('batch_id')) {
            $query->where('batch_id', $request->input('batch_id'));
        }

        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('delivery_date', [
                $request->input('date_from'),
                $request->input('date_to')
            ]);
        }

        $deliveries = $query->latest()->paginate(15);

        if ($request->wantsJson()) {
            return response()->json($deliveries);
        }

        return view('deliveries.index', compact('deliveries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Delivery::class);
        $batches = Batch::whereDoesntHave('delivery')->pluck('name', 'id');
        return view('deliveries.create', compact('batches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DeliveryStoreRequest $request)
    {
        // Authorization is handled by middleware and policy

        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Handle file uploads
            if ($request->hasFile('delivery_photos')) {
                $data['delivery_photos'] = $this->uploadFiles($request->file('delivery_photos'));
            }

            $delivery = Delivery::create($data);

            // Log the delivery creation
            $this->traceabilityService->logEvent(
                'delivery_created',
                'Delivery created for batch ' . $delivery->batch->name,
                $delivery->batch_id,
                'App\\Models\\Batch',
                ['delivery_id' => $delivery->id]
            );

            DB::commit();

            return redirect()
                ->route('deliveries.show', $delivery)
                ->with('success', 'Delivery created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating delivery: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Failed to create delivery. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Delivery $delivery)
    {
        // Authorization is handled by middleware and policy

        $delivery->load(['batch']);

        if (request()->wantsJson()) {
            return response()->json($elivery);
        }

        return view('deliveries.show', compact('delivery'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Delivery $delivery)
    {
        // Authorization is handled by middleware and policy

        $batches = Batch::whereDoesntHave('delivery')
            ->orWhere('id', $delivery->batch_id)
            ->pluck('name', 'id');

        return view('deliveries.edit', compact('delivery', 'batches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DeliveryUpdateRequest $request, Delivery $delivery)
    {
        // Authorization is handled by middleware and policy

        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Handle file uploads
            if ($request->hasFile('delivery_photos')) {
                // Delete old photos if needed
                if (!empty($delivery->delivery_photos)) {
                    Storage::delete($delivery->delivery_photos);
                }
                $data['delivery_photos'] = $this->uploadFiles($request->file('delivery_photos'));
            }

            // Handle feedback submission
            if ($request->has('customer_rating') && !$delivery->feedback_submitted_at) {
                $data['feedback_submitted_at'] = now();
                if (empty($data['feedback_status'])) {
                    $data['feedback_status'] = 'submitted';
                }
            }

            $delivery->update($data);

            // Log the delivery update
            $this->traceabilityService->logEvent(
                'delivery_updated',
                'Delivery updated for batch ' . $delivery->batch->name,
                $delivery->batch_id,
                'App\\Models\\Batch',
                ['delivery_id' => $delivery->id]
            );

            DB::commit();

            return redirect()
                ->route('deliveries.show', $delivery)
                ->with('success', 'Delivery updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating delivery: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Failed to update delivery. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Delivery $delivery)
    {
        // Authorization is handled by middleware and policy

        try {
            DB::beginTransaction();

            $batchId = $delivery->batch_id;
            $delivery->delete();

            // Log the delivery deletion
            $this->traceabilityService->logEvent(
                'delivery_deleted',
                'Delivery deleted for batch ' . $delivery->batch->name,
                $batchId,
                'App\\Models\\Batch',
                ['delivery_id' => $delivery->id]
            );

            DB::commit();

            return redirect()
                ->route('deliveries.index')
                ->with('success', 'Delivery deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting delivery: ' . $e->getMessage());

            return back()
                ->with('error', 'Failed to delete delivery. Please try again.');
        }
    }

    /**
     * Upload files and return their paths
     */
    protected function uploadFiles($files): array
    {
        $paths = [];

        foreach ($files as $file) {
            $paths[] = $file->store('delivery-photos', 'public');
        }

        return $paths;
    }

    /**
     * Update delivery status
     */
    public function updateStatus(Request $request, Delivery $delivery): JsonResponse
    {
        $this->authorize('updateStatus', $delivery);

        $request->validate([
            'status' => 'required|in:pending,in_transit,delivered,failed',
            'notes' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $delivery->delivery_status;
        $newStatus = $request->input('status');

        $delivery->update([
            'delivery_status' => $newStatus,
            'delivery_notes' => $request->input('notes') ?: $delivery->delivery_notes,
        ]);

        // Log the status change
        $this->traceabilityService->logEvent(
            'delivery_status_updated',
            "Delivery status changed from {$oldStatus} to {$newStatus}",
            $delivery->batch_id,
            'App\\Models\\Batch',
            [
                'delivery_id' => $delivery->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Delivery status updated successfully.',
            'data' => $delivery->fresh()
        ]);
    }
}
