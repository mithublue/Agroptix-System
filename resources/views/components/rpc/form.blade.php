@props([
    'rpcUnit' => null,
    'method' => 'POST',
    'action' => '',
    'submitText' => 'Save RPC Unit',
    'cancelRoute' => 'rpcunit.index'
])

@php
    $isEdit = $rpcUnit !== null;
    $action = $action ?: ($isEdit ? route('rpcunit.update', $rpcUnit) : route('rpcunit.store'));
    $method = $isEdit ? 'PUT' : 'POST';

    $formData = [
        'rpc_identifier' => old('rpc_identifier', $rpcUnit->rpc_identifier ?? ''),
        'capacity_kg' => old('capacity_kg', $rpcUnit->capacity_kg ?? ''),
        'material_type' => old('material_type', $rpcUnit->material_type ?? 'plastic'),
        'status' => old('status', $rpcUnit->status ?? 'available'),
        'total_wash_cycles' => old('total_wash_cycles', $rpcUnit->total_wash_cycles ?? 0),
        'total_reuse_count' => old('total_reuse_count', $rpcUnit->total_reuse_count ?? 0),
        'initial_purchase_date' => old('initial_purchase_date', $rpcUnit->initial_purchase_date ?? ''),
        'last_washed_date' => old('last_washed_date', $rpcUnit->last_washed_date ?? ''),
        'current_location' => old('current_location', $rpcUnit->current_location ?? ''),
        'notes' => old('notes', $rpcUnit->notes ?? '')
    ];
@endphp

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('rpcForm', () => ({
            isLoading: false,
            errors: {},
            isEdit: @json($isEdit),
            redirectUrl: @json(route('rpcunit.index')),

            testFunction() {
                alert('test function called');
                console.log('test function executed');
            },

            async submitForm(event) {
                event.preventDefault();
                this.isLoading = true;
                this.errors = {};

                try {
                    const form = event.target;
                    const formData = new FormData(form);

                    const response = await axios({
                        method: form.method,
                        url: form.action,
                        data: formData,
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    console.log('response:', response);
                    // return false;

                    // Show success message
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: {
                            message: `RPC Unit ${this.isEdit ? 'updated' : 'created'} successfully!`,
                            type: 'success'
                        }
                    }));

                    // Close the drawer
                    const drawer = document.getElementById('rpc-unit-drawer');
                    if (drawer) {
                        drawer.classList.add('hidden');
                    }

                    // Emit event to refresh the RPC units list
                    window.dispatchEvent(new CustomEvent('rpc-unit-created', {
                        detail: {
                            rpcUnit: response.data.data
                        }
                    }));

                    // Reset the form
                    form.reset();
                    this.errors = {};
                } catch (error) {
                    console.error('Error:', error);

                    if (error.response) {
                        // Server responded with a status other than 2xx
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors || {};
                        }

                        const errorMessage = error.response.data?.message ||
                                          error.response.statusText ||
                                          'Failed to save RPC Unit';

                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: {
                                message: errorMessage,
                                type: 'error'
                            }
                        }));
                    } else if (error.request) {
                        // The request was made but no response was received
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: {
                                message: 'No response from server. Please try again.',
                                type: 'error'
                            }
                        }));
                    } else {
                        // Something happened in setting up the request
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: {
                                message: error.message || 'Failed to process request',
                                type: 'error'
                            }
                        }));
                    }
                } finally {
                    this.isLoading = false;
                }
            }
        }));
    });
</script>

<div x-data="rpcForm" class="h-full">

    <form method="POST" action="{{ $action }}" class="space-y-6 h-full flex flex-col" @submit.prevent="submitForm">
    @csrf
    @method($method)

    <!-- Form Header -->
    <div class="border-b border-gray-200 pb-5 mb-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900">
            {{ $isEdit ? 'Edit' : 'Create' }} RPC Unit
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            {{ $isEdit ? 'Update the RPC unit details below.' : 'Fill in the details below to create a new RPC unit.' }}
        </p>
    </div>

    <div class="space-y-6">
        <!-- RPC Identifier -->
        <div>
            <x-input-label for="rpc_identifier" :value="__('RPC Identifier *')" />
            <x-text-input id="rpc_identifier" name="rpc_identifier" type="text" class="mt-1 block w-full"
                         :value="$formData['rpc_identifier']" required autofocus />
            <template x-if="errors.rpc_identifier">
                <p x-text="errors.rpc_identifier[0]" class="mt-1 text-sm text-red-600"></p>
            </template>
        </div>

        <!-- Capacity -->
        <div>
            <x-input-label for="capacity_kg" :value="__('Capacity (kg) *')" />
            <x-text-input id="capacity_kg" name="capacity_kg" type="number" step="0.01" class="mt-1 block w-full"
                         :value="$formData['capacity_kg']" required />
            <template x-if="errors.capacity_kg">
                <p x-text="errors.capacity_kg[0]" class="mt-1 text-sm text-red-600"></p>
            </template>
        </div>

        <!-- Material Type -->
        <div>
            <x-input-label for="material_type" :value="__('Material Type')" />
            <select id="material_type" name="material_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="plastic" {{ $formData['material_type'] === 'plastic' ? 'selected' : '' }}>Plastic</option>
                <option value="metal" {{ $formData['material_type'] === 'metal' ? 'selected' : '' }}>Metal</option>
                <option value="wood" {{ $formData['material_type'] === 'wood' ? 'selected' : '' }}>Wood</option>
                <option value="other" {{ $formData['material_type'] === 'other' ? 'selected' : '' }}>Other</option>
            </select>
            <template x-if="errors.material_type">
                <p x-text="errors.material_type[0]" class="mt-1 text-sm text-red-600"></p>
            </template>
        </div>

        <!-- Status -->
        <div>
            <x-input-label for="status" :value="__('Status')" />
            <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="available" {{ $formData['status'] === 'available' ? 'selected' : '' }}>Available</option>
                <option value="in_use" {{ $formData['status'] === 'in_use' ? 'selected' : '' }}>In Use</option>
                <option value="damaged" {{ $formData['status'] === 'damaged' ? 'selected' : '' }}>Damaged</option>
                <option value="in_repair" {{ $formData['status'] === 'in_repair' ? 'selected' : '' }}>In Repair</option>
                <option value="retired" {{ $formData['status'] === 'retired' ? 'selected' : '' }}>Retired</option>
            </select>
            <template x-if="errors.status">
                <p x-text="errors.status[0]" class="mt-1 text-sm text-red-600"></p>
            </template>
        </div>

        <!-- Initial Purchase Date -->
        <div>
            <x-input-label for="initial_purchase_date" :value="__('Initial Purchase Date')" />
            <x-text-input id="initial_purchase_date" name="initial_purchase_date" type="date" class="mt-1 block w-full"
                         :value="$formData['initial_purchase_date']" />
            <template x-if="errors.initial_purchase_date">
                <p x-text="errors.initial_purchase_date[0]" class="mt-1 text-sm text-red-600"></p>
            </template>
        </div>

        <!-- Last Washed Date -->
        <div>
            <x-input-label for="last_washed_date" :value="__('Last Washed Date')" />
            <x-text-input id="last_washed_date" name="last_washed_date" type="date" class="mt-1 block w-full"
                         :value="$formData['last_washed_date']" />
            <template x-if="errors.last_washed_date">
                <p x-text="errors.last_washed_date[0]" class="mt-1 text-sm text-red-600"></p>
            </template>
        </div>

        <!-- Current Location -->
        <div>
            <x-input-label for="current_location" :value="__('Current Location')" />
            <x-text-input id="current_location" name="current_location" type="text" class="mt-1 block w-full"
                         :value="$formData['current_location']" />
            <template x-if="errors.current_location">
                <p x-text="errors.current_location[0]" class="mt-1 text-sm text-red-600"></p>
            </template>
        </div>

        <!-- Notes -->
        <div>
            <x-input-label for="notes" :value="__('Notes')" />
            <textarea id="notes" name="notes" rows="3"
                     class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ $formData['notes'] }}</textarea>
            <template x-if="errors.notes">
                <p x-text="errors.notes[0]" class="mt-1 text-sm text-red-600"></p>
            </template>
        </div>
    </div>

    <div class="pt-5">
        <div class="flex justify-end gap-3">
        <a href="{{ route($cancelRoute) }}"
           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
            {{ __('Cancel') }}
        </a>

        <button type="submit"
                :disabled="isLoading"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50">
            <svg x-show="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Add
            <span x-show="!isLoading">{{ $submitText }}</span>
            <span x-show="isLoading">{{ __('Saving...') }}</span>
        </button>
        </div>
    </form>
</div>
