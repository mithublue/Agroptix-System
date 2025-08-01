<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Delivery') }}
            </h2>
            <a href="{{ route('deliveries.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Back to Deliveries
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold">Edit Delivery #{{ $delivery->id }}</h2>
                        <div class="flex space-x-2">
                            @if(app()->environment('local'))
                                <button type="button" onclick="fillTestData()"
                                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">
                                    Fill Test Data
                                </button>
                            @endif
                            <a href="{{ route('deliveries.show', $delivery) }}"
                               class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">
                                View Details
                            </a>
                        </div>
                    </div>

                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <ul class="list-disc pl-5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form Component -->
                    <div x-data="deliveryForm(@js($delivery))" x-init="init()">
                        <x-delivery.form-edit :delivery="$delivery" :batches="$batches" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@if(app()->environment('local'))
<script>
    function fillTestData() {
        // This will be handled by the Alpine.js component
        window.dispatchEvent(new CustomEvent('fill-test-data'));
    }
</script>
@endif
