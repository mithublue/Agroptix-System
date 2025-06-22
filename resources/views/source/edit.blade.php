<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Source') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('sources.update', $source) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Type -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                                <select name="type" id="type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Select type</option>
                                    @foreach(config('at.type') as $key => $value)
                                        <option value="{{ $key }}" {{ old('type', $source->type) == $key ? 'selected' : '' }}>{{ $value }}</option>
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
                                        value="{{ old('gps_lat', $source->gps_lat) }}">
                                    @error('gps_lat')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="gps_long" class="block text-sm font-medium text-gray-700">Longitude</label>
                                    <input type="text" name="gps_long" id="gps_long"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        value="{{ old('gps_long', $source->gps_long) }}">
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
                                    <option value="Natural" {{ old('production_method', $source->production_method) == 'Natural' ? 'selected' : '' }}>Natural</option>
                                    <option value="Organic" {{ old('production_method', $source->production_method) == 'Organic' ? 'selected' : '' }}>Organic</option>
                                    <option value="Mixed" {{ old('production_method', $source->production_method) == 'Mixed' ? 'selected' : '' }}>Mixed</option>
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
                                    value="{{ old('area', $source->area) }}">
                                @error('area')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            @can('manage_source')
                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="pending" {{ old('status', $source->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ old('status', $source->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ old('status', $source->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="active" {{ old('status', $source->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $source->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Owner -->
                            <div>
                                <label for="owner_id" class="block text-sm font-medium text-gray-700">Owner</label>
                                <select name="owner_id" id="owner_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @foreach (\App\Models\User::all() as $user)
                                        <option value="{{ $user->id }}" {{ old('owner_id', $source->owner_id) == $user->id ? 'selected' : '' }}>
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
                            <a href="{{ route('sources.show', $source) }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Update Source
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
