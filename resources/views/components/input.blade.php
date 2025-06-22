@props(['id' => null, 'name' => null, 'type' => 'text', 'value' => '', 'error' => null, 'placeholder' => '', 'required' => false])

@php
    $name = $name ?? $id;
    $hasError = $errors->has($name);
    $classes = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm';
    if ($hasError) {
        $classes .= ' border-red-500';
    }
@endphp

<input
    type="{{ $type }}"
    @if($id) id="{{ $id }}" @endif
    @if($name) name="{{ $name }}" @endif
    value="{{ old($name, $value) }}"
    {{ $attributes->merge(['class' => $classes]) }}
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    @if($required) required @endif
>

@if($hasError)
    <p class="mt-1 text-sm text-red-600">{{ $errors->first($name) }}</p>
@endif
