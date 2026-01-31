<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Shipment') }}
            </h2>
            <a href="{{ route('shipments.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Back to Shipments
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('shipments.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Batch Selection -->
                            <div class="col-span-1 md:col-span-2">
                                <x-label for="batch_id" :value="__('Batch')" />
                                <select id="batch_id" name="batch_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">-- Select Batch --</option>
                                    @foreach($batches as $batch)
                                        <option value="{{ $batch->id }}" {{ old('batch_id') == $batch->id ? 'selected' : '' }}>
                                            {{ $batch->batch_code }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('batch_id')" class="mt-2" />
                            </div>

                            <!-- Origin -->
                            <div>
                                <x-label for="origin" :value="__('Origin')" />
                                <x-input id="origin" class="block mt-1 w-full" type="text" name="origin" :value="old('origin')" required />
                                <x-input-error :messages="$errors->get('origin')" class="mt-2" />
                            </div>

                            <!-- Destination -->
                            <div>
                                <x-label for="destination" :value="__('Destination')" />
                                <x-input id="destination" class="block mt-1 w-full" type="text" name="destination" :value="old('destination')" required />
                                <x-input-error :messages="$errors->get('destination')" class="mt-2" />
                            </div>

                            <!-- Transport Mode (Critical for Carbon Footprint) -->
                            <div>
                                <x-label for="mode" :value="__('Transport Mode')" />
                                <select id="mode" name="mode" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">-- Select Mode --</option>
                                    <option value="Road" {{ old('mode') == 'Road' ? 'selected' : '' }}>Road 🚚 (0.1g/kg-km)</option>
                                    <option value="Air" {{ old('mode') == 'Air' ? 'selected' : '' }}>Air ✈️ (1g/kg-km)</option>
                                    <option value="Sea" {{ old('mode') == 'Sea' ? 'selected' : '' }}>Sea 🚢 (0.01g/kg-km)</option>
                                    <option value="Rail" {{ old('mode') == 'Rail' ? 'selected' : '' }}>Rail 🚂 (0.05g/kg-km)</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Required for Carbon Footprint calculation.</p>
                                <x-input-error :messages="$errors->get('mode')" class="mt-2" />
                            </div>

                            <!-- Route Distance (Critical for Carbon Footprint) -->
                            <div>
                                <x-label for="route_distance" :value="__('Route Distance (km)')" />
                                <x-input id="route_distance" class="block mt-1 w-full" type="number" step="0.1" name="route_distance" :value="old('route_distance')" required />
                                <p class="mt-1 text-xs text-gray-500">Required for Carbon Footprint calculation.</p>
                                <x-input-error :messages="$errors->get('route_distance')" class="mt-2" />
                            </div>

                            <!-- Vehicle Type -->
                            <div>
                                <x-label for="vehicle_type" :value="__('Vehicle Type')" />
                                <x-input id="vehicle_type" class="block mt-1 w-full" type="text" name="vehicle_type" :value="old('vehicle_type')" placeholder="e.g. Refrigerated Truck" />
                                <x-input-error :messages="$errors->get('vehicle_type')" class="mt-2" />
                            </div>

                            <!-- Fuel Type -->
                            <div>
                                <x-label for="fuel_type" :value="__('Fuel Type')" />
                                <x-input id="fuel_type" class="block mt-1 w-full" type="text" name="fuel_type" :value="old('fuel_type')" placeholder="e.g. Diesel" />
                                <x-input-error :messages="$errors->get('fuel_type')" class="mt-2" />
                            </div>

                            <!-- Temperature -->
                            <div>
                                <x-label for="temperature" :value="__('Temperature (°C)')" />
                                <x-input id="temperature" class="block mt-1 w-full" type="number" step="0.1" name="temperature" :value="old('temperature')" />
                                <x-input-error :messages="$errors->get('temperature')" class="mt-2" />
                            </div>

                            <!-- CO2 Estimate (Override) -->
                            <div>
                                <x-label for="co2_estimate" :value="__('CO2 Estimate (Override)')" />
                                <x-input id="co2_estimate" class="block mt-1 w-full" type="number" step="0.01" name="co2_estimate" :value="old('co2_estimate')" />
                                <p class="mt-1 text-xs text-gray-500">Leave blank to auto-calculate based on distance & mode.</p>
                                <x-input-error :messages="$errors->get('co2_estimate')" class="mt-2" />
                            </div>
                            
                            <!-- Departure Time -->
                            <div>
                                <x-label for="departure_time" :value="__('Departure Time')" />
                                <x-input id="departure_time" class="block mt-1 w-full" type="datetime-local" name="departure_time" :value="old('departure_time')" />
                                <x-input-error :messages="$errors->get('departure_time')" class="mt-2" />
                            </div>

                            <!-- Arrival Time -->
                            <div>
                                <x-label for="arrival_time" :value="__('Arrival Time')" />
                                <x-input id="arrival_time" class="block mt-1 w-full" type="datetime-local" name="arrival_time" :value="old('arrival_time')" />
                                <x-input-error :messages="$errors->get('arrival_time')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-button class="ml-4">
                                {{ __('Create Shipment') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
