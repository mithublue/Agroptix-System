<?php

namespace App\Http\Controllers;

use App\Http\Requests\QualityTestStoreRequest;
use App\Http\Requests\QualityTestUpdateRequest;
use App\Models\QualityTest;
use App\Models\Batch;
use App\Models\Product;
use App\Models\Source;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Models\TraceEvent;
use Illuminate\Support\Facades\Auth;
use App\Services\TraceabilityService;

class QualityTestController extends Controller
{
    protected $traceabilityService;

    public function __construct(TraceabilityService $traceabilityService)
    {
        $this->traceabilityService = $traceabilityService;
    }
    public function index(Request $request): View
    {
        $qualityTests = QualityTest::all();

        return view('qualityTest.index', [
            'qualityTests' => $qualityTests,
        ]);
    }

    public function batchList(Request $request)
    {
        $query = Batch::query()
            ->where(function ($builder) {
                $builder->whereRaw('LOWER(status) = ?', ['completed'])
                    ->orWhereRaw('LOWER(status) = ?', ['packaging']);
            })
            ->with(['product', 'source']);

        $search = trim((string) $request->input('batch_code', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('batch_code', 'like', "%{$search}%")
                    ->orWhere('trace_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        if ($request->filled('source_id')) {
            $query->where('source_id', $request->input('source_id'));
        }

        $result = trim((string) $request->input('result', ''));
        if ($result !== '') {
            if ($result === 'no_result') {
                $query->whereDoesntHave('qualityTests');
            } else {
                $query->whereHas('qualityTests', function ($builder) use ($result) {
                    $builder->whereRaw('LOWER(result) = ?', [strtolower($result)]);
                });
            }
        }

        $batches = $query
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        $readyBatchIds = $batches->getCollection()
            ->filter(fn(Batch $batch) => $batch->isReadyForPackaging())
            ->pluck('id')
            ->values();

        $products = Product::orderBy('name')
            ->get()
            ->mapWithKeys(fn(Product $product) => [$product->id => $product->name ?? "Product #{$product->id}"]);

        $sources = Source::orderBy('id')
            ->get()
            ->mapWithKeys(function (Source $source) {
                $label = $source->type
                    ? Str::title(str_replace('_', ' ', $source->type))
                    : 'Source';

                return [$source->id => $label . " (#{$source->id})"];
            });

        $resultOptions = QualityTest::query()
            ->select('result')
            ->whereNotNull('result')
            ->distinct()
            ->orderBy('result')
            ->pluck('result')
            ->filter()
            ->values();

        return view('qualityTest.batch-list', [
            'batches' => $batches,
            'readyBatchIds' => $readyBatchIds,
            'products' => $products,
            'sources' => $sources,
            'resultOptions' => $resultOptions,
        ]);
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

            $formattedTests = $tests->map(function ($test) {
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
    }

    public function markReadyForPackaging(Request $request, Batch $batch): JsonResponse
    {
        $user = $request->user();

        if (!$user || (!$user->can('create_quality_test') && !$user->can('edit_batch') && !$user->can('manage_batch'))) {
            abort(403, 'This action is unauthorized.');
        }

        $tests = $batch->qualityTests()->get(['result']);

        if ($tests->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No quality tests found for this batch.'
            ], 422);
        }

        $allPassed = $tests->every(fn($test) => strtolower((string) $test->result) === 'pass');

        if (!$allPassed) {
            return response()->json([
                'success' => false,
                'message' => 'All quality tests must pass before proceeding to the next stage.'
            ], 422);
        }

        if ($batch->isReadyForPackaging()) {
            return response()->json([
                'success' => true,
                'message' => 'Batch is already marked as ready for packaging.',
                'status' => $batch->status
            ]);
        }

        $batch->status = Batch::STATUS_PACKAGING;
        $batch->save();

        return response()->json([
            'success' => true,
            'message' => 'Batch marked as ready for packaging.',
            'status' => $batch->status
        ]);
    }

    /**
     * Proceed to next stage (packaging ready) for a batch if all quality tests pass
     */
    public function proceedToNextStage(Batch $batch)
    {
        // Check if all quality tests pass
        if (!$batch->allQualityTestsPassed()) {
            return response()->json([
                'success' => false,
                'message' => 'All quality tests must pass before proceeding to packaging.'
            ], 400);
        }

        try {
            // Update batch status to packaging ready
            $batch->status = Batch::STATUS_PACKAGING;
            $batch->save();

            // Log the event
            $this->traceabilityService->logEvent(
                batch: $batch,
                eventType: TraceEvent::TYPE_QC_APPROVED,
                actor: Auth::user(),
                data: [
                    'action' => 'proceeded_to_packaging',
                    'quality_tests_passed' => true
                ],
                location: 'Lab'
            );

            return response()->json([
                'success' => true,
                'message' => 'Batch is now ready for packaging.',
                'batch_status' => $batch->status
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to proceed to next stage: ' . $e->getMessage(), [
                'batch_id' => $batch->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to proceed to next stage: ' . $e->getMessage()
            ], 500);
        }
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
                'test_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            ]);
            if (!$request->hasFile('test_certificate')) {
                return response()->json(['success' => false, 'message' => 'No file uploaded.'], 400);
            }
            $file = $request->file('test_certificate');
            $path = $file->store('temp-certificates', 'public'); // store as temp
            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => url('storage/' . $path),
                'original_name' => $file->getClientOriginalName(),
                'message' => 'File uploaded temporarily.'
            ]);
        } catch (\Exception $e) {
            \Log::error('File upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error uploading file: ' . $e->getMessage()], 500);
        }
    }

    /**
     * AJAX endpoint to delete a test certificate file from storage
     */
    public function deleteCertificate(Request $request)
    {
        $request->validate([
            'path' => ['required', 'string'],
        ]);
        $path = $request->input('path');
        if (\Storage::disk('public')->exists($path)) {
            \Storage::disk('public')->delete($path);
            return response()->json(['success' => true, 'message' => 'File deleted.']);
        }
        return response()->json(['success' => false, 'message' => 'File not found.'], 404);
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
            'parameter_tested' => json_encode($request['parameters_tested'] ?? []),
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
        $validator = Validator::make($insertData, $rules);
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

        // Move temp file to permanent location if present
        if (!empty($validatedData['test_certificate']) && str_starts_with($validatedData['test_certificate'], 'temp-certificates/')) {
            $tempPath = $validatedData['test_certificate'];
            $finalPath = 'test-certificates/' . basename($tempPath);
            if (\Storage::disk('public')->exists($tempPath)) {
                \Storage::disk('public')->move($tempPath, $finalPath);
                $validatedData['test_certificate'] = $finalPath;
            }
        }

        // --- TRACESURE AUTO-VALIDATION START ---
        $batch = Batch::with(['product', 'source'])->find($validatedData['batch_id']);
        if ($batch && $batch->product && $batch->source) {
            $region = $batch->source->region ?? 'EU'; // Default to EU if not set
            $cropType = $batch->product->name; // Assuming product name maps to crop type

            // Get active standards for this context
            $standards = \App\Models\ComplianceStandard::where('is_active', true)
                ->where('region', $region)
                ->where('crop_type', $cropType)
                ->get();

            $autoFail = false;
            $validationRemarks = [];

            foreach ($standards as $standard) {
                // Check if this parameter was tested
                $paramKey = strtolower(str_replace(' ', '_', $standard->parameter_name));
                // We need to match the standard's parameter_name with the form's parameter keys
                // The form uses dynamic keys e.g., 'pesticide_residue_result'
                // We iterate through tested parameters to find a match
                foreach (($request['parameters_tested'] ?? []) as $testedParam) {
                    if (str_contains(strtolower($testedParam), $paramKey) || str_contains($paramKey, strtolower($testedParam))) {
                        $resultKey = $testedParam . '_result';
                        $measuredValue = $request[$resultKey] ?? null;

                        if ($measuredValue !== null && is_numeric($measuredValue)) {
                            // Check Max Value
                            if ($standard->max_value !== null && $measuredValue > $standard->max_value) {
                                $msg = "VIOLATION: {$standard->parameter_name} ({$measuredValue} {$standard->unit}) exceeds limit ({$standard->max_value} {$standard->unit})";
                                $validationRemarks[] = $msg;
                                if ($standard->critical_action === 'reject_batch') {
                                    $autoFail = true;
                                }
                            }
                        }
                    }
                }
            }

            if ($autoFail) {
                $validatedData['result'] = 'fail';
                $validationRemarks[] = "Result AUTO-FAILED due to critical compliance violation.";
            }

            if (!empty($validationRemarks)) {
                $existingRemarks = $validatedData['remarks'] ?? '';
                $validatedData['remarks'] = $existingRemarks . "\n\n[System Validation]:\n" . implode("\n", $validationRemarks);
            }
        }
        // --- TRACESURE AUTO-VALIDATION END ---

        $qualityTest = QualityTest::create($validatedData);

        // --- TRACESURE AUTO-REJECTION TRIGGER ---
        if ($autoFail) {
            try {
                // Log the rejection event securely on the blockchain ledger
                $this->traceabilityService->logEvent(
                    batch: $batch,
                    eventType: TraceEvent::TYPE_QC_REJECTION,
                    actor: Auth::user() ?? \App\Models\User::first(), // Fallback if automated
                    data: [
                        'test_id' => $qualityTest->id,
                        'reason' => 'Auto-Validation Protocol Violation',
                        'remarks' => implode("; ", $validationRemarks)
                    ],
                    location: 'Automated QC System',
                    ipAddress: $request->ip()
                );

                // Force Batch Status to REJECTED to prevent further processing
                // This is a "Kill Switch" for the batch
                $batch->status = 'qc_rejected'; // Using string directly or Batch::STATUS_QC_REJECTED
                $batch->save();
            } catch (\Exception $e) {
                \Log::error('Auto-Rejection Log Failed: ' . $e->getMessage());
            }
        }
        // --- END TRACESURE TRIGGER ---

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
        // Use the same validation and assignment logic as in store()
        $formRequest = new QualityTestStoreRequest();
        if (!$formRequest->authorize()) {
            abort(403, 'This action is unauthorized.');
        }
        $rules = $formRequest->rules();

        $updateData = [
            'batch_id' => $request['batch_id'] ?? $qualityTest->batch_id,
            'test_date' => $request['test_date'] ?? $qualityTest->test_date,
            'lab_name' => $request['lab_name'] ?? $qualityTest->lab_name,
            'parameter_tested' => json_encode($request['parameters_tested'] ?? []),
            'result' => $request['final_pass_fail'] ?? $qualityTest->result,
            'test_certificate' => $request['test_certificate'] ?? null, // Use the path from AJAX upload
            'remarks' => $request['remarks'] ?? $qualityTest->remarks,
        ];
        $result_status = [];
        foreach (($request['parameters_tested'] ?? []) as $param) {
            $resultKey = $param . '_result';
            if (isset($request[$resultKey])) {
                $result_status[$resultKey] = $request[$resultKey];
            }
        }
        $updateData['result_status'] = json_encode($result_status);
        // Handle test_certificate via AJAX or fallback
        if (!empty($request['test_certificate'])) {
            $updateData['test_certificate'] = $request['test_certificate'];
        } elseif (!empty($request['test_certificate_path'])) {
            $updateData['test_certificate'] = $request['test_certificate_path'];
        }
        // Validate
        $validator = Validator::make($updateData, $rules);
        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'errors' => $validator->errors(),
                ], 500);
            }
            return back()->withErrors($validator)->withInput()->setStatusCode(422);
        }
        $validatedData = $validator->validated();

        // Move temp file to permanent location if present
        if (!empty($validatedData['test_certificate']) && str_starts_with($validatedData['test_certificate'], 'temp-certificates/')) {
            $tempPath = $validatedData['test_certificate'];
            $finalPath = 'test-certificates/' . basename($tempPath);
            if (\Storage::disk('public')->exists($tempPath)) {
                \Storage::disk('public')->move($tempPath, $finalPath);
                $validatedData['test_certificate'] = $finalPath;
            }
        }

        $qualityTest->update($validatedData);
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Test updated successfully',
            ]);
        }
        return redirect()->route('quality-tests.batchList', $batch)->with('success', 'Test updated successfully');
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
