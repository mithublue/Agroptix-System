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