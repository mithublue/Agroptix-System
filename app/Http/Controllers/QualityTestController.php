<?php

namespace App\Http\Controllers;

use App\Http\Requests\QualityTestStoreRequest;
use App\Http\Requests\QualityTestUpdateRequest;
use App\Models\QualityTest;
use App\Models\Batch;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QualityTestController extends Controller
{
    public function index(Request $request): View
    {
        $qualityTests = QualityTest::all();

        return view('qualityTest.index', [
            'qualityTests' => $qualityTests,
        ]);
    }

    public function batchList(Request $request)
    {
        $batches = Batch::where('status', 'Completed')
                       ->with('product')
                       ->orderBy('created_at', 'desc')
                       ->paginate(10);

        return view('qualityTest.batch-list', compact('batches'));
    }

    public function create(Batch $batch): View
    {
        return view('qualityTest.create', compact('batch'));
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
