<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($ecoProcess) ? 'Edit' : 'Create' }} Eco Process
        </h2>
    </x-slot>
    {{ json_encode(old() ?: $ecoProcess ?? []) }}

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8" 
             x-data="foodProcessingForm({{ json_encode(array_merge(
                ['stage' => ''],
                old() ?: [],
                isset($ecoProcess) ? ['stage' => $ecoProcess->stage] : []
             )) }})">
            <form method="POST" action="{{ isset($ecoProcess) ? route('batches.eco-processes.update', [$batch, $ecoProcess] ) : route('batches.eco-processes.store', $batch) }}">
                @csrf
                @if(isset($ecoProcess))
                    @method('PUT')
                @endif

                <div class="max-w-4xl mx-auto px-6">
                    <!-- Main Form Component -->
                    <div class="bg-white rounded-lg shadow-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h1 class="text-2xl font-bold text-gray-900">Food Processing Tracking Form</h1>
                        </div>
                        <div class="p-6">
                            <form @submit.prevent="submitForm" class="space-y-6">
                                <!-- Stage Selection -->
                                <div class="space-y-2">
                                    <label for="stage" class="block text-sm font-medium text-gray-700">
                                        Stage <span class="text-red-500">*</span>
                                    </label>
                                    <!-- Debug Info -->
                                    <div class="mb-2 p-2 bg-gray-100 rounded text-xs">
                                        <div>formData.stage: <span x-text="formData.stage || 'null'"></span></div>
                                        <div>config.stage.values: <span x-text="JSON.stringify(config.stage.values)"></span></div>
                                    </div>
                                    
                                    <!-- Stage Selector -->
                                    <div>
                                        <select
                                            id="stage"
                                            x-model="formData.stage"
                                            @change="handleStageChange"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        >
                                            <option value="">Select processing stage</option>
                                            <option value="harvest_processing">Harvest Processing</option>
                                            <option value="cutting_peeling">Cutting/Peeling</option>
                                            <option value="packaging_prep">Packaging Preparation</option>
                                            <option value="washing_n_treatment">Washing & Treatment</option>
                                            <option value="drying_n_pre_cooling">Drying & Pre Cooling</option>
                                            <option value="waste_handling">Waste Handling</option>
                                        </select>
                                        
                                        <!-- Debug Info -->
                                        <div class="mt-2 p-2 bg-blue-50 rounded text-xs">
                                            <div>formData.stage: <span x-text="formData.stage || 'null'"></span></div>
                                            <div>Select value: <span x-text="$el.querySelector('#stage').value || 'null'"></span></div>
                                            <div>EcoProcess ID: <span>{{ $ecoProcess->id ?? 'null' }}</span></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Conditional Fields -->
                                <div x-show="formData.stage" x-transition class="border-t pt-6">
                                    <h3 x-text="getStageTitle()" class="text-lg font-semibold mb-4"></h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <template x-for="fieldGroup in getConditionalFields()" :key="fieldGroup.on">
                                            <template x-for="(fieldConfig, fieldKey) in fieldGroup.fields" :key="fieldKey">
                                                <div :class="getFieldClass(fieldConfig)">
                                                    <!-- Text Input -->
                                                    <template x-if="fieldConfig.type === 'text'">
                                                        <div class="space-y-2">
                                                            <label :for="fieldKey" class="block text-sm font-medium text-gray-700">
                                                                <span x-text="fieldConfig.label"></span>
                                                                <span x-show="fieldConfig.required" class="text-red-500 ml-1">*</span>
                                                            </label>
                                                            <input
                                                                :id="fieldKey"
                                                                :name="fieldKey"
                                                                type="text"
                                                                x-model="formData[fieldKey]"
                                                                :placeholder="fieldConfig.placeholder || ''"
                                                                :required="fieldConfig.required || false"
                                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                            >
                                                        </div>
                                                    </template>

                                                    <!-- Textarea -->
                                                    <template x-if="fieldConfig.type === 'textarea'">
                                                        <div class="space-y-2">
                                                            <label :for="fieldKey" class="block text-sm font-medium text-gray-700">
                                                                <span x-text="fieldConfig.label"></span>
                                                                <span x-show="fieldConfig.required" class="text-red-500 ml-1">*</span>
                                                            </label>
                                                            <textarea
                                                                :id="fieldKey"
                                                                :name="fieldKey"
                                                                x-model="formData[fieldKey]"
                                                                :placeholder="fieldConfig.placeholder || ''"
                                                                :required="fieldConfig.required || false"
                                                                rows="3"
                                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                            ></textarea>
                                                        </div>
                                                    </template>

                                                    <!-- Number Input -->
                                                    <template x-if="fieldConfig.type === 'number'">
                                                        <div class="space-y-2">
                                                            <label :for="fieldKey" class="block text-sm font-medium text-gray-700">
                                                                <span x-text="fieldConfig.label"></span>
                                                                <span x-show="fieldConfig.required" class="text-red-500 ml-1">*</span>
                                                            </label>
                                                            <input
                                                                :id="fieldKey"
                                                                :name="fieldKey"
                                                                type="number"
                                                                x-model="formData[fieldKey]"
                                                                :min="fieldConfig.min || ''"
                                                                :max="fieldConfig.max || ''"
                                                                :step="fieldConfig.step || ''"
                                                                :required="fieldConfig.required || false"
                                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                            >
                                                        </div>
                                                    </template>

                                                    <!-- DateTime Input -->
                                                    <template x-if="fieldConfig.type === 'datetime-local'">
                                                        <div class="space-y-2">
                                                            <label :for="fieldKey" class="block text-sm font-medium text-gray-700">
                                                                <span x-text="fieldConfig.label"></span>
                                                                <span x-show="fieldConfig.required" class="text-red-500 ml-1">*</span>
                                                            </label>
                                                            <input
                                                                :id="fieldKey"
                                                                :name="fieldKey"
                                                                type="datetime-local"
                                                                x-model="formData[fieldKey]"
                                                                :required="fieldConfig.required || false"
                                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                            >
                                                        </div>
                                                    </template>

                                                    <!-- Select Dropdown -->
                                                    <template x-if="fieldConfig.type === 'select'">
                                                        <div class="space-y-2">
                                                            <label :for="fieldKey" class="block text-sm font-medium text-gray-700">
                                                                <span x-text="fieldConfig.label"></span>
                                                                <span x-show="fieldConfig.required" class="text-red-500 ml-1">*</span>
                                                            </label>
                                                            <select
                                                                :id="fieldKey"
                                                                :name="fieldKey"
                                                                x-model="formData[fieldKey]"
                                                                :required="fieldConfig.required || false"
                                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                            >
                                                                <option value="">Select option</option>
                                                                <template x-for="(label, value) in fieldConfig.values" :key="value">
                                                                    <option :value="value" x-text="label"></option>
                                                                </template>
                                                            </select>
                                                        </div>
                                                    </template>

                                                    <!-- Checkbox -->
                                                    <template x-if="fieldConfig.type === 'checkbox'">
                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-medium text-gray-700" x-text="fieldConfig.label"></label>
                                                            <div class="space-y-2">
                                                                <template x-for="(label, value) in fieldConfig.values" :key="value">
                                                                    <label class="flex items-center">
                                                                        <input
                                                                            type="checkbox"
                                                                            :name="fieldKey + '[]'"
                                                                            :value="value"
                                                                            x-model="formData[fieldKey]"
                                                                            @change="handleCheckboxChange(fieldKey, value, fieldConfig)"
                                                                            class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                                        >
                                                                        <span x-text="label" class="text-sm text-gray-700"></span>
                                                                    </label>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </template>

                                                    <!-- Group Fields -->
                                                    <template x-if="fieldConfig.type === 'group'">
                                                        <div class="space-y-4 p-4 border border-gray-200 rounded-md bg-gray-50">
                                                            <h4 x-text="fieldConfig.label" class="font-medium text-gray-900"></h4>
                                                            <div class="grid grid-cols-1 gap-4">
                                                                <template x-for="(subFieldConfig, subFieldKey) in fieldConfig.fields" :key="subFieldKey">
                                                                    <div>
                                                                        <!-- Render sub-fields recursively -->
                                                                        <template x-if="subFieldConfig.type === 'text' || subFieldConfig.type === 'number'">
                                                                            <div class="space-y-2">
                                                                                <label :for="subFieldKey" class="block text-sm font-medium text-gray-700">
                                                                                    <span x-text="subFieldConfig.label"></span>
                                                                                    <span x-show="subFieldConfig.required" class="text-red-500 ml-1">*</span>
                                                                                </label>
                                                                                <input
                                                                                    :id="subFieldKey"
                                                                                    :name="subFieldKey"
                                                                                    :type="subFieldConfig.type"
                                                                                    x-model="formData[subFieldKey]"
                                                                                    :min="subFieldConfig.min || ''"
                                                                                    :max="subFieldConfig.max || ''"
                                                                                    :step="subFieldConfig.step || ''"
                                                                                    :required="subFieldConfig.required || false"
                                                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                                                >
                                                                            </div>
                                                                        </template>
                                                                        <template x-if="subFieldConfig.type === 'select'">
                                                                            <div class="space-y-2">
                                                                                <label :for="subFieldKey" class="block text-sm font-medium text-gray-700">
                                                                                    <span x-text="subFieldConfig.label"></span>
                                                                                    <span x-show="subFieldConfig.required" class="text-red-500 ml-1">*</span>
                                                                                </label>
                                                                                <select
                                                                                    :id="subFieldKey"
                                                                                    :name="subFieldKey"
                                                                                    x-model="formData[subFieldKey]"
                                                                                    :required="subFieldConfig.required || false"
                                                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                                                >
                                                                                    <option value="">Select option</option>
                                                                                    <template x-for="(label, value) in subFieldConfig.values" :key="value">
                                                                                        <option :value="value" x-text="label"></option>
                                                                                    </template>
                                                                                </select>
                                                                            </div>
                                                                        </template>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </template>

                                        <!-- Conditional Sub-fields -->
                                        {{--<template x-for="conditionalField in getConditionalSubFields()" :key="conditionalField.fieldKey">
                                            <div x-show="shouldShowConditionalField(conditionalField)" x-transition>
                                                <template x-for="(subFieldConfig, subFieldKey) in conditionalField.fields" :key="subFieldKey">
                                                    <div class="space-y-2">
                                                        <label :for="subFieldKey" class="block text-sm font-medium text-gray-700">
                                                            <span x-text="subFieldConfig.label"></span>
                                                            <span x-show="subFieldConfig.required" class="text-red-500 ml-1">*</span>
                                                        </label>
                                                        <input
                                                            :id="subFieldKey"
                                                            :name="subFieldKey"
                                                            :type="subFieldConfig.type"
                                                            x-model="formData[subFieldKey]"
                                                            :min="subFieldConfig.min || ''"
                                                            :max="subFieldConfig.max || ''"
                                                            :step="subFieldConfig.step || ''"
                                                            :required="subFieldConfig.required || false"
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                        >
                                                    </div>
                                                </template>
                                            </div>
                                        </template>--}}
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex justify-end pt-6">
                                    <button
                                        type="submit"
                                        :disabled="!formData.stage"
                                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        Submit Form
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Debug Panel -->
                    {{--<div x-show="Object.keys(formData).length > 1" x-transition class="bg-white rounded-lg shadow-lg mt-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-gray-900">Form Data (Debug)</h2>
                        </div>
                        <div class="p-6">
                            <pre x-text="JSON.stringify(formData, null, 2)" class="bg-gray-100 p-4 rounded text-sm "></pre>
                        </div>
                    </div>--}}
                </div>
            </form>
        </div>
    </div>

    <script>
        function foodProcessingForm(initialData) {
            // Ensure initialData is an object
            initialData = initialData || {};
            
            // Log initial data for debugging
            console.log('Form initial data:', initialData);
            
            return {
                // Form data with default values
                formData: {
                    stage: initialData.stage || ''
                },
                
                // Initialize the component
                init() {
                    // Log when the component is initialized
                    console.log('Component initialized with stage:', this.formData.stage);
                    
                    // Ensure the select element has the correct value
                    this.$nextTick(() => {
                        const select = this.$el.querySelector('#stage');
                        if (select) {
                            select.value = this.formData.stage || '';
                            console.log('Select element value set to:', select.value);
                        }
                    });
                },
                // Form configuration (converted from PHP array)
                config: {
                    "stage": {
                        "label": "Stage",
                        "type": "select",
                        "values": {
                            "harvest_processing": "Harvest Processing",
                            "cutting_peeling": "Cutting\/Peeling",
                            "packaging_prep": "Packaging Preparation",
                            "washing_n_treatment": "Washing & Treatment",
                            "drying_n_pre_cooling": "Drying & Pre Cooling",
                            "waste_handling": "Waste Handling"
                        },
                        "conditional_field_group": [
                            {
                                "on": "stage:harvest_processing",
                                "fields": {
                                    "delatex_steps": {
                                        "label": "De-Latex Steps",
                                        "type": "textarea",
                                        "placeholder": "E.g., Stand fruit on plywood, trim prickles, coat cuts with oil",
                                        "required": true
                                    },
                                    "delatex_operator": {
                                        "label": "Operator Name",
                                        "type": "text",
                                        "required": true
                                    },
                                    "delatex_timestamp": {
                                        "label": "Date & Time",
                                        "type": "datetime-local",
                                        "required": true
                                    },
                                    "delatex_notes": {
                                        "label": "Additional Notes",
                                        "type": "textarea",
                                        "required": false
                                    }
                                }
                            },
                            {
                                "on": "stage:cutting_peeling",
                                "fields": {
                                    "processing_type": {
                                        "label": "Processing Type",
                                        "type": "checkbox",
                                        "values": {
                                            "peeling": "Peeling",
                                            "segmenting": "Segmenting",
                                            "chipping": "Chipping",
                                            "pulping": "Pulping"
                                        }
                                    },
                                    "sanitization": {
                                        "label": "Sanitization",
                                        "type": "group",
                                        "fields": {
                                            "sanitizer_type": {
                                                "label": "Sanitizer Type",
                                                "type": "select",
                                                "values": {
                                                    "citric_acid": "Citric Acid",
                                                    "chlorine": "Chlorine",
                                                    "other": "Other"
                                                }
                                            },
                                            "sanitizer_concentration": {
                                                "label": "Concentration (ppm)",
                                                "type": "number",
                                                "min": 0
                                            },
                                            "immersion_time": {
                                                "label": "Immersion Time (minutes)",
                                                "type": "number",
                                                "min": 0
                                            }
                                        }
                                    },
                                    "preservative_used": {
                                        "label": "Preservative Used",
                                        "type": "checkbox",
                                        "values": {
                                            "citric_acid": "Citric Acid",
                                            "ascorbic_acid": "Ascorbic Acid",
                                            "calcium_chloride": "Calcium Chloride",
                                            "none": "None"
                                        }
                                    },
                                    "operator_signature": {
                                        "label": "Operator Signature",
                                        "type": "text",
                                        "required": true
                                    }
                                }
                            },
                            {
                                "on": "stage:packaging_prep",
                                "fields": {
                                    "package_type": {
                                        "label": "Package Type",
                                        "type": "select",
                                        "values": {
                                            "ventilated_crate": "Ventilated Crate",
                                            "vacuum_bag": "Vacuum Bag",
                                            "plastic_container": "Plastic Container",
                                            "other": "Other"
                                        },
                                        "required": true
                                    },
                                    "batch_id": {
                                        "label": "Batch ID",
                                        "type": "text",
                                        "required": true
                                    },
                                    "package_details": {
                                        "label": "Package Details",
                                        "type": "group",
                                        "fields": {
                                            "package_id": {
                                                "label": "Package ID\/QR Code",
                                                "type": "text",
                                                "required": true
                                            },
                                            "net_weight": {
                                                "label": "Net Weight (kg)",
                                                "type": "number",
                                                "step": "0.01",
                                                "min": 0,
                                                "required": true
                                            },
                                            "packer_id": {
                                                "label": "Packer ID",
                                                "type": "text",
                                                "required": true
                                            }
                                        }
                                    },
                                    "storage_conditions": {
                                        "label": "Storage Conditions",
                                        "type": "group",
                                        "fields": {
                                            "temperature": {
                                                "label": "Temperature (°C)",
                                                "type": "number",
                                                "required": true
                                            },
                                            "humidity": {
                                                "label": "Humidity (%)",
                                                "type": "number",
                                                "min": 0,
                                                "max": 100
                                            }
                                        }
                                    }
                                }
                            },
                            {
                                "on": "stage:washing_n_treatment",
                                "fields": {
                                    "washing_water_usage": {
                                        "label": "Washing Water Usage",
                                        "type": "number",
                                        "min": 0,
                                        "max": 100
                                    },
                                    "disinfection_steps": {
                                        "label": "Disinfection Steps",
                                        "type": "checkbox",
                                        "values": {
                                            "chlorine_solution_strength": "Chlorine Solution Strength",
                                            "temperature": "Temperature"
                                        },
                                        "conditional_field_group": [
                                            {
                                                "on": "disinfection_steps:temperature",
                                                "fields": {
                                                    "temperature": {
                                                        "label": "Temperature (°C)",
                                                        "type": "number",
                                                        "min": 0,
                                                        "max": 100
                                                    }
                                                }
                                            },
                                            {
                                                "on": "disinfection_steps:chlorine_solution_strength",
                                                "fields": {
                                                    "chlorine_solution_strength": {
                                                        "label": "Chlorine Solution Strength",
                                                        "type": "number",
                                                        "min": 0,
                                                        "max": 100
                                                    }
                                                }
                                            }
                                        ]
                                    },
                                    "us_export": {
                                        "label": "US Export",
                                        "type": "checkbox",
                                        "values": {
                                            "us_export": "US Export"
                                        },
                                        "conditional_field_group": [
                                            {
                                                "on": "us_export:us_export",
                                                "fields": {
                                                    "hot_water_temperature": {
                                                        "label": "Hot Water Temperature (°C)",
                                                        "type": "number"
                                                    },
                                                    "hot_water_duration": {
                                                        "label": "Hot Water Duration (minutes)",
                                                        "type": "number"
                                                    }
                                                }
                                            }
                                        ]
                                    }
                                }
                            },
                            {
                                "on": "stage:drying_n_pre_cooling",
                                "fields": {
                                    "cold_storage": {
                                        "label": "Cold Storage",
                                        "type": "checkbox",
                                        "values": {
                                            "cold_storage": "Cold Storage"
                                        },
                                        "conditional_field_group": [
                                            {
                                                "on": "cold_storage:cold_storage",
                                                "fields": {
                                                    "temperature": {
                                                        "label": "Temperature (°C)",
                                                        "type": "number"
                                                    },
                                                    "humidity": {
                                                        "label": "Humidity (%)",
                                                        "type": "number"
                                                    }
                                                }
                                            }
                                        ]
                                    }
                                }
                            },
                            {
                                "on": "stage:waste_handling",
                                "fields": {
                                    "washwater_amount": {
                                        "label": "Washwater Amount (L)",
                                        "type": "number"
                                    },
                                    "rejection_weight": {
                                        "label": "Rejection Weight (kg)",
                                        "type": "number"
                                    }
                                }
                            }
                        ]
                    }
                },



                // Initialize checkboxes as arrays
                init() {
                    this.initializeCheckboxFields();
                    console.log(this.formData);
                },

                initializeCheckboxFields() {
                    // Initialize checkbox fields as arrays
                    this.config.stage.conditional_field_group.forEach(group => {
                        Object.entries(group.fields).forEach(([key, field]) => {
                            if (field.type === 'checkbox') {
                                this.formData[key] = [];
                            }
                        });
                    });
                },

                handleStageChange() {console.log(Object.keys(this.formData));
                    console.log(Object.values(this.formData));
                    // Reset form data when stage changes
                    const stage = this.formData.stage;
                    this.formData = { stage };
                    this.initializeCheckboxFields();

                },

                getStageTitle() {
                    if (!this.formData.stage) return '';
                    return this.config.stage.values[this.formData.stage] + ' Details';
                },

                getConditionalFields() {
                    if (!this.formData.stage) return [];
                    return this.config.stage.conditional_field_group.filter(
                        group => group.on === `stage:${this.formData.stage}`
                    );
                },

                getFieldClass(fieldConfig) {
                    // Return appropriate CSS classes based on field type
                    if (fieldConfig.type === 'textarea' || fieldConfig.type === 'group') {
                        return 'md:col-span-2';
                    }
                    return '';
                },

                handleCheckboxChange(fieldKey, value, fieldConfig) {
                    // Ensure the field is initialized as an array
                    if (!Array.isArray(this.formData[fieldKey])) {
                        this.formData[fieldKey] = [];
                    }
                },

                getConditionalSubFields() {
                    const conditionalFields = [];

                    if (!this.formData.stage) return conditionalFields;

                    const currentStageFields = this.getConditionalFields();

                    currentStageFields.forEach(group => {
                        Object.entries(group.fields).forEach(([fieldKey, fieldConfig]) => {
                            if (fieldConfig.conditional_field_group) {
                                fieldConfig.conditional_field_group.forEach(conditionalGroup => {
                                    conditionalFields.push({
                                        fieldKey,
                                        condition: conditionalGroup.on,
                                        fields: conditionalGroup.fields
                                    });
                                });
                            }
                        });
                    });

                    return conditionalFields;
                },

                shouldShowConditionalField(conditionalField) {
                    const [parentField, expectedValue] = conditionalField.condition.split(':');
                    const currentValue = this.formData[parentField];

                    if (Array.isArray(currentValue)) {
                        return currentValue.includes(expectedValue);
                    }

                    return currentValue === expectedValue;
                },

                submitForm() {
                    console.log('Form submitted:', this.formData);
                    alert('Form submitted! Check console and debug panel for data.');
                }
            }
        }
    </script>
</x-app-layout>
