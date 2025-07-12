<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\EcoProcess;
use App\Http\Requests\EcoProcessRequest;
use Illuminate\Http\JsonResponse;
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
     * Display the specified eco process.
     */
    public function show(Batch $batch, EcoProcess $ecoProcess): View
    {
        return view('eco_processes.show', compact('batch', 'ecoProcess'));
    }
    /**
     * Show the form for creating a new eco process.
     */
    public function create(Batch $batch): View
    {
        $formData = [
            'stage' => '',
            'processing_type' => [],
            'preservative_used' => [],
            // Add other default form fields here
        ];

        return view('eco_processes.create', compact('batch', 'formData'));
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
    public function update(Request $request, Batch $batch, EcoProcess $ecoProcess): JsonResponse
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
            $ecoProcess->update($ecoProcessData);


            // If this is an AJAX request, return JSON response
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Eco process updated successfully',
                    'redirect' => route('batches.eco-processes.index', $batch)
                ]);
            }

            return redirect()
                ->route('batches.eco-processes.index', $batch)
                ->with('success', 'Eco process updated successfully.');

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error creating eco process: ' . $e->getMessage());

            // If this is an AJAX request, return JSON error
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating eco process: ' . $e->getMessage()
                ], 422);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error updating eco process: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified eco process from storage.
     */
    public function destroy(Batch $batch, EcoProcess $ecoProcess)
    {
        try {
            $ecoProcess->delete();

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Eco process deleted successfully.'
                ]);
            }

            return redirect()
                ->route('batches.eco-processes.index', $batch)
                ->with('success', 'Eco process deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('Error deleting eco process: ' . $e->getMessage());

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting eco process: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->with('error', 'Error deleting eco process: ' . $e->getMessage());
        }
    }

    /**
     * Update the status of the specified eco process.
     */
    public function updateStatus(Request $request, Batch $batch, EcoProcess $ecoProcess)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,failed'
        ]);

        try {
            $status = $request->input('status');
            $now = now();

            $updateData = ['status' => $status];

            // Update timestamps based on status
            if ($status === 'in_progress' && !$ecoProcess->start_time) {
                $updateData['start_time'] = $now;
            } elseif (in_array($status, ['completed', 'failed']) && !$ecoProcess->end_time) {
                $updateData['end_time'] = $now;
            }

            $ecoProcess->update($updateData);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Eco process status updated successfully.',
                    'data' => [
                        'status' => $status,
                        'status_display' => str_replace('_', ' ', ucfirst($status)),
                        'start_time' => $ecoProcess->fresh()->start_time?->format('Y-m-d H:i') ?? 'N/A',
                        'end_time' => $ecoProcess->fresh()->end_time?->format('Y-m-d H:i') ?? 'N/A'
                    ]
                ]);
            }

            return back()->with('success', 'Eco process status updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Error updating eco process status: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating eco process status: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error updating eco process status: ' . $e->getMessage());
        }
    }
}
