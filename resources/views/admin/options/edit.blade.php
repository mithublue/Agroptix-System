<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Option') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.options.update', $option->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Option Name</label>
                            <input type="text" name="option_name" value="{{ $option->option_name }}" class="w-full px-4 py-2 border rounded bg-gray-100" disabled>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Option Value</label>
                            <textarea name="option_value" rows="4" class="w-full px-4 py-2 border rounded">{{ old('option_value', $option->option_value) }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Autoload</label>
                            <select name="autoload" class="w-full px-4 py-2 border rounded">
                                <option value="yes" @if($option->autoload == 'yes') selected @endif>Yes</option>
                                <option value="no" @if($option->autoload == 'no') selected @endif>No</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Save</button>
                        <a href="{{ route('admin.options.index') }}" class="ml-4 text-gray-600 hover:underline">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
