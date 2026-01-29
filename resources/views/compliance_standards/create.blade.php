<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($complianceStandard) ? __('Edit Compliance Standard') : __('Create Compliance Standard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST"
                        action="{{ isset($complianceStandard) ? route('admin.compliance-standards.update', $complianceStandard) : route('admin.compliance-standards.store') }}">
                        @csrf
                        @if(isset($complianceStandard))
                            @method('PUT')
                        @endif

                        <!-- Region -->
                        <div class="mb-4">
                            <label for="region" class="block text-sm font-medium text-gray-700">Region / Market</label>
                            <input type="text" name="region" id="region"
                                value="{{ old('region', $complianceStandard->region ?? 'EU') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required>
                            @error('region') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Crop Type -->
                        <div class="mb-4">
                            <label for="crop_type" class="block text-sm font-medium text-gray-700">Crop Type</label>
                            <input type="text" name="crop_type" id="crop_type"
                                value="{{ old('crop_type', $complianceStandard->crop_type ?? '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="e.g. Tomato" required>
                            @error('crop_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Parameter Name -->
                        <div class="mb-4">
                            <label for="parameter_name" class="block text-sm font-medium text-gray-700">Parameter
                                Tested</label>
                            <input type="text" name="parameter_name" id="parameter_name"
                                value="{{ old('parameter_name', $complianceStandard->parameter_name ?? '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="e.g. pesticide_residue" required>
                            <p class="text-xs text-gray-500 mt-1">Must match the parameter code used in Quality Tests
                                (approximate match).</p>
                            @error('parameter_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <!-- Min Value -->
                            <div>
                                <label for="min_value" class="block text-sm font-medium text-gray-700">Min Value
                                    (Optional)</label>
                                <input type="number" step="0.0001" name="min_value" id="min_value"
                                    value="{{ old('min_value', $complianceStandard->min_value ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <!-- Max Value -->
                            <div>
                                <label for="max_value" class="block text-sm font-medium text-gray-700">Max Value
                                    (Optional)</label>
                                <input type="number" step="0.0001" name="max_value" id="max_value"
                                    value="{{ old('max_value', $complianceStandard->max_value ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>

                        <!-- Unit -->
                        <div class="mb-4">
                            <label for="unit" class="block text-sm font-medium text-gray-700">Unit of
                                Measurement</label>
                            <input type="text" name="unit" id="unit"
                                value="{{ old('unit', $complianceStandard->unit ?? 'mg/kg') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required>
                            @error('unit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Critical Action -->
                        <div class="mb-4">
                            <label for="critical_action" class="block text-sm font-medium text-gray-700">Critical
                                Violation Action</label>
                            <select name="critical_action" id="critical_action"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="warning" {{ old('critical_action', $complianceStandard->critical_action ?? '') == 'warning' ? 'selected' : '' }}>Warning Only</option>
                                <option value="reject_batch" {{ old('critical_action', $complianceStandard->critical_action ?? '') == 'reject_batch' ? 'selected' : '' }}>
                                    Available for Auto-Rejection (Kill Switch)</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">If set to Auto-Rejection, exceeding the limit will
                                instantly REJECT the batch.</p>
                        </div>

                        <!-- Active Status -->
                        <div class="mb-6">
                            <div class="flex items-center">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $complianceStandard->is_active ?? true) ? 'checked' : '' }}
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Active Standard
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('admin.compliance-standards.index') }}"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md mr-2">Cancel</a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                                {{ isset($complianceStandard) ? 'Update Standard' : 'Create Standard' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>