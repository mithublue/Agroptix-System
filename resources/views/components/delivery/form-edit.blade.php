<div x-data="deliveryForm()" x-init="init()" class="space-y-6">
    <form @submit.prevent="submitForm" enctype="multipart/form-data">
        <input type="hidden" name="_method" x-bind:value="deliveryId ? 'PUT' : 'POST'">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Batch Selection -->
        <div class="col-span-2">
            <label for="batch_id" class="block text-sm font-medium text-gray-700">Batch</label>
            <select id="batch_id" name="batch_id" x-model="formData.batch_id" required
                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Select a batch</option>
                @foreach($batches as $batch)
                    <option value="{{ $batch->id }}">
                        Batch #{{ $batch->id }} - {{ $batch->name ?? 'Unnamed Batch' }}
                    </option>
                @endforeach
            </select>
            @error('batch_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Delivery Date -->
        <div>
            <label for="delivery_date" class="block text-sm font-medium text-gray-700">Delivery Date</label>
            <input type="datetime-local" id="delivery_date" name="delivery_date" x-model="formData.delivery_date" required
                   class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('delivery_date')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Delivery Status -->
        <div>
            <label for="delivery_status" class="block text-sm font-medium text-gray-700">Delivery Status</label>
            <select id="delivery_status" name="delivery_status" x-model="formData.delivery_status" required
                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Select status</option>
                <option value="pending">Pending</option>
                <option value="in_transit">In Transit</option>
                <option value="delivered">Delivered</option>
                <option value="failed">Failed</option>
            </select>
            @error('delivery_status')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Delivery Person -->
        <div>
            <label for="delivery_person" class="block text-sm font-medium text-gray-700">Delivery Person</label>
            <input type="text" id="delivery_person" name="delivery_person" x-model="formData.delivery_person" required
                   class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('delivery_person')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Delivery Contact -->
        <div>
            <label for="delivery_contact" class="block text-sm font-medium text-gray-700">Contact Number</label>
            <input type="text" id="delivery_contact" name="delivery_contact" x-model="formData.delivery_contact"
                   class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('delivery_contact')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Delivery Address -->
        <div class="col-span-2">
            <label for="delivery_address" class="block text-sm font-medium text-gray-700">Delivery Address</label>
            <textarea id="delivery_address" name="delivery_address" rows="3" x-model="formData.delivery_address" required
                      class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            @error('delivery_address')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Delivery Notes -->
        <div class="col-span-2">
            <label for="delivery_notes" class="block text-sm font-medium text-gray-700">Delivery Notes</label>
            <textarea id="delivery_notes" name="delivery_notes" rows="3" x-model="formData.delivery_notes"
                      class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            @error('delivery_notes')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Signature Section -->
        <div class="col-span-2">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Signature & Confirmation</h3>
        </div>

        <!-- Recipient Name -->
        <div>
            <label for="signature_recipient_name" class="block text-sm font-medium text-gray-700">Recipient Name</label>
            <input type="text" id="signature_recipient_name" name="signature_recipient_name" x-model="formData.signature_recipient_name"
                   class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('signature_recipient_name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Signature Data -->
        <div>
            <label for="signature_data" class="block text-sm font-medium text-gray-700">Signature Data</label>
            <textarea id="signature_data" name="signature_data" rows="2" x-model="formData.signature_data"
                      class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            @error('signature_data')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Checkboxes -->
        <div class="col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center">
                <input type="checkbox" id="delivery_confirmation" name="delivery_confirmation" value="1"
                       x-model="formData.delivery_confirmation"
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="delivery_confirmation" class="ml-2 block text-sm text-gray-900">
                    Delivery Confirmed
                </label>
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="temperature_check" name="temperature_check" value="1"
                       x-model="formData.temperature_check"
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="temperature_check" class="ml-2 block text-sm text-gray-900">
                    Temperature Check
                </label>
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="quality_check" name="quality_check" value="1"
                       x-model="formData.quality_check"
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="quality_check" class="ml-2 block text-sm text-gray-900">
                    Quality Check
                </label>
            </div>
        </div>

        <!-- Current Delivery Photos -->
        <template x-if="formData.delivery_photos && formData.delivery_photos.length > 0">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Delivery Photos</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <template x-for="(photo, index) in formData.delivery_photos" :key="index">
                        <div class="relative">
                            <img :src="photo.url" :alt="'Delivery Photo ' + (index + 1)" class="w-full h-24 object-cover rounded-lg">
                        </div>
                    </template>
                </div>
                <p class="mt-1 text-xs text-gray-500">Upload new photos below to replace current ones</p>
            </div>
        </template>

        <!-- Delivery Photos -->
        <div class="col-span-2">
            <label for="delivery_photos" class="block text-sm font-medium text-gray-700">Delivery Photos</label>
            <input type="file" name="delivery_photos[]" id="delivery_photos" multiple
                   class="mt-1 block w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-semibold
                                          @error('delivery_photos') file:bg-red-50 file:text-red-700 @else file:bg-indigo-50 file:text-indigo-700 @enderror
                                          hover:file:bg-indigo-100">
            @error('delivery_photos')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @else
                <p class="mt-1 text-xs text-gray-500">Upload multiple photos if needed (will replace existing photos)</p>
                @enderror
        </div>

        <!-- Additional Notes -->
        <div class="col-span-2">
            <label for="additional_notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
            <textarea id="additional_notes" name="additional_notes" rows="3" x-model="formData.additional_notes"
                      class="mt-1 block w-full rounded-md @error('additional_notes') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror"></textarea>
            @error('additional_notes')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end space-x-3">
        <button type="button" @click="$dispatch('close-drawer')"
                class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Cancel
        </button>
        <button type="submit"
                :disabled="loading"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
            <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span x-show="!loading">Save Delivery</span>
            <span x-show="loading">Saving...</span>
        </button>
    </div>
    </form>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('deliveryForm', () => ({
        loading: false,
        deliveryId: null,
        formData: {
            batch_id: '',
            delivery_date: '',
            delivery_status: '',
            delivery_person: '',
            delivery_contact: '',
            delivery_address: '',
            delivery_notes: '',
            signature_recipient_name: '',
            signature_data: '',
            delivery_confirmation: false,
            temperature_check: false,
            quality_check: false,
            delivery_photos: []
        },
        
        init() {
            // Listen for edit-delivery event to load delivery data
            this.$el.addEventListener('edit-delivery', (event) => {
                this.loadDelivery(event.detail.deliveryId);
            });
            
            // Reset form when drawer is closed
            this.$el.addEventListener('drawer-closed', () => {
                this.resetForm();
            });
        },
        
        async loadDelivery(deliveryId) {
            if (!deliveryId) {
                this.resetForm();
                return;
            }
            
            this.loading = true;
            this.deliveryId = deliveryId;
            
            try {
                const response = await axios.get(`/deliveries/${deliveryId}/edit`);
                const delivery = response.data.delivery;
                
                // Format dates for datetime-local input
                if (delivery.delivery_date) {
                    const date = new Date(delivery.delivery_date);
                    delivery.delivery_date = date.toISOString().slice(0, 16);
                }
                
                // Map the delivery data to form fields
                Object.keys(this.formData).forEach(key => {
                    if (delivery[key] !== undefined) {
                        this.formData[key] = delivery[key];
                    }
                });
                
                // Handle delivery photos
                if (delivery.delivery_photos && Array.isArray(delivery.delivery_photos)) {
                    this.formData.delivery_photos = delivery.delivery_photos.map(photo => ({
                        url: photo.startsWith('http') ? photo : `/storage/${photo}`,
                        path: photo
                    }));
                }
                
                // Show the form
                this.$dispatch('show-edit-form');
            } catch (error) {
                console.error('Error loading delivery:', error);
                this.$dispatch('toast', {
                    type: 'error',
                    message: 'Failed to load delivery data. Please try again.'
                });
            } finally {
                this.loading = false;
            }
        },
        
        async submitForm() {
            this.loading = true;
            
            try {
                const formData = new FormData(this.$el.querySelector('form'));
                const url = this.deliveryId 
                    ? `/deliveries/${this.deliveryId}`
                    : '/deliveries';
                
                const response = await axios({
                    method: this.deliveryId ? 'PUT' : 'POST',
                    url: url,
                    data: formData,
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                this.$dispatch('toast', {
                    type: 'success',
                    message: response.data.message || 'Delivery saved successfully!'
                });
                
                // Emit event to refresh deliveries list
                this.$dispatch('delivery-saved');
                
                // Close the drawer
                this.$dispatch('close-drawer');
                
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    // Handle validation errors
                    const errors = error.response.data.errors;
                    let errorMessage = 'Please fix the following errors:\n';
                    
                    Object.keys(errors).forEach(field => {
                        errorMessage += `\n${errors[field].join('\n')}`;
                    });
                    
                    this.$dispatch('toast', {
                        type: 'error',
                        message: errorMessage
                    });
                } else {
                    console.error('Error saving delivery:', error);
                    this.$dispatch('toast', {
                        type: 'error',
                        message: error.response?.data?.message || 'Failed to save delivery. Please try again.'
                    });
                }
            } finally {
                this.loading = false;
            }
        },
        
        resetForm() {
            this.deliveryId = null;
            this.formData = {
                batch_id: '',
                delivery_date: '',
                delivery_status: '',
                delivery_person: '',
                delivery_contact: '',
                delivery_address: '',
                delivery_notes: '',
                signature_recipient_name: '',
                signature_data: '',
                delivery_confirmation: false,
                temperature_check: false,
                quality_check: false,
                delivery_photos: []
            };
            
            // Reset file inputs
            const fileInputs = this.$el.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.value = '';
            });
        }
    }));
});
</script>
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
                delivery_notes: '',
                signature_recipient_name: '',
                signature_data: '',
                delivery_confirmation: false,
                temperature_check: false,
                quality_check: false,
                delivery_photos: []
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
<script>
    // Function to fill form with test data
    function fillTestData() {
        // Set form values
        const now = new Date();
        const formattedDate = now.toISOString().slice(0, 16);
        const futureDate = new Date();
        futureDate.setDate(now.getDate() + 1);
        const futureFormattedDate = futureDate.toISOString().slice(0, 16);

        // Basic info
        document.getElementById('batch_id').value = '{{ $batches->first()->id ?? '1' }}';
        document.getElementById('delivery_date').value = formattedDate;
        document.getElementById('delivery_status').value = 'in_transit';
        document.getElementById('delivery_person').value = 'John Doe';
        document.getElementById('delivery_contact').value = '+1 (555) 123-4567';
        document.getElementById('delivery_address').value = '123 Main St, Anytown, AN 12345';

        // Signature and checks
        document.getElementById('signature_recipient_name').value = 'Jane Smith';
        document.getElementById('delivery_confirmation').checked = true;
        document.getElementById('temperature_check').checked = true;
        document.getElementById('quality_check').checked = true;

        // Customer feedback
        document.getElementById('customer_rating').value = '5';
        document.getElementById('additional_notes').value = 'Delivery was on time and in perfect condition.';
        document.getElementById('customer_comments').value = 'Great service, will order again!';
        document.getElementById('customer_complaints').value = 'None';
        document.getElementById('feedback_status').value = 'acknowledged';
        document.getElementById('feedback_submitted_at').value = futureFormattedDate;
    }

    // Add event listener for the test data button
    document.addEventListener('DOMContentLoaded', function() {
        const fillButton = document.getElementById('fillTestData');
        if (fillButton) {
            fillButton.addEventListener('click', fillTestData);
        }
        // Set default delivery date to now
        const now = new Date();
        // Format the date to YYYY-MM-DDTHH:MM
        const formattedDate = now.toISOString().slice(0, 16);
        document.getElementById('delivery_date').value = formattedDate;

        // Set feedback date to now if feedback status is being set
        const feedbackStatus = document.getElementById('feedback_status');
        if (feedbackStatus) {
            feedbackStatus.addEventListener('change', function() {
                if (this.value && !document.getElementById('feedback_submitted_at').value) {
                    document.getElementById('feedback_submitted_at').value = formattedDate;
                }
            });
        }
    });
</script>
