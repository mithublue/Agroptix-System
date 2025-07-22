<?php

namespace App\Http\Controllers;

use App\Models\Packaging;
use Illuminate\Http\Request;

class PackagingController extends Controller
{
    /**
     * Display a listing of the packaging records.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->authorize('view_packaging', Packaging::class);
        
        $packages = Packaging::with('batch')
            ->latest()
            ->paginate(15);
            
        return view('admin.packaging.index', compact('packages'));
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
        $this->authorize('update_packaging', $packaging);
        
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Packaging $packaging)
    {
        $this->authorize('delete_packaging', $packaging);
        
        try {
            $packaging->delete();
            
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Packaging deleted successfully'
                ]);
            }
            
            return redirect()->route('admin.packaging.index')
                ->with('success', 'Packaging deleted successfully');
                
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error deleting packaging',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error deleting packaging: ' . $e->getMessage());
        }
    }
}
