@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Edit Option</h1>
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
@endsection
