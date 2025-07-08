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
    /**
     * Display a listing of all quality tests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $qualityTests = QualityTest::with(['batch', 'user'])
            ->latest()
            ->paginate(20);

        return view('qualityTest.index', [
            'qualityTests' => $qualityTests,
        ]);
    }

    /**
     * Display quality tests for a specific batch.
     *
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\View\View
     */
    public function batchTests(Batch $batch)
    {
        $qualityTests = $batch->qualityTests()
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('qualityTest.batch-tests', [
            'batch' => $batch,
            'qualityTests' => $qualityTests,
        ]);
    }

    /**
     * Show the form for creating a new quality test.
     *
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function create(Batch $batch)
    {
        // If it's an AJAX request, return the form HTML
        if (request()->ajax() || request()->wantsJson()) {
            return view('qualityTest.partials.form', [
                'batch' => $batch
            ]);
        }

        // For regular requests, return the full view
        return view('qualityTest.create', [
            'batch' => $batch
        ]);
    }

    public function batchList(Request $request)
    {
        try {
            $batches = Batch::where('status', 'Completed')
                ->with(['product' => function($query) {
                    $query->select('id', 'name');
                }])
                ->orderBy('created_at', 'desc')
                ->select('id', 'batch_code', 'product_id', 'harvest_time', 'status', 'created_at')
                ->paginate(10);

            return view('qualityTest.batch-list', compact('batches'));

        } catch (\Exception $e) {
            \Log::error('Error in batchList: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            // Return a more helpful error response
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load batches. ' . $e->getMessage(),
                    'data' => []
                ], 500);
            }

            // For regular requests, redirect back with error
            return back()->with('error', 'Failed to load batches. Please try again.');
        }
    }

    /**
     * Store a newly created quality test in storage.
     *
     * @param  \App\Http\Requests\QualityTestStoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Store a newly created quality test in storage.
     *
     * @param  \App\Http\Requests\QualityTestStoreRequest  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(QualityTestStoreRequest $request)
    {
        try {
            // Prepare result status for different parameters
            $resultStatus = [];
            foreach (($request->parameters_tested ?? []) as $param) {
                $resultKey = $param . '_result';
                if ($request->has($resultKey)) {
                    $resultStatus[$param] = $request->$resultKey;
                } else {
                    $resultStatus[$param] = $request->final_pass_fail;
                }
            }

            // Create the quality test
            $qualityTest = QualityTest::create([
                'batch_id' => $request->batch_id,
                'user_id' => auth()->id(),
                'parameter_tested' => json_encode($request->parameters_tested),
                'result' => $request->final_pass_fail,
                'result_status' => json_encode($resultStatus),
                'test_certificate' => $request->test_certificate,
                'remarks' => $request->remarks,
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Quality test created successfully',
                    'data' => $qualityTest
                ]);
            }

            return redirect()->route('quality-tests.index')
                ->with('success', 'Quality test created successfully');

        } catch (\Exception $e) {
            \Log::error('Error creating quality test: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create quality test',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->withInput()
                ->with('error', 'Failed to create quality test. Please try again.');
        }
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
