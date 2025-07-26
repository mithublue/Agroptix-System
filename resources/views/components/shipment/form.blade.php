@props(['shipment' => null, 'batches' => [], 'method' => 'POST', 'action' => ''])

<div class="space-y-6">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Batch Selection -->
        <div class="col-span-2">
            <x-input-label for="batch_id" :value="__('Batch')" />
            <select id="batch_id" name="batch_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">-- Select Batch --</option>
                @foreach($batches as $batch)
                    <option value="{{ $batch->id }}" {{ old('batch_id', $shipment?->batch_id) == $batch->id ? 'selected' : '' }}>
                        {{ $batch->batch_code ?? 'Batch #' . $batch->id }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('batch_id')" class="mt-2" />
        </div>

        <!-- Origin -->
        <div>
            <x-input-label for="origin" :value="__('Origin')" />
            <x-text-input id="origin" name="origin" type="text" class="mt-1 block w-full" :value="old('origin', $shipment?->origin)" required />
            <x-input-error :messages="$errors->get('origin')" class="mt-2" />
        </div>

        <!-- Destination -->
        <div>
            <x-input-label for="destination" :value="__('Destination')" />
            <x-text-input id="destination" name="destination" type="text" class="mt-1 block w-full" :value="old('destination', $shipment?->destination)" required />
            <x-input-error :messages="$errors->get('destination')" class="mt-2" />
        </div>

        <!-- Vehicle Type -->
        <div>
            <x-input-label for="vehicle_type" :value="__('Vehicle Type')" />
            <select id="vehicle_type" name="vehicle_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">-- Select Vehicle Type --</option>
                @foreach(['Truck', 'Ship', 'Train', 'Air', 'Other'] as $type)
                    <option value="{{ $type }}" {{ old('vehicle_type', $shipment?->vehicle_type) == $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('vehicle_type')" class="mt-2" />
        </div>

        <!-- Mode -->
        <div>
            <x-input-label for="mode" :value="__('Transport Mode')" />
            <select id="mode" name="mode" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">-- Select Mode --</option>
                @foreach(['Road', 'Sea', 'Rail', 'Air', 'Multimodal'] as $mode)
                    <option value="{{ $mode }}" {{ old('mode', $shipment?->mode) == $mode ? 'selected' : '' }}>
                        {{ $mode }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('mode')" class="mt-2" />
        </div>

        <!-- Route Distance -->
        <div>
            <x-input-label for="route_distance" :value="__('Route Distance (km)')" />
            <x-text-input id="route_distance" name="route_distance" type="number" step="0.01" class="mt-1 block w-full" :value="old('route_distance', $shipment?->route_distance)" />
            <x-input-error :messages="$errors->get('route_distance')" class="mt-2" />
        </div>

        <!-- Fuel Type -->
        <div>
            <x-input-label for="fuel_type" :value="__('Fuel Type')" />
            <x-text-input id="fuel_type" name="fuel_type" type="text" class="mt-1 block w-full" :value="old('fuel_type', $shipment?->fuel_type)" />
            <x-input-error :messages="$errors->get('fuel_type')" class="mt-2" />
        </div>

        <!-- Temperature -->
        <div>
            <x-input-label for="temperature" :value="__('Temperature (°C)')" />
            <x-text-input id="temperature" name="temperature" type="number" step="0.01" class="mt-1 block w-full" :value="old('temperature', $shipment?->temperature)" />
            <x-input-error :messages="$errors->get('temperature')" class="mt-2" />
        </div>

        <!-- Departure Time -->
        <div>
            <x-input-label for="departure_time" :value="__('Departure Time')" />
            <x-text-input id="departure_time" name="departure_time" type="datetime-local" class="mt-1 block w-full" :value="old('departure_time', $shipment?->departure_time ? \Carbon\Carbon::parse($shipment->departure_time)->format('Y-m-d\TH:i') : '')" />
            <x-input-error :messages="$errors->get('departure_time')" class="mt-2" />
        </div>

        <!-- Expected Arrival Time -->
        <div>
            <x-input-label for="expected_arrival_time" :value="__('Expected Arrival Time')" />
            <x-text-input id="expected_arrival_time" name="expected_arrival_time" type="datetime-local" class="mt-1 block w-full" :value="old('expected_arrival_time', $shipment?->expected_arrival_time ? \Carbon\Carbon::parse($shipment->expected_arrival_time)->format('Y-m-d\TH:i') : '')" />
            <x-input-error :messages="$errors->get('expected_arrival_time')" class="mt-2" />
        </div>
    </div>

    <!-- Notes -->
    <div>
        <x-input-label for="notes" :value="__('Notes')" />
        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('notes', $shipment?->notes) }}</textarea>
        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
        <button type="button" @click="show = false" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Cancel
        </button>
        <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            {{ $shipment ? 'Update' : 'Create' }} Shipment
        </button>
    </div>
</div>
