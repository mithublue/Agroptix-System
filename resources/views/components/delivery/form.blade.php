<form x-data="deliveryForm" id="delivery-form" method="POST" action="{{ route('deliveries.store') }}" enctype="multipart/form-data" class="space-y-6" @submit.prevent="submitForm">
    @csrf
    <input type="hidden" name="_method" x-bind:value="isEdit ? 'PUT' : 'POST'">
    <input type="hidden" name="delivery_id" x-bind:value="deliveryId">

    <!-- Batch Selection -->
    <div>
        <label for="batch_id" class="block text-sm font-medium text-gray-700">Batch</label>
        <select id="batch_id" name="batch_id" required x-model="formData.batch_id"
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            <option value="">Select a batch</option>
            @foreach($batches as $batch)
                <option value="{{ $batch->id }}">
                    {{ $batch->batch_code ?? 'Batch #' . $batch->id }}
                </option>
            @endforeach
        </select>
        <p x-show="errors.batch_id" x-text="errors.batch_id" class="mt-1 text-sm text-red-600"></p>
    </div>

    <!-- Delivery Date -->
    <div>
        <label for="delivery_date" class="block text-sm font-medium text-gray-700">Delivery Date</label>
        <input type="datetime-local" id="delivery_date" name="delivery_date" required x-model="formData.delivery_date"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        <p x-show="errors.delivery_date" x-text="errors.delivery_date" class="mt-1 text-sm text-red-600"></p>
    </div>

    <!-- Delivery Person -->
    <div>
        <label for="delivery_person" class="block text-sm font-medium text-gray-700">Delivery Person</label>
        <input type="text" id="delivery_person" name="delivery_person" required x-model="formData.delivery_person"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        <p x-show="errors.delivery_person" x-text="errors.delivery_person" class="mt-1 text-sm text-red-600"></p>
    </div>

    <!-- Delivery Contact -->
    <div>
        <label for="delivery_contact" class="block text-sm font-medium text-gray-700">Contact Number</label>
        <input type="tel" id="delivery_contact" name="delivery_contact" required x-model="formData.delivery_contact"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        <p x-show="errors.delivery_contact" x-text="errors.delivery_contact" class="mt-1 text-sm text-red-600"></p>
    </div>

    <!-- Delivery Address -->
    <div>
        <label for="delivery_address" class="block text-sm font-medium text-gray-700">Delivery Address</label>
        <textarea id="delivery_address" name="delivery_address" rows="3" required x-model="formData.delivery_address"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
        <p x-show="errors.delivery_address" x-text="errors.delivery_address" class="mt-1 text-sm text-red-600"></p>
    </div>

    <!-- Delivery Status -->
    <div>
        <label for="delivery_status" class="block text-sm font-medium text-gray-700">Status</label>
        <select id="delivery_status" name="delivery_status" required x-model="formData.delivery_status"
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            <option value="pending">Pending</option>
            <option value="in_transit">In Transit</option>
            <option value="delivered">Delivered</option>
            <option value="failed">Failed</option>
        </select>
        <p x-show="errors.delivery_status" x-text="errors.delivery_status" class="mt-1 text-sm text-red-600"></p>
    </div>

    <!-- Current Photos (for edit mode) -->
    <div x-show="isEdit && currentPhotos.length > 0">
        <label class="block text-sm font-medium text-gray-700 mb-2">Current Photos</label>
        <div class="grid grid-cols-3 gap-2 mb-4">
            <template x-for="photo in currentPhotos" :key="photo">
                <img :src="`/storage/${photo}`" alt="Current photo" class="w-full h-20 object-cover rounded">
            </template>
        </div>
        <p class="text-xs text-gray-500">Upload new photos below to replace current ones</p>
    </div>

    <!-- Delivery Photos -->
    <div>
        <label for="delivery_photos" class="block text-sm font-medium text-gray-700">Delivery Photos</label>
        <input type="file" id="delivery_photos" name="delivery_photos[]" multiple
               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        <p x-show="errors.delivery_photos" x-text="errors.delivery_photos" class="mt-1 text-sm text-red-600"></p>
    </div>

    <!-- Notes -->
    <div>
        <label for="delivery_notes" class="block text-sm font-medium text-gray-700">Notes</label>
        <textarea id="delivery_notes" name="delivery_notes" rows="3" x-model="formData.delivery_notes"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
        <p x-show="errors.delivery_notes" x-text="errors.delivery_notes" class="mt-1 text-sm text-red-600"></p>
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end space-x-3 pt-4">
        <button type="button" @click="$dispatch('close-drawer')" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Cancel
        </button>
        <button type="submit" :disabled="loading" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
            <span x-show="!loading" x-text="isEdit ? 'Update Delivery' : 'Create Delivery'"></span>
            <span x-show="loading">Processing...</span>
        </button>
    </div>
</form>
<script>
    function deliveryForm() {
        return {
            loading: false,
            isEdit: false,
            deliveryId: null,
            errors: {},
            currentPhotos: [],
            formData: {
                batch_id: '',
                delivery_date: '',
                delivery_person: '',
                delivery_contact: '',
                delivery_address: '',
                delivery_status: 'pending',
                delivery_notes: ''
            },

            init() {
                // Listen for drawer open events
                window.addEventListener('delivery-form-drawer:show', (event) => {
                    this.handleDrawerOpen(event.detail);
                });

                // Listen for close drawer events
                window.addEventListener('close-drawer', () => {
                    this.resetForm();
                });
            },

            handleDrawerOpen(detail) {
                this.resetForm();

                if (detail.mode === 'edit' && detail.deliveryData) {
                    this.isEdit = true;
                    this.deliveryId = detail.deliveryId;
                    this.loadDeliveryData(detail.deliveryData);
                } else {
                    this.isEdit = false;
                    this.deliveryId = null;
                }
            },

            loadDeliveryData(delivery) {
                this.formData = {
                    batch_id: delivery.batch_id || '',
                    delivery_date: delivery.delivery_date ? new Date(delivery.delivery_date).toISOString().slice(0, 16) : '',
                    delivery_person: delivery.delivery_person || '',
                    delivery_contact: delivery.delivery_contact || '',
                    delivery_address: delivery.delivery_address || '',
                    delivery_status: delivery.delivery_status || 'pending',
                    delivery_notes: delivery.delivery_notes || ''
                };

                this.currentPhotos = delivery.delivery_photos || [];
            },

            async submitForm() {
                this.loading = true;
                this.errors = {};

                try {
                    const form = document.getElementById('delivery-form');
                    const formData = new FormData(form);

                    // Set the correct URL and method for edit mode
                    let url = this.isEdit ? `/deliveries/${this.deliveryId}` : '/deliveries';

                    const response = await axios.post(url, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    // Success - dispatch event and close drawer
                    window.dispatchEvent(new CustomEvent(this.isEdit ? 'delivery-updated' : 'delivery-created', {
                        detail: response.data
                    }));

                    // Show success message
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: {
                            message: `Delivery ${this.isEdit ? 'updated' : 'created'} successfully!`,
                            type: 'success'
                        }
                    }));

                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        // Validation errors
                        this.errors = error.response.data.errors || {};
                    } else {
                        // Other errors
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: {
                                message: 'An error occurred. Please try again.',
                                type: 'error'
                            }
                        }));
                    }
                } finally {
                    this.loading = false;
                }
            },

            resetForm() {
                this.loading = false;
                this.isEdit = false;
                this.deliveryId = null;
                this.errors = {};
                this.currentPhotos = [];
                this.formData = {
                    batch_id: '',
                    delivery_date: '',
                    delivery_person: '',
                    delivery_contact: '',
                    delivery_address: '',
                    delivery_status: 'pending',
                    delivery_notes: ''
                };

                // Reset file input
                const fileInput = document.getElementById('delivery_photos');
                if (fileInput) {
                    fileInput.value = '';
                }
            }
        }
    }
</script>
