<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Product') }}
            </h2>
            <a href="{{ route('products.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Back to Products
            </a>
        </div>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <!-- Name -->
                        <div>
                            <x-label for="name" :value="__('Product Name')" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $product->name)" required autofocus />
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <x-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-label for="price" :value="__('Price')" />
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <x-input id="price" class="pl-7 block w-full" type="number" step="0.01" name="price" :value="old('price', $product->price)" required />
                                </div>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <x-label for="is_active" :value="__('Status')" />
                                <select id="is_active" name="is_active" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                    <option value="1" {{ old('is_active', $product->is_active) ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !old('is_active', $product->is_active) ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between mt-6">
                            <button type="button" onclick="confirm('Are you sure you want to delete this product?') ? document.getElementById('delete-form').submit() : ''" class="text-red-600 hover:text-red-900">
                                {{ __('Delete Product') }}
                            </button>
                            <div class="flex items-center">
                                <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                                    {{ __('Cancel') }}
                                </a>
                                <x-button type="submit" class="bg-blue-600 hover:bg-blue-700 focus:ring-blue-500">
                                    {{ __('Update Product') }}
                                </x-button>
                            </div>
                        </div>
                    </form>

                    <!-- Delete Form -->
                    <form id="delete-form" action="{{ route('products.destroy', $product) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
