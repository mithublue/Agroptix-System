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

                <form method="POST" action="{{ route('admin.options.saveGeneralSettings') }}">
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

                    <div class="flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded shadow">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
