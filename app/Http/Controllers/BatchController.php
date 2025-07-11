<?php

namespace App\Http\Controllers;

use App\Http\Requests\BatchStoreRequest;
use App\Http\Requests\BatchUpdateRequest;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\SourceUpdateRequest;
use App\Models\Batch;
use App\Models\Source;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class BatchController extends Controller
{

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

        $filters = $request->only(['status', 'source_id', 'product_id']);

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
        //    to get access to its rules and authorization logic.
        $formRequest = new BatchStoreRequest();

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
            // 6. Manually handle the failure.
            //    To make it work with Hotwired Turbo, we must set the 422 status code.
            return back()->withErrors($validator)->withInput()->setStatusCode(422);
        }

        // 7. If validation passes, get the validated data.
        $validatedData = $validator->validated();

        // Create the Source record
        try {
            Batch::create($validatedData);
            return redirect()
                ->route('batches.index')
                ->with('success', 'Batch created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create batch. ' . $e->getMessage());
        }
    }

    public function show(Batch $batch): View
    {
        $batch->load(['source', 'product']);
        return view('batch.show', compact('batch'));
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
        //    to get access to its rules and authorization logic.
        $formRequest = new BatchUpdateRequest();

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
            // 6. Manually handle the failure.
            //    To make it work with Hotwired Turbo, we must set the 422 status code.
            return back()->withErrors($validator)->withInput()->setStatusCode(422);
        }

        // 7. If validation passes, get the validated data.
        $validatedData = $validator->validated();

        try {
            $batch->update($validatedData);
            return redirect()
                ->route('batches.index')
                ->with('success', 'Batch updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update batch. ' . $e->getMessage());
        }
    }

    public function destroy(Batch $batch)
    {
        $batch->delete();

        return redirect()->route('batches.index')
            ->with('success', 'Batch deleted successfully');
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
}
