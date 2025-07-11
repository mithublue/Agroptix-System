<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Dashboard Overview') }}
            </h2>
            <span class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</span>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <!-- Sources Card -->
            <div class="relative group">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-400 to-cyan-400 rounded-2xl blur opacity-20 group-hover:opacity-40 transition duration-300"></div>
                <div class="relative bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-xl overflow-hidden transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 border border-gray-100">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-100 rounded-full opacity-20"></div>
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-6">
                            <div class="p-3 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-medium text-gray-500">Total Sources</span>
                                <h3 class="text-4xl font-extrabold text-gray-800">{{ $totalSources }}</h3>
                            </div>
                        </div>
                        <div class="relative z-10 grid grid-cols-2 gap-4 mt-8 pt-5 border-t border-gray-100">
                            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-xl text-center transform transition-all duration-300 hover:scale-105">
                                <div class="w-10 h-10 mx-auto mb-2 flex items-center justify-center bg-green-100 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Perishable</p>
                                <p class="text-xl font-extrabold text-green-700 mt-1">{{ $perishableSources }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-xl text-center transform transition-all duration-300 hover:scale-105">
                                <div class="w-10 h-10 mx-auto mb-2 flex items-center justify-center bg-blue-100 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Non-Perishable</p>
                                <p class="text-xl font-extrabold text-blue-700">{{ $nonPerishableSources }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Card -->
            <div class="relative group">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-green-400 to-emerald-400 rounded-2xl blur opacity-20 group-hover:opacity-40 transition duration-300"></div>
                <div class="relative bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-xl overflow-hidden transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 border border-gray-100">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-green-100 rounded-full opacity-20"></div>
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-6">
                            <div class="p-3 rounded-xl bg-gradient-to-br from-green-500 to-emerald-500 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-medium text-gray-500">Total Products</span>
                                <h3 class="text-4xl font-extrabold text-gray-800">{{ $totalProducts }}</h3>
                            </div>
                        </div>
                        <div class="relative z-10 grid grid-cols-2 gap-4 mt-8 pt-5 border-t border-gray-100">
                            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-xl text-center transform transition-all duration-300 hover:scale-105">
                                <div class="w-10 h-10 mx-auto mb-2 flex items-center justify-center bg-green-100 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Active</p>
                                <p class="text-xl font-extrabold text-green-700">{{ $activeProducts }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-4 rounded-xl text-center transform transition-all duration-300 hover:scale-105">
                                <div class="w-10 h-10 mx-auto mb-2 flex items-center justify-center bg-gray-100 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Inactive</p>
                                <p class="text-xl font-extrabold text-gray-600">{{ $inactiveProducts }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Batches Card -->
            <div class="relative group">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-amber-400 to-yellow-400 rounded-2xl blur opacity-20 group-hover:opacity-40 transition duration-300"></div>
                <div class="relative bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-xl overflow-hidden transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 border border-gray-100">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-yellow-100 rounded-full opacity-20"></div>
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-6">
                            <div class="p-3 rounded-xl bg-gradient-to-br from-amber-500 to-yellow-500 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-medium text-gray-500">Total Batches</span>
                                <h3 class="text-4xl font-extrabold text-gray-800">{{ $totalBatches }}</h3>
                            </div>
                        </div>
                        <div class="relative z-10 grid grid-cols-3 gap-3 mt-8 pt-5 border-t border-gray-100">
                            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-3 rounded-xl text-center transform transition-all duration-300 hover:scale-105">
                                <div class="w-9 h-9 mx-auto mb-2 flex items-center justify-center bg-yellow-100 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </div>
                                <p class="text-[11px] font-semibold text-yellow-600 uppercase tracking-wider">Processing</p>
                                <p class="text-lg font-extrabold text-yellow-700">{{ $processingBatches }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-green-50 to-green-100 p-3 rounded-xl text-center transform transition-all duration-300 hover:scale-105">
                                <div class="w-9 h-9 mx-auto mb-2 flex items-center justify-center bg-green-100 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="text-[11px] font-semibold text-green-600 uppercase tracking-wider">Completed</p>
                                <p class="text-lg font-extrabold text-green-700">{{ $completedBatches }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-3 rounded-xl text-center transform transition-all duration-300 hover:scale-105">
                                <div class="w-9 h-9 mx-auto mb-2 flex items-center justify-center bg-blue-100 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-[11px] font-semibold text-blue-600 uppercase tracking-wider">Pending</p>
                                <p class="text-lg font-extrabold text-blue-700">{{ $pendingBatches }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-xl shadow-lg overflow-hidden mb-8 border border-indigo-100">
            <div class="p-6 md:p-8">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="mb-6 md:mb-0 md:mr-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Welcome back!</h3>
                        <p class="text-gray-600 max-w-2xl">Here's an overview of your system's current status. You have {{ $totalBatches }} batches in progress, with {{ $processingBatches }} currently being processed.</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="p-4 bg-white rounded-lg shadow-inner border border-gray-200">
                            <div class="flex items-center">
                                <div class="p-2 bg-indigo-100 rounded-lg mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">System Status</p>
                                    <p class="text-lg font-bold text-indigo-600">All Systems Operational</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</x-app-layout>
