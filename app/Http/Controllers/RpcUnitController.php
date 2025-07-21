<?php

namespace App\Http\Controllers;

use App\Models\RpcUnit;
use Illuminate\Http\Request;

class RpcUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $rpcUnits = RpcUnit::paginate(20);
        return view('rpcunit.index', compact('rpcUnits'));
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
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return response()->json([
            'message' => 'RPC Unit created successfully',
            'data' => $request->all()
        ], 200);
        try {
            $validated = $request->validate([
                'rpc_identifier' => 'required|string|max:255|unique:rpc_units,rpc_identifier',
                'capacity_kg' => 'required|numeric|min:0',
                'material_type' => 'required|string|in:plastic,metal,wood,other',
                'status' => 'required|string|in:active,in_use,maintenance,retired',
                'total_wash_cycles' => 'sometimes|integer|min:0',
                'total_reuse_count' => 'sometimes|integer|min:0',
                'initial_purchase_date' => 'sometimes|date',
                'last_washed_date' => 'nullable|date',
                'current_location' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
            ]);

            $rpcUnit = RpcUnit::create($validated);

            return response()->json([
                'message' => 'RPC Unit created successfully',
                'data' => $rpcUnit
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create RPC Unit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
