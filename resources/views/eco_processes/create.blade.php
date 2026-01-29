<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ isset($ecoProcess) ? 'Edit' : 'Create' }} Eco Process
            </h2>
            <a href="{{ route('batches.eco-processes.index', $batch) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Back to Eco Processes') }}
            </a>
        </div>
    </x-slot>
    @php
        $formData = json_encode(old() ?: $ecoProcess->data ?? []);
    @endphp

    <form method="POST"
        action="{{ isset($ecoProcess) ? route('batches.eco-processes.update', [$batch, $ecoProcess]) : route('batches.eco-processes.store', $batch) }}"
        x-on:submit.prevent="submitForm()" x-data="formHandler()">
        @csrf
        @if(isset($ecoProcess))
            @method('PUT')
        @endif
        <div class="container mx-auto px-4 py-8" x-cloak>
            <div class="max-w-6xl mx-auto">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                        <h1 class="text-2xl font-bold text-white">Processing Stage Form</h1>
                        <p class="text-blue-100 mt-1">Complete the form based on your processing stage</p>
                    </div>

                    <div class="grid lg:grid-cols-2 gap-6 p-6">
                        <!-- Form Section -->
                        <div class="space-y-6">
                            <!-- Stage Selection -->
                            <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-blue-500">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Processing Stage *
                                </label>
                                <select x-model="formData.stage" @change="updateFormData('stage', $event.target.value)"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="">Select a stage...</option>
                                    <template x-for="[key, label] in Object.entries(config.stage.values)" :key="key">
                                        <option :value="key" x-text="label" :selected="formData.stage === key"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Dynamic Fields -->
                            <div x-show="formData.stage" class="space-y-4">
                                <template
                                    x-for="[fieldKey, fieldConfig] in Object.entries(getConditionalFields(formData.stage))"
                                    :key="fieldKey">
                                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                        <!-- Text Input -->
                                        <template x-if="fieldConfig.type === 'text'">
                                            <div>
                                                <label :for="fieldKey"
                                                    class="block text-sm font-medium text-gray-700 mb-2">
                                                    <span x-text="fieldConfig.label"></span>
                                                    <span x-show="fieldConfig.required" class="text-red-500">*</span>
                                                </label>
                                                <input :id="fieldKey" type="text" x-model="formData[fieldKey]"
                                                    :placeholder="fieldConfig.placeholder || ''"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                            </div>
                                        </template>

                                        <!-- Textarea -->
                                        <template x-if="fieldConfig.type === 'textarea'">
                                            <div>
                                                <label :for="fieldKey"
                                                    class="block text-sm font-medium text-gray-700 mb-2">
                                                    <span x-text="fieldConfig.label"></span>
                                                    <span x-show="fieldConfig.required" class="text-red-500">*</span>
                                                </label>
                                                <textarea :id="fieldKey" x-model="formData[fieldKey]"
                                                    :placeholder="fieldConfig.placeholder || ''" rows="3"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                            </div>
                                        </template>

                                        <!-- Number Input -->
                                        <template x-if="fieldConfig.type === 'number'">
                                            <div>
                                                <label :for="fieldKey"
                                                    class="block text-sm font-medium text-gray-700 mb-2">
                                                    <span x-text="fieldConfig.label"></span>
                                                    <span x-show="fieldConfig.required" class="text-red-500">*</span>
                                                </label>
                                                <input :id="fieldKey" type="number" x-model="formData[fieldKey]"
                                                    :min="fieldConfig.min || 0" :max="fieldConfig.max || ''"
                                                    :step="fieldConfig.step || '1'"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                            </div>
                                        </template>

                                        <!-- DateTime Input -->
                                        <template x-if="fieldConfig.type === 'datetime-local'">
                                            <div>
                                                <label :for="fieldKey"
                                                    class="block text-sm font-medium text-gray-700 mb-2">
                                                    <span x-text="fieldConfig.label"></span>
                                                    <span x-show="fieldConfig.required" class="text-red-500">*</span>
                                                </label>
                                                <input :id="fieldKey" type="datetime-local" x-model="formData[fieldKey]"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                            </div>
                                        </template>

                                        <!-- Select Dropdown -->
                                        <template x-if="fieldConfig.type === 'select'">
                                            <div>
                                                <label :for="fieldKey"
                                                    class="block text-sm font-medium text-gray-700 mb-2">
                                                    <span x-text="fieldConfig.label"></span>
                                                    <span x-show="fieldConfig.required" class="text-red-500">*</span>
                                                </label>
                                                <select :id="fieldKey" x-model="formData[fieldKey]"
                                                    @change="handleSelectChange(fieldKey, $event.target.value)"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                                    <option value="">Select an option...</option>
                                                    <template
                                                        x-for="[optKey, optLabel] in Object.entries(fieldConfig.values)"
                                                        :key="optKey">
                                                        <option :value="optKey" x-text="optLabel"></option>
                                                    </template>
                                                </select>

                                                <!-- Nested conditional fields for select -->
                                                <template
                                                    x-if="fieldConfig.conditional_field_group && formData[fieldKey]">
                                                    <div class="mt-4 pl-4 border-l-2 border-blue-200 space-y-3">
                                                        <template
                                                            x-for="[nestedKey, nestedConfig] in Object.entries(getNestedConditionalFields(fieldKey, 'any'))"
                                                            :key="nestedKey">
                                                            <div>
                                                                <label :for="nestedKey"
                                                                    class="block text-sm font-medium text-gray-600 mb-1"
                                                                    x-text="nestedConfig.label"></label>
                                                                <input :id="nestedKey" :type="nestedConfig.type"
                                                                    x-model="formData[nestedKey]"
                                                                    :min="nestedConfig.min || 0"
                                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <!-- Checkbox Group -->
                                        <template x-if="fieldConfig.type === 'checkbox'">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                                    <span x-text="fieldConfig.label"></span>
                                                    <span x-show="fieldConfig.required" class="text-red-500">*</span>
                                                </label>
                                                <div class="space-y-2">
                                                    <template
                                                        x-for="[optKey, optLabel] in Object.entries(fieldConfig.values)"
                                                        :key="optKey">
                                                        <label
                                                            class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer">
                                                            <input type="checkbox" :value="optKey"
                                                                x-model="formData[fieldKey]"
                                                                @change="handleCheckboxChange(fieldKey, optKey, $event.target.checked)"
                                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
                                                            <span class="text-sm text-gray-700"
                                                                x-text="optLabel"></span>
                                                        </label>
                                                    </template>
                                                </div>

                                                <!-- Nested conditional fields for checkboxes -->
                                                <template
                                                    x-if="fieldConfig.conditional_field_group && formData[fieldKey] && formData[fieldKey].length > 0">
                                                    <div class="mt-4 pl-4 border-l-2 border-green-200 space-y-3">
                                                        <template x-for="checkedValue in formData[fieldKey]"
                                                            :key="checkedValue">
                                                            <template
                                                                x-for="[nestedKey, nestedConfig] in Object.entries(getNestedConditionalFields(fieldKey, checkedValue))"
                                                                :key="nestedKey">
                                                                <div>
                                                                    <label :for="nestedKey"
                                                                        class="block text-sm font-medium text-gray-600 mb-1"
                                                                        x-text="nestedConfig.label"></label>
                                                                    <input :id="nestedKey" :type="nestedConfig.type"
                                                                        x-model="formData[nestedKey]"
                                                                        :min="nestedConfig.min || 0"
                                                                        :max="nestedConfig.max || ''"
                                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                                                </div>
                                                            </template>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <!-- Submit Button -->
                            <div x-show="formData.stage" class="pt-4">
                                <button type="submit"
                                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 px-4 rounded-md hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium">
                                    Submit Form
                                </button>
                            </div>
                        </div>

                        <!-- JSON Display Section -->
                        <div class="space-y-4">
                            <div class="bg-gray-900 rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Form Data (JSON)
                                </h3>
                                <pre class="text-green-400 text-sm overflow-auto max-h-96 bg-gray-800 p-3 rounded border"
                                    x-text="JSON.stringify(formData, null, 2)"></pre>
                            </div>

                            <!-- Field Count Info -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="font-medium text-blue-900 mb-2">Form Statistics</h4>
                                <div class="text-sm text-blue-700 space-y-1">
                                    <div>Total Fields: <span class="font-medium"
                                            x-text="Object.keys(formData).length"></span></div>
                                    <div>Current Stage: <span class="font-medium"
                                            x-text="formData.stage || 'None selected'"></span></div>
                                    <div>Filled Fields: <span class="font-medium"
                                            x-text="Object.values(formData).filter(v => v !== '' && v !== 0 && (!Array.isArray(v) || v.length > 0)).length"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>


    <script>
        function formHandler() {
            return {
                formData: JSON.parse(<?php echo json_encode($formData); ?>) || {
                    stage: '',
                    processing_type: [],
                    preservative_used: [],
                    disinfection_steps: [],
                    us_export: [],
                    cold_storage: []
                },

                config: {
                    stage: {
                        label: 'Stage',
                        type: 'select',
                        values: {
                            'harvest_processing': 'Harvest Processing',
                            'cutting_peeling': 'Cutting/Peeling',
                            'packaging_prep': 'Packaging Preparation',
                            'washing_n_treatment': 'Washing & Treatment',
                            'drying_n_pre_cooling': 'Drying & Pre Cooling',
                            'waste_handling': 'Waste Handling'
                        },
                        conditional_field_group: [
                            {
                                on: 'stage:harvest_processing',
                                fields: {
                                    delatex_steps: { label: 'De-Latex Steps', type: 'textarea', placeholder: 'E.g., Stand fruit on plywood, trim prickles, coat cuts with oil', required: true },
                                    delatex_operator: { label: 'Operator Name', type: 'text', required: true },
                                    delatex_timestamp: { label: 'Date & Time', type: 'datetime-local', required: true },
                                    delatex_notes: { label: 'Additional Notes', type: 'textarea', required: false }
                                }
                            },
                            {
                                on: 'stage:cutting_peeling',
                                fields: {
                                    processing_type: { label: 'Processing Type', type: 'checkbox', values: { peeling: 'Peeling', segmenting: 'Segmenting', chipping: 'Chipping', pulping: 'Pulping' } },
                                    energy_usage: { label: 'Energy Usage (kWh)', type: 'number', step: '0.01', min: 0 },
                                    sanitizer_type: {
                                        label: 'Sanitizer Type',
                                        type: 'select',
                                        values: { citric_acid: 'Citric Acid', chlorine: 'Chlorine', other: 'Other' },
                                        conditional_field_group: [
                                            {
                                                on: 'any',
                                                fields: {
                                                    sanitizer_concentration: { label: 'Concentration (ppm)', type: 'number', min: 0 },
                                                    immersion_time: { label: 'Immersion Time (minutes)', type: 'number', min: 0 }
                                                }
                                            }
                                        ]
                                    },
                                    preservative_used: { label: 'Preservative Used', type: 'checkbox', values: { citric_acid: 'Citric Acid', ascorbic_acid: 'Ascorbic Acid', calcium_chloride: 'Calcium Chloride', none: 'None' } },
                                    operator_signature: { label: 'Operator Signature', type: 'text', required: true }
                                }
                            },
                            {
                                on: 'stage:packaging_prep',
                                fields: {
                                    package_type: { label: 'Package Type', type: 'select', values: { ventilated_crate: 'Ventilated Crate', vacuum_bag: 'Vacuum Bag', plastic_container: 'Plastic Container', other: 'Other' }, required: true },
                                    batch_id: { label: 'Batch ID', type: 'text', required: true },
                                    package_id: { label: 'Package ID/QR Code', type: 'text', required: true },
                                    net_weight: { label: 'Net Weight (kg)', type: 'number', step: '0.01', min: 0, required: true },
                                    packer_id: { label: 'Packer ID', type: 'text', required: true },
                                    storage_temperature: { label: 'Temperature (°C)', type: 'number', required: true },
                                    storage_humidity: { label: 'Humidity (%)', type: 'number', min: 0, max: 100 }
                                }
                            },
                            {
                                on: 'stage:washing_n_treatment',
                                fields: {
                                    washing_water_usage: { label: 'Washing Water Usage (Liters)', type: 'number', min: 0, max: 1000 },
                                    energy_usage: { label: 'Energy Usage (kWh)', type: 'number', step: '0.01', min: 0 },
                                    disinfection_steps: {
                                        label: 'Disinfection Steps',
                                        type: 'checkbox',
                                        values: { chlorine_solution_strength: 'Chlorine Solution Strength', temperature: 'Temperature' },
                                        conditional_field_group: [
                                            {
                                                on: 'disinfection_steps:temperature',
                                                fields: { temperature_value: { label: 'Temperature', type: 'number', min: 0, max: 100 } }
                                            },
                                            {
                                                on: 'disinfection_steps:chlorine_solution_strength',
                                                fields: { chlorine_solution_strength_value: { label: 'Chlorine Solution Strength', type: 'number', min: 10, max: 100 } }
                                            }
                                        ]
                                    },
                                    us_export: {
                                        label: 'US Export',
                                        type: 'checkbox',
                                        values: { us_export: 'US Export' },
                                        conditional_field_group: [
                                            {
                                                on: 'us_export:us_export',
                                                fields: {
                                                    hot_water_temperature: { label: 'Hot Water Temperature', type: 'number' },
                                                    hot_water_duration: { label: 'Hot Water Duration', type: 'number' }
                                                }
                                            }
                                        ]
                                    }
                                }
                            },
                            {
                                on: 'stage:drying_n_pre_cooling',
                                fields: {
                                    energy_usage: { label: 'Energy Usage (kWh)', type: 'number', step: '0.01', min: 0 },
                                    cold_storage: {
                                        label: 'Cold Storage',
                                        type: 'checkbox',
                                        values: { cold_storage: 'Cold Storage' },
                                        conditional_field_group: [
                                            {
                                                on: 'cold_storage:cold_storage',
                                                fields: {
                                                    temperature: { label: 'Temperature', type: 'number' },
                                                    humidity: { label: 'Humidity', type: 'number' }
                                                }
                                            }
                                        ]
                                    }
                                }
                            },
                            {
                                on: 'stage:waste_handling',
                                fields: {
                                    washwater_amount: { label: 'Washwater Amount', type: 'number' },
                                    rejection_weight: { label: 'Rejection Weight', type: 'number' }
                                }
                            }
                        ]
                    }
                },

                getConditionalFields(stage) {
                    if (!stage) return {};
                    const group = this.config.stage.conditional_field_group.find(g => g.on === `stage:${stage}`);
                    return group ? group.fields : {};
                },

                getNestedConditionalFields(parentField, value) {
                    const fieldConfig = this.getFieldConfig(parentField);
                    if (fieldConfig && fieldConfig.conditional_field_group) {
                        if (value === 'any' && parentField === 'sanitizer_type') {
                            return fieldConfig.conditional_field_group.find(g => g.on === 'any').fields;
                        }
                        const group = fieldConfig.conditional_field_group.find(g => g.on === `${parentField}:${value}`);
                        return group ? group.fields : {};
                    }
                    return {};
                },

                getFieldConfig(fieldPath) {
                    const stage = this.formData.stage;
                    if (!stage) return null;

                    const fields = this.getConditionalFields(stage);
                    return fields[fieldPath] || null;
                },

                updateFormData(field, value) {
                    this.formData[field] = value;
                    if (field === 'stage') {
                        this.initializeVisibleFields();
                    }
                },

                handleSelectChange(fieldKey, value) {
                    this.formData[fieldKey] = value;
                    this.initializeNestedFields(fieldKey, value);
                },

                handleCheckboxChange(fieldKey, optionKey, checked) {
                    if (!Array.isArray(this.formData[fieldKey])) {
                        this.formData[fieldKey] = [];
                    }

                    if (checked) {
                        if (!this.formData[fieldKey].includes(optionKey)) {
                            this.formData[fieldKey].push(optionKey);
                        }
                        this.initializeNestedFields(fieldKey, optionKey);
                    } else {
                        this.formData[fieldKey] = this.formData[fieldKey].filter(item => item !== optionKey);
                        this.cleanupNestedFields(fieldKey, optionKey);
                    }
                },

                initializeVisibleFields() {
                    if (!this.formData.stage) return;

                    // Clear existing fields except stage
                    const stage = this.formData.stage;
                    this.formData = { stage };

                    const fields = this.getConditionalFields(stage);
                    Object.keys(fields).forEach(key => {
                        const field = fields[key];
                        if (field.type === 'checkbox') {
                            this.formData[key] = [];
                        } else if (field.type === 'number') {
                            this.formData[key] = 0;
                        } else {
                            this.formData[key] = '';
                        }
                    });
                },

                initializeNestedFields(parentField, value) {
                    const nestedFields = this.getNestedConditionalFields(parentField, value);
                    Object.keys(nestedFields).forEach(nestedKey => {
                        const nestedField = nestedFields[nestedKey];
                        if (!this.formData.hasOwnProperty(nestedKey)) {
                            this.formData[nestedKey] = nestedField.type === 'number' ? 0 : '';
                        }
                    });
                },

                cleanupNestedFields(parentField, value) {
                    const nestedFields = this.getNestedConditionalFields(parentField, value);
                    Object.keys(nestedFields).forEach(nestedKey => {
                        delete this.formData[nestedKey];
                    });
                },

                submitForm() {
                    // Convert form data to JSON and submit via fetch
                    const form = this.$el;
                    const formData = new FormData(form);

                    // Add the form data from Alpine.js to the FormData
                    Object.entries(this.formData).forEach(([key, value]) => {
                        if (Array.isArray(value)) {
                            value.forEach(val => formData.append(`${key}[]`, val));
                        } else if (value !== null && value !== undefined) {
                            formData.set(key, value);
                        }
                    });

                    // Add the stage data
                    formData.set('stage', this.formData.stage);
                    formData.set('data', JSON.stringify(this.formData));

                    // Submit the form
                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => { throw err; });
                            }
                            return response.json();
                        })
                        .then(data => {
                            // Show success toast with SweetAlert2
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 1000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer);
                                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                                }
                            });

                            Toast.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Eco process has been saved successfully.'
                            }).then(() => {
                                // Redirect after toast is closed
                                window.location.href = data.redirect || '/dashboard';
                            });
                        })
                        .catch(error => {
                            // Show error toast with SweetAlert2
                            console.error('Error:', error);
                            const errorMessage = error.message || 'An error occurred while submitting the form.';

                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMessage,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3b82f6'
                            });
                        });
                }
            }
        }
    </script>
</x-app-layout>