<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($ecoProcess) ? 'Edit Eco Process' : 'Create Eco Process' }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ isset($ecoProcess) ? route('eco_processes.update', $ecoProcess) : route('batches.eco-processes.store', [$batch]) }}">
                @csrf
                @if(isset($ecoProcess))
                    @method('PUT')
                @endif

                <div x-data="formComponent()" x-init="init()">
                    <!-- Stage Dropdown -->
                    <div class="mb-4">
                        <label for="stage" class="block font-medium text-gray-700">Stage</label>
                        <select id="stage" name="stage" class="mt-1 block w-full" x-model="stage">
                            <option value="">Select Stage</option>
                            <option value="washing_n_treatment">Washing & Treatment</option>
                            <option value="drying_n_pre_cooling">Drying & Pre Cooling</option>
                            <option value="waste_handling">Waste Handling</option>
                        </select>
                    </div>

                    <!-- Washing & Treatment Fields -->
                    <template x-if="stage === 'washing_n_treatment'">
                        <div>
                            <div class="mb-4">
                                <label class="block">Washing Water Usage</label>
                                <input type="number" min="0" max="100" class="mt-1 block w-full" name="washing_water_usage" x-model="formData.washing_water_usage">
                            </div>

                            <div class="mb-4">
                                <label class="block">Disinfection Steps</label>
                                <div>
                                    <label><input type="checkbox" value="temperature" x-model="disinfectionSteps"> Temperature</label>
                                    <label><input type="checkbox" value="chlorine_solution_strength" x-model="disinfectionSteps"> Chlorine Strength</label>
                                </div>
                            </div>

                            <template x-if="disinfectionSteps.includes('temperature')">
                                <div class="mb-4">
                                    <label class="block">Temperature</label>
                                    <input type="number" min="0" max="100" class="mt-1 block w-full" name="temperature" x-model="formData.temperature">
                                </div>
                            </template>

                            <template x-if="disinfectionSteps.includes('chlorine_solution_strength')">
                                <div class="mb-4">
                                    <label class="block">Chlorine Solution Strength</label>
                                    <input type="number" min="0" max="100" class="mt-1 block w-full" name="chlorine_solution_strength" x-model="formData.chlorine_solution_strength">
                                </div>
                            </template>

                            <div class="mb-4">
                                <label><input type="checkbox" value="1" x-model="formData.us_export"> US Export</label>
                            </div>

                            <template x-if="formData.us_export">
                                <div>
                                    <div class="mb-4">
                                        <label class="block">Hot Water Temperature</label>
                                        <input type="number" class="mt-1 block w-full" name="hot_water_temperature" x-model="formData.hot_water_temperature">
                                    </div>
                                    <div class="mb-4">
                                        <label class="block">Hot Water Duration</label>
                                        <input type="number" class="mt-1 block w-full" name="hot_water_duration" x-model="formData.hot_water_duration">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Drying & Pre Cooling Fields -->
                    <template x-if="stage === 'drying_n_pre_cooling'">
                        <div>
                            <div class="mb-4">
                                <label><input type="checkbox" value="1" x-model="formData.cold_storage"> Cold Storage</label>
                            </div>

                            <template x-if="formData.cold_storage">
                                <div>
                                    <div class="mb-4">
                                        <label class="block">Temperature</label>
                                        <input type="number" class="mt-1 block w-full" name="cold_storage_temperature" x-model="formData.cold_storage_temperature">
                                    </div>
                                    <div class="mb-4">
                                        <label class="block">Humidity</label>
                                        <input type="number" class="mt-1 block w-full" name="cold_storage_humidity" x-model="formData.cold_storage_humidity">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Waste Handling Fields -->
                    <template x-if="stage === 'waste_handling'">
                        <div>
                            <div class="mb-4">
                                <label class="block">Washwater Amount</label>
                                <input type="number" class="mt-1 block w-full" name="washwater_amount" x-model="formData.washwater_amount">
                            </div>
                            <div class="mb-4">
                                <label class="block">Rejection Weight</label>
                                <input type="number" class="mt-1 block w-full" name="rejection_weight" x-model="formData.rejection_weight">
                            </div>
                        </div>
                    </template>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function formComponent() {
            return {
                stage: '{{ old('stage', $ecoProcess->stage ?? '') }}',
                disinfectionSteps: @json(old('disinfection_steps', $ecoProcess->disinfection_steps ?? [])),
                formData: {
                    washing_water_usage: '{{ old('washing_water_usage', $ecoProcess->washing_water_usage ?? '') }}',
                    temperature: '{{ old('temperature', $ecoProcess->temperature ?? '') }}',
                    chlorine_solution_strength: '{{ old('chlorine_solution_strength', $ecoProcess->chlorine_solution_strength ?? '') }}',
                    us_export: @json(old('us_export', $ecoProcess->us_export ?? false)),
                    hot_water_temperature: '{{ old('hot_water_temperature', $ecoProcess->hot_water_temperature ?? '') }}',
                    hot_water_duration: '{{ old('hot_water_duration', $ecoProcess->hot_water_duration ?? '') }}',
                    cold_storage: @json(old('cold_storage', $ecoProcess->cold_storage ?? false)),
                    cold_storage_temperature: '{{ old('cold_storage_temperature', $ecoProcess->cold_storage_temperature ?? '') }}',
                    cold_storage_humidity: '{{ old('cold_storage_humidity', $ecoProcess->cold_storage_humidity ?? '') }}',
                    washwater_amount: '{{ old('washwater_amount', $ecoProcess->washwater_amount ?? '') }}',
                    rejection_weight: '{{ old('rejection_weight', $ecoProcess->rejection_weight ?? '') }}',
                },
                init() {
                    if (!Array.isArray(this.disinfectionSteps)) {
                        this.disinfectionSteps = [];
                    }
                }
            }
        }
    </script>
</x-app-layout>
