@props(['product'])

<div class="space-y-4">
    @if(empty($product))
        <div class="text-center text-gray-500 py-8">
            <p>No product data available</p>
        </div>
    @else
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 bg-gray-50">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ $product['name'] ?? 'Product Details' }}
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Details and information about this product
                </p>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product['name'] ?? 'N/A' }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">SKU</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product['sku'] ?? 'N/A' }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Price</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ isset($product['price']) ? '\$' . number_format($product['price'], 2) : 'N/A' }}
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm">
                            @if(isset($product['is_active']))
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product['is_active'] ? 'Active' : 'Inactive' }}
                                </span>
                            @else
                                N/A
                            @endif
                        </dd>
                    </div>
                    @if(isset($product['source']))
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Source</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $product['source']['name'] ?? 'N/A' }}
                            </dd>
                        </div>
                    @endif
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">
                            {{ $product['description'] ?? 'No description available' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    @endif
</div>
