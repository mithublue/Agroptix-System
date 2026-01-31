<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LiveTrackingController extends Controller
{
    /**
     * Display map with active shipments.
     */
    public function index()
    {
        $activeShipments = Shipment::whereIn('tracking_status', ['in_transit', 'delayed'])
            ->get();

        return view('admin.live_monitoring.index', compact('activeShipments'));
    }

    /**
     * API Endpoint: Get current locations of all active shipments.
     */
    public function apiLocations()
    {
        $shipments = Shipment::whereIn('tracking_status', ['in_transit', 'delayed'])
            ->whereNotNull('current_location_lat')
            ->whereNotNull('current_location_lng')
            ->get(['id', 'batch_id', 'vehicle_type', 'current_location_lat', 'current_location_lng', 'tracking_status', 'updated_at']);

        // Format for frontend
        $data = $shipments->map(function ($s) {
            return [
                'id' => $s->id,
                'batch_id' => $s->batch_id,
                'vehicle' => $s->vehicle_type,
                'lat' => $s->current_location_lat,
                'lng' => $s->current_location_lng,
                'status' => $s->tracking_status,
                'last_update' => $s->updated_at->diffForHumans(),
            ];
        });

        return response()->json($data);
    }

    /**
     * API Endpoint: Simulate or Receive GPS Update.
     */
    public function updateLocation(Request $request, Shipment $shipment)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'status' => 'nullable|in:pending,in_transit,delivered,delayed'
        ]);

        $shipment->update([
            'current_location_lat' => $request->lat,
            'current_location_lng' => $request->lng,
            'last_location_update' => now(),
            'tracking_status' => $request->status ?? $shipment->tracking_status
        ]);

        return response()->json(['success' => true, 'message' => 'Location updated']);
    }
}
