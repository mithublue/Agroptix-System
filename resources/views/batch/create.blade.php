<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Batch') }}
            </h2>
            <a href="{{ route('batches.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Back to Batches') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('batches.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Batch Code -->
                            <div>
                                <x-label for="batch_code" :value="__('Batch Code')" />
                                <x-input id="batch_code" name="batch_code" type="text" class="mt-1 block w-full" 
                                    :value="old('batch_code')" />
                                <x-input-error :messages="$errors->get('batch_code')" class="mt-2" />
                            </div>

                            <!-- Source -->
                            <div>
                                <x-label for="source_id" :value="__('Source')" required />
                                <select id="source_id" name="source_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- Select Source --</option>
                                    @foreach($sources as $id => $source)
                                        <option value="{{ $id }}" {{ old('source_id') == $id ? 'selected' : '' }}>{{ $source }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('source_id')" class="mt-2" />
                            </div>

                            <!-- Product -->
                            <div>
                                <x-label for="product_id" :value="__('Product')" required />
                                <select id="product_id" name="product_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- Select Product --</option>
                                    @foreach($products as $id => $product)
                                        <option value="{{ $id }}" {{ old('product_id') == $id ? 'selected' : '' }}>{{ $product }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                            </div>

                            <!-- Harvest Time -->
                            <div>
                                <x-label for="harvest_time" :value="__('Harvest Time')" required />
                                <x-input id="harvest_time" name="harvest_time" type="date" class="mt-1 block w-full" 
                                    :value="old('harvest_time')" />
                                <x-input-error :messages="$errors->get('harvest_time')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div>
                                <x-label for="status" :value="__('Status')" required />
                                <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @foreach(\App\Models\Batch::STATUSES as $value => $label)
                                        <option value="{{ $value }}" {{ old('status', 'pending') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <!-- Weight -->
                            <div>
                                <x-label for="weight" :value="__('Weight (kg)')" />
                                <x-input id="weight" name="weight" type="number" step="0.01" class="mt-1 block w-full" 
                                    :value="old('weight')" />
                                <x-input-error :messages="$errors->get('weight')" class="mt-2" />
                            </div>

                            <!-- Grade -->
                            <div>
                                <x-label for="grade" :value="__('Grade')" />
                                <select id="grade" name="grade" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- Select Grade --</option>
                                    @foreach(\App\Models\Batch::GRADES as $value => $label)
                                        <option value="{{ $value }}" {{ old('grade') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('grade')" class="mt-2" />
                            </div>

                            <!-- Has Defect -->
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="has_defect" name="has_defect" type="checkbox" 
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        {{ old('has_defect') ? 'checked' : '' }}
                                        value="1">
                                </div>
                                <div class="ml-3 text-sm">
                                    <x-label for="has_defect" :value="__('Has Defect')" class="font-medium text-gray-700" />
                                    <p class="text-gray-500">Check if the batch has any defects</p>
                                </div>
                                <x-input-error :messages="$errors->get('has_defect')" class="mt-2" />
                            </div>

                            <!-- Remark -->
                            <div class="md:col-span-2">
                                <x-label for="remark" :value="__('Remarks')" />
                                <textarea id="remark" name="remark" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('remark') }}</textarea>
                                <x-input-error :messages="$errors->get('remark')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Hidden required fields -->
                        <input type="hidden" name="source_as_source_id" value="1">
                        <input type="hidden" name="product_as_product_id" value="1">

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('batches.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
                            </a>
                            <x-button type="submit" class="ml-3">
                                {{ __('Create Batch') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
