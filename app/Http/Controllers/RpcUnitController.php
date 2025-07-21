<?php

namespace App\Http\Controllers;

use App\Models\RpcUnit;
use App\Http\Requests\RpcUnitStoreRequest;
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
    public function store(RpcUnitStoreRequest $request)
    {
        $this->authorize('create_packaging');

        try {
            // Create the RPC Unit with validated data
            $rpcUnit = RpcUnit::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'RPC Unit created successfully.',
                'data' => $rpcUnit
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating RPC Unit: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create RPC Unit. ' . $e->getMessage(),
                //get validation errors

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
