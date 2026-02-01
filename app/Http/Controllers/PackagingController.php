<?php

namespace App\Http\Controllers;

use App\Models\Packaging;
use App\Models\Batch;
use App\Models\RpcUnit;
use App\Models\User;
use Illuminate\Http\Request;

use App\Models\TraceEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PackagingController extends Controller
{
    protected $traceabilityService;

    public function __construct(\App\Services\TraceabilityService $traceabilityService)
    {
        $this->traceabilityService = $traceabilityService;
    }
    /**
     * Display a listing of the packaging records.
     *
     * @return \Illuminate\View\View
     */
    /**
     * Display a listing of the packaging records.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->authorize('view_packaging', Packaging::class);

        // Define valid per page values
        $perPage = in_array($request->per_page, [5, 10, 20, 30, 50])
            ? (int) $request->per_page
            : 15; // Default to 15 if not specified or invalid

        $packages = Packaging::with('batch')
            ->when($request->filled('batch_id'), function ($query) use ($request) {
                $query->where('batch_id', $request->batch_id);
            })
            ->when($request->filled('package_type'), function ($query) use ($request) {
                $query->where('package_type', 'like', '%' . $request->package_type . '%');
            })
            ->when($request->filled('material_type'), function ($query) use ($request) {
                $query->where('material_type', 'like', '%' . $request->material_type . '%');
            })
            ->when($request->filled('quantity_of_units'), function ($query) use ($request) {
                $query->where('quantity_of_units', $request->quantity_of_units);
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $availableBatches = Batch::readyForPackaging()
            ->with('product')
            ->latest()
            ->get();

        $packagedBatchIds = $packages->pluck('batch_id')->filter()->unique();
        if ($packagedBatchIds->isNotEmpty()) {
            $additionalBatches = Batch::with('product')
                ->whereIn('id', $packagedBatchIds)
                ->get();
            $availableBatches = $availableBatches
                ->concat($additionalBatches)
                ->unique('id')
                ->values();
        }

        $rpcUnits = RpcUnit::all();
        $users = User::orderBy('name')->get();

        return view('admin.packaging.index', [
            'packages' => $packages,
            'batches' => $availableBatches,
            'rpcUnits' => $rpcUnits,
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create_packaging', Packaging::class);

        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'package_type' => 'required|string|max:255',
            'material_type' => 'required|string|max:255',
            'unit_weight_packaging' => 'required|numeric|min:0',
            'quantity_of_units' => 'required|integer|min:1',
            'rpc_unit_id' => 'nullable|exists:rpc_units,id',
            'packer_id' => 'required|exists:users,id',
            'packaging_location' => 'required|string|max:255',
            'cleanliness_checklist' => 'sometimes|boolean'
        ]);

        try {
            // Set default values
            $validated['cleanliness_checklist'] = $validated['cleanliness_checklist'] ?? false;

            // Generate QR code (you might want to implement your own logic here)
            $validated['qr_code'] = 'PKG-' . uniqid();

            $packaging = Packaging::create($validated);

            $batch = Batch::find($validated['batch_id']);

            if ($batch && $batch->status == Batch::STATUS_PACKAGING) {
                $batch->status = Batch::STATUS_PACKAGED;
                $batch->save();
            }

            return response()->json([
                'message' => 'Packaging created successfully',
                'data' => $packaging
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating packaging',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified packaging record.
     *
     * @param  \App\Models\Packaging  $packaging
     * @return \Illuminate\View\View
     */
    public function show(Packaging $packaging)
    {
        $this->authorize('view_packaging', $packaging);

        // Eager load relationships for the view
        $packaging->load(['batch', 'packer']);

        return view('admin.packaging.show', compact('packaging'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Packaging $packaging)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Packaging  $packaging
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Packaging $packaging)
    {
        $this->authorize('edit_packaging', $packaging);

        $validated = $request->validate([
            'batch_id' => 'sometimes|required|exists:batches,id',
            'package_type' => 'sometimes|required|string|max:255',
            'material_type' => 'sometimes|required|string|max:255',
            'unit_weight_packaging' => 'sometimes|required|numeric|min:0',
            'quantity_of_units' => 'sometimes|required|integer|min:1',
            'rpc_unit_id' => 'nullable|exists:rpc_units,id',
            'packer_id' => 'sometimes|required|exists:users,id',
            'packaging_location' => 'sometimes|required|string|max:255',
            'cleanliness_checklist' => 'sometimes|boolean'
        ]);

        try {
            $packaging->update($validated);

            return response()->json([
                'message' => 'Packaging updated successfully',
                'data' => $packaging
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating packaging',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Packaging  $packaging
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Packaging $packaging, Request $request)
    {
        $this->authorize('delete_packaging', $packaging);

        try {
            // Get the associated batch before deletion
            $batch = $packaging->batch;
            $packageId = $packaging->id;

            // Log the packaging deletion event
            try {
                if ($batch) {
                    $this->traceabilityService->logEvent(
                        batch: $batch,
                        eventType: TraceEvent::TYPE_PACKAGING_DELETED,
                        actor: Auth::user(),
                        data: [
                            'package_id' => $packageId,
                            'package_type' => $packaging->package_type,
                            'material_type' => $packaging->material_type,
                            'weight' => $packaging->weight . ' ' . $packaging->weight_unit,
                            'quantity' => $packaging->quantity_of_units,
                            'packaging_date' => $packaging->packaging_date,
                            'expiry_date' => $packaging->expiry_date,
                            'location' => 'Packaging Facility',
                            'ip_address' => request()->ip()
                        ]
                    );

                    // If this was the last package, update batch status
                    $remainingPackages = $batch->packages()->count();
                    if ($remainingPackages === 0) {
                        $batch->status = Batch::STATUS_QC_APPROVED; // Revert to QC approved if no packages left
                        $batch->save();
                    }
                }
            } catch (\Exception $e) {
                /*Log::error('Failed to log packaging deletion event: ' . $e->getMessage(), [
                    'package_id' => $packageId,
                    'batch_id' => $batch->id ?? null,
                    'trace' => $e->getTraceAsString()
                ]);*/
                // Continue with deletion even if logging fails
            }

            // Delete the packaging record
            $packaging->delete();
            return redirect()->route('admin.packaging.index')
                ->with('success', 'Packaging record deleted successfully');
            /*return response()->json([
                'success' => true,
                'message' => 'Packaging record deleted successfully',
                'batch_status' => $batch->status ?? null
            ]);*/


        } catch (\Exception $e) {
            /*Log::error('Failed to delete packaging record: ' . $e->getMessage(), [
                'package_id' => $packaging->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);*/

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete packaging record: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error deleting packaging: ' . $e->getMessage());
        }
    }

    /**
     * Update the status of the batch associated with the packaging.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Packaging  $packaging
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, Packaging $packaging)
    {
        // Check if user has permission to manage packaging status
        if (!Auth::user()->can('manage_packaging')) {
            return response()->json(['message' => 'Unauthorized. Manage Packaging permission required.'], 403);
        }

        $request->validate([
            'status' => 'required|string|in:' . implode(',', [
                Batch::STATUS_PACKAGING,
                Batch::STATUS_PACKAGED,
            ])
        ]);

        $batch = $packaging->batch;

        if (!$batch) {
            return response()->json(['message' => 'No batch associated with this packaging.'], 404);
        }

        $oldStatus = $batch->status;
        $newStatus = $request->status;

        if ($oldStatus !== $newStatus) {
            $batch->status = $newStatus;
            $batch->save();

            // Log the status change event
            $this->traceabilityService->logEvent(
                batch: $batch,
                eventType: 'status_change', // Using a generic string or define a constant if available
                actor: Auth::user(),
                data: [
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'reason' => 'Packaging status update',
                    'package_id' => $packaging->id,
                    'location' => 'Packaging Facility',
                    'ip_address' => $request->ip()
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'status' => $batch->status
        ]);
    }
}
