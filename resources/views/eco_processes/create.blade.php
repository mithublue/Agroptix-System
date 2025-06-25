<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($ecoProcess) ? 'Edit' : 'Create' }} Eco Process
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8"
             x-data="ecoProcessForm({{ json_encode(old() ?: $ecoProcess ?? []) }})"
             x-init="init()"
        >
            <form method="POST" action="{{ isset($ecoProcess) ? route('batches.eco-processes.update', [$batch, $ecoProcess] ) : route('batches.eco-processes.store', $batch) }}">
                @csrf
                @if(isset($ecoProcess))
                    @method('PUT')
                @endif

                <!-- Stage -->
                <div class="mb-4">
                    <label for="stage" class="block text-sm font-medium text-gray-700">Stage</label>
                    <select id="stage" name="stage" x-model="form.stage" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <template x-for="(label, value) in stageOptions" :key="value">
                            <option :value="value" x-text="label"></option>
                        </template>
                    </select>
                </div>

                <!-- Conditional Fields -->
                <template x-if="form.stage">
                    <div>
                        <template x-if="stageFieldGroups[form.stage]">
                            <template x-for="(field, key) in stageFieldGroups[form.stage]" :key="key">
                                <div class="mb-4" x-html="renderField(key, field, form.stage)"></div>
                            </template>
                        </template>
                    </div>
                </template>
                <input type="hidden" name="stage" x-model="form.stage">
                <div class="mt-6">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        {{ isset($ecoProcess) ? 'Update' : 'Save' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function ecoProcessForm(initialData) {
            return {
                form: {
                    stage: initialData.stage || '',
                    ...(initialData.data || {})
                },
                stageOptions: {
                    harvest_processing: 'Harvest Processing',
                    cutting_peeling: 'Cutting/Peeling',
                    packaging_prep: 'Packaging Preparation',
                    washing_n_treatment: 'Washing & Treatment',
                    drying_n_pre_cooling: 'Drying & Pre Cooling',
                    waste_handling: 'Waste Handling',
                },
                stageFieldGroups: {
                    harvest_processing: {
                        delatex_steps: { label: 'De-Latex Steps', type: 'textarea', required: true },
                        delatex_operator: { label: 'Operator Name', type: 'text', required: true },
                        delatex_timestamp: { label: 'Date & Time', type: 'datetime-local', required: true },
                        delatex_notes: { label: 'Additional Notes', type: 'textarea', required: false },
                    },
                    cutting_peeling: {
                        processing_type: {
                            label: 'Processing Type',
                            type: 'checkbox',
                            values: {
                                peeling: 'Peeling',
                                segmenting: 'Segmenting',
                                chipping: 'Chipping',
                                pulping: 'Pulping'
                            }
                        },
                        sanitization: {
                            label: 'Sanitization',
                            type: 'group',
                            fields: {
                                sanitizer_type: {
                                    label: 'Sanitizer Type',
                                    type: 'select',
                                    values: {
                                        citric_acid: 'Citric Acid',
                                        chlorine: 'Chlorine',
                                        other: 'Other'
                                    }
                                },
                                sanitizer_concentration: {
                                    label: 'Concentration (ppm)',
                                    type: 'number',
                                    min: 0
                                },
                                immersion_time: {
                                    label: 'Immersion Time (minutes)',
                                    type: 'number',
                                    min: 0
                                }
                            }
                        },
                        preservative_used: {
                            label: 'Preservative Used',
                            type: 'checkbox',
                            values: {
                                citric_acid: 'Citric Acid',
                                ascorbic_acid: 'Ascorbic Acid',
                                calcium_chloride: 'Calcium Chloride',
                                none: 'None'
                            }
                        },
                        operator_signature: { label: 'Operator Signature', type: 'text', required: true }
                    },
                    packaging_prep: {
                        package_type: {
                            label: 'Package Type',
                            type: 'select',
                            values: {
                                ventilated_crate: 'Ventilated Crate',
                                vacuum_bag: 'Vacuum Bag',
                                plastic_container: 'Plastic Container',
                                other: 'Other'
                            },
                            required: true
                        },
                        batch_id: { label: 'Batch ID', type: 'text', required: true },
                        package_details: {
                            label: 'Package Details',
                            type: 'group',
                            fields: {
                                package_id: { label: 'Package ID/QR Code', type: 'text', required: true },
                                net_weight: { label: 'Net Weight (kg)', type: 'number', step: 0.01, min: 0, required: true },
                                packer_id: { label: 'Packer ID', type: 'text', required: true }
                            }
                        },
                        storage_conditions: {
                            label: 'Storage Conditions',
                            type: 'group',
                            fields: {
                                temperature: { label: 'Temperature (°C)', type: 'number', required: true },
                                humidity: { label: 'Humidity (%)', type: 'number', min: 0, max: 100 }
                            }
                        }
                    },
                    washing_n_treatment: {
                        washing_water_usage: {
                            label: 'Washing Water Usage (L)',
                            type: 'number',
                            min: 0,
                            max: 100
                        }
                    }
                },
                init() {
                    // For default field visibility in edit mode
                },
                renderField(key, field, stage) {
                    const name = key;
                    const value = this.form[key] || '';
                    const label = field.label;

                    switch (field.type) {
                        case 'text':
                        case 'datetime-local':
                        case 'number':
                            return `<label class="block mb-1">${label}</label>
                                <input type="${field.type}" name="${name}" value="${value}" class="w-full border rounded px-3 py-2" ${field.required ? 'required' : ''}>`;

                        case 'textarea':
                            return `<label class="block mb-1">${label}</label>
                                <textarea name="${name}" class="w-full border rounded px-3 py-2" ${field.required ? 'required' : ''}>${value}</textarea>`;

                        case 'select':
                            return `<label class="block mb-1">${label}</label>
                                <select name="${name}" class="w-full border rounded px-3 py-2">
                                    ${Object.entries(field.values).map(([val, lbl]) =>
                                `<option value="${val}" ${val === value ? 'selected' : ''}>${lbl}</option>`
                            ).join('')}
                                </select>`;

                        case 'checkbox':
                            return `<label class="block mb-1">${label}</label>
                                ${Object.entries(field.values).map(([val, lbl]) =>
                                `<label class="inline-flex items-center mr-4">
                                        <input type="checkbox" name="${name}[]" value="${val}" ${Array.isArray(value) && value.includes(val) ? 'checked' : ''}>
                                        <span class="ml-1">${lbl}</span>
                                    </label>`
                            ).join('')}`;

                        case 'group':
                            return `<fieldset class="border p-3 rounded mb-4">
                                <legend class="text-sm font-semibold mb-2">${label}</legend>
                                ${Object.entries(field.fields).map(([subKey, subField]) =>
                                this.renderField(`${key}[${subKey}]`, subField, stage)
                            ).join('')}
                            </fieldset>`;
                    }

                    return '';
                }
            }
        }
    </script>
</x-app-layout>
