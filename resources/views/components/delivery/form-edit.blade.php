<form x-data="deliveryForm" action="{{ route('deliveries.update', $delivery) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Batch Selection -->
        <div class="col-span-2">
            <label for="batch_id" class="block text-sm font-medium text-gray-700">Batch</label>
            <select id="batch_id" name="batch_id" required
                    class="mt-1 block w-full rounded-md @error('batch_id') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">
                <option value="">Select a batch</option>
                @foreach($batches as $batch)
                    <option value="{{ $batch->id }}" @if(old('batch_id', $delivery->batch_id) == $batch->id) selected @endif>
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
            <input type="datetime-local" id="delivery_date" name="delivery_date" required
                   value="{{ old('delivery_date', $delivery->delivery_date ? $delivery->delivery_date->format('Y-m-d\TH:i') : '') }}"
                   class="mt-1 block w-full rounded-md @error('delivery_date') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">
            @error('delivery_date')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Delivery Status -->
        <div>
            <label for="delivery_status" class="block text-sm font-medium text-gray-700">Delivery Status</label>
            <select id="delivery_status" name="delivery_status" required
                    class="mt-1 block w-full rounded-md @error('delivery_status') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">
                <option value="">Select status</option>
                <option value="pending" @if(old('delivery_status', $delivery->delivery_status) == 'pending') selected @endif>Pending</option>
                <option value="in_transit" @if(old('delivery_status', $delivery->delivery_status) == 'in_transit') selected @endif>In Transit</option>
                <option value="delivered" @if(old('delivery_status', $delivery->delivery_status) == 'delivered') selected @endif>Delivered</option>
                <option value="failed" @if(old('delivery_status', $delivery->delivery_status) == 'failed') selected @endif>Failed</option>
            </select>
            @error('delivery_status')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Delivery Person -->
        <div>
            <label for="delivery_person" class="block text-sm font-medium text-gray-700">Delivery Person</label>
            <input type="text" id="delivery_person" name="delivery_person" required
                   value="{{ old('delivery_person', $delivery->delivery_person) }}"
                   class="mt-1 block w-full rounded-md @error('delivery_person') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">
            @error('delivery_person')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Delivery Contact -->
        <div>
            <label for="delivery_contact" class="block text-sm font-medium text-gray-700">Contact Number</label>
            <input type="text" id="delivery_contact" name="delivery_contact"
                   value="{{ old('delivery_contact', $delivery->delivery_contact) }}"
                   class="mt-1 block w-full rounded-md @error('delivery_contact') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">
            @error('delivery_contact')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Delivery Address -->
        <div class="col-span-2">
            <label for="delivery_address" class="block text-sm font-medium text-gray-700">Delivery Address</label>
            <textarea id="delivery_address" name="delivery_address" rows="3" required
                      class="mt-1 block w-full rounded-md @error('delivery_address') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">{{ old('delivery_address', $delivery->delivery_address) }}</textarea>
            @error('delivery_address')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Delivery Notes -->
        <div class="col-span-2">
            <label for="delivery_notes" class="block text-sm font-medium text-gray-700">Delivery Notes</label>
            <textarea id="delivery_notes" name="delivery_notes" rows="3"
                      class="mt-1 block w-full rounded-md @error('delivery_notes') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">{{ old('delivery_notes', $delivery->delivery_notes) }}</textarea>
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
            <input type="text" id="signature_recipient_name" name="signature_recipient_name"
                   value="{{ old('signature_recipient_name', $delivery->signature_recipient_name) }}"
                   class="mt-1 block w-full rounded-md @error('signature_recipient_name') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">
            @error('signature_recipient_name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Signature Data -->
        <div>
            <label for="signature_data" class="block text-sm font-medium text-gray-700">Signature Data</label>
            <textarea id="signature_data" name="signature_data" rows="2"
                      class="mt-1 block w-full rounded-md @error('signature_data') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">{{ old('signature_data', $delivery->signature_data) }}</textarea>
            @error('signature_data')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Checkboxes -->
        <div class="col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center">
                <input type="checkbox" id="delivery_confirmation" name="delivery_confirmation" value="1"
                       @if(old('delivery_confirmation', $delivery->delivery_confirmation)) checked @endif
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="delivery_confirmation" class="ml-2 block text-sm text-gray-900">
                    Delivery Confirmed
                </label>
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="temperature_check" name="temperature_check" value="1"
                       @if(old('temperature_check', $delivery->temperature_check)) checked @endif
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="temperature_check" class="ml-2 block text-sm text-gray-900">
                    Temperature Check
                </label>
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="quality_check" name="quality_check" value="1"
                       @if(old('quality_check', $delivery->quality_check)) checked @endif
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="quality_check" class="ml-2 block text-sm text-gray-900">
                    Quality Check
                </label>
            </div>
        </div>

        <!-- Current Delivery Photos -->
        @if($delivery->delivery_photos && count($delivery->delivery_photos) > 0)
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Delivery Photos</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($delivery->delivery_photos as $photo)
                        <div class="relative">
                            <img src="{{ Storage::url($photo) }}" alt="Delivery Photo" class="w-full h-24 object-cover rounded-lg">
                        </div>
                    @endforeach
                </div>
                <p class="mt-1 text-xs text-gray-500">Upload new photos below to replace current ones</p>
            </div>
        @endif

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
            <textarea id="additional_notes" name="additional_notes" rows="3"
                      class="mt-1 block w-full rounded-md @error('additional_notes') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">{{ old('additional_notes', $delivery->additional_notes) }}</textarea>
            @error('additional_notes')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
        <a href="{{ route('deliveries.index') }}"
           class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Cancel
        </a>
        <button type="submit"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Update Delivery
        </button>
    </div>
</form>
<script>
    function deliveryForm() {
        return {
            loading: false,
            isEdit: true,
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
