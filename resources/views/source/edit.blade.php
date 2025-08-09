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

                        <!-- Address Section -->
                        <div class="space-y-4" x-data="countryState()" x-init="
                            selectedCountry = '{{ old('country', $source->country_code) }}';
                            if (selectedCountry) {
                                loadStates();
                                // Small delay to ensure states are loaded before selecting
                                setTimeout(() => {
                                    selectedState = '{{ old('state', $source->state) }}';
                                }, 100);
                            }
                        ">
                            <h3 class="text-lg font-medium text-gray-900">Address Information</h3>
                            
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <!-- Address Line 1 -->
                                <div class="sm:col-span-2">
                                    <label for="address_line1" class="block text-sm font-medium text-gray-700">Address Line 1</label>
                                    <input type="text" name="address_line1" id="address_line1" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        value="{{ old('address_line1', $source->address_line1) }}">
                                    @error('address_line1')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Address Line 2 -->
                                <div class="sm:col-span-2">
                                    <label for="address_line2" class="block text-sm font-medium text-gray-700">Address Line 2 (Optional)</label>
                                    <input type="text" name="address_line2" id="address_line2"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        value="{{ old('address_line2', $source->address_line2) }}">
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
                                            <option :value="country.code" :selected="'{{ old('country', $source->country_code) }}' === country.code">
                                                <span x-text="formatOption(country)"></span>
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
                                            <option :value="state.name" :selected="'{{ old('state', $source->state) }}' === state.name">
                                                <span x-text="formatOption(state)"></span>
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
                                    @foreach(config('at.production_methods') as $value => $label)
                                        <option value="{{ $value }}" {{ old('production_method', $source->production_method) == $value ? 'selected' : '' }}>{{ $label }}</option>
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
                                    @foreach(config('at.source_status') as $value => $label)
                                        <option value="{{ $value }}" {{ old('status', $source->status) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
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

                        <!-- Products (optional, filtered by Owner) -->
                        <div class="mt-6">
                            <label for="product_ids" class="block text-sm font-medium text-gray-700">Products (optional)</label>
                            <select id="product_ids" name="product_ids[]" multiple
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></select>
                            <p id="product_help" class="mt-1 text-sm text-gray-500">Select one or more products owned by the selected owner.</p>
                            @error('product_ids')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @if ($errors->has('product_ids.*'))
                                <p class="mt-1 text-sm text-red-600">Invalid product selection.</p>
                            @endif
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
    <script>
        (function () {
            function initProductSelectEdit() {
                const sel = document.getElementById('product_ids');
                if (!sel || sel.dataset.tsInit === '1') return;
                sel.dataset.tsInit = '1';

                const ownerSelect = document.getElementById('owner_id');
                const defaultOwnerId = parseInt(ownerSelect?.value || @json($source->owner_id)) || null;
                const productsUrl = @json(route('ajax.products.by-owner'));
                const help = document.getElementById('product_help');
                const preselected = @json(old('product_ids', $source->products()->pluck('products.id')));

                const ts = new TomSelect(sel, {
                    plugins: ['remove_button'],
                    valueField: 'value',
                    labelField: 'text',
                    searchField: 'text',
                    options: [],
                    persist: false,
                    create: false,
                    closeAfterSelect: true,
                    maxOptions: 100,
                    render: {
                        option: function(data, escape) {
                            return '<div>' + escape(data.text) + '</div>';
                        }
                    }
                });

                async function reload(ownerId) {
                    if (!ownerId) return;
                    try {
                        const res = await fetch(productsUrl + '?owner_id=' + ownerId);
                        const json = await res.json();
                        ts.clearOptions();
                        if (json && json.success) {
                            ts.addOptions(json.data || []);
                            if (Array.isArray(preselected) && preselected.length) {
                                const allowed = (json.data || []).map(i => i.value);
                                const pre = preselected.filter(id => allowed.includes(id));
                                if (pre.length) ts.setValue(pre, true);
                            }
                            help.textContent = (json.data || []).length ? 'Select one or more products owned by the selected owner.' : 'No products available for this owner.';
                        } else {
                            help.textContent = 'Failed to load products.';
                        }
                    } catch (e) {
                        help.textContent = 'Error loading products.';
                    }
                }

                reload(defaultOwnerId);
                ownerSelect?.addEventListener('change', (e) => {
                    ts.clear(true);
                    const ownerId = parseInt(e.target.value || 0);
                    reload(ownerId);
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initProductSelectEdit);
            } else {
                initProductSelectEdit();
            }
            document.addEventListener('turbo:load', initProductSelectEdit);
        })();
    </script>
</x-app-layout>
