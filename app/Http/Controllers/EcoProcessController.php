<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\EcoProcess;
use App\Http\Requests\EcoProcessRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class EcoProcessController extends Controller
{
    /**
     * Display a listing of the eco processes for a batch.
     */
    public function index(Batch $batch): View
    {
        $ecoProcesses = $batch->ecoProcesses()->latest()->paginate(10);
        
        return view('eco_processes.index', compact('batch', 'ecoProcesses'));
    }
    /**
     * Show the form for creating a new eco process.
     */
    public function create(Batch $batch): View
    {
        return view('eco_processes.create', compact('batch'));
    }

    /**
     * Store a newly created eco process in storage.
     */
    public function store(EcoProcessRequest $request, Batch $batch): RedirectResponse
    {
        $ecoProcess = $batch->ecoProcesses()->create($request->validated());
        
        return redirect()
            ->route('batches.show', $batch)
            ->with('success', 'Eco process created successfully.');
    }

    /**
     * Show the form for editing the specified eco process.
     */
    public function edit(Batch $batch, EcoProcess $ecoProcess): View
    {
        return view('eco_processes.edit', compact('batch', 'ecoProcess'));
    }

    /**
     * Update the specified eco process in storage.
     */
    public function update(EcoProcessRequest $request, Batch $batch, EcoProcess $ecoProcess): RedirectResponse
    {
        $ecoProcess->update($request->validated());
        
        return redirect()
            ->route('batches.show', $batch)
            ->with('success', 'Eco process updated successfully.');
    }
}
