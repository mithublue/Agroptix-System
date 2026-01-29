<?php

namespace App\Http\Controllers;

use App\Models\ComplianceStandard;
use Illuminate\Http\Request;

class ComplianceStandardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $standards = ComplianceStandard::latest()->paginate(10);
        return view('compliance_standards.index', compact('standards'));
    }

    public function create()
    {
        return view('compliance_standards.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'region' => 'required|string|max:255',
            'crop_type' => 'required|string|max:255',
            'parameter_name' => 'required|string|max:255',
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'unit' => 'required|string|max:50',
            'critical_action' => 'required|in:warning,reject_batch',
            'is_active' => 'boolean',
        ]);

        ComplianceStandard::create($validated);

        return redirect()->route('admin.compliance-standards.index')
            ->with('success', 'Compliance standard created successfully.');
    }

    public function show(ComplianceStandard $complianceStandard)
    {
        // Not used currently
    }

    public function edit(ComplianceStandard $complianceStandard)
    {
        return view('compliance_standards.create', compact('complianceStandard'));
    }

    public function update(Request $request, ComplianceStandard $complianceStandard)
    {
        $validated = $request->validate([
            'region' => 'required|string|max:255',
            'crop_type' => 'required|string|max:255',
            'parameter_name' => 'required|string|max:255',
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'unit' => 'required|string|max:50',
            'critical_action' => 'required|in:warning,reject_batch',
            'is_active' => 'boolean',
        ]);

        $complianceStandard->update($validated);

        return redirect()->route('admin.compliance-standards.index')
            ->with('success', 'Compliance standard updated successfully.');
    }

    public function destroy(ComplianceStandard $complianceStandard)
    {
        $complianceStandard->delete();

        return redirect()->route('admin.compliance-standards.index')
            ->with('success', 'Compliance standard deleted successfully.');
    }
}
