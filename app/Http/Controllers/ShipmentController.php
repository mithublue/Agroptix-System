<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShipmentStoreRequest;
use App\Http\Requests\ShipmentUpdateRequest;
use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    public function index(Request $request): Response
    {
        $shipments = Shipment::all();

        return view('shipment.index', [
            'shipments' => $shipments,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('shipment.create');
    }

    public function store(ShipmentStoreRequest $request): Response
    {
        $shipment = Shipment::create($request->validated());

        $request->session()->flash('shipment.id', $shipment->id);

        return redirect()->route('shipments.index');
    }

    public function show(Request $request, Shipment $shipment): Response
    {
        return view('shipment.show', [
            'shipment' => $shipment,
        ]);
    }

    public function edit(Request $request, Shipment $shipment): Response
    {
        return view('shipment.edit', [
            'shipment' => $shipment,
        ]);
    }

    public function update(ShipmentUpdateRequest $request, Shipment $shipment): Response
    {
        $shipment->update($request->validated());

        $request->session()->flash('shipment.id', $shipment->id);

        return redirect()->route('shipments.index');
    }

    public function destroy(Request $request, Shipment $shipment): Response
    {
        $shipment->delete();

        return redirect()->route('shipments.index');
    }
}
