<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_id' => 'required|exists:sources,id',
            'type' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'certifying_body' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
        ]);

        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('certifications', 'public');
            $validated['document_path'] = $path;
        }

        Certification::create($validated);

        return back()->with('success', 'Certification uploaded successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Certification $certification)
    {
        // Delete file
        if ($certification->document_path) {
            Storage::disk('public')->delete($certification->document_path);
        }

        $certification->delete();

        return back()->with('success', 'Certification deleted successfully.');
    }
}
