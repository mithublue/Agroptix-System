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

    public function index(): View
    {
        $batches = Batch::with(['source', 'product'])->latest()->paginate(10);
        return view('batch.index', compact('batches'));
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

    public function destroy(Batch $batch): RedirectResponse
    {
        try {
            $batch->delete();
            return redirect()
                ->route('batches.index')
                ->with('success', 'Batch deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete batch. ' . $e->getMessage());
        }
    }
}
