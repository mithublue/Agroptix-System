<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Registration & Activation Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-8">
                <form method="POST" action="{{ route('admin.options.saveUserOptions') }}" x-data="{
                        activation: '{{ option('users_need_activation', 'yes') }}',
                        activationMethod: '{{ option('users_activation_method', 'email') }}',
                        adminApproval: '{{ option('users_need_admin_approval', 'no') }}'
                    }">
                    @csrf
                    <!-- Users need activation -->
                    <div class="flex items-center justify-between mb-6">
                        <label class="block font-medium text-gray-700 text-lg">Users need activation?</label>
                        <button type="button" @click="activation = (activation === 'yes' ? 'no' : 'yes')"
                            :aria-pressed="activation === 'yes'"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            :class="activation === 'yes' ? 'bg-indigo-600' : 'bg-gray-300'">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                :class="activation === 'yes' ? 'translate-x-6' : 'translate-x-1'"></span>
                        </button>
                        <input type="hidden" name="users_need_activation" :value="activation">
                    </div>

                    <!-- Activate by (conditional) -->
                    <div class="mb-6" x-show="activation === 'yes'" x-transition>
                        <label class="block font-medium text-gray-700 text-lg mb-2">Activate by:</label>
                        <div class="flex space-x-6">
                            <label class="inline-flex items-center">
                                <input type="radio" class="form-radio text-indigo-600" name="users_activation_method"
                                    value="email" x-model="activationMethod">
                                <span class="ml-2 text-gray-700">Email</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" class="form-radio text-indigo-600" name="users_activation_method"
                                    value="phone" x-model="activationMethod">
                                <span class="ml-2 text-gray-700">Phone</span>
                            </label>
                        </div>
                    </div>

                    <!-- Roles available for registration -->
                    <div class="mb-6">
                        <label class="block font-medium text-gray-700 text-lg mb-2">Which roles can new users
                            choose?</label>
                        <select name="registration_roles[]" multiple
                            x-init="new TomSelect($el, { plugins: ['remove_button'] })"
                            class="w-full px-4 py-2 border rounded bg-gray-50">
                            @foreach(\Spatie\Permission\Models\Role::all() as $role)
                                <option value="{{ $role->name }}" @if(collect(json_decode(option('registration_roles', '[]')))->contains($role->name)) selected @endif>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-gray-500 text-sm mt-1">You can search and select multiple roles.</p>
                    </div>

                    <!-- Registered users need admin approval -->
                    <div class="flex items-center justify-between mb-6">
                        <label class="block font-medium text-gray-700 text-lg">Registered users need admin
                            approval?</label>
                        <button type="button" @click="adminApproval = (adminApproval === 'yes' ? 'no' : 'yes')"
                            :aria-pressed="adminApproval === 'yes'"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            :class="adminApproval === 'yes' ? 'bg-indigo-600' : 'bg-gray-300'">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                :class="adminApproval === 'yes' ? 'translate-x-6' : 'translate-x-1'"></span>
                        </button>
                        <input type="hidden" name="users_need_admin_approval" :value="adminApproval">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded shadow">Save
                            Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>