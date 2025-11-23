<!DOCTYPE html>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('General Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-8">
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.options.saveGeneralSettings') }}" enctype="multipart/form-data" x-data="{ previewUrl: null }">
                    @csrf
                    
                    <!-- Project Name -->
                    <div class="mb-6">
                        <label for="project_name" class="block font-medium text-gray-700 text-lg mb-2">Project Name</label>
                        <input type="text" 
                               name="project_name" 
                               id="project_name" 
                               value="{{ old('project_name', option('project_name', 'Agroptix')) }}"
                               class="w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('project_name') border-red-500 @enderror"
                               placeholder="Enter project name">
                        @error('project_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-sm mt-1">This name will be displayed throughout the application.</p>
                    </div>

                    <!-- Favicon Upload -->
                    <div class="mb-6">
                        <label for="favicon" class="block font-medium text-gray-700 text-lg mb-2">Favicon</label>
                        
                        <!-- Current Favicon Display -->
                        @if(option('favicon'))
                            <div class="mb-3 p-3 bg-gray-50 rounded-md inline-block">
                                <p class="text-sm text-gray-600 mb-2">Current Favicon:</p>
                                <img src="{{ asset('storage/' . option('favicon')) }}" alt="Current Favicon" class="h-8 w-8 object-contain">
                            </div>
                        @endif

                        <!-- File Input -->
                        <input type="file" 
                               name="favicon" 
                               id="favicon" 
                               accept="image/x-icon,image/png,image/jpeg,image/jpg,image/svg+xml"
                               class="w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('favicon') border-red-500 @enderror"
                               @change="previewUrl = URL.createObjectURL($event.target.files[0])">
                        @error('favicon')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-sm mt-1">Upload a favicon image (ICO, PNG, JPG, SVG). Max size: 2MB. Recommended: 32x32px or 16x16px.</p>
                        
                        <!-- Preview -->
                        <div x-show="previewUrl" class="mt-3 p-3 bg-gray-50 rounded-md inline-block">
                            <p class="text-sm text-gray-600 mb-2">Preview:</p>
                            <img :src="previewUrl" alt="Favicon Preview" class="h-8 w-8 object-contain">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded shadow">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
