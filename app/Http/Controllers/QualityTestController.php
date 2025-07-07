<?php

namespace App\Http\Controllers;

use App\Http\Requests\QualityTestStoreRequest;
use App\Http\Requests\QualityTestUpdateRequest;
use App\Models\QualityTest;
use App\Models\Batch;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class QualityTestController extends Controller
{
    public function index(Request $request): View
    {
        $qualityTests = QualityTest::all();

        return view('qualityTest.index', [
            'qualityTests' => $qualityTests,
        ]);
    }

    public function batchList(Request $request)
    {
        $batches = Batch::where('status', 'Completed')
                       ->with('product')
                       ->orderBy('created_at', 'desc')
                       ->paginate(10);

        return view('qualityTest.batch-list', compact('batches'));
    }

    public function create(Batch $batch): View
    {
        return view('qualityTest.create', compact('batch'));
    }

    public function store(Request $request)
    {
        // Define validation rules
        //    to get access to its rules and authorization logic.
        $formRequest = new QualityTestStoreRequest();

        // 2. Manually check authorization (IMPORTANT: This is NOT done automatically now)
        if (!$formRequest->authorize()) {
            abort(403, 'This action is unauthorized.');
        }

        // 3. Get the validation rules from your Form Request class.
        $rules = $formRequest->rules();

        //add new value to request
        $insertData = [
            'batch_id' => $request['batch_id'] ?? null,
            'parameters_tested' => json_encode($request['parameters_tested'] ?? [] ),
            'result' => $request['final_pass_fail'] ?? null,
            'test_certificate' => $request['test_certificate'] ?? null,
            'remarks' => $request['remarks'] ?? null,
        ];

        //result_status for different parameters
        $result_status = [];
        foreach (($request['parameters_tested'] ?? []) as $param) {
            $resultKey = $param . '_result';
            if (isset($request[$resultKey])) {
                $result_status[$resultKey] = $request[$resultKey];
            }
        }
        $insertData['result_status'] = json_encode($result_status);

        // 4. Create a new validator instance manually.
        $validator = Validator::make( $insertData, $rules);

        // Validate the request
        if ($validator->fails()) {

            // 6. Manually handle the failure.
            //    To make it work with Hotwired Turbo, we must set the 422 status code.
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                ], 500);
            }
            return back()->withErrors($validator)->withInput()->setStatusCode(422);
        }

        // 7. If validation passes, get the validated data.
        $validatedData = $validator->validated();

        QualityTest::create($validatedData);


        // If this is an AJAX request, return JSON response
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Test created successfully',
                'redirect' => route('quality-tests.batchList')
            ]);
        }
        ///////
    }

    public function show(Request $request, QualityTest $qualityTest): Response
    {
        return view('qualityTest.show', [
            'qualityTest' => $qualityTest,
        ]);
    }

    public function edit(Request $request, QualityTest $qualityTest): Response
    {
        return view('qualityTest.edit', [
            'qualityTest' => $qualityTest,
        ]);
    }

    public function update(QualityTestUpdateRequest $request, QualityTest $qualityTest): Response
    {
        $qualityTest->update($request->validated());

        $request->session()->flash('qualityTest.id', $qualityTest->id);

        return redirect()->route('qualityTests.index');
    }

    public function destroy(Request $request, QualityTest $qualityTest): Response
    {
        $qualityTest->delete();

        return redirect()->route('qualityTests.index');
    }
}
