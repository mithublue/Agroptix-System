<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($ecoProcess) ? 'Edit' : 'Create' }} Eco Process
        </h2>
    </x-slot>
    {{ json_encode(old() ?: $ecoProcess ?? []) }}

    <div class="py-6">

        <div x-data="{
        formData: {
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
                            washing_water_usage: { label: 'Washing Water Usage', type: 'number', values: { min: 0, max: 100 } },
                            disinfection_steps: {
                                label: 'Disinfection Steps',
                                type: 'checkbox',
                                values: { chlorine_solution_strength: 'Chlorine Solution Strength', temperature: 'Temperature' },
                                conditional_field_group: [
                                    {
                                        on: 'disinfection_steps:temperature',
                                        fields: { temperature_value: { label: 'Temperature', type: 'number', values: { min: 0, max: 100 } } }
                                    },
                                    {
                                        on: 'disinfection_steps:chlorine_solution_strength',
                                        fields: { chlorine_solution_strength_value: { label: 'Chlorine Solution Strength', type: 'number', values: { min: 10, max: 100 } } }
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
                                            hot_water_temperature: { label: 'Hot Water Temperature', type: 'number', values: '' },
                                            hot_water_duration: { label: 'Hot Water Duration', type: 'number', values: '' }
                                        }
                                    }
                                ]
                            }
                        }
                    },
                    {
                        on: 'stage:drying_n_pre_cooling',
                        fields: {
                            cold_storage: {
                                label: 'Cold Storage',
                                type: 'checkbox',
                                values: { cold_storage: 'Cold Storage' },
                                conditional_field_group: [
                                    {
                                        on: 'cold_storage:cold_storage',
                                        fields: {
                                            temperature: { label: 'Temperature', type: 'number', values: '' },
                                            humidity: { label: 'Humidity', type: 'number', values: '' }
                                        }
                                    }
                                ]
                            }
                        }
                    },
                    {
                        on: 'stage:waste_handling',
                        fields: {
                            washwater_amount: { label: 'Washwater Amount', type: 'number', values: '' },
                            rejection_weight: { label: 'Rejection Weight', type: 'number', values: '' }
                        }
                    }
                ]
            }
        },
        getConditionalFields(stage) {
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
            let current = this.config;
            for (const key of fieldPath.split('.')) {
                current = current[key] || current.fields?.[key];
                if (!current) return null;
            }
            return current;
        },
        updateFormData(field, value) {
            this.formData[field] = value;
            Object.keys(this.formData).forEach(key => {
                if (key !== 'stage' && !this.isFieldVisible(key)) {
                    delete this.formData[key];
                }
            });
            if (field === 'stage') {
                this.initializeVisibleFields();
            }
        },
        initializeVisibleFields() {
            if (!this.formData.stage) return;
            const fields = this.getConditionalFields(this.formData.stage);
            Object.keys(fields).forEach(key => {
                if (!this.formData[key]) {
                    const field = fields[key];
                    if (field.type === 'checkbox') {
                        this.formData[key] = [];
                    } else if (field.type === 'number') {
                        this.formData[key] = 0;
                    } else {
                        this.formData[key] = '';
                    }
                    // Handle nested conditional fields
                    if (field.type === 'checkbox' && field.conditional_field_group) {
                        field.conditional_field_group.forEach(group => {
                            Object.keys(group.fields).forEach(nestedKey => {
                                const nestedField = group.fields[nestedKey];
                                this.formData[nestedKey] = nestedField.type === 'number' ? 0 : '';
                            });
                        });
                    } else if (field.type === 'select' && key === 'sanitizer_type' && field.conditional_field_group) {
                        const nestedFields = this.getNestedConditionalFields(key, 'any');
                        Object.keys(nestedFields).forEach(nestedKey => {
                            const nestedField = nestedFields[nestedKey];
                            this.formData[nestedKey] = nestedField.type === 'number' ? 0 : '';
                        });
                    }
                }
            });
        },
        isFieldVisible(field) {
            if (field === 'stage') return true;
            const stage = this.formData.stage;
            if (!stage) return false;
            const fields = this.getConditionalFields(stage);
            const fieldParts = field.split('.');
            let currentFields = fields;
            for (let i = 0; i < fieldParts.length - 1; i++) {
                const part = fieldParts[i];
                if (currentFields[part]?.type === 'group') {
                    currentFields = currentFields[part].fields;
                } else if (currentFields[part]?.type === 'checkbox' && currentFields[part].conditional_field_group) {
                    const checkboxValue = this.formData[part];
                    if (checkboxValue && Array.isArray(checkboxValue)) {
                        const nestedFields = currentFields[part].conditional_field_group
                            .filter(g => checkboxValue.includes(g.on.split(':')[1]))
                            .reduce((acc, g) => ({ ...acc, ...g.fields }), {});
                        currentFields = nestedFields;
                    } else {
                        return false;
                    }
                } else if (currentFields[part]?.type === 'select' && currentFields[part].conditional_field_group && part === 'sanitizer_type') {
                    const selectValue = this.formData[part];
                    if (selectValue) {
                        const nestedFields = this.getNestedConditionalFields(part, 'any');
                        currentFields = nestedFields;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
            return !!currentFields[fieldParts[fieldParts.length - 1]];
        }
    }" x-init="initializeVisibleFields()" class="w-full max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-lg">
            <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Processing Form</h1>

            <!-- Stage Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2" x-text="config.stage.label"></label>
                <select x-model="formData.data.stage" @change="updateFormData('stage', $event.target.value)"
                        class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <option value="">Select a stage</option>
                    <template x-for="[key, value] in Object.entries(config.stage.values)" :key="key">
                        <option :value="key" x-text="value"></option>
                    </template>
                </select>
            </div>

            <!-- Dynamic Fields -->
            <template x-if="formData.stage">
                <div x-bind="getConditionalFields(formData.stage)" class="space-y-6 fade-in">
                    <template x-for="[key, field] in Object.entries(getConditionalFields(formData.stage))" :key="key">
                        <div class="mb-6" x-show="isFieldVisible(key)">
                            <template x-if="field.type === 'text' || field.type === 'number' || field.type === 'datetime-local'">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2" x-text="field.label"></label>
                                    <input :type="field.type" x-model="formData[key]" @input="updateFormData(key, $event.target.value)"
                                           :required="field.required" :min="field.min || field.values?.min" :max="field.max || field.values?.max" :step="field.step || ''"
                                           class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                </div>
                            </template>
                            <template x-if="field.type === 'textarea'">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2" x-text="field.label"></label>
                                    <textarea x-model="formData[key]" @input="updateFormData(key, $event.target.value)"
                                              :required="field.required" :placeholder="field.placeholder"
                                              class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition resize-y"></textarea>
                                </div>
                            </template>
                            <template x-if="field.type === 'select'">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2" x-text="field.label"></label>
                                    <select x-model="formData[key]" @change="updateFormData(key, $event.target.value)"
                                            :required="field.required"
                                            class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                        <option value="">Select an option</option>
                                        <template x-for="[val, label] in Object.entries(field.values)" :key="val">
                                            <option :value="val" x-text="label"></option>
                                        </template>
                                    </select>
                                    <!-- Nested Conditional Fields for Select (sanitizer_type) -->
                                    <template x-if="field.conditional_field_group && key === 'sanitizer_type'">
                                        <div class="ml-6 mt-4 space-y-4" x-show="formData[key]">
                                            <template x-for="group in field.conditional_field_group" :key="group.on">
                                                <div x-show="formData[key]" class="fade-in">
                                                    <template x-for="[nestedKey, nestedField] in Object.entries(getNestedConditionalFields(key, 'any'))" :key="nestedKey">
                                                        <div class="mb-4" x-show="isFieldVisible(`${key}.${nestedKey}`)">
                                                            <label class="block text-sm font-medium text-gray-700 mb-2" x-text="nestedField.label"></label>
                                                            <input :type="nestedField.type" x-model="formData[nestedKey]"
                                                                   @input="updateFormData(nestedKey, $event.target.value)"
                                                                   :required="nestedField.required" :min="nestedField.min || nestedField.values?.min" :max="nestedField.max || nestedField.values?.max"
                                                                   class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="field.type === 'checkbox'">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2" x-text="field.label"></label>
                                    <div class="space-y-2">
                                        <template x-for="[val, label] in Object.entries(field.values)" :key="val">
                                            <div class="flex items-center">
                                                <input type="checkbox" :value="val" x-model="formData[key]"
                                                       @change="updateFormData(key, Array.isArray(formData[key]) ? formData[key] : [])"
                                                       class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition">
                                                <label class="ml-2 text-sm text-gray-600" x-text="label"></label>
                                            </div>
                                        </template>
                                    </div>
                                    <!-- Nested Conditional Fields for Checkboxes -->
                                    <template x-if="field.conditional_field_group">
                                        <div class="ml-6 mt-4 space-y-4">
                                            <template x-for="group in field.conditional_field_group" :key="group.on">
                                                <div x-show="formData[key] && formData[key].includes(group.on.split(':')[1])" class="fade-in">
                                                    <template x-for="[nestedKey, nestedField] in Object.entries(group.fields)" :key="nestedKey">
                                                        <div class="mb-4" x-show="isFieldVisible(`${key}.${nestedKey}`)">
                                                            <label class="block text-sm font-medium text-gray-700 mb-2" x-text="nestedField.label"></label>
                                                            <span x-html="nestedKey"></span>
                                                            <input :type="nestedField.type" x-model="formData[nestedKey]"
                                                                   @input="updateFormData(nestedKey, $event.target.value)"
                                                                   :required="nestedField.required" :min="nestedField.min || nestedField.values?.min" :max="nestedField.max || nestedField.values?.max"
                                                                   class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Display JSON Data -->
            <div class="mt-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Form Data JSON</h2>
                <pre class="bg-gray-100 p-4 rounded-lg shadow-sm text-sm text-gray-700 overflow-auto"><code x-text="JSON.stringify(formData, null, 2)"></code></pre>
            </div>
            <div class="mt-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Form Configuration JSON</h2>
                <pre class="bg-gray-100 p-4 rounded-lg shadow-sm text-sm text-gray-700 overflow-auto"><code x-text="JSON.stringify(config, null, 2)"></code></pre>
            </div>
        </div>

    </div>
</x-app-layout>
