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
    public function store(Request $request, Batch $batch)
    {
        try {
            // Get the raw JSON data if it was sent
            $jsonData = $request->input('data');
            $formData = $jsonData ? json_decode($jsonData, true) : $request->except(['_token', '_method']);
            
            // If we couldn't decode JSON, use the request data directly
            if (json_last_error() !== JSON_ERROR_NONE) {
                $formData = $request->except(['_token', '_method']);
            }

            // Get the stage from the form data or request
            $stage = $formData['stage'] ?? $request->input('stage');
            
            if (!$stage) {
                throw new \Exception('Stage is required');
            }

            // Prepare the data for storage
            $ecoProcessData = [
                'batch_id' => $batch->id,
                'stage' => $stage,
                'data' => $formData,
                'status' => 'in_progress',
                'start_time' => now(),
            ];

            // Create the eco process
            $ecoProcess = EcoProcess::create($ecoProcessData);

            // If this is an AJAX request, return JSON response
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Eco process created successfully',
                    'redirect' => route('batches.eco-processes.index', $batch)
                ]);
            }

            return redirect()
                ->route('batches.eco-processes.index', $batch)
                ->with('success', 'Eco process created successfully.');
                
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error creating eco process: ' . $e->getMessage());
            
            // If this is an AJAX request, return JSON error
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating eco process: ' . $e->getMessage()
                ], 422);
            }
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Error creating eco process: ' . $e->getMessage()]);
        }
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
