<div x-data="deliveryDrawer()"
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
                        <h2 class="text-lg font-medium text-white" x-text="title">
                            {{ isset($delivery) ? 'Edit Delivery' : 'Add New Delivery' }}
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
                        <div x-show="loading" class="flex justify-center py-8">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                        </div>
                        
                        <div x-show="!loading">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function deliveryDrawer() {
        return {
            open: false,
            loading: false,
            title: 'Add New Delivery',
            url: '',
            method: 'POST',
            form: null,
            
            init() {
                // Listen for the open-delivery-drawer event
                window.addEventListener('open-delivery-drawer', (event) => {
                    this.openDrawer(event.detail);
                });
                
                // Set up the form reference
                this.$nextTick(() => {
                    this.form = this.$refs.deliveryForm;
                });
            },
            
            openDrawer(detail) {
                this.title = detail.title || 'Add New Delivery';
                this.url = detail.url || '';
                this.method = detail.method || 'POST';
                
                // If we have a delivery ID, fetch the data
                if (detail.deliveryId) {
                    this.fetchDelivery(detail.deliveryId);
                } else {
                    this.resetForm();
                    this.open = true;
                }
            },
            
            async fetchDelivery(deliveryId) {
                this.loading = true;
                this.open = true;
                
                try {
                    const response = await fetch(`/deliveries/${deliveryId}/edit`);
                    if (!response.ok) throw new Error('Failed to fetch delivery');
                    
                    const data = await response.json();
                    this.populateForm(data);
                } catch (error) {
                    console.error('Error fetching delivery:', error);
                    // Show error to user
                    this.$dispatch('notify', {
                        type: 'error',
                        message: 'Failed to load delivery details.'
                    });
                    this.close();
                } finally {
                    this.loading = false;
                }
            },
            
            populateForm(data) {
                // This will be handled by the form component
                this.$dispatch('populate-form', { delivery: data });
            },
            
            resetForm() {
                if (this.form) {
                    this.form.reset();
                    // Reset any file inputs
                    this.form.querySelectorAll('input[type="file"]').forEach(input => {
                        input.value = '';
                    });
                }
            },
            
            close() {
                this.open = false;
                this.loading = false;
                this.$dispatch('delivery-drawer-closed');
            },
            
            handleSubmit() {
                if (this.form) {
                    this.form.submit();
                }
            }
        };
    }
</script>
@endpush
