<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    @push('scripts')
    <script>
        function productIndex() {
            return {
                showViewDrawer: false,
                isLoading: false,
                productDetails: '',
                currentProduct: null,

                async viewProduct(productId) {
                    try {
                        // Reset state
                        this.isLoading = true;
                        this.productDetails = '';
                        this.currentProduct = null;

                        // Show the drawer immediately
                        this.showViewDrawer = true;

                        // Fetch product data
                        const response = await fetch(`{{ route('products.show', '') }}/${productId}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Failed to fetch product details');
                        }

                        const { data: product } = await response.json();
                        this.currentProduct = product;

                        // Create HTML for the product details
                        const formatDate = (dateString) => {
                            if (!dateString) return 'N/A';
                            const date = new Date(dateString);
                            return date.toLocaleDateString();
                        };

                        const statusBadge = product.is_active ?
                            '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>' :
                            '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>';

                        this.productDetails = `
                            <div class="space-y-4">
                                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                    <div class="px-4 py-5 sm:px-6 bg-gray-50">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                                            ${product.name || 'Product Details'}
                                        </h3>
                                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                            Details and information about this product
                                        </p>
                                    </div>
                                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                                        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                                            <div class="sm:col-span-1">
                                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                                <dd class="mt-1 text-sm text-gray-900">${product.name || 'N/A'}</dd>
                                            </div>
                                            <div class="sm:col-span-1">
                                                <dt class="text-sm font-medium text-gray-500">SKU</dt>
                                                <dd class="mt-1 text-sm text-gray-900">${product.sku || 'N/A'}</dd>
                                            </div>
                                            <div class="sm:col-span-1">
                                                <dt class="text-sm font-medium text-gray-500">Price</dt>
                                                <dd class="mt-1 text-sm text-gray-900">
                                                    ${product.price ? '$' + parseFloat(product.price).toFixed(2) : 'N/A'}
                                                </dd>
                                            </div>
                                            <div class="sm:col-span-1">
                                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                                <dd class="mt-1 text-sm">
                                                    ${statusBadge}
                                                </dd>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">
                                                    ${product.description || 'No description available'}
                                                </dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        `;

                    } catch (error) {
                        console.error('Error fetching product:', error);
                        this.productDetails = `
                            <div class="p-4 text-red-600">
                                <p>Error loading product details. Please try again.</p>
                                <button @click="viewProduct(${productId})" class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                                    Retry
                                </button>
                            </div>
                        `;
                    } finally {
                        this.isLoading = false;
                    }
                }
            };
        }
    </script>
    @endpush
    <div class="container mx-auto px-4 py-8" x-data="productIndex()">
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Products</h1>
            @can('create_products')
                <a href="{{ route('products.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition duration-200">
                    Add New Product
                </a>
            @endcan
        </div>

        <!-- Filters -->
        <div class="mb-6 bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Filters</h3>
            <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Search -->
                <div>
                    <label for="q" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" id="q" name="q"
                           value="{{ request('q') }}"
                           class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                           placeholder="Search name, description, SKU, or exact price">
                </div>
                <!-- Min Price -->
                <div>
                    <label for="min_price" class="block text-sm font-medium text-gray-700 mb-1">Min Price</label>
                    <input type="number" id="min_price" name="min_price"
                           value="{{ request('min_price') }}" step="0.01" min="0"
                           class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                           placeholder="Min price">
                </div>

                <!-- Max Price -->
                <div>
                    <label for="max_price" class="block text-sm font-medium text-gray-700 mb-1">Max Price</label>
                    <input type="number" id="max_price" name="max_price"
                           value="{{ request('max_price') }}" step="0.01" min="0"
                           class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                           placeholder="Max price">
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">All Statuses</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="flex flex-col justify-end">
                    <div class="flex space-x-2">
                        <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 h-10">
                            Apply
                        </button>
                        <a href="{{ route('products.index') }}" class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 h-10">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($products->count() > 0)
            @can('delete_product')
                <form id="bulk-delete-products" method="POST" action="{{ route('products.bulk-destroy') }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="redirect" value="{{ url()->full() }}">
                </form>
            @endcan
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @can('delete_product')
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="select-all-products" class="rounded border-gray-300" form="bulk-delete-products">
                        </th>
                        @endcan
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Price
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($products as $product)
                    <tr>
                        @can('delete_product')
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="ids[]" value="{{ $product->id }}" class="row-checkbox-product rounded border-gray-300" form="bulk-delete-products">
                        </td>
                        @endcan
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $product->name }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500">
                                {{ Str::limit($product->description, 50) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                ${{ number_format($product->price, 2) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @can('manage_product')
                            <div x-data="{
                                isActive: {{ $product->is_active ? 'true' : 'false' }},
                                isLoading: false,
                                toggleStatus() {
                                    if (this.isLoading) return;

                                    this.isLoading = true;

                                    axios.patch('{{ route('products.update.status', $product) }}', {
                                        is_active: !this.isActive
                                    })
                                    .then(response => {
                                        if (response.data.success) {
                                            this.isActive = response.data.is_active;
                                            this.$dispatch('notify', {
                                                type: 'success',
                                                message: response.data.message
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        this.$dispatch('notify', {
                                            type: 'error',
                                            message: 'Failed to update status. Please try again.'
                                        });
                                    })
                                    .finally(() => {
                                        this.isLoading = false;
                                    });
                                }
                            }" class="flex items-center">
                                <button type="button"
                                    @click="toggleStatus"
                                    :disabled="isLoading"
                                    :class="{
                                        'bg-green-500': isActive,
                                        'bg-gray-200': !isActive,
                                        'cursor-not-allowed': isLoading
                                    }"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                    role="switch"
                                    :aria-checked="isActive">
                                    <span class="sr-only">Toggle status</span>
                                    <span
                                        :class="{
                                            'translate-x-5': isActive,
                                            'translate-x-0': !isActive
                                        }"
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    ></span>
                                </button>
                                <span class="ml-3 text-sm" :class="{
                                    'text-gray-600': isLoading,
                                    'text-green-600': !isLoading && isActive,
                                    'text-red-600': !isLoading && !isActive
                                }">
                                    <template x-if="isLoading">
                                        <span>Updating...</span>
                                    </template>
                                    <template x-if="!isLoading">
                                        <span x-text="isActive ? 'Active' : 'Inactive'"></span>
                                    </template>
                                </span>
                            </div>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @endcan
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex space-x-2">
                                @can('view_product')
                                <button type="button" @click.prevent="viewProduct({{ $product->id }})" class="text-blue-600 hover:text-blue-900">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                @endcan

                                @can('edit_product')
                                <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                @endcan

                                @can('delete_product')
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @can('delete_product')
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <span id="selected-count-products" class="text-sm text-gray-600">0 selected</span>
                </div>
                <div>
                    <button type="submit" form="bulk-delete-products" id="bulk-delete-btn-products" disabled class="px-4 py-2 bg-red-600 text-white text-sm rounded disabled:opacity-50">Delete Selected</button>
                </div>
            </div>
            @endcan

            <div class="px-6 py-4">
                {{ $products->links() }}
            </div>
        @else
            <div class="p-6 text-center text-gray-500">
                No products found. @can('create_product')<a href="{{ route('products.create') }}" class="text-blue-600 hover:underline">Create one now</a>.@endcan
            </div>
        @endif
    </div>
        <!-- Product Details Drawer -->
        <div x-show="showViewDrawer"
             class="fixed inset-0 overflow-hidden z-50"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:leave="transition ease-in duration-200">
            <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                 @click="showViewDrawer = false"
                 aria-hidden="true"></div>
            <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex"
                 x-on:click.away.stop
                 x-on:keydown.escape.window="showViewDrawer = false"
                 x-show="showViewDrawer"
                 x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">
                <div class="relative w-screen max-w-2xl">
                    <div class="h-full flex flex-col bg-white shadow-xl overflow-y-scroll">
                        <div class="flex-1 py-6 overflow-y-auto px-4 sm:px-6">
                            <div class="flex items-start justify-between">
                                <h2 class="text-lg font-medium text-gray-900" id="slide-over-title">
                                    Product Details
                                </h2>
                                <div class="ml-3 h-7 flex items-center">
                                    <button @click="showViewDrawer = false" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <span class="sr-only">Close panel</span>
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-8">
                                <div x-show="isLoading">
                                    <x-product.loading-state />
                                </div>
                                <div x-show="!isLoading" x-html="productDetails"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all-products');
    const checkboxes = document.querySelectorAll('.row-checkbox-product');
    const deleteBtn = document.getElementById('bulk-delete-btn-products');
    const selectedCount = document.getElementById('selected-count-products');

    function updateState() {
        const selected = Array.from(checkboxes).filter(c => c.checked).length;
        if (selectedCount) selectedCount.textContent = `${selected} selected`;
        if (deleteBtn) deleteBtn.disabled = selected === 0;
        if (selectAll) selectAll.checked = selected > 0 && selected === checkboxes.length;
        if (selectAll) selectAll.indeterminate = selected > 0 && selected < checkboxes.length;
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateState();
        });
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateState));
    updateState();
});
</script>
@endpush
