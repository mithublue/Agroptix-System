<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShipmentStoreRequest;
use App\Http\Requests\ShipmentUpdateRequest;
use App\Models\Batch;
use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Shipment::with(['batch']);

        // Apply filters
        if ($request->filled('origin')) {
            $query->where('origin', 'like', '%' . $request->origin . '%');
        }

        if ($request->filled('destination')) {
            $query->where('destination', 'like', '%' . $request->destination . '%');
        }

        if ($request->filled('vehicle_type')) {
            $query->where('vehicle_type', $request->vehicle_type);
        }

        if ($request->filled('mode')) {
            $query->where('mode', $request->mode);
        }

        // Apply sorting (default to latest first)
        $sortField = $request->input('sort', 'id');
        $sortDirection = $request->input('direction', 'desc');

        // Validate sort field to prevent SQL injection
        $validSortFields = ['id', 'origin', 'destination', 'vehicle_type', 'mode', 'departure_time', 'created_at'];
        if (!in_array($sortField, $validSortFields)) {
            $sortField = 'id';
        }

        $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');

        // Paginate the results
        $shipments = $query->paginate(15)->withQueryString();

        // Get unique values for filter dropdowns
        $vehicleTypes = Shipment::select('vehicle_type')
            ->whereNotNull('vehicle_type')
            ->distinct()
            ->pluck('vehicle_type')
            ->filter();

        $modes = Shipment::select('mode')
            ->whereNotNull('mode')
            ->distinct()
            ->pluck('mode')
            ->filter();

        return view('shipment.index', [
            'shipments' => $shipments,
            'vehicleTypes' => $vehicleTypes,
            'modes' => $modes,
            'filters' => $request->only(['origin', 'destination', 'vehicle_type', 'mode']),
        ]);
    }

    public function create(Request $request): Response
    {
        return view('shipment.create');
    }

    public function store(ShipmentStoreRequest $request)
    {
        try {
            $shipment = Shipment::create($request->validated());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Shipment created successfully!',
                    'redirect' => route('shipments.index')
                ]);
            }

            return redirect()
                ->route('shipments.index')
                ->with('success', 'Shipment created successfully!');

        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create shipment: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to create shipment: ' . $e->getMessage());
        }
    }

    public function show(Request $request, Shipment $shipment): Response
    {
        return view('shipment.show', [
            'shipment' => $shipment,
        ]);
    }

    public function edit(Request $request, Shipment $shipment): Response
    {
        return view('shipment.edit', [
            'shipment' => $shipment,
        ]);
    }

    public function update(ShipmentUpdateRequest $request, Shipment $shipment): RedirectResponse
    {
        $shipment->update($request->validated());

        $request->session()->flash('shipment.id', $shipment->id);

        return redirect()->route('shipments.index');
    }

    public function destroy(Request $request, Shipment $shipment): RedirectResponse
    {
        try {
            // Get the associated batch before deletion
            $batch = $shipment->batch;
            $shipmentId = $shipment->id;

            // Log the shipment deletion event
            try {
                if ($batch) {
                    $this->traceabilityService->logEvent(
                        batch: $batch,
                        eventType: TraceEvent::TYPE_SHIPPING_DELETED,
                        actor: Auth::user(),
                        data: [
                            'shipment_id' => $shipmentId,
                            'tracking_number' => $shipment->tracking_number,
                            'carrier' => $shipment->carrier,
                            'status' => $shipment->status,
                            'origin' => $shipment->origin,
                            'destination' => $shipment->destination,
                        ],
                        location: 'System',
                        ipAddress: request()->ip()
                    );

                    // Revert batch status to packaged if this was the only shipment
                    $remainingShipments = $batch->shipments()->count();
                    if ($remainingShipments === 1 && $batch->status === Batch::STATUS_SHIPPED) {
                        $batch->status = Batch::STATUS_PACKAGED;
                        $batch->save();
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to log shipment deletion event: ' . $e->getMessage(), [
                    'shipment_id' => $shipmentId,
                    'batch_id' => $batch->id ?? null,
                    'trace' => $e->getTraceAsString()
                ]);
                // Continue with deletion even if logging fails
            }

            // Delete the shipment
            $shipment->delete();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Shipment deleted successfully',
                    'batch_status' => $batch->status ?? null
                ]);
            }

            return redirect()->route('shipments.index')
                ->with('success', 'Shipment deleted successfully');

        } catch (\Exception $e) {
            Log::error('Failed to delete shipment: ' . $e->getMessage(), [
                'shipment_id' => $shipment->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete shipment: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->with('error', 'Failed to delete shipment: ' . $e->getMessage());
        }
    }
}
