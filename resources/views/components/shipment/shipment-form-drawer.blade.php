<div x-data="shipmentDrawer()"
     x-init="init()"
     @keydown.escape.window="if(open) close()"
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
             @click="close()"></div>

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
                    <!-- Header -->
                    <div class="px-4 py-6 bg-indigo-700 sm:px-6 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-white">
                            {{ isset($shipment) ? 'Edit Shipment' : 'Add New Shipment' }}
                        </h2>
                        <div class="ml-3 h-7 flex items-center">
                            <button type="button"
                                    @click="close()"
                                    class="bg-indigo-700 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                                <span class="sr-only">Close panel</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto py-6 px-4 sm:px-6">
                        {{ $slot }}
                    </div>

                    <!-- Footer -->
                    <div class="flex-shrink-0 px-4 py-4 flex justify-end border-t border-gray-200">
                        <button type="button"
                                @click="close()"
                                class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </button>
                        <button type="submit"
                                form="shipment-form"
                                class="ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ isset($shipment) ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function shipmentDrawer() {
        return {
            open: false,
            init() {
                // Listen for the show event
                window.addEventListener('shipment-form-drawer:show', () => {
                    this.open = true;
                });
            },
            close() {
                this.open = false;
                // Dispatch closed event
                window.dispatchEvent(new CustomEvent('shipment-form-drawer:closed'));
            }
        };
    }
</script>
@endpush
