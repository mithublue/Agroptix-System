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

        \Log::info('Fetching quality tests for batch:', [
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
            \Log::debug('Quality tests query:', [
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

            \Log::debug('Raw tests from database:', $tests->toArray());

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

            \Log::info('Returning response:', $response);

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
            \Log::error($errorMessage, [
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
                'redirect' => route('quality-tests.batchList')
            ]);
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

    public function destroy(Request $request, Batch $batch, QualityTest $quality_test)
    {
        // Verify that the quality test belongs to the batch
        if ($quality_test->batch_id !== $batch->id) {
            $message = 'Quality test does not belong to this batch';
            
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $message], 422)
                : back()->with('error', $message);
        }

        $quality_test->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Quality test deleted successfully'
            ]);
        }

        return redirect()
            ->route('batches.quality-tests.index', $batch)
            ->with('success', 'Quality test deleted successfully');
    }
}
