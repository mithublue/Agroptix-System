<?php

namespace App\Http\Controllers;

use App\Http\Requests\BatchStoreRequest;
use App\Http\Requests\BatchUpdateRequest;
use App\Models\Batch;
use App\Models\Source;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    public function store(BatchStoreRequest $request): RedirectResponse
    {
        try {
            // Debug the validated data
            \Log::info('Creating batch with data:', $request->validated());

            $batch = Batch::create($request->validated());

            // Debug the created batch
            \Log::info('Batch created successfully:', ['batch_id' => $batch->id]);

            return redirect()
                ->route('batches.index')
                ->with('success', 'Batch created successfully.');
        } catch (\Exception $e) {
            // Log the full error
            \Log::error('Error creating batch:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);

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
        $sources = Source::pluck('name', 'id');
        $products = Product::pluck('name', 'id');
        return view('batch.edit', compact('batch', 'sources', 'products'));
    }

    public function update(BatchUpdateRequest $request, Batch $batch): RedirectResponse
    {
        try {
            $batch->update($request->validated());
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
