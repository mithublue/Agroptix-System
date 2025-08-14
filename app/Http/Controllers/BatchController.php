<?php

namespace App\Http\Controllers;

use App\Http\Requests\BatchStoreRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Requests\BatchUpdateRequest;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\SourceUpdateRequest;
use App\Models\Batch;
use App\Models\Source;
use App\Models\Product;
use App\Models\TraceEvent;
use App\Services\TraceabilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class BatchController extends Controller
{
    /**
     * The traceability service instance.
     */
    protected $traceabilityService;

    /**
     * Create a new controller instance.
     */
    public function __construct(TraceabilityService $traceabilityService)
    {
        $this->traceabilityService = $traceabilityService;

        // Apply middleware to specific methods
        $this->middleware('can:view_batch')->only(['show', 'showTimeline', 'showQrCode', 'listTraceEvents']);
    }

    /**
     * Bulk delete selected batches.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        abort_unless(auth()->user() && auth()->user()->can('delete_batch'), 403);

        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));
        if (empty($ids)) {
            return redirect()->to($request->input('redirect', route('batches.index')))
                ->with('error', 'No batches selected.');
        }

        $batches = Batch::whereIn('id', $ids)->get();
        $count = $batches->count();

        foreach ($batches as $batch) {
            try {
                // Log deletion similar to destroy()
                try {
                    $this->traceabilityService->logEvent(
                        batch: $batch,
                        eventType: \App\Models\TraceEvent::TYPE_DISPOSED,
                        actor: Auth::user(),
                        data: [
                            'batch_code' => $batch->batch_code,
                            'source_id' => $batch->source_id,
                            'product_id' => $batch->product_id,
                            'action' => 'deleted_bulk',
                            'ip_address' => $request->ip()
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to log batch bulk deletion event: ' . $e->getMessage(), [
                        'batch_id' => $batch->id,
                    ]);
                }
                $batch->delete();
            } catch (\Exception $e) {
                Log::error('Failed to delete batch in bulk: ' . $e->getMessage(), [
                    'batch_id' => $batch->id,
                ]);
            }
        }

        return redirect()->to($request->input('redirect', route('batches.index')))
            ->with('success', $count . ' batch(es) deleted successfully.');
    }

    public function index(Request $request): View
    {
        $query = Batch::with(['source', 'product']);

        // Apply status filter if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply source filter if provided
        if ($request->filled('source_id')) {
            $query->where('source_id', $request->source_id);
        }

        // Apply product filter if provided
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Type-sensitive search
        if ($request->filled('q')) {
            $q = trim((string) $request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('batch_code', 'like', "%{$q}%")
                    ->orWhere('trace_code', 'like', "%{$q}%")
                    ->orWhere('status', 'like', "%{$q}%");
                if (is_numeric($q)) {
                    $sub->orWhere('weight', '=', (float) $q)
                        ->orWhere('id', '=', (int) $q);
                }
                $sub->orWhereHas('product', function ($p) use ($q) {
                    $p->where('name', 'like', "%{$q}%");
                });
                $sub->orWhereHas('source', function ($s) use ($q) {
                    $s->where('type', 'like', "%{$q}%");
                });
            });
        }

        $batches = $query->latest()->paginate(10)->withQueryString();

        // Get distinct statuses for filter dropdown
        $statuses = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];

        // Get sources for filter dropdown
        $sources = Source::select('id', 'type')
            ->get()
            ->mapWithKeys(function ($source) {
                $display = $source->type
                    ? "{$source->type} (ID: {$source->id})"
                    : ($source->name ?: "Source #{$source->id}");
                return [$source->id => $display];
            });

        // Get products for filter dropdown
        $products = Product::pluck('name', 'id');

        $filters = $request->only(['status', 'source_id', 'product_id', 'q']);

        return view('batch.index', compact(
            'batches',
            'statuses',
            'sources',
            'products',
            'filters'
        ));
    }

    public function create(): View
    {
        // Format sources as 'Type (ID: X)' or 'Source #X' if type is empty
        $sources = Source::all()->mapWithKeys(function ($source) {
            $display = $source->type
                ? "{$source->type} (ID: {$source->id})"
                : "Source #{$source->id}";
            return [$source->id => $display];
        });

        // Get products with their names
        $products = Product::pluck('name', 'id');

        return view('batch.create', compact('sources', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Define validation rules
        $formRequest = new BatchStoreRequest();

        // Manually check authorization
        if (!$formRequest->authorize()) {
            abort(403, 'This action is unauthorized.');
        }

        // Get the validation rules from the Form Request class
        $rules = $formRequest->rules();
        $validator = Validator::make($request->all(), $rules);

        // Validate the request
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->setStatusCode(422);
        }

        // Get the validated data
        $validatedData = $validator->validated();

        try {
            // Create the batch
            $batch = Batch::create($validatedData);

            // Log the batch creation event
            try {
                $this->traceabilityService->logEvent(
                    batch: $batch,
                    eventType: TraceEvent::TYPE_HARVEST, // Using HARVEST as the initial event type for batch creation
                    actor: Auth::user(),
                    data: [
                        'source_id' => $batch->source_id,
                        'product_id' => $batch->product_id,
                        'weight' => $batch->weight,
                        'grade' => $batch->grade,
                        'location' => 'System'
                    ]
                );

                // If batch has a harvest time, log a harvest event
                if ($batch->harvest_time) {
                    $this->traceabilityService->logEvent(
                        batch: $batch,
                        eventType: TraceEvent::TYPE_HARVEST,
                        actor: Auth::user(),
                        data: [
                            'harvest_time' => $batch->harvest_time->toDateTimeString(),
                            'source_id' => $batch->source_id,
                            'location' => 'Field'
                        ]
                    );

                    // Update batch status to harvested
                    $batch->status = Batch::STATUS_HARVESTED;
                    $batch->save();
                }

            } catch (\Exception $e) {
                Log::error('Failed to log batch creation event: ' . $e->getMessage(), [
                    'batch_id' => $batch->id,
                    'trace' => $e->getTraceAsString()
                ]);
            }

            return redirect()
                ->route('batches.index')
                ->with('success', 'Batch created successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to create batch: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['_token'])
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to create batch. ' . $e->getMessage());
        }
    }

    public function show(Batch $batch): View
    {
        $batch->load(['source', 'product', 'traceEvents.actor']);
        $timeline = $batch->traceEvents()->with('actor')->latest()->paginate(10);
        
        return view('batch.show', [
            'batch' => $batch,
            'timeline' => $timeline
        ]);
    }

    public function edit(Batch $batch): View
    {
        $sources = Source::all()->mapWithKeys(function ($source) {
            $display = $source->type ?: "Source #{$source->id}";
            if ($source->owner) {
                $display .= " (Owner: {$source->owner->name})";
            }
            return [$source->id => $display];
        });
        $products = Product::pluck('name', 'id');
        return view('batch.edit', compact('batch', 'sources', 'products'));
    }

    public function update(Request $request, Batch $batch): RedirectResponse
    {
        // Get the original data before update
        $originalData = $batch->getOriginal();

        // Get the form request for validation and authorization
        $formRequest = new BatchUpdateRequest();

        // Manually check authorization
        if (!$formRequest->authorize()) {
            abort(403, 'This action is unauthorized.');
        }

        // Get the validation rules and validate
        $rules = $formRequest->rules();
        $validator = Validator::make($request->all(), $rules);

        // Validate the request
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->setStatusCode(422);
        }

        // Get the validated data
        $validatedData = $validator->validated();

        try {
            // Update the batch
            $batch->update($validatedData);

            // Log the batch update event if there are changes
            $changes = [];
            foreach ($validatedData as $key => $value) {
                if (array_key_exists($key, $originalData) && $originalData[$key] != $value) {
                    $changes[$key] = [
                        'old' => $originalData[$key],
                        'new' => $value
                    ];
                }
            }

            if (!empty($changes)) {
                try {
                    $this->traceabilityService->logEvent(
                        batch: $batch,
                        eventType: TraceEvent::TYPE_PROCESSING, // Using PROCESSING as the event type for batch updates
                        actor: Auth::user(),
                        data: [
                            'changes' => $changes,
                            'reason' => $request->input('update_reason', 'No reason provided'),
                            'location' => 'System',
                            'ip_address' => $request->ip()
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to log batch update event: ' . $e->getMessage(), [
                        'batch_id' => $batch->id,
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            return redirect()
                ->route('batches.index')
                ->with('success', 'Batch updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to update batch: ' . $e->getMessage(), [
                'batch_id' => $batch->id,
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['_token', '_method'])
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to update batch. ' . $e->getMessage());
        }
    }

    public function destroy(Batch $batch)
    {
        try {
            // Log the batch deletion event
            try {
                $this->traceabilityService->logEvent(
                    batch: $batch,
                    eventType: TraceEvent::TYPE_DISPOSED, // Using DISPOSED as the closest match for deletion
                    actor: Auth::user(),
                    data: [
                        'batch_code' => $batch->batch_code,
                        'source_id' => $batch->source_id,
                        'product_id' => $batch->product_id,
                        'action' => 'deleted', // Adding action to clarify this is a deletion
                        'ip_address' => request()->ip()
                    ]
                );
            } catch (\Exception $e) {
                Log::error('Failed to log batch deletion event: ' . $e->getMessage(), [
                    'batch_id' => $batch->id,
                    'trace' => $e->getTraceAsString()
                ]);
            }

            // Delete the batch
            $batch->delete();

            return redirect()->route('batches.index')
                ->with('success', 'Batch deleted successfully');

        } catch (\Exception $e) {
            Log::error('Failed to delete batch: ' . $e->getMessage(), [
                'batch_id' => $batch->id,
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to delete batch: ' . $e->getMessage());
        }
    }

    /**
     * Update the status of the specified batch.
     */
    public function updateStatus(Request $request, Batch $batch)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        try {
            $batch->update(['status' => $request->status]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Batch status updated successfully.',
                    'data' => [
                        'status' => $batch->status,
                        'status_display' => ucfirst($batch->status)
                    ]
                ]);
            }

            return back()->with('success', 'Batch status updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Error updating batch status: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating batch status: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error updating batch status.');
        }
    }

    /**
     * Display the batch timeline.
     */
    public function showTimeline(Batch $batch)
    {
        try {
            $timeline = $batch->traceEvents()
                ->with('actor')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('batch.timeline', [
                'batch' => $batch,
                'timeline' => $timeline,
                'title' => 'Timeline: ' . $batch->batch_code
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to load batch timeline: ' . $e->getMessage(), [
                'batch_id' => $batch->id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to load batch timeline. Please try again.');
        }
    }

    /**
     * Display the QR code for the batch.
     *
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\View\View
     */
    public function showQrCode(Batch $batch)
    {
        try {
            // Generate the QR code URL for this batch's timeline
            $qrCodeUrl = route('batches.timeline', $batch->trace_code);
            
            // Generate the QR code as an SVG string
            $qrCode = QrCode::format('svg')
                ->size(300)
                ->generate($qrCodeUrl);
            
            return view('batch.qr-code', [
                'batch' => $batch,
                'qrCode' => $qrCode,
                'title' => 'QR Code: ' . $batch->batch_code
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to generate QR code: ' . $e->getMessage(), [
                'batch_id' => $batch->id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to generate QR code. Please try again.');
        }
    }

    /**
     * List all trace events for the batch.
     */
    public function listTraceEvents(Batch $batch)
    {
        $events = $this->traceabilityService->getTraceEvents($batch->trace_code);

        return view('batch.trace-events', [
            'batch' => $batch,
            'events' => $events,
            'title' => 'Trace Events: ' . $batch->batch_code
        ]);
    }
}
