<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Packaging') }}
            </h2>
            @can('create_packaging')
            <button @click="$store.drawer.open = true; $store.drawer.packagingData = null" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Add New Packaging
            </button>
            @endcan
        </div>
    </x-slot>
    
    <!-- Success Message -->
    <div x-data="{ show: {{ session('success') ? 'true' : 'false' }}" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 5000)"
         class="fixed top-4 right-4 z-50">
        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded shadow-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>
                </div>
                <div class="ml-4">
                    <button @click="show = false" class="text-green-500 hover:text-green-600 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="py-6" x-data="{
        init() {
            // Listen for refresh event
            this.$el.addEventListener('refresh-data', () => {
                // Reload the page to refresh data
                window.location.reload();
            });
            
            // Listen for notifications
            this.$el.addEventListener('notify', (event) => {
                const { type, message } = event.detail;
                // You could implement a toast notification system here
                alert(`${type.toUpperCase()}: ${message}`);
            });
        }
    }">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Packaging Records
                    </h3>
                </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Batch
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($packages as $package)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $package->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($package->batch)
                                        {{ $package->batch->batch_code }} (ID: {{ $package->batch_id }})
                                    @else
                                        <span class="text-gray-400">No batch assigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        @can('view_packaging')
                                        <a href="{{ route('admin.packaging.show', $package->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        @endcan
                                        
                                        @can('edit_packaging')
                                        <a href="{{ route('admin.packaging.edit', $package->id) }}" class="text-yellow-600 hover:text-yellow-900">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        @endcan
                                        
                                        @can('delete_packaging')
                                        <form action="{{ route('admin.packaging.destroy', $package->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this packaging record?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No packaging records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($packages->hasPages())
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
                    {{ $packages->links() }}
                </div>
            @endif
        </div>
    </div>
    </div>
        <!-- Side Drawer -->
        <div x-data="{
            open: false,
            packagingData: null
        }" 
        x-init="
            $store.drawer = {
                open: false,
                packagingData: null
            };
            $watch('open', value => $store.drawer.open = value);
            $watch('$store.drawer.open', value => open = value);
            $watch('$store.drawer.packagingData', value => packagingData = value);
        "
        @keydown.escape.window="if(open) open = false"
        class="fixed inset-0 overflow-hidden z-50"
        style="display: none;"
        x-show="open"
        x-transition:opacity.300ms>
            <div class="absolute inset-0 overflow-hidden">
                <!-- Overlay -->
                <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                     x-show="open" 
                     x-transition:enter="ease-in-out duration-500" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in-out duration-500" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0"
                     @click="open = false"></div>
                
                <!-- Drawer Panel -->
                <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                    <div class="w-screen max-w-md" 
                         x-show="open" 
                         x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" 
                         x-transition:enter-start="translate-x-full" 
                         x-transition:enter-end="translate-x-0" 
                         x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" 
                         x-transition:leave-start="translate-x-0" 
                         x-transition:leave-end="translate-x-full">
                        <div class="h-full flex flex-col bg-white shadow-xl">
                            @php
                                $batches = \App\Models\Batch::with('product')->latest()->get();
                                $rpcUnits = \App\Models\RpcUnit::all();
                                $users = \App\Models\User::all();
                            @endphp
                            <x-packaging.form :batches="$batches" :rpcUnits="$rpcUnits" :users="$users" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        // Initialize Alpine store if not already initialized
        document.addEventListener('alpine:init', () => {
            if (!window.Alpine.store('drawer')) {
                window.Alpine.store('drawer', {
                    open: false,
                    packagingData: null
                });
            }
            
            // Add global event dispatcher
            window.dispatchEvent = function(event, detail = {}) {
                window.dispatchEvent(new CustomEvent(event, { detail }));
                return true;
            };
        });
        </script>
    </x-app-layout>
