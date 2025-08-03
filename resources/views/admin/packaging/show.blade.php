<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Packaging Details') }}
        </h2>
    </x-slot>

    <div class="py-6">
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Packaging Details
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            Detailed information about the packaging record
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.packaging.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Back to List
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                    <!-- Basic Information -->
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">ID</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $packaging->id }}</dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Batch</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($packaging->batch)
                                {{ $packaging->batch->batch_code }} (ID: {{ $packaging->batch_id }})
                            @else
                                <span class="text-gray-400">No batch assigned</span>
                            @endif
                        </dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Package Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $packaging->package_type ?? 'N/A' }}</dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Material Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $packaging->material_type ?? 'N/A' }}</dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Unit Packaging Weight (Package Weight)</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $packaging->unit_weight_packaging ? number_format($packaging->unit_weight_packaging, 3) . ' kg' : 'N/A' }}</dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Total Product Weight</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $packaging->total_product_weight ? number_format($packaging->total_product_weight, 3) . ' kg' : 'N/A' }}</dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Total Package Weight</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $packaging->total_package_weight ? number_format($packaging->total_package_weight, 3) . ' kg' : 'N/A' }}</dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Quantity of Units</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $packaging->quantity_of_units ?? 'N/A' }}</dd>
                    </div>

                    <!-- Timestamps -->
                    <div class="sm:col-span-2 border-t border-gray-200 pt-5">
                        <h4 class="text-sm font-medium text-gray-500 mb-4">Timestamps</h4>
                        <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Packaging Start Time</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $packaging->packaging_start_time ? $packaging->packaging_start_time->format('M d, Y H:i') : 'N/A' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Packaging End Time</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $packaging->packaging_end_time ? $packaging->packaging_end_time->format('M d, Y H:i') : 'N/A' }}
                                </dd>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="sm:col-span-2 border-t border-gray-200 pt-5">
                        <h4 class="text-sm font-medium text-gray-500 mb-4">Additional Information</h4>
                        <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Packager</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($packaging->packer)
                                        {{ $packaging->packer->name }}
                                    @else
                                        N/A
                                    @endif
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Packaging Location</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $packaging->packaging_location ?? 'N/A' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Cleanliness Checklist</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($packaging->cleanliness_checklist === 1)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @endif
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">CO2 Estimate</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $packaging->co2_estimate ? number_format($packaging->co2_estimate, 3) . ' kg CO₂e' : 'N/A' }}
                                </dd>
                            </div>

                            @if($packaging->qr_code)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">QR Code</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $packaging->qr_code }}</dd>
                                <div class="mt-2 p-2 border rounded-md bg-gray-50 inline-block">
                                    {!! QrCode::size(150)->generate($packaging->qr_code) !!}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </dl>
            </div>

            <div class="px-4 py-4 bg-gray-50 sm:px-6 flex justify-between">
                <div class="text-sm text-gray-500">
                    Created {{ $packaging->created_at->diffForHumans() }}
                    @if($packaging->created_at != $packaging->updated_at)
                        <br>
                        Updated {{ $packaging->updated_at->diffForHumans() }}
                    @endif
                </div>

                @can('delete_packaging')
                <form action="{{ route('admin.packaging.destroy', $packaging) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this packaging record? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Delete Packaging
                    </button>
                </form>
                @endcan
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
