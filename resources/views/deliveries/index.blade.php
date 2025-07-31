<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Delivery List') }}
            </h2>
            <div class="flex space-x-2">
                @can('create_deliveries')
                    <button @click="$dispatch('delivery-form-drawer:show')"
                            class="add-delivery-btn inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Add New Delivery') }}
                    </button>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="deliveryIndex()" x-init="init()">
        <!-- Delivery Form Drawer -->
        <x-delivery.form-drawer>
            <x-delivery.form :batches="\App\Models\Batch::latest()->take(50)->get()" />
        </x-delivery.form-drawer>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Filters -->
            <div class="mb-6 bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Filters</h3>
                <form method="GET" action="{{ route('deliveries.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                        <!-- Status Filter -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <!-- Batch Filter -->
                        <div>
                            <label for="batch_id" class="block text-sm font-medium text-gray-700 mb-1">Batch</label>
                            <select id="batch_id" name="batch_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">All Batches</option>
                                @foreach(\App\Models\Batch::pluck('batch_code', 'id') as $id => $name)
                                    <option value="{{ $id }}" {{ request('batch_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                                   class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        </div>

                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                            <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                                   class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        </div>
                        <div class="flex space-x-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Apply Filters
                            </button>
                            <a href="{{ route('deliveries.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Deliveries Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if($deliveries->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($deliveries as $delivery)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $delivery->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $delivery->batch->name ?? 'N/A' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $delivery->batch->batch_number ?? '' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $delivery->delivery_date ? $delivery->delivery_date->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $delivery->recipient_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $delivery->recipient_phone }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'in_transit' => 'bg-blue-100 text-blue-800',
                                                    'delivered' => 'bg-green-100 text-green-800',
                                                    'cancelled' => 'bg-red-100 text-red-800',
                                                ][$delivery->delivery_status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors }}">
                                                {{ ucfirst(str_replace('_', ' ', $delivery->delivery_status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                @can('view_deliveries')
                                                    <a href="{{ route('deliveries.show', $delivery) }}" class="text-indigo-600 hover:text-indigo-900" title="View Delivery">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </a>
                                                @endcan

                                                @can('edit_deliveries')
                                                    <button data-id="{{ $delivery->id }}" class="edit-delivery-btn text-yellow-600 hover:text-yellow-900" title="Edit Delivery">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </button>
                                                @endcan

                                                @can('delete_deliveries')
                                                    <div class="inline-flex items-center" x-data="{ showConfirm: false }">
                                                        <button x-show="!showConfirm"
                                                                @click="showConfirm = true"
                                                                class="text-red-600 hover:text-red-900"
                                                                x-tooltip.raw="Delete">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                        <div x-show="showConfirm" class="flex items-center space-x-2">
                                                            <span class="text-xs text-gray-500">Are you sure?</span>
                                                            <form action="{{ route('deliveries.destroy', $delivery->id) }}" method="POST" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-medium">
                                                                    Yes
                                                                </button>
                                                            </form>
                                                            <button @click="showConfirm = false" class="text-gray-500 hover:text-gray-700 text-xs font-medium">
                                                                No
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($deliveries->hasPages())
                        <div class="px-6 py-4 bg-gray-50">
                            {{ $deliveries->withQueryString()->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-6 text-center text-gray-500">
                        No deliveries found.
                        @can('create_deliveries')
                            <a href="{{ route('deliveries.create') }}" class="text-indigo-600 hover:underline">Create one now</a>.
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function deliveryIndex() {
            return {
                init() {
                    // Check URL parameters on page load
                    this.checkUrlParams();

                    // Listen for popstate events (back/forward navigation)
                    window.addEventListener('popstate', () => {
                        this.checkUrlParams();
                    });

                    // Listen for edit button clicks
                    document.querySelectorAll('.edit-delivery-btn').forEach(button => {
                        button.addEventListener('click', (e) => {
                            e.preventDefault();
                            const deliveryId = button.dataset.id;
                            this.openEditDrawer(deliveryId);
                        });
                    });

                    // Listen for successful form submissions
                    window.addEventListener('delivery-created', () => {
                        this.closeDrawer();
                        // Refresh the page or update the list
                        window.location.reload();
                    });

                    window.addEventListener('delivery-updated', () => {
                        this.closeDrawer();
                        // Refresh the page or update the list
                        window.location.reload();
                    });

                    // Listen for toast notifications
                    window.addEventListener('show-toast', (event) => {
                        const { message, type } = event.detail;
                        window.Toast.fire({
                            icon: type === 'success' ? 'success' : 'error',
                            title: message
                        });
                    });
                },

                checkUrlParams() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const action = urlParams.get('action');
                    const deliveryId = urlParams.get('delivery_id');

                    if (action === 'create') {
                        this.openCreateDrawer();
                    } else if (action === 'edit' && deliveryId) {
                        this.openEditDrawer(deliveryId);
                    }
                },

                openCreateDrawer() {
                    // Update URL without page reload
                    const url = new URL(window.location);
                    url.searchParams.set('action', 'create');
                    window.history.pushState({}, '', url);

                    // Dispatch event to open drawer
                    window.dispatchEvent(new CustomEvent('delivery-form-drawer:show', {
                        detail: {
                            title: 'Add New Delivery',
                            mode: 'create'
                        }
                    }));
                },

                async openEditDrawer(deliveryId) {
                    try {
                        // Update URL without page reload
                        const url = new URL(window.location);
                        url.searchParams.set('action', 'edit');
                        url.searchParams.set('delivery_id', deliveryId);
                        window.history.pushState({}, '', url);

                        // Fetch delivery data
                        const response = await axios.get(`/deliveries/${deliveryId}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (response.data.success) {
                            // Dispatch event to open drawer with data
                            window.dispatchEvent(new CustomEvent('delivery-form-drawer:show', {
                                detail: {
                                    title: 'Edit Delivery',
                                    mode: 'edit',
                                    deliveryId: deliveryId,
                                    deliveryData: response.data.data
                                }
                            }));
                        }
                    } catch (error) {
                        console.error('Error fetching delivery data:', error);
                        alert('Error loading delivery data. Please try again.');
                    }
                },

                closeDrawer() {
                    // Remove URL parameters
                    const url = new URL(window.location);
                    url.searchParams.delete('action');
                    url.searchParams.delete('delivery_id');
                    window.history.pushState({}, '', url);

                    // Close the drawer
                    window.dispatchEvent(new CustomEvent('close-drawer'));
                }
            };
        }
    </script>
</x-app-layout>
