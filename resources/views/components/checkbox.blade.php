@props(['name' => null, 'value' => 1, 'checked' => false, 'label' => null, 'id' => null])

@php
    $id = $id ?? $name;
    $checked = old($name) !== null ? (bool)old($name) : $checked;
@if($id) @endphp
    <div class="flex items-center">
        <input
            type="checkbox"
            id="{{ $id }}"
            name="{{ $name }}"
            value="{{ $value }}"
            {{ $checked ? 'checked' : '' }}
            {{ $attributes->merge(['class' => 'h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500']) }}
        >
        @if($label)
            <label for="{{ $id }}" class="ml-2 block text-sm text-gray-900">
                {{ $label }}
            </label>
        @endif
    </div>
@endif
