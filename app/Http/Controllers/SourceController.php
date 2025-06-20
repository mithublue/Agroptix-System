<?php

namespace App\Http\Controllers;

use App\Http\Requests\SourceStoreRequest;
use App\Http\Requests\SourceUpdateRequest;
use App\Models\Source;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SourceController extends Controller
{

    public function index(): View
    {
        $sources = Source::latest()->paginate(10);
        return view('source.index', compact('sources'));
    }

    public function create(): View
    {
        return view('source.create');
    }

    public function store(SourceStoreRequest $request): RedirectResponse
    {
        try {
            Source::create($request->validated());
            return redirect()
                ->route('sources.index')
                ->with('success', 'Source created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create source. ' . $e->getMessage());
        }
    }

    public function show(Source $source): View
    {
        return view('source.show', compact('source'));
    }

    public function edit(Source $source): View
    {
        return view('source.edit', compact('source'));
    }

    public function update(SourceUpdateRequest $request, Source $source): RedirectResponse
    {
        try {
            $source->update($request->validated());
            return redirect()
                ->route('sources.index')
                ->with('success', 'Source updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update source. ' . $e->getMessage());
        }
    }

    public function destroy(Source $source): RedirectResponse
    {
        try {
            $source->delete();
            return redirect()
                ->route('sources.index')
                ->with('success', 'Source deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete source. ' . $e->getMessage());
        }
    }
}
