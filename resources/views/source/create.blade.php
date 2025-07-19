<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Source') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('sources.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Address Section -->
                        <div class="space-y-4" x-data="countryState()" x-init="
                            selectedCountry = '{{ old('country', '') }}';
                            if (selectedCountry) loadStates();
                        ">
                            <h3 class="text-lg font-medium text-gray-900">Address Information</h3>
                            
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <!-- Address Line 1 -->
                                <div class="sm:col-span-2">
                                    <label for="address_line1" class="block text-sm font-medium text-gray-700">Address Line 1</label>
                                    <input type="text" name="address_line1" id="address_line1" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        value="{{ old('address_line1') }}">
                                    @error('address_line1')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Address Line 2 -->
                                <div class="sm:col-span-2">
                                    <label for="address_line2" class="block text-sm font-medium text-gray-700">Address Line 2 (Optional)</label>
                                    <input type="text" name="address_line2" id="address_line2"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        value="{{ old('address_line2') }}">
                                    @error('address_line2')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Country -->
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                                    <select name="country" id="country" required
                                        x-model="selectedCountry"
                                        @change="loadStates()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Select country</option>
                                        <template x-for="country in countries" :key="country.code">
                                            <option :value="country.code" :selected="'{{ old('country') }}' === country.code"
                                                x-text="formatOption(country)">
                                            </option>
                                        </template>
                                    </select>
                                    @error('country')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- State/Region -->
                                <div>
                                    <label for="state" class="block text-sm font-medium text-gray-700">State/Region</label>
                                    <select name="state" id="state" required
                                        x-model="selectedState"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Select state/region</option>
                                        <template x-for="state in states" :key="state.code">
                                            <option :value="state.name" :selected="'{{ old('state') }}' === state.name"
                                                x-text="formatOption(state)">
                                            </option>
                                        </template>
                                    </select>
                                    @error('state')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Other Fields -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Type -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                                <select name="type" id="type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Select type</option>
                                    @foreach(config('at.type') as $key => $value)
                                        <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- GPS Coordinates -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="gps_lat" class="block text-sm font-medium text-gray-700">Latitude</label>
                                    <input type="text" name="gps_lat" id="gps_lat"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        value="{{ old('gps_lat') }}">
                                    @error('gps_lat')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="gps_long" class="block text-sm font-medium text-gray-700">Longitude</label>
                                    <input type="text" name="gps_long" id="gps_long"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        value="{{ old('gps_long') }}">
                                    @error('gps_long')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Production Method -->
                            <div>
                                <label for="production_method" class="block text-sm font-medium text-gray-700">Production Method</label>
                                <select name="production_method" id="production_method" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Select production method</option>
                                    @foreach(config('at.production_methods') as $value => $label)
                                        <option value="{{ $value }}" {{ old('production_method') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('production_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Area -->
                            <div>
                                <label for="area" class="block text-sm font-medium text-gray-700">Area</label>
                                <input type="text" name="area" id="area"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    value="{{ old('area') }}">
                                @error('area')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            @can('manage_source')
                            <div class="mt-4">
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @foreach(config('at.source_status') as $value => $label)
                                        <option value="{{ $value }}" {{ old('status', 'pending') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mt-4">
                                <label for="owner_id" class="block text-sm font-medium text-gray-700">Owner</label>
                                <select name="owner_id" id="owner_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @foreach (App\Models\User::all() as $user)
                                        <option value="{{ $user->id }}" {{ old('owner_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('owner_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            @endcan
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3 pt-4">
                            <a href="{{ route('sources.index') }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Save Source
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
