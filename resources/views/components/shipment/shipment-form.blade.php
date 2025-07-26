<form id="shipment-form" method="POST" action="{{ isset($shipment) ? route('shipments.update', $shipment) : route('shipments.store') }}">
    @csrf
    @if(isset($shipment))
        @method('PUT')
    @endif

    <div class="space-y-6">
        <!-- Batch Selection -->
        <div>
            <label for="batch_id" class="block text-sm font-medium text-gray-700">Batch</label>
            <select id="batch_id" name="batch_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">Select a batch</option>
                @foreach($batches as $batch)
                    <option value="{{ $batch->id }}" {{ (isset($shipment) && $shipment->batch_id == $batch->id) ? 'selected' : '' }}>
                        {{ $batch->batch_code ?? 'Batch #' . $batch->id }}
                    </option>
                @endforeach
            </select>
            @error('batch_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Origin and Destination -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="origin" class="block text-sm font-medium text-gray-700">Origin</label>
                <input type="text" id="origin" name="origin" value="{{ old('origin', $shipment->origin ?? '') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('origin')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="destination" class="block text-sm font-medium text-gray-700">Destination</label>
                <input type="text" id="destination" name="destination" value="{{ old('destination', $shipment->destination ?? '') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('destination')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Vehicle and Mode -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="vehicle_type" class="block text-sm font-medium text-gray-700">Vehicle Type</label>
                <select id="vehicle_type" name="vehicle_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">Select vehicle type</option>
                    @foreach(['Truck', 'Ship', 'Train', 'Air', 'Other'] as $type)
                        <option value="{{ $type }}" {{ old('vehicle_type', $shipment->vehicle_type ?? '') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
                @error('vehicle_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="mode" class="block text-sm font-medium text-gray-700">Transport Mode</label>
                <select id="mode" name="mode" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">Select transport mode</option>
                    @foreach(['Road', 'Sea', 'Rail', 'Air', 'Multimodal'] as $mode)
                        <option value="{{ $mode }}" {{ old('mode', $shipment->mode ?? '') == $mode ? 'selected' : '' }}>{{ $mode }}</option>
                    @endforeach
                </select>
                @error('mode')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Fuel Type and Temperature -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="fuel_type" class="block text-sm font-medium text-gray-700">Fuel Type</label>
                <input type="text" id="fuel_type" name="fuel_type" value="{{ old('fuel_type', $shipment->fuel_type ?? '') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                       placeholder="e.g., Diesel, Electric, etc.">
                @error('fuel_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="temperature" class="block text-sm font-medium text-gray-700">Temperature (°C)</label>
                <input type="number" step="0.01" id="temperature" name="temperature" value="{{ old('temperature', $shipment->temperature ?? '') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                       placeholder="e.g., 4.5">
                @error('temperature')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Route Distance -->
        <div>
            <label for="route_distance" class="block text-sm font-medium text-gray-700">Route Distance (km)</label>
            <input type="number" step="0.01" id="route_distance" name="route_distance" value="{{ old('route_distance', $shipment->route_distance ?? '') }}" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @error('route_distance')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Departure and Arrival Times -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="departure_time" class="block text-sm font-medium text-gray-700">Departure Time</label>
                <input type="datetime-local" id="departure_time" name="departure_time" 
                       value="{{ old('departure_time', isset($shipment->departure_time) ? $shipment->departure_time->format('Y-m-d\TH:i') : '') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('departure_time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="arrival_time" class="block text-sm font-medium text-gray-700">Arrival Time (Estimated)</label>
                <input type="datetime-local" id="arrival_time" name="arrival_time" 
                       value="{{ old('arrival_time', isset($shipment->arrival_time) ? $shipment->arrival_time->format('Y-m-d\TH:i') : '') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('arrival_time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Status -->
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                @foreach(['pending', 'in_transit', 'delayed', 'delivered', 'cancelled'] as $status)
                    <option value="{{ $status }}" {{ old('status', $shipment->status ?? 'pending') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Notes -->
        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
            <textarea id="notes" name="notes" rows="3" 
                     class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('notes', $shipment->notes ?? '') }}</textarea>
            @error('notes')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</form>
