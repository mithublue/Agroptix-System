<x-app-layout>
    {{-- This optional header slot will go into the <header> of your layout --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sources') }}
        </h2>
    </x-slot>

    {{-- This is the main content that will go into the {{ $slot }} variable --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <p>Your list of sources and other content goes here!</p>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
