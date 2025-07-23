<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShipmentStoreRequest;
use App\Http\Requests\ShipmentUpdateRequest;
use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    public function index(Request $request): View
    {
        $shipments = Shipment::all();

        return view('shipment.index', [
            'shipments' => $shipments,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('shipment.create');
    }

    public function store(ShipmentStoreRequest $request): Response
    {
        $shipment = Shipment::create($request->validated());

        $request->session()->flash('shipment.id', $shipment->id);

        return redirect()->route('shipments.index');
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

    public function update(ShipmentUpdateRequest $request, Shipment $shipment): Response
    {
        $shipment->update($request->validated());

        $request->session()->flash('shipment.id', $shipment->id);

        return redirect()->route('shipments.index');
    }

    public function destroy(Request $request, Shipment $shipment): Response
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
