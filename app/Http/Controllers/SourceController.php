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
    public function index(Request $request): Response
    {
        $sources = Source::all();

        return view('source.index', [
            'sources' => $sources,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('source.create');
    }

    public function store(SourceStoreRequest $request): Response
    {
        $source = Source::create($request->validated());

        $request->session()->flash('source.id', $source->id);

        return redirect()->route('sources.index');
    }

    public function show(Request $request, Source $source): Response
    {
        return view('source.show', [
            'source' => $source,
        ]);
    }

    public function edit(Request $request, Source $source): Response
    {
        return view('source.edit', [
            'source' => $source,
        ]);
    }

    public function update(SourceUpdateRequest $request, Source $source): Response
    {
        $source->update($request->validated());

        $request->session()->flash('source.id', $source->id);

        return redirect()->route('sources.index');
    }

    public function destroy(Request $request, Source $source): Response
    {
        $source->delete();

        return redirect()->route('sources.index');
    }
}
