<?php

namespace App\Http\Controllers;

use App\Http\Requests\QualityTestStoreRequest;
use App\Http\Requests\QualityTestUpdateRequest;
use App\Models\QualityTest;
use App\Models\Batch;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
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

    /**
     * Get quality tests for a specific batch
     *
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function getTestsForBatch(Batch $batch)
    {
        // Enable error reporting for debugging
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        Log::info('Fetching quality tests for batch:', [
            'batch_id' => $batch->id,
            'batch_code' => $batch->batch_code,
            'exists' => $batch->exists,
            'wasRecentlyCreated' => $batch->wasRecentlyCreated,
            'class' => get_class($batch)
        ]);
        try {
            // First ensure the batch exists and is loaded
            if (!$batch->exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Batch not found',
                    'data' => []
                ], 404);
            }

            // Log the relationship query
            $query = $batch->qualityTests();
            Log::debug('Quality tests query:', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            // Get the tests with error handling
            $tests = $query
                ->select([
                    'id',
                    'batch_id',
                    'user_id',
                    'parameter_tested',
                    'result',
                    'result_status',
                    'test_certificate'
                ])
                ->orderBy('id', 'desc')
                ->get();

            Log::debug('Raw tests from database:', $tests->toArray());

            $formattedTests = $tests->map(function($test) {
                return [
                    'id' => $test->id,
                    'batch_id' => $test->batch_id,
                    'user_id' => $test->user_id,
                    'parameter_tested' => $test->parameter_tested,
                    'result' => $test->result,
                    'result_status' => $test->result_status,
                    'test_certificate' => $test->test_certificate,
                    'remarks' => $test->remarks,
                    'created_at' => $test->created_at,
                    'updated_at' => $test->updated_at,
                    'created_at_formatted' => optional($test->created_at)->format('Y-m-d H:i:s'),
                    'updated_at_formatted' => optional($test->updated_at)->format('Y-m-d H:i:s'),
                ];
            });

            $response = [
                'success' => true,
                'data' => $formattedTests,
                'message' => $formattedTests->isEmpty() ? 'No tests found' : 'Tests retrieved successfully'
            ];

            Log::info('Returning response:', $response);

            // Return response with proper headers
            return response()->json(
                $response,
                200,
                [
                    'Content-Type' => 'application/json; charset=UTF-8',
                    'Charset' => 'utf-8'
                ],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
            );

        } catch (\Exception $e) {
            $errorMessage = 'Error fetching quality tests: ' . $e->getMessage();
            Log::error($errorMessage, [
                'batch_id' => $batch->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => $errorMessage,
                    'data' => []
                ],
                500,
                [
                    'Content-Type' => 'application/json; charset=UTF-8',
                    'Charset' => 'utf-8'
                ],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
            );
        }

            // Error response is already handled above
        }

    /**
     * Handle AJAX file upload for test certificates
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadCertificate(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'test_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            ]);

            if (!$request->hasFile('test_certificate')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file uploaded.',
                ], 400);
            }

            $file = $request->file('test_certificate');
            $path = $file->store('test-certificates', 'public');

            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
                'original_name' => $file->getClientOriginalName(),
                'message' => 'File uploaded successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('File upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error uploading file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\View\View
     */
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
            'test_date' => $request['test_date'] ?? null,
            'lab_name' => $request['lab_name'] ?? null,
            'parameter_tested' => json_encode($request['parameters_tested'] ?? [] ),
            'result' => $request['final_pass_fail'] ?? null,
            'test_certificate' => $request['test_certificate'] ?? null, // Use the path from AJAX upload
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
                    'errors' => $validator->errors(),
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
            ]);
        }
        return back()->withErrors($validator)->withInput()->setStatusCode(422);
    }

    public function edit(Request $request, Batch $batch, QualityTest $qualityTest): View
    {
        // If $qualityTest is not a model instance, try to find it
        if (!($qualityTest instanceof \App\Models\QualityTest)) {
            $qualityTest = \App\Models\QualityTest::findOrFail($qualityTest);
        }

        // Parse the JSON fields
        $parametersTested = json_decode($qualityTest->parameter_tested, true) ?? [];
        $resultStatus = json_decode($qualityTest->result_status, true) ?? [];

        // Structure the form data
        $formData = [
            'batch_id' => $qualityTest->batch_id,
            'test_date' => $qualityTest->test_date,
            'lab_name' => $qualityTest->lab_name,
            'technician_name' => $qualityTest->user?->name ?? '',
            'parameters_tested' => $parametersTested,
            'test_certificate' => $qualityTest->test_certificate,
            'remarks' => $qualityTest->remarks,
            'final_pass_fail' => $qualityTest->result,
        ];

        // Add result_status fields to form data
        if (is_array($resultStatus)) {
            foreach ($resultStatus as $key => $value) {
                $formData[$key] = $value;
            }
        }

        // Convert to JSON for JavaScript
        $formDataJson = json_encode($formData, JSON_HEX_APOS | JSON_HEX_QUOT);

        return view('qualityTest.create', [
            'isEdit' => true,
            'qualityTest' => $qualityTest,
            'formData' => $formData,
            'formDataJson' => $formDataJson,
            'batch' => $batch
        ]);
    }

    public function update(Request $request, $batch, QualityTest $qualityTest)
    {
        try {
            // Get and deduplicate parameters_tested array from the request
            $parametersTested = array_unique($request->input('parameters_tested', []));

            // Prepare the data for update
            $updateData = [
                'test_date' => $request->input('test_date'),
                'lab_name' => $request->input('lab_name'),
                'parameter_tested' => json_encode(array_values($parametersTested)), // Ensure sequential array
                'result' => $request->input('final_pass_fail'),
                'remarks' => $request->input('remarks'),
            ];

            // Process result_status for different parameters
            $result_status = [];
            foreach ($parametersTested as $param) {
                $resultKey = $param . '_result';
                if ($request->has($resultKey)) {
                    $result_status[$resultKey] = $request->input($resultKey);
                }
            }
            $updateData['result_status'] = json_encode($result_status);

            // Handle file upload if present
            if ($request->hasFile('test_certificate')) {
                $path = $request->file('test_certificate')->store('test-certificates', 'public');
                $updateData['test_certificate'] = $path;
            }

            // Log the test update event
            try {
                $this->traceabilityService->logEvent(
                    batch: $batch,
                    eventType: TraceEvent::TYPE_QC_UPDATE,
                    actor: Auth::user(),
                    data: [
                        'test_id' => $qualityTest->id,
                        'test_type' => $qualityTest->test_type,
                        'update_data' => $updateData
                    ],
                    location: 'Lab',
                    ipAddress: $request->ip()
                );
            } catch (\Exception $e) {
                Log::error('Failed to log test update event: ' . $e->getMessage(), [
                    'test_id' => $qualityTest->id,
                    'batch_id' => $batch->id ?? null,
                    'trace' => $e->getTraceAsString()
                ]);
                // Continue with the response even if logging fails
            }

            return response()->json([
                'success' => true,
                'message' => 'Test updated successfully',
                'redirect' => route('quality-tests.batchList', $batch)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating test: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rejectTest(Request $request, QualityTest $qualityTest)
    {
        try {
            // Validate the request
            $request->validate([
                'rejection_reason' => 'required|string|max:1000',
                'rejection_date' => 'required|date',
                'corrective_action' => 'nullable|string|max:1000'
            ]);

            // Update the test status
            $qualityTest->update([
                'status' => 'rejected',
                'rejected_by' => Auth::id(),
                'rejected_at' => now(),
                'rejection_reason' => $request->input('rejection_reason'),
                'corrective_action' => $request->input('corrective_action')
            ]);

            // Get the associated batch
            $batch = $qualityTest->batch;

            // Log the test rejection event
            try {
                if ($batch) {
                    $this->traceabilityService->logEvent(
                        batch: $batch,
                        eventType: TraceEvent::TYPE_QC_REJECTION,
                        actor: Auth::user(),
                        data: [
                            'test_id' => $qualityTest->id,
                            'test_type' => $qualityTest->test_type,
                            'rejection_reason' => $request->input('rejection_reason'),
                            'corrective_action' => $request->input('corrective_action'),
                            'rejection_date' => $request->input('rejection_date')
                        ],
                        location: 'Lab',
                        ipAddress: $request->ip()
                    );

                    // Update batch status to QC rejected
                    $batch->status = Batch::STATUS_QC_REJECTED;
                    $batch->save();
                }

            } catch (\Exception $e) {
                Log::error('Failed to log test rejection event: ' . $e->getMessage(), [
                    'test_id' => $qualityTest->id,
                    'batch_id' => $batch->id ?? null,
                    'trace' => $e->getTraceAsString()
                ]);
                // Continue with the response even if logging fails
            }

            return response()->json([
                'success' => true,
                'message' => 'Test rejected successfully',
                'data' => $qualityTest,
                'batch_status' => $batch->status ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to reject test: ' . $e->getMessage(), [
                'test_id' => $qualityTest->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject test: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, Batch $batch, QualityTest $quality_test): JsonResponse
    {
        // Verify that the quality test belongs to the batch
        if ($quality_test->batch_id !== $batch->id) {
            return response()->json([
                'success' => false,
                'message' => 'Quality test does not belong to this batch'
            ], 422);
        }

        $quality_test->delete();

        return response()->json([
            'success' => true,
            'message' => 'Quality test deleted successfully'
        ]);
    }
}
