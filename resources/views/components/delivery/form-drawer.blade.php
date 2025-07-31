<div x-data="deliveryFormDrawer()"
     @close-drawer.window="isOpen = false"
     x-init="init()"
     @keydown.escape.window="if(isOpen) close()"
     x-show="isOpen"
     class="fixed inset-0 overflow-hidden z-50"
     style="display: none;"
     x-transition:opacity.300ms
     @delivery-form-drawer:show.window="open($event.detail)">
    <div class="absolute inset-0 overflow-hidden">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
             x-show="isOpen"
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
                 x-show="isOpen"
                 x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">
                <div class="h-full flex flex-col bg-white shadow-xl">
                    <!-- Header -->
                    <div class="px-4 py-6 bg-indigo-700 sm:px-6 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-white" x-text="title || 'Add New Delivery'">
                            Add New Delivery
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

                        <div x-show="!loading" x-ref="formContainer">
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
    function deliveryFormDrawer() {
        return {
            isOpen: false,
            loading: false,
            title: 'Add New Delivery',
            form: null,

            init() {
                this.$nextTick(() => {
                    this.form = this.$el.querySelector('form');
                    if (this.form) {
                        this.form.addEventListener('submit', (e) => {});
                    }
                });

                // Listen for close event
                this.$el.addEventListener('close', () => this.close());

                // Check for validation errors on page load
                this.checkForErrors();
            },

            checkForErrors() {
                // Check if there are validation errors and URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                const action = urlParams.get('action');
                const hasErrors = document.querySelector('.text-red-600'); // Check for error messages

                if (hasErrors && action) {
                    // Open drawer if there are errors and action parameter
                    this.open({
                        title: action === 'create' ? 'Add New Delivery' : 'Edit Delivery',
                        mode: action
                    });
                }
            },

            open(detail = {}) {
                this.title = detail.title || 'Add New Delivery';
                this.isOpen = true;

                // Reset form if no delivery ID is provided (new delivery)
                if (!detail.deliveryId) {
                    this.resetForm();
                }

                // Focus the first input when drawer opens
                this.$nextTick(() => {
                    const firstInput = this.$el.querySelector('input, select, textarea');
                    if (firstInput) firstInput.focus();
                });
            },

            close() {
                this.isOpen = false;
                this.loading = false;

                // Small delay to allow the close animation to complete
                setTimeout(() => {
                    this.resetForm();
                }, 300);
            },

            resetForm() {
                if (this.form) {
                    this.form.reset();
                    // Reset form action to create route
                    this.form.action = '{{ route('deliveries.store') }}';

                    // Remove _method field if it exists
                    const methodInput = this.form.querySelector('input[name="_method"]');
                    if (methodInput) {
                        methodInput.remove();
                    }

                    // Reset file inputs
                    this.form.querySelectorAll('input[type="file"]').forEach(input => {
                        input.value = '';
                    });
                }
            },

            onDeliveryCreated(event) {
                // Close the drawer when a delivery is created successfully
                if (event.detail && event.detail.delivery) {
                    this.close();
                }
            }
        };
    }
</script>
@endpush
