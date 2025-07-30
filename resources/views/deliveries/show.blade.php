<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Delivery Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('deliveries.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    &larr; Back to Deliveries
                </a>
                @can('edit_deliveries')
                    <a href="{{ route('deliveries.edit', $delivery) }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Edit Delivery
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold">Delivery #{{ $delivery->id }}</h2>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($delivery->delivery_status == 'delivered') bg-green-100 text-green-800
                                @elseif($delivery->delivery_status == 'in_transit') bg-blue-100 text-blue-800
                                @elseif($delivery->delivery_status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($delivery->delivery_status == 'failed') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $delivery->delivery_status)) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Basic Information -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Batch</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            @if($delivery->batch)
                                                <a href="{{ route('batches.show', $delivery->batch) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    Batch #{{ $delivery->batch->id }} - {{ $delivery->batch->name ?? 'Unnamed Batch' }}
                                                </a>
                                            @else
                                                <span class="text-gray-500">No batch assigned</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Delivery Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $delivery->delivery_date ? $delivery->delivery_date->format('M d, Y \a\t g:i A') : 'Not set' }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Delivery Person</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $delivery->delivery_person ?? 'Not specified' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Contact Number</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $delivery->delivery_contact ?? 'Not provided' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Delivery Address</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $delivery->delivery_address ?? 'Not specified' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Signature & Confirmation -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Signature & Confirmation</h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Recipient Name</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $delivery->signature_recipient_name ?? 'Not provided' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Signature Data</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $delivery->signature_data ?? 'No signature captured' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($delivery->delivery_confirmation) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                                {{ $delivery->delivery_confirmation ? 'Confirmed' : 'Not Confirmed' }}
                                            </span>
                                            <span class="ml-2 text-sm text-gray-500">Delivery</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($delivery->temperature_check) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                                {{ $delivery->temperature_check ? 'Passed' : 'Not Checked' }}
                                            </span>
                                            <span class="ml-2 text-sm text-gray-500">Temperature</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($delivery->quality_check) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                                {{ $delivery->quality_check ? 'Passed' : 'Not Checked' }}
                                            </span>
                                            <span class="ml-2 text-sm text-gray-500">Quality</span>
                                        </div>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Notes & Photos -->
                        <div class="space-y-6">
                            <!-- Notes -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Notes</h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Delivery Notes</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $delivery->delivery_notes ?? 'No delivery notes provided' }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Additional Notes</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $delivery->additional_notes ?? 'No additional notes provided' }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Delivery Photos -->
                            @if($delivery->delivery_photos && count($delivery->delivery_photos) > 0)
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Delivery Photos</h3>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        @foreach($delivery->delivery_photos as $photo)
                                            <div class="relative group">
                                                <img src="{{ Storage::url($photo) }}" alt="Delivery Photo" 
                                                     class="w-full h-32 object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity"
                                                     onclick="openImageModal('{{ Storage::url($photo) }}')">
                                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center">
                                                    <svg class="h-8 w-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Delivery Photos</h3>
                                    <p class="text-sm text-gray-500">No delivery photos uploaded</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Customer Feedback Section -->
                    @if($delivery->customer_rating || $delivery->customer_comments || $delivery->customer_complaints)
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Feedback</h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    @if($delivery->customer_rating)
                                        <div class="mb-4">
                                            <dt class="text-sm font-medium text-gray-500">Rating</dt>
                                            <dd class="mt-1 flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="h-5 w-5 {{ $i <= $delivery->customer_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                                <span class="ml-2 text-sm text-gray-600">({{ $delivery->customer_rating }}/5)</span>
                                            </dd>
                                        </div>
                                    @endif
                                    @if($delivery->customer_comments)
                                        <div class="mb-4">
                                            <dt class="text-sm font-medium text-gray-500">Comments</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $delivery->customer_comments }}</dd>
                                        </div>
                                    @endif
                                    @if($delivery->customer_complaints)
                                        <div class="mb-4">
                                            <dt class="text-sm font-medium text-gray-500">Complaints</dt>
                                            <dd class="mt-1 text-sm text-red-600">{{ $delivery->customer_complaints }}</dd>
                                        </div>
                                    @endif
                                </div>
                                @if($delivery->feedback_photos && count($delivery->feedback_photos) > 0)
                                    <div>
                                        <h4 class="text-md font-medium text-gray-900 mb-2">Feedback Photos</h4>
                                        <div class="grid grid-cols-2 gap-2">
                                            @foreach($delivery->feedback_photos as $photo)
                                                <img src="{{ Storage::url($photo) }}" alt="Feedback Photo" 
                                                     class="w-full h-24 object-cover rounded cursor-pointer hover:opacity-75"
                                                     onclick="openImageModal('{{ Storage::url($photo) }}')">
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if($delivery->feedback_submitted_at)
                                <p class="mt-4 text-xs text-gray-500">
                                    Feedback submitted on {{ $delivery->feedback_submitted_at->format('M d, Y \a\t g:i A') }}
                                </p>
                            @endif
                        </div>
                    @endif

                    <!-- Admin Notes -->
                    @if($delivery->admin_notes)
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Admin Notes</h3>
                            <p class="text-sm text-gray-900">{{ $delivery->admin_notes }}</p>
                        </div>
                    @endif

                    <!-- Timestamps -->
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Record Information</h3>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $delivery->created_at->format('M d, Y \a\t g:i A') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $delivery->updated_at->format('M d, Y \a\t g:i A') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center" onclick="closeImageModal()">
        <div class="max-w-4xl max-h-full p-4">
            <img id="modalImage" src="" alt="Full size image" class="max-w-full max-h-full object-contain">
        </div>
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300">
            &times;
        </button>
    </div>

    <script>
        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
</x-app-layout>
