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
    public function store(Request $request, Batch $batch): RedirectResponse
    {
        // Get all form data except _token and _method
        $formData = $request->except(['_token', '_method']);
        $stage = $request->input('stage');

        // Prepare the data for storage
        $ecoProcessData = [
            'batch_id' => $batch->id,
            'stage' => $stage,
            'data' => $formData, // This will be automatically JSON-encoded by Laravel
            'status' => 'in_progress', // Default status
            'start_time' => now(), // Default start time
        ];

        // Create the eco process
        $ecoProcess = EcoProcess::create($ecoProcessData);

        return redirect()
            ->route('batches.eco-processes.index', [$batch])
            ->with('success', 'Eco process created successfully.');
        ////
//        $ecoProcess = $batch->ecoProcesses()->create($request->validated());
    }

    /**
     * Show the form for editing the specified eco process.
     */
    public function edit(Batch $batch, EcoProcess $ecoProcess): View
    {
        return view('eco_processes.create', compact('batch', 'ecoProcess'));
    }

    /**
     * Update the specified eco process in storage.
     */
    public function update(Request $request, Batch $batch, EcoProcess $ecoProcess): RedirectResponse
    {
        // Get all form data except _token and _method
        $formData = $request->except(['_token', '_method']);

        // Update the eco process
        $ecoProcess->update([
            'stage' => $request->input('stage'),
            'data' => $formData, // This will be automatically JSON-encoded by Laravel
        ]);

        return redirect()
            ->route('batches.eco-processes.index', [$batch])
            ->with('success', 'Eco process updated successfully.');
    }
}
