@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Site Options</h1>
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    <table class="min-w-full bg-white border">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Option Name</th>
                <th class="py-2 px-4 border-b">Value</th>
                <th class="py-2 px-4 border-b">Autoload</th>
                <th class="py-2 px-4 border-b">Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($options as $option)
            <tr>
                <td class="py-2 px-4 border-b">{{ $option->option_name }}</td>
                <td class="py-2 px-4 border-b">{{ Str::limit($option->option_value, 60) }}</td>
                <td class="py-2 px-4 border-b">{{ $option->autoload }}</td>
                <td class="py-2 px-4 border-b">
                    <a href="{{ route('admin.options.edit', $option->id) }}" class="text-blue-600 hover:underline">Edit</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-4">{{ $options->links() }}</div>
</div>
@endsection
