<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Sources Card -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Total Sources</h3>
                                <p class="text-3xl font-bold">{{ $totalSources }}</p>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Perishable</p>
                                <p class="text-lg font-semibold text-green-600">{{ $perishableSources }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Non-Perishable</p>
                                <p class="text-lg font-semibold text-blue-600">{{ $nonPerishableSources }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Card -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Total Products</h3>
                                <p class="text-3xl font-bold">{{ $totalProducts }}</p>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Perishable</p>
                                <p class="text-lg font-semibold text-green-600">{{ $perishableProducts }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Non-Perishable</p>
                                <p class="text-lg font-semibold text-blue-600">{{ $nonPerishableProducts }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Batches Card -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Total Batches</h3>
                                <p class="text-3xl font-bold">{{ $totalBatches }}</p>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-3 gap-2">
                            <div class="text-center">
                                <p class="text-xs text-gray-500">Processing</p>
                                <p class="text-lg font-semibold text-yellow-600">{{ $processingBatches }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500">Completed</p>
                                <p class="text-lg font-semibold text-green-600">{{ $completedBatches }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500">Pending</p>
                                <p class="text-lg font-semibold text-blue-600">{{ $pendingBatches }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional content can go here -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Welcome back!</h3>
                    <p class="text-gray-600">Here's an overview of your system's current status.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
