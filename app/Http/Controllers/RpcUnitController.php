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
    public function store(Request $request)
    {
        $this->authorize('create_packaging' );

        // Define validation rules
        //    to get access to its rules and authorization logic.
        $formRequest = new RpcUnitStoreRequest();

        // 2. Manually check authorization (IMPORTANT: This is NOT done automatically now)
        if (!$formRequest->authorize()) {
            abort(403, 'This action is unauthorized.');
        }

        // 3. Get the validation rules from your Form Request class.
        $rules = $formRequest->rules();

        // 4. Create a new validator instance manually.
        $validator = Validator::make($request->all(), $rules);

        // Validate the request
        if ($validator->fails()) {
            // If this is an AJAX request, return JSON response
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'RPC unit creation failed.',
                    'errors' => $validator->errors(),
                ]);
            }
            return back()->withErrors($validator)->withInput()->setStatusCode(422);
        }

        // 7. If validation passes, get the validated data.
        $validatedData = $validator->validated();


        // Create the Source record
        try {
            RpcUnit::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'RPC Unit created successfully.',
//                'redirect' => route('batches.eco-processes.index', $batch)
            ]);

            return redirect()
                ->route('rpcunit.index')
                ->with('success', 'RPC Unit created successfully.');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.' . $e->getMessage()
            ]);
            return back()
                ->withInput()
                ->with('error', 'Failed to create source. ' . $e->getMessage());
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
