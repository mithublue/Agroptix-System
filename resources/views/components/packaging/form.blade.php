@props([
    'batches' => collect(),
    'rpcUnits' => collect(),
    'users' => collect(),
])

<div x-data="packagingForm()" class="h-full flex flex-col">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">
            <span x-text="editing ? 'Edit Packaging' : 'Add New Packaging'"></span>
        </h3>
    </div>

    <div class="flex-1 overflow-y-auto p-6">
        <form id="packaging-form" x-ref="packagingForm" @submit.prevent="submitForm">
            @csrf
            <input type="hidden" name="_method" x-bind:value="editing ? 'PUT' : 'POST'">
            
            <div class="space-y-6">
                <!-- Batch Selection -->
                <div>
                    <label for="batch_id" class="block text-sm font-medium text-gray-700">Batch</label>
                    <select id="batch_id" name="batch_id" x-model="formData.batch_id"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">Select a batch</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}">{{ $batch->batch_code }} - {{ $batch->product->name ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                    <p x-show="errors.batch_id" x-text="errors.batch_id" class="mt-1 text-sm text-red-600"></p>
                </div>

                <!-- Package Type -->
                <div>
                    <label for="package_type" class="block text-sm font-medium text-gray-700">Package Type</label>
                    <input type="text" id="package_type" name="package_type" x-model="formData.package_type"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <p x-show="errors.package_type" x-text="errors.package_type" class="mt-1 text-sm text-red-600"></p>
                </div>

                <!-- Material Type -->
                <div>
                    <label for="material_type" class="block text-sm font-medium text-gray-700">Material Type</label>
                    <input type="text" id="material_type" name="material_type" x-model="formData.material_type"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <p x-show="errors.material_type" x-text="errors.material_type" class="mt-1 text-sm text-red-600"></p>
                </div>

                <!-- Unit Weight -->
                <div>
                    <label for="unit_weight_packaging" class="block text-sm font-medium text-gray-700">Unit Weight (g)</label>
                    <input type="number" step="0.001" id="unit_weight_packaging" name="unit_weight_packaging" x-model="formData.unit_weight_packaging"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <p x-show="errors.unit_weight_packaging" x-text="errors.unit_weight_packaging" class="mt-1 text-sm text-red-600"></p>
                </div>

                <!-- Quantity of Units -->
                <div>
                    <label for="quantity_of_units" class="block text-sm font-medium text-gray-700">Quantity of Units</label>
                    <input type="number" id="quantity_of_units" name="quantity_of_units" x-model="formData.quantity_of_units"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <p x-show="errors.quantity_of_units" x-text="errors.quantity_of_units" class="mt-1 text-sm text-red-600"></p>
                </div>

                <!-- RPC Unit -->
                <div>
                    <label for="rpc_unit_id" class="block text-sm font-medium text-gray-700">RPC Unit (Optional)</label>
                    <select id="rpc_unit_id" name="rpc_unit_id" x-model="formData.rpc_unit_id"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">Select an RPC unit (optional)</option>
                        @foreach($rpcUnits as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->rpc_identifier }} ({{ $unit->material_type }})</option>
                        @endforeach
                    </select>
                    <p x-show="errors.rpc_unit_id" x-text="errors.rpc_unit_id" class="mt-1 text-sm text-red-600"></p>
                </div>

                <!-- Packer -->
                <div>
                    <label for="packer_id" class="block text-sm font-medium text-gray-700">Packer</label>
                    <select id="packer_id" name="packer_id" x-model="formData.packer_id"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">Select a packer</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <p x-show="errors.packer_id" x-text="errors.packer_id" class="mt-1 text-sm text-red-600"></p>
                </div>

                <!-- Packaging Location -->
                <div>
                    <label for="packaging_location" class="block text-sm font-medium text-gray-700">Packaging Location</label>
                    <input type="text" id="packaging_location" name="packaging_location" x-model="formData.packaging_location"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <p x-show="errors.packaging_location" x-text="errors.packaging_location" class="mt-1 text-sm text-red-600"></p>
                </div>

                <!-- Cleanliness Checklist -->
                <div class="flex items-center">
                    <input type="checkbox" id="cleanliness_checklist" name="cleanliness_checklist" x-model="formData.cleanliness_checklist"
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="cleanliness_checklist" class="ml-2 block text-sm text-gray-700">
                        Cleanliness Checklist Completed
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" @click="$dispatch('close-drawer')" 
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </button>
                <button type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        :disabled="loading">
                    <span x-show="!loading">
                        <span x-text="editing ? 'Update' : 'Create'"></span> Packaging
                    </span>
                    <span x-show="loading" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function packagingForm() {
    return {
        loading: false,
        editing: false,
        formData: {
            batch_id: '',
            package_type: '',
            material_type: '',
            unit_weight_packaging: '',
            quantity_of_units: '',
            rpc_unit_id: '',
            packer_id: '',
            packaging_location: '',
            cleanliness_checklist: false,
            _method: 'POST'
        },
        errors: {},
        store: null,
        
        init() {
            // Store the Alpine store reference
            this.store = window.Alpine.store('drawer');
            
            // Initialize the form based on store state
            if (this.store && this.store.packagingData) {
                this.editing = true;
                this.formData = {
                    ...this.formData,
                    ...this.store.packagingData,
                    _method: 'PUT'
                };
            }
            
            // Listen for store changes
            if (this.store) {
                this.$watch('store.packagingData', (data) => {
                    if (data) {
                        this.editing = true;
                        this.formData = {
                            ...this.formData,
                            ...data,
                            _method: 'PUT'
                        };
                    } else {
                        this.resetForm();
                    }
                });
            }
        },
        
        resetForm() {
            this.editing = false;
            this.formData = {
                batch_id: '',
                package_type: '',
                material_type: '',
                unit_weight_packaging: '',
                quantity_of_units: '',
                rpc_unit_id: '',
                packer_id: '',
                packaging_location: '',
                cleanliness_checklist: false,
                _method: 'POST'
            };
            this.errors = {};
        },
        
        async submitForm() {
            try {
                this.loading = true;
                this.errors = {};
                
                // Get the form element safely
                const form = this.$refs.packagingForm;
                if (!form) {
                    console.error('Form element not found');
                    return;
                }
                
                // Create FormData from the form
                const formData = new FormData(form);
                
                // Ensure checkbox values are properly set
                const checkboxes = form.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    formData.set(checkbox.name, checkbox.checked ? '1' : '0');
                });
                
                // Add method override for PUT requests if editing
                if (this.editing) {
                    formData.set('_method', 'PUT');
                }
                
                const url = this.editing 
                    ? `/admin/packaging/${this.formData.id}`
                    : '{{ route("admin.packaging.store") }}';
                
                // Convert FormData to URL-encoded string for better debugging
                const formDataObj = {};
                for (let [key, value] of formData.entries()) {
                    formDataObj[key] = value;
                }
                console.log('Submitting form data:', formDataObj);
                
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                        // Let the browser set the Content-Type with boundary for FormData
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (!response.ok) {
                    if (response.status === 422) {
                        // Convert Laravel validation errors to a more usable format
                        const formattedErrors = {};
                        for (const [field, messages] of Object.entries(data.errors || {})) {
                            formattedErrors[field] = Array.isArray(messages) ? messages[0] : messages;
                        }
                        this.errors = formattedErrors;
                        
                        // Scroll to the first error
                        this.$nextTick(() => {
                            const firstError = Object.keys(formattedErrors)[0];
                            if (firstError) {
                                const element = this.$el.querySelector(`[name="${firstError}"]`);
                                if (element) {
                                    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                    element.focus();
                                }
                            }
                        });
                        return;
                    }
                    throw new Error(data.message || 'Something went wrong');
                }
                
                // Show success message
                const successMessage = this.editing ? 'Packaging updated successfully' : 'Packaging created successfully';
                
                // Close the drawer
                if (this.store) {
                    this.store.open = false;
                }
                
                // Reset the form
                this.resetForm();
                
                // Dispatch refresh event
                window.dispatchEvent(new CustomEvent('refresh-data'));
                
                // Show success notification
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: {
                        type: 'success',
                        message: successMessage
                    }
                }));
                
            } catch (error) {
                console.error('Error:', error);
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: {
                        type: 'error',
                        message: error.message || 'An error occurred. Please try again.'
                    }
                }));
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
