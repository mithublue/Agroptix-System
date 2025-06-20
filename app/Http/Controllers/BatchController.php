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
    public function __construct()
    {
        $this->middleware('can:view_batch')->only(['index', 'show']);
        $this->middleware('can:create_batch')->only(['create', 'store']);
        $this->middleware('can:edit_batch')->only(['edit', 'update']);
        $this->middleware('can:delete_batch')->only('destroy');
    }

    public function index(): View
    {
        $batches = Batch::with(['source', 'product'])->latest()->paginate(10);
        return view('batch.index', compact('batches'));
    }

    public function create(): View
    {
        $sources = Source::pluck('name', 'id');
        $products = Product::pluck('name', 'id');
        return view('batch.create', compact('sources', 'products'));
    }

    public function store(BatchStoreRequest $request): RedirectResponse
    {
        try {
            $batch = Batch::create($request->validated());
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
