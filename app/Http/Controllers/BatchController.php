<?php

namespace App\Http\Controllers;

use App\Http\Requests\BatchStoreRequest;
use App\Http\Requests\BatchUpdateRequest;
use App\Models\Batch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BatchController extends Controller
{
    public function index(Request $request): Response
    {
        $batches = Batch::all();

        return view('batch.index', [
            'batches' => $batches,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('batch.create');
    }

    public function store(BatchStoreRequest $request): Response
    {
        $batch = Batch::create($request->validated());

        $request->session()->flash('batch.id', $batch->id);

        return redirect()->route('batches.index');
    }

    public function show(Request $request, Batch $batch): Response
    {
        return view('batch.show', [
            'batch' => $batch,
        ]);
    }

    public function edit(Request $request, Batch $batch): Response
    {
        return view('batch.edit', [
            'batch' => $batch,
        ]);
    }

    public function update(BatchUpdateRequest $request, Batch $batch): Response
    {
        $batch->update($request->validated());

        $request->session()->flash('batch.id', $batch->id);

        return redirect()->route('batches.index');
    }

    public function destroy(Request $request, Batch $batch): Response
    {
        $batch->delete();

        return redirect()->route('batches.index');
    }
}
