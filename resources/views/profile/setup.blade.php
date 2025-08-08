<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Complete Your Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200" x-data="setupWizard()" x-init="init()">
            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="flex justify-between mb-2">
                    <h2 class="text-lg font-medium text-gray-900">Complete Your Profile</h2>
                    <span class="text-sm text-gray-500">
                        Step <span x-text="currentStep"></span> of <span x-text="totalSteps"></span>
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-blue-600 h-2.5 rounded-full" 
                         :style="'width: ' + (currentStep / totalSteps * 100) + '%'">
                    </div>
                </div>
            </div>

            <!-- Step 1: Role Selection -->
            <div x-show="currentStep === 1" class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900">What are you?</h3>
                <p class="text-sm text-gray-500">Please select your role to continue.</p>
                
                <div class="grid gap-4 md:grid-cols-2 mt-6">
                    @foreach($registrationRoles as $role)
                        <div class="relative">
                            <input 
                                type="radio" 
                                name="role" 
                                id="role-{{ $role }}" 
                                value="{{ $role }}" 
                                x-model="formData.role"
                                class="hidden peer"
                                @change="if (formData.role === 'supplier') { hasProducts = true; } else { hasProducts = false; }"
                                required>
                            <label 
                                for="role-{{ $role }}" 
                                class="flex p-4 w-full bg-white border border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50">
                                <div class="flex items-center justify-between w-full">
                                    <div class="flex items-center">
                                        <div class="text-sm">
                                            <div class="font-medium text-gray-900 capitalize">{{ ucfirst($role) }}</div>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-blue-600" x-show="formData.role === '{{ $role }}'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
                
                <div class="flex justify-end mt-8">
                    <button 
                        type="button" 
                        @click="nextStep()"
                        :disabled="!formData.role"
                        :class="{'opacity-50 cursor-not-allowed': !formData.role, 'bg-blue-600 hover:bg-blue-700': formData.role}"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Continue
                    </button>
                </div>
            </div>

            <!-- Step 2: Product Selection (only for suppliers) -->
            <div x-show="currentStep === 2" class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900">What products will you sell?</h3>
                <p class="text-sm text-gray-500">Select all the products you plan to sell.</p>
                
                <div class="mt-4 space-y-2">
                    @foreach($products as $product)
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="product-{{ $product->id }}" 
                                value="{{ $product->id }}"
                                x-model="formData.products"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="product-{{ $product->id }}" class="ml-3 block text-sm font-medium text-gray-700">
                                {{ $product->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
                
                <div class="flex justify-between mt-8">
                    <button 
                        type="button" 
                        @click="previousStep()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Back
                    </button>
                    <button 
                        type="button" 
                        @click="submitForm()"
                        :disabled="!formData.products || formData.products.length === 0"
                        :class="{'opacity-50 cursor-not-allowed': !formData.products || formData.products.length === 0, 'bg-blue-600 hover:bg-blue-700': formData.products && formData.products.length > 0}"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Complete Setup
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div x-show="isSubmitting" class="text-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                <p class="mt-2 text-sm text-gray-500">Saving your preferences...</p>
            </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
<script>
    function setupWizard() {
        return {
            currentStep: 1,
            totalSteps: {{ in_array('supplier', $registrationRoles) ? 2 : 1 }},
            isSubmitting: false,
            hasProducts: false,
            formData: {
                role: '',
                products: []
            },
            
            init() {
                // If supplier is the only role, auto-select it
                const registrationRoles = @json($registrationRoles);
                if (registrationRoles.length === 1) {
                    this.formData.role = registrationRoles[0];
                    if (this.formData.role === 'supplier') {
                        this.hasProducts = true;
                    }
                }
                
                // Update total steps based on role
                this.$watch('formData.role', (value) => {
                    this.totalSteps = (value === 'supplier') ? 2 : 1;
                });
            },
            
            nextStep() {
                if (this.formData.role) {
                    this.currentStep++;
                }
            },
            
            previousStep() {
                this.currentStep--;
            },
            
            async submitForm() {
                this.isSubmitting = true;
                
                try {
                    const response = await fetch('{{ route('profile.setup.save') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        alert('An error occurred. Please try again.');
                        this.isSubmitting = false;
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                    this.isSubmitting = false;
                }
            }
        };
    }
</script>
    @endpush
</x-app-layout>
