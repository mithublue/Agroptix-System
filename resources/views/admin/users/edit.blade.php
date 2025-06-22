<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <x-label for="name" :value="__('Name')" />
                                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <x-label for="email" :value="__('Email')" />
                                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password (Optional) -->
                            <div>
                                <x-label for="password" :value="__('New Password (leave blank to keep current)')" />
                                <x-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" />
                                @error('password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <x-label for="password_confirmation" :value="__('Confirm New Password')" />
                                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
                            </div>

                            <!-- Roles -->
                            <div class="md:col-span-2">
                                <x-label :value="__('Roles')" />
                                <div class="mt-2 space-y-2">
                                    @foreach($roles as $role)
                                        <div class="flex items-center">
                                            <input id="role_{{ $role->id }}" type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                                {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <label for="role_{{ $role->id }}" class="ml-2 text-sm text-gray-700">{{ $role->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('roles')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                                Cancel
                            </a>
                            <x-button class="ml-4">
                                {{ __('Update User') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
