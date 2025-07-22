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
    public function index(Request $request)
    {
        // Define valid per page values
        $perPage = in_array($request->per_page, [5, 10, 20, 30, 50])
            ? (int)$request->per_page
            : 20; // Default to 20 if not specified or invalid
            
        $rpcUnits = RpcUnit::query()
            ->when($request->filled('rpc_identifier'), function($query) use ($request) {
                $query->where('rpc_identifier', 'like', '%' . $request->rpc_identifier . '%');
            })
            ->when($request->filled('material_type'), function($query) use ($request) {
                $query->where('material_type', $request->material_type);
            })
            ->when($request->filled('status'), function($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('capacity_kg'), function($query) use ($request) {
                $query->where('capacity_kg', $request->capacity_kg);
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
            
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
     * Remove the specified RPC unit from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        try {
            $rpcUnit = RpcUnit::findOrFail($id);
            $this->authorize('delete_packaging', $rpcUnit);
            
            $rpcUnit->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'RPC Unit deleted successfully',
                ]);
            }

            return redirect()->route('rpcunit.index')
                ->with('success', 'RPC Unit deleted successfully');

        } catch (\Exception $e) {
            Log::error('Error deleting RPC Unit: ' . $e->getMessage());
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete RPC Unit. ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Failed to delete RPC Unit: ' . $e->getMessage());
        }
    }
}
