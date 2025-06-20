    @extends('layouts.app')

    @section('content')
        @if (session('success'))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 3000)"
                class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50"
                role="alert"
            >
                <div class="flex justify-between">
                    <span class="font-medium">{{ 'Hello world !' }}</span>
                    <button @click="show = false" type="button" class="font-bold">X</button>
                </div>
            </div>
        @endif
    @endsection
