<?php

namespace App\Http\Controllers;

use App\Http\Requests\QualityTestStoreRequest;
use App\Http\Requests\QualityTestUpdateRequest;
use App\Models\QualityTest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QualityTestController extends Controller
{
    public function index(Request $request): Response
    {
        $qualityTests = QualityTest::all();

        return view('qualityTest.index', [
            'qualityTests' => $qualityTests,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('qualityTest.create');
    }

    public function store(QualityTestStoreRequest $request): Response
    {
        $qualityTest = QualityTest::create($request->validated());

        $request->session()->flash('qualityTest.id', $qualityTest->id);

        return redirect()->route('qualityTests.index');
    }

    public function show(Request $request, QualityTest $qualityTest): Response
    {
        return view('qualityTest.show', [
            'qualityTest' => $qualityTest,
        ]);
    }

    public function edit(Request $request, QualityTest $qualityTest): Response
    {
        return view('qualityTest.edit', [
            'qualityTest' => $qualityTest,
        ]);
    }

    public function update(QualityTestUpdateRequest $request, QualityTest $qualityTest): Response
    {
        $qualityTest->update($request->validated());

        $request->session()->flash('qualityTest.id', $qualityTest->id);

        return redirect()->route('qualityTests.index');
    }

    public function destroy(Request $request, QualityTest $qualityTest): Response
    {
        $qualityTest->delete();

        return redirect()->route('qualityTests.index');
    }
}
