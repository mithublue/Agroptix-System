<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($isEdit) ? __('Edit Quality Test') : __('Create Quality Test') }}
        </h2>
    </x-slot>
    <form method="POST" action="{{ isset($isEdit) ? route('quality-tests.update', ['batch' => $batch, 'qualityTest' => $qualityTest->id]) : route('quality-tests.store', $batch) }}" x-on:submit.prevent="submitForm()" x-data="labTestingForm()">
        @csrf
        @if(isset($isEdit))
            @method('PUT')
        @endif

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                    <h1 class="text-2xl font-bold text-white">Lab Testing Quality Control Form</h1>
                    <p class="text-green-100 mt-1">Complete quality testing parameters and results</p>
                </div>

                <div class="grid lg:grid-cols-2 gap-6 p-6">
                    <!-- Form Section -->
                    <div class="space-y-6">
                        <!-- Basic Information Section -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Basic Information
                            </h3>
                            <div class="grid md:grid-cols-2 gap-4">
                                <!-- Hidden Batch ID -->
                                <input
                                    type="hidden"
                                    id="batch_id"
                                    name="batch_id"
                                    x-model="formData.batch_id"
                                    x-init="formData.batch_id = '{{ $batch->id }}'"
                                    value="{{ $batch->id }}"
                                />

                                <!-- Test Date -->
                                <div>
                                    <label for="test_date" class="block text-sm font-medium text-gray-700 mb-2">
                                        Date of Test <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="test_date"
                                        type="date"
                                        x-model="formData.test_date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    />
                                </div>

                                <!-- Lab Name -->
                                <div>
                                    <label for="lab_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Testing Lab Name <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="lab_name"
                                        type="text"
                                        x-model="formData.lab_name"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        placeholder="Enter lab name"
                                    />
                                </div>

                                <!-- Technician Name -->
                                <div>
                                    <label for="technician_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Technician Name <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="technician_name"
                                        type="text"
                                        x-model="formData.technician_name"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        placeholder="Enter technician name"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Parameters Testing Section -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-yellow-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                Parameters Tested
                            </h3>

                            <div class="space-y-3 mb-4">
                                <template x-for="[paramKey, paramLabel] in Object.entries(config.parameters_tested.values)" :key="paramKey">
                                    <label class="flex items-center space-x-3 p-3 rounded-md hover:bg-yellow-100 cursor-pointer border border-yellow-200">
                                        <input
                                            type="checkbox"
                                            :value="paramKey"
                                            x-model="formData.parameters_tested"
                                            @change="handleParameterChange(paramKey, $event.target.checked)"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                        />
                                        <span class="text-sm font-medium text-gray-700" x-text="paramLabel"></span>
                                    </label>
                                </template>
                            </div>

                            <!-- Conditional Parameter Results -->
                            <div x-show="formData.parameters_tested.length > 0" class="space-y-4">
                                <h4 class="text-md font-semibold text-yellow-800 border-b border-yellow-300 pb-2">Test Results</h4>

                                <template x-for="selectedParam in formData.parameters_tested" :key="selectedParam">
                                    <div class="bg-white border border-yellow-300 rounded-lg p-4 shadow-sm">
                                        <h5 class="font-medium text-gray-800 mb-3" x-text="config.parameters_tested.values[selectedParam]"></h5>

                                        <template x-for="[fieldKey, fieldConfig] in Object.entries(getParameterFields(selectedParam))" :key="fieldKey">
                                            <div class="mb-3">
                                                <!-- Number Input -->
                                                <template x-if="fieldConfig.type === 'number'">
                                                    <div>
                                                        <label :for="fieldKey" class="block text-sm font-medium text-gray-700 mb-1" x-text="fieldConfig.label"></label>
                                                        <input
                                                            :id="fieldKey"
                                                            type="number"
                                                            x-model="formData[fieldKey]"
                                                            :min="fieldConfig.min || 0"
                                                            :step="fieldConfig.step || '1'"
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                        />
                                                    </div>
                                                </template>

                                                <!-- Select Input -->
                                                <template x-if="fieldConfig.type === 'select'">
                                                    <div>
                                                        <label :for="fieldKey" class="block text-sm font-medium text-gray-700 mb-1" x-text="fieldConfig.label"></label>
                                                        <select
                                                            :id="fieldKey"
                                                            x-model="formData[fieldKey]"
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white"
                                                        >
                                                            <option value="">Select result...</option>
                                                            <template x-for="[optKey, optLabel] in Object.entries(fieldConfig.values)" :key="optKey">
                                                                <option :value="optKey" x-text="optLabel" :selected="formData[fieldKey] === optKey"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Additional Information Section -->
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-purple-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                Additional Information
                            </h3>

                            <div class="space-y-4">
                                <!-- File Upload -->
                                <div>
                                    <label for="test_certificate" class="block text-sm font-medium text-gray-700 mb-2">
                                        Test Certificate Upload
                                    </label>
                                    <input
                                        id="test_certificate"
                                        type="file"
                                        @change="handleFileUpload($event)"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                                    />
                                    <p class="text-xs text-gray-500 mt-1">Accepted formats: PDF, JPG, JPEG, PNG</p>
                                    <div x-show="formData.test_certificate" class="mt-2 text-sm text-green-600">
                                        File selected: <span x-text="formData.test_certificate"></span>
                                    </div>
                                </div>

                                <!-- Remarks -->
                                <div>
                                    <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">
                                        Remarks
                                    </label>
                                    <textarea
                                        id="remarks"
                                        x-model="formData.remarks"
                                        rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        placeholder="Enter any additional remarks or observations..."
                                    ></textarea>
                                </div>

                                <!-- Final Verdict -->
                                <div>
                                    <label for="final_pass_fail" class="block text-sm font-medium text-gray-700 mb-2">
                                        Final Verdict
                                    </label>
                                    <select
                                        id="final_pass_fail"
                                        x-model="formData.final_pass_fail"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white"
                                        :class="{
                                            'border-green-500 bg-green-50': formData.final_pass_fail === 'pass',
                                            'border-red-500 bg-red-50': formData.final_pass_fail === 'fail',
                                            'border-yellow-500 bg-yellow-50': formData.final_pass_fail === 'warning'
                                        }"
                                    >
                                        <option value="">Select verdict...</option>
                                        <option value="pass">✅ Pass</option>
                                        <option value="fail">❌ Fail</option>
                                        <option value="warning">⚠️ Near Fail (Trigger Review)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button
                                type="button"
                                @click="submitForm()"
                                :disabled="!isFormValid()"
                                class="w-full py-3 px-4 rounded-md font-medium transition duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                                :class="isFormValid() ?
                                    'bg-gradient-to-r from-green-600 to-green-700 text-white hover:from-green-700 hover:to-green-800' :
                                    'bg-gray-300 text-gray-500 cursor-not-allowed'"
                            >
                                <span x-show="!isFormValid()">Complete Required Fields</span>
                                <span x-show="isFormValid()">Submit Lab Test Results</span>
                            </button>
                        </div>
                    </div>

                    <!-- JSON Display Section -->
                    <div class="space-y-4">
                        <div class="bg-gray-900 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Form Data (JSON)
                            </h3>
                            <pre class="text-green-400 text-sm overflow-auto max-h-96 bg-gray-800 p-3 rounded border" x-text="JSON.stringify(formData, null, 2)"></pre>
                        </div>

                        <!-- Test Summary -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="font-medium text-green-900 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Test Summary
                            </h4>
                            <div class="text-sm text-green-700 space-y-2">
                                <div class="flex justify-between">
                                    <span>Parameters Selected:</span>
                                    <span class="font-medium" x-text="formData.parameters_tested.length"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Results Entered:</span>
                                    <span class="font-medium" x-text="getResultsCount()"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Form Completion:</span>
                                    <span class="font-medium" x-text="getCompletionPercentage() + '%'"></span>
                                </div>
                                <div x-show="formData.final_pass_fail" class="flex justify-between pt-2 border-t border-green-200">
                                    <span>Final Verdict:</span>
                                    <span class="font-bold"
                                          :class="{
                                              'text-green-600': formData.final_pass_fail === 'pass',
                                              'text-red-600': formData.final_pass_fail === 'fail',
                                              'text-yellow-600': formData.final_pass_fail === 'warning'
                                          }"
                                          x-text="config.final_pass_fail.values[formData.final_pass_fail] || ''">
                                    </span>
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
        // Initialize Alpine.js component
        function labTestingForm() {
            // Get the form data from PHP if in edit mode
            const serverFormData = @json($formDataJson ?? '{}');
            const isEditMode = @json(isset($isEdit) ? true : false);

            // Parse the server data if it exists
            let initialFormData = {
                batch_id: '',
                test_date: '',
                lab_name: '',
                technician_name: '',
                parameters_tested: [],
                test_certificate: '',
                remarks: '',
                final_pass_fail: ''
            };

            // If we have server data, merge it with the initial data
            if (serverFormData && isEditMode) {
                try {
                    const parsedData = JSON.parse(serverFormData);
                    initialFormData = { ...initialFormData, ...parsedData };

                    // Ensure parameters_tested is an array
                    if (typeof initialFormData.parameters_tested === 'string') {
                        initialFormData.parameters_tested = JSON.parse(initialFormData.parameters_tested);
                    } else if (!Array.isArray(initialFormData.parameters_tested)) {
                        initialFormData.parameters_tested = [];
                    }
                } catch (e) {
                    console.error('Error parsing form data:', e);
                }
            }

            return {
                isEdit: isEditMode,
                formData: initialFormData,

                config: {
                    parameters_tested: {
                        label: "Parameters Tested",
                        type: "checkbox",
                        values: {
                            "e_coli": "E. coli",
                            "salmonella": "Salmonella",
                            "brix": "Brix (Sugar %)",
                            "firmness": "Firmness",
                            "pesticide_residues": "Pesticide Residues",
                            "aspergillus": "Aspergillus (for Jackfruit)",
                            "co2_level": "CO₂ Emission (Ambient Gas)",
                            "ph": "pH Level",
                            "heavy_metals": "Heavy Metals"
                        },
                        conditional_field_group: [
                            {
                                on: "parameters_tested:e_coli",
                                fields: {
                                    e_coli_result: {
                                        label: "E. coli Result (cfu/g)",
                                        type: "number",
                                        min: 0
                                    }
                                }
                            },
                            {
                                on: "parameters_tested:salmonella",
                                fields: {
                                    salmonella_result: {
                                        label: "Salmonella (Detected/Not Detected)",
                                        type: "select",
                                        values: {
                                            not_detected: "Not Detected",
                                            detected: "Detected"
                                        }
                                    }
                                }
                            },
                            {
                                on: "parameters_tested:brix",
                                fields: {
                                    brix_result: {
                                        label: "Brix Value (%)",
                                        type: "number",
                                        min: 0,
                                        step: "0.1"
                                    }
                                }
                            },
                            {
                                on: "parameters_tested:firmness",
                                fields: {
                                    firmness_result: {
                                        label: "Firmness (kg/cm² or other)",
                                        type: "number",
                                        step: "0.1"
                                    }
                                }
                            },
                            {
                                on: "parameters_tested:pesticide_residues",
                                fields: {
                                    residue_result: {
                                        label: "Pesticide Residue Level (ppm)",
                                        type: "number",
                                        min: 0,
                                        step: "0.01"
                                    }
                                }
                            },
                            {
                                on: "parameters_tested:aspergillus",
                                fields: {
                                    aspergillus_result: {
                                        label: "Aspergillus (cfu/g)",
                                        type: "number"
                                    }
                                }
                            },
                            {
                                on: "parameters_tested:co2_level",
                                fields: {
                                    co2_reading: {
                                        label: "CO₂ Concentration (%)",
                                        type: "number",
                                        step: "0.1"
                                    }
                                }
                            },
                            {
                                on: "parameters_tested:ph",
                                fields: {
                                    ph_result: {
                                        label: "pH Level",
                                        type: "number",
                                        step: "0.1"
                                    }
                                }
                            },
                            {
                                on: "parameters_tested:heavy_metals",
                                fields: {
                                    heavy_metal_result: {
                                        label: "Heavy Metals (ppm)",
                                        type: "number",
                                        step: "0.01"
                                    }
                                }
                            }
                        ]
                    },
                    final_pass_fail: {
                        values: {
                            pass: "Pass",
                            fail: "Fail",
                            warning: "Near Fail (Trigger Review)"
                        }
                    }
                },

                handleParameterChange(paramKey, checked) {
                    if (checked) {
                        if (!this.formData.parameters_tested.includes(paramKey)) {
                            this.formData.parameters_tested.push(paramKey);
                        }
                        this.initializeParameterFields(paramKey);
                    } else {
                        this.formData.parameters_tested = this.formData.parameters_tested.filter(item => item !== paramKey);
                        this.cleanupParameterFields(paramKey);
                    }
                },

                getParameterFields(paramKey) {
                    const group = this.config.parameters_tested.conditional_field_group.find(
                        g => g.on === `parameters_tested:${paramKey}`
                    );
                    return group ? group.fields : {};
                },

                initializeParameterFields(paramKey) {
                    const fields = this.getParameterFields(paramKey);
                    Object.keys(fields).forEach(fieldKey => {
                        const field = fields[fieldKey];
                        if (!this.formData.hasOwnProperty(fieldKey)) {
                            this.formData[fieldKey] = field.type === 'number' ? 0 : '';
                        }
                    });
                },

                cleanupParameterFields(paramKey) {
                    const fields = this.getParameterFields(paramKey);
                    Object.keys(fields).forEach(fieldKey => {
                        delete this.formData[fieldKey];
                    });
                },

                handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.formData.test_certificate = file.name;
                        this.formData.test_certificate_file = file; // Store the actual file object
                    } else {
                        this.formData.test_certificate = '';
                        this.formData.test_certificate_file = null;
                    }
                },

                isFormValid() {
                    return this.formData.batch_id &&
                        this.formData.test_date &&
                        this.formData.lab_name &&
                        this.formData.technician_name;
                },

                getResultsCount() {
                    let count = 0;
                    this.formData.parameters_tested.forEach(param => {
                        const fields = this.getParameterFields(param);
                        Object.keys(fields).forEach(fieldKey => {
                            if (this.formData[fieldKey] && this.formData[fieldKey] !== '' && this.formData[fieldKey] !== 0) {
                                count++;
                            }
                        });
                    });
                    return count;
                },

                getCompletionPercentage() {
                    const totalFields = 8; // Basic fields + optional fields
                    const filledFields = Object.values(this.formData).filter(v =>
                        v !== '' && v !== 0 && (!Array.isArray(v) || v.length > 0)
                    ).length;
                    return Math.round((filledFields / totalFields) * 100);
                },

                submitForm() {
                    // Get the form element
                    const form = document.querySelector('form');
                    if (!form) {
                        console.error('Form element not found');
                        return;
                    }

                    // Create FormData object
                    const formData = new FormData(form);

                    // Manually append the parameters_tested array
                    if (this.formData.parameters_tested && this.formData.parameters_tested.length > 0) {
                        // Clear any existing parameters_tested values
                        formData.delete('parameters_tested[]');

                        // Add each parameter
                        this.formData.parameters_tested.forEach(param => {
                            formData.append('parameters_tested[]', param);
                        });
                    }

                    // Add the form data from Alpine.js to the FormData
                    Object.entries(this.formData).forEach(([key, value]) => {
                        if (value === null || value === undefined) return;

                        if (Array.isArray(value)) {
                            // Remove any existing values for this key to avoid duplicates
                            formData.delete(key);
                            value.forEach(val => formData.append(`${key}[]`, val));
                        } else if (value instanceof File) {
                            // Handle file uploads
                            if (value.size > 0) {
                                formData.set(key, value);
                            }
                        } else if (typeof value === 'object') {
                            // Stringify objects
                            formData.set(key, JSON.stringify(value));
                        } else {
                            formData.set(key, value);
                        }
                    });

                    // Add the final verdict as result_status for backend compatibility
                    formData.set('result_status', this.formData.final_pass_fail || '');

                    // Add the batch ID if not already set
                    if (!formData.get('batch_id')) {
                        formData.set('batch_id', '{{ $batch->id }}');
                    }

                    // Set the correct HTTP method based on edit mode
                    const method = this.isEdit ? 'POST' : 'POST';
                    const url = form.action;

                    // Add _method for Laravel to handle PUT for updates
                    if (this.isEdit) {
                        formData.append('_method', 'PUT');
                    }
                    console.log('formData', this.formData);

                    // Submit the form
                    axios({
                        method: method,
                        url: url,
                        data: formData,
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        console.log('Response:', response);
                        if (response.data.success) {
                            // Get the redirect URL
                            const redirectUrl = response.data.redirect || '{{ route("quality-tests.batchList") }}';

                            // Show success toast with progress bar
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer);
                                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                                },
                                willClose: () => {
                                    // Navigate after toast closes
                                    if (window.Turbo) {
                                        Turbo.visit(redirectUrl, { action: 'replace' });
                                    } else {
                                        window.location.href = redirectUrl;
                                    }
                                }
                            });

                            Toast.fire({
                                icon: 'success',
                                title: 'Quality test saved successfully!',
                                timer: 1500,
                                timerProgressBar: true
                            });
                        } else {
                            throw new Error(response.data.message || 'Failed to save quality test');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        let errorMessage = 'An error occurred while saving the test.';

                        if (error.response?.data?.errors) {
                            // Handle validation errors
                            const errors = error.response.data.errors;
                            errorMessage = Object.values(errors).flat().join('\n');
                        } else if (error.response?.data?.message) {
                            errorMessage = error.response.data.message;
                        } else if (error.message) {
                            errorMessage = error.message;
                        }

                        alert(errorMessage);
                    })
                    .finally(() => {
                        this.isSubmitting = false;
                    });
                }
            }
        }
    </script>
</x-app-layout>
