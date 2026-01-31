<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Batch') }}
            </h2>
            <a href="{{ route('batches.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Back to Batches
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('batches.update', $batch) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <!-- Batch Code -->
                        <div>
                            <x-label for="batch_code" :value="__('Batch Code')" />
                            <x-input id="batch_code" class="block mt-1 w-full" type="text" name="batch_code" :value="old('batch_code', $batch->batch_code)" required autofocus />
                            @error('batch_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Producer -->
                            <div>
                                <x-label for="producer_id" :value="__('Producer')" />
                                <select id="producer_id" name="producer_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                    @if(old('producer_id', $batch->producer_id))
                                        <option value="{{ old('producer_id', $batch->producer_id) }}" selected>Loading...</option>
                                    @else
                                        <option value="">-- Select Producer --</option>
                                    @endif
                                </select>
                                @error('producer_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Source -->
                            <div>
                                <x-label for="source_id" :value="__('Source')" />
                                <select id="source_id" name="source_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                    <option value="">-- Select Source --</option>
                                    @foreach($sources as $id => $source)
                                        <option value="{{ $id }}" {{ old('source_id', $batch->source_id) == $id ? 'selected' : '' }}>{{ $source }}</option>
                                    @endforeach
                                </select>
                                @error('source_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Product -->
                            <div>
                                <x-label for="product_id" :value="__('Product')" />
                                <select id="product_id" name="product_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                    <option value="">-- Select Product --</option>
                                    @foreach($products as $id => $product)
                                        <option value="{{ $id }}" {{ old('product_id', $batch->product_id) == $id ? 'selected' : '' }}>{{ $product }}</option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Harvest Time -->
                            <div>
                                <x-label for="harvest_time" :value="__('Harvest Time')" />
                                <x-input id="harvest_time" class="block mt-1 w-full" type="date" name="harvest_time" :value="old('harvest_time', $batch->harvest_time ? $batch->harvest_time->format('Y-m-d') : '')" required />
                                @error('harvest_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <x-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                    @foreach(\App\Models\Batch::STATUSES as $value => $label)
                                        <option value="{{ $value }}" {{ old('status', $batch->status) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Weight -->
                            <div>
                                <x-label for="weight" :value="__('Weight (kg)')" />
                                <x-input id="weight" class="block mt-1 w-full" type="number" step="0.01" name="weight" :value="old('weight', $batch->weight)" />
                                @error('weight')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Fair Trade Premium & Currency -->
                            <div>
                                <x-label for="fair_trade_premium" :value="__('Ethical Sourcing Premium')" />
                                <div class="mt-1 relative rounded-md shadow-sm flex">
                                    <input type="number" name="fair_trade_premium" id="fair_trade_premium" step="0.01"
                                           class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                                           placeholder="0.00" value="{{ old('fair_trade_premium', $batch->fair_trade_premium) }}">
                                    <select name="currency" id="currency"
                                            class="focus:ring-indigo-500 focus:border-indigo-500 h-full py-0 pl-2 pr-7 border-l-0 bg-gray-50 text-gray-500 sm:text-sm rounded-r-md border-gray-300">
                                        @foreach(['SAR', 'USD', 'EUR', 'GBP', 'AED'] as $curr)
                                            <option value="{{ $curr }}" {{ old('currency', $batch->currency ?? 'SAR') === $curr ? 'selected' : '' }}>{{ $curr }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Optional: Extra amount paid for ethical sourcing.</p>
                                @error('fair_trade_premium')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Grade -->
                            <div>
                                <x-label for="grade" :value="__('Grade')" />
                                <select id="grade" name="grade" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                    <option value="">-- Select Grade --</option>
                                    @foreach(\App\Models\Batch::GRADES as $value => $label)
                                        <option value="{{ $value }}" {{ old('grade', $batch->grade) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('grade')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Has Defect -->
                            <div class="flex items-center">
                                <div class="flex items-center h-5">
                                    <input id="has_defect" name="has_defect" type="checkbox" 
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        {{ old('has_defect', $batch->has_defect) ? 'checked' : '' }}
                                        value="1">
                                </div>
                                <div class="ml-3 text-sm">
                                    <x-label for="has_defect" :value="__('Has Defect')" class="font-medium text-gray-700" />
                                </div>
                            </div>
                        </div>

                        <!-- Remark -->
                        <div>
                            <x-label for="remark" :value="__('Remarks')" />
                            <textarea id="remark" name="remark" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">{{ old('remark', $batch->remark) }}</textarea>
                            @error('remark')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        @push('scripts')
                        <script>
                            (function () {
                                function initBatchTomSelectEdit() {
                                    const producerSel = document.getElementById('producer_id');
                                    const sourceSel = document.getElementById('source_id');
                                    const productSel = document.getElementById('product_id');
                                    if (!producerSel || producerSel.dataset.tsInit === '1') return;
                                    producerSel.dataset.tsInit = '1';

                                    const producersUrl = @json(route('ajax.producers'));
                                    const sourcesUrl = @json(route('ajax.sources.by-owner'));
                                    const productsUrl = @json(route('ajax.products.by-owner'));

                                    const initial = {
                                        producerId: @json((int) old('producer_id', $batch->producer_id)),
                                        sourceId: @json((int) old('source_id', $batch->source_id)),
                                        productId: @json((int) old('product_id', $batch->product_id)),
                                    };

                                    const producerTS = new TomSelect(producerSel, {
                                        valueField: 'value',
                                        labelField: 'text',
                                        searchField: 'text',
                                        options: [],
                                        load: function (query, callback) {
                                            const url = producersUrl + (query ? ('?q=' + encodeURIComponent(query)) : '');
                                            fetch(url)
                                                .then(r => r.json())
                                                .then(json => callback(json && json.success ? (json.data || []) : []))
                                                .catch(() => callback());
                                        },
                                        onFocus: function() {
                                            if (!this.loading && !this.options || Object.keys(this.options).length === 0) {
                                                this.load('');
                                            }
                                        }
                                    });

                                    const sourceTS = new TomSelect(sourceSel, {
                                        valueField: 'value',
                                        labelField: 'text',
                                        searchField: 'text',
                                        options: [],
                                        persist: false,
                                        create: false,
                                        closeAfterSelect: true,
                                        maxOptions: 100
                                    });

                                    const productTS = new TomSelect(productSel, {
                                        valueField: 'value',
                                        labelField: 'text',
                                        searchField: 'text',
                                        options: [],
                                        persist: false,
                                        create: false,
                                        closeAfterSelect: true,
                                        maxOptions: 100
                                    });

                                    async function ensureProducerOption(id) {
                                        if (!id) return;
                                        try {
                                            const res = await fetch(producersUrl + '?id=' + id);
                                            const json = await res.json();
                                            if (json && json.success && Array.isArray(json.data)) {
                                                json.data.forEach(o => producerTS.addOption(o));
                                            }
                                        } catch (e) { }
                                    }

                                    async function reloadSources(ownerId, productId) {
                                        if (!ownerId) return;
                                        try {
                                            const u = new URL(sourcesUrl, window.location.origin);
                                            u.searchParams.set('owner_id', ownerId);
                                            if (productId) u.searchParams.set('product_id', productId);
                                            const res = await fetch(u.toString());
                                            const json = await res.json();
                                            sourceTS.clearOptions();
                                            if (json && json.success) {
                                                sourceTS.addOptions(json.data || []);
                                                if (initial.sourceId && (json.data || []).some(i => i.value == initial.sourceId)) {
                                                    sourceTS.setValue(initial.sourceId, true);
                                                }
                                            }
                                        } catch (e) { }
                                    }

                                    async function reloadProducts(ownerId, sourceId) {
                                        if (!ownerId) return;
                                        try {
                                            const u = new URL(productsUrl, window.location.origin);
                                            u.searchParams.set('owner_id', ownerId);
                                            if (sourceId) u.searchParams.set('source_id', sourceId);
                                            const res = await fetch(u.toString());
                                            const json = await res.json();
                                            productTS.clearOptions();
                                            if (json && json.success) {
                                                productTS.addOptions(json.data || []);
                                                if (initial.productId && (json.data || []).some(i => i.value == initial.productId)) {
                                                    productTS.setValue(initial.productId, true);
                                                }
                                            }
                                        } catch (e) { }
                                    }

                                    producerTS.on('change', (val) => {
                                        sourceTS.clear(true);
                                        productTS.clear(true);
                                        initial.sourceId = null;
                                        initial.productId = null;
                                        const ownerId = parseInt(val || 0);
                    
                                        if (ownerId) {
                                            reloadSources(ownerId);
                                            reloadProducts(ownerId);
                                        }
                                    });

                                    productTS.on('change', (val) => {
                                        const productId = parseInt(val || 0);
                                        const ownerId = parseInt(producerTS.getValue() || 0);
                                        if (ownerId) reloadSources(ownerId, productId || null);
                                    });

                                    sourceTS.on('change', (val) => {
                                        const sourceId = parseInt(val || 0);
                                        const ownerId = parseInt(producerTS.getValue() || 0);
                                        if (ownerId) reloadProducts(ownerId, sourceId || null);
                                    });

                                    // Preselect initial values
                                    if (initial.producerId) {
                                        ensureProducerOption(initial.producerId).then(() => {
                                            // Set the producer value silently (without triggering change event)
                                            producerTS.setValue(initial.producerId, true);
                                            // Then reload sources and products
                                            reloadSources(initial.producerId, initial.productId || null);
                                            reloadProducts(initial.producerId, initial.sourceId || null);
                                        });
                                    } else {
                                        // If no initial producer, still try to load the option to prevent "Loading..."
                                        const producerIdFromSelect = producerSel.value;
                                        if (producerIdFromSelect) {
                                            ensureProducerOption(producerIdFromSelect).then(() => {
                                                producerTS.setValue(producerIdFromSelect, true);
                                            });
                                        }
                                    }
                                }

                                if (document.readyState === 'loading') {
                                    document.addEventListener('DOMContentLoaded', initBatchTomSelectEdit);
                                } else {
                                    initBatchTomSelectEdit();
                                }
                                document.addEventListener('turbo:load', initBatchTomSelectEdit);
                            })();
                        </script>
                        @endpush

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('batches.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-button type="submit" class="bg-blue-600 hover:bg-blue-700 focus:ring-blue-500">
                                {{ __('Update Batch') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>