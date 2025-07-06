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
        $request->merge([
            'batch_id' => $request['batch_id'] ?? null,
            'parameters_tested' => $request['parameters_tested'] ?? [],
            'result' => $request['final_pass_fail'] ?? null,
            'test_certificate' => $formData['test_certificate'] ?? null,
            'remarks' => $formData['remarks'] ?? null,
        ]);

        //result_status for different parameters
        $result_status = [];
        foreach (($request['parameters_tested'] ?? []) as $param) {
            $resultKey = $param . '_result';
            if (isset($formData[$resultKey])) {
                $result_status[$resultKey] = $request[$resultKey];
            }
        }
        $request->merge([
            'result_status' => $result_status,
        ]);


        // 4. Create a new validator instance manually.
        $validator = Validator::make($request->all(), $rules);

        // Validate the request
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
//            'formData' => $formData,
                'message' => $request['batch_id'],
            ], 500);
            // 6. Manually handle the failure.
            //    To make it work with Hotwired Turbo, we must set the 422 status code.
            return back()->withErrors($validator)->withInput()->setStatusCode(422);
        }

        // 7. If validation passes, get the validated data.
        $validatedData = $validator->validated();

        // Create the Source record
        try {
            QualityTest::create($validatedData);
            return redirect()
                ->route('quality-tests.batchList')
                ->with('success', 'Test created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create test. ' . $e->getMessage());
        }
        ///////
        try {
            // Handle JSON data if it's sent as formData
            if ($request->has('formData')) {
                $formData = json_decode($request->formData, true);

                // Extract batch_id and final_pass_fail from formData
                $request->merge([
                    'batch_id' => $formData['batch_id'] ?? null,
                    'result_status' => $formData['final_pass_fail'] ?? null,
                    'parameters_tested' => $formData['parameters_tested'] ?? [],
                    'test_certificate' => $formData['test_certificate'] ?? null,
                    'remarks' => $formData['remarks'] ?? null,
                ]);

                // Add parameter results
                foreach (($formData['parameters_tested'] ?? []) as $param) {
                    $resultKey = $param . '_result';
                    if (isset($formData[$resultKey])) {
                        $request->merge([$resultKey => $formData[$resultKey]]);
                    }
                }
            }

            // Get validation rules
            $formRequest = new QualityTestStoreRequest();
            $rules = $formRequest->rules();

            // Validate the request
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();
            $validated['user_id'] = auth()->id();

            // Handle file upload if present
            $certificatePath = null;
            if ($request->hasFile('test_certificate')) {
                $certificatePath = $request->file('test_certificate')->store('test-certificates', 'public');
            }

            // Create a new quality test record
            $qualityTest = new QualityTest();
            $qualityTest->batch_id = $validated['batch_id'];
            $qualityTest->user_id = $validated['user_id'];
            $qualityTest->parameter_tested = json_encode($validated['parameters_tested'] ?? []);

            // Store parameter results
            $results = [];
            foreach (($validated['parameters_tested'] ?? []) as $param) {
                $resultKey = $param . '_result';
                if (isset($validated[$resultKey])) {
                    $results[$param] = $validated[$resultKey];
                }
            }

            $qualityTest->result = json_encode($results);
            $qualityTest->result_status = $validated['result_status'] ?? null;
            $qualityTest->test_certificate_path = $certificatePath;
            $qualityTest->remarks = $validated['remarks'] ?? null;
            $qualityTest->save();

            // Update batch status based on test result
            $batch = Batch::find($validated['batch_id']);
            if ($batch) {
                $batch->status = ($validated['result_status'] ?? '') === 'pass' ? 'Approved' : 'Rejected';
                $batch->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Quality test saved successfully',
                'redirect' => route('batches.show', $validated['batch_id'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving quality test: ' . $e->getMessage()
            ], 500);
        }
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
