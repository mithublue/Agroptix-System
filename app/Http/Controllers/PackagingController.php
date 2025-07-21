<?php

namespace App\Http\Controllers;

use App\Models\Packaging;
use Illuminate\Http\Request;

class PackagingController extends Controller
{
    /**
     * Display a listing of the packaging records.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->authorize('view_packaging', Packaging::class);
        
        $packages = Packaging::with('batch')
            ->latest()
            ->paginate(15);
            
        return view('admin.packaging.index', compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified packaging record.
     *
     * @param  \App\Models\Packaging  $packaging
     * @return \Illuminate\View\View
     */
    public function show(Packaging $packaging)
    {
        $this->authorize('view_packaging', $packaging);
        
        // Eager load relationships for the view
        $packaging->load(['batch', 'packer']);
        
        return view('admin.packaging.show', compact('packaging'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Packaging $packaging)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Packaging $packaging)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Packaging $packaging)
    {
        //
    }
}
