<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Quality Test') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                
                <form action="{{ route('quality-tests.store', ['batch' => $batch->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Quality Validation Section -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Quality Validation</h3>
                            
                            <div class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
                                <!-- Batch ID -->
                                <div class="sm:col-span-3">
                                    <label for="batch_id" class="block text-sm font-medium text-gray-700">Batch ID</label>
                                    <input type="text" name="batch_id" id="batch_id" value="{{ $batch->batch_code }}" readonly
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100">
                                    <p class="mt-1 text-xs text-gray-500">Enter the unique batch identifier</p>
                                </div>
                                
                                <!-- Test Date -->
                                <div class="sm:col-span-3">
                                    <label for="test_date" class="block text-sm font-medium text-gray-700">Date of Test <span class="text-red-500">*</span></label>
                                    <input type="date" name="test_date" id="test_date" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                
                                <!-- Parameter Tested -->
                                <div class="sm:col-span-3">
                                    <label for="test_parameter" class="block text-sm font-medium text-gray-700">Parameter Tested <span class="text-red-500">*</span></label>
                                    <select id="test_parameter" name="test_parameter" required
                                        class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Select a parameter</option>
                                        <option value="microbes">Microbes</option>
                                        <option value="ph">pH Level</option>
                                        <option value="heavy_metals">Heavy Metals</option>
                                        <option value="toxins">Toxins</option>
                                        <option value="pesticides">Pesticides</option>
                                        <option value="moisture">Moisture Content</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                
                                <!-- Test Result -->
                                <div class="sm:col-span-3">
                                    <label for="test_result" class="block text-sm font-medium text-gray-700">Test Result <span class="text-red-500">*</span></label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <input type="number" step="0.01" name="test_result" id="test_result" required
                                            class="block w-full rounded-none rounded-l-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <span class="inline-flex items-center rounded-r-md border border-l-0 border-gray-300 bg-gray-50 px-3 text-gray-500 sm:text-sm" id="result_unit_display">
                                            --
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Unit of Measurement -->
                                <div class="sm:col-span-3">
                                    <label for="result_unit" class="block text-sm font-medium text-gray-700">Unit of Measurement <span class="text-red-500">*</span></label>
                                    <select id="result_unit" name="result_unit" required
                                        class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Select a unit</option>
                                        <option value="ppm">ppm</option>
                                        <option value="percent">%</option>
                                        <option value="cfu_g">CFU/g</option>
                                        <option value="ph">pH</option>
                                        <option value="mg_kg">mg/kg</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                
                                <!-- Threshold Value -->
                                <div class="sm:col-span-3">
                                    <label for="threshold_value" class="block text-sm font-medium text-gray-700">Threshold Value <span class="text-red-500">*</span></label>
                                    <input type="number" step="0.01" name="threshold_value" id="threshold_value" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <p class="mt-1 text-xs text-gray-500">Maximum acceptable value for this parameter</p>
                                </div>
                                
                                <!-- Pass/Fail Status -->
                                <div class="sm:col-span-6">
                                    <label class="block text-sm font-medium text-gray-700">Pass/Fail Status <span class="text-red-500">*</span></label>
                                    <div class="mt-1 space-x-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="pass_fail" value="pass" required
                                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                            <span class="ml-2 text-sm text-gray-700">Pass</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="pass_fail" value="fail" required
                                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                            <span class="ml-2 text-sm text-gray-700">Fail</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Tester Name -->
                                <div class="sm:col-span-6">
                                    <label for="tester_name" class="block text-sm font-medium text-gray-700">Name of Testing Lab/Technician <span class="text-red-500">*</span></label>
                                    <input type="text" name="tester_name" id="tester_name" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                
                                <!-- Test Certificate -->
                                <div class="sm:col-span-6">
                                    <label for="test_certificate" class="block text-sm font-medium text-gray-700">Upload Test Certificate/Lab Scan</label>
                                    <input type="file" name="test_certificate" id="test_certificate"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        class="mt-1 block w-full text-sm text-gray-500
                                            file:mr-4 file:py-2 file:px-4
                                            file:rounded-md file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-indigo-50 file:text-indigo-700
                                            hover:file:bg-indigo-100">
                                    <p class="mt-1 text-xs text-gray-500">Accepted formats: PDF, JPG, JPEG, PNG</p>
                                </div>
                                
                                <!-- Additional Notes -->
                                <div class="sm:col-span-6">
                                    <label for="additional_notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                                    <textarea id="additional_notes" name="additional_notes" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="Enter any additional observations or comments"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="pt-5">
                        <div class="flex justify-end">
                            <a href="{{ route('batches.show', $batch) }}"
                                class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Cancel
                            </a>
                            <button type="submit"
                                class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Save Test
                            </button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Update the unit display when the unit select changes
        document.getElementById('result_unit').addEventListener('change', function() {
            const unit = this.value;
            const displayMap = {
                'ppm': 'ppm',
                'percent': '%',
                'cfu_g': 'CFU/g',
                'ph': 'pH',
                'mg_kg': 'mg/kg',
                'other': 'Unit'
            };
            document.getElementById('result_unit_display').textContent = displayMap[unit] || '--';
        });
        
        // Trigger change event on page load if a unit is already selected
        document.addEventListener('DOMContentLoaded', function() {
            const unitSelect = document.getElementById('result_unit');
            if (unitSelect.value) {
                unitSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
    @endpush
</x-app-layout>