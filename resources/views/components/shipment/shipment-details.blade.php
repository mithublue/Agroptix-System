@props(['shipment'])

<div class="space-y-4">
    @if(empty($shipment))
        <div class="text-center text-gray-500 py-8">
            <p>No shipment data available</p>
        </div>
    @else
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 bg-gray-50">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Shipment #{{ $shipment['id'] ?? 'N/A' }}
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Details and information about this shipment
                </p>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Batch</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $shipment['batch']['name'] ?? 'N/A' }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Origin</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $shipment['origin'] ?? 'N/A' }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Destination</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $shipment['destination'] ?? 'N/A' }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Vehicle Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $shipment['vehicle_type'] ?? 'N/A' }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Mode</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $shipment['mode'] ?? 'N/A' }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Fuel Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $shipment['fuel_type'] ?? 'N/A' }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Route Distance (km)</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $shipment['route_distance'] ?? 'N/A' }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">CO2 Estimate</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ isset($shipment['co2_estimate']) ? number_format($shipment['co2_estimate'], 2) : 'N/A' }}
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Departure Time</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if(!empty($shipment['departure_time']))
                                {{ \Carbon\Carbon::parse($shipment['departure_time'])->format('M d, Y H:i') }}
                            @else
                                N/A
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Arrival Time</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if(!empty($shipment['arrival_time']))
                                {{ \Carbon\Carbon::parse($shipment['arrival_time'])->format('M d, Y H:i') }}
                            @else
                                N/A
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Notes</dt>
                        <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">
                            {{ $shipment['notes'] ?? 'No notes available' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    @endif
</div>
