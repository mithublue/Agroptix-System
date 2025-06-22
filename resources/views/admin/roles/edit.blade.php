<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Role') }}: {{ $role->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Role Name -->
                        <div>
                            <x-label for="name" :value="__('Role Name')" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $role->name)" required autofocus />
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Permissions -->
                        <div class="mt-6">
                            <x-label :value="__('Permissions')" />
                            <div class="mt-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($permissions->groupBy(function($item) {
                                    return explode('_', $item->name)[0] ?? 'other';
                                }) as $group => $groupedPermissions)
                                    <div class="border rounded-md p-3">
                                        <h4 class="text-sm font-medium text-gray-700 mb-2 capitalize">{{ $group }}</h4>
                                        <div class="space-y-2">
                                            @foreach($groupedPermissions as $permission)
                                                <div class="flex items-center">
                                                    <input id="permission_{{ $permission->id }}" 
                                                           type="checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $permission->id }}"
                                                           {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                    <label for="permission_{{ $permission->id }}" class="ml-2 text-sm text-gray-700">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('permissions')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.roles.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                                Cancel
                            </a>
                            <x-button class="ml-4">
                                {{ __('Update Role') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
