<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Delivery') }}
        </h2>
    </x-slot>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Create New Delivery</h2>
                    @if(app()->environment('local'))
                        <button type="button" id="fillTestData"
                                class="inline-flex items-center px-4 py-2 bg-yellow-100 border border-transparent rounded-md font-semibold text-xs text-yellow-800 uppercase tracking-widest hover:bg-yellow-200 focus:outline-none focus:border-yellow-300 focus:ring focus:ring-yellow-200 transition">
                            Fill with Test Data
                        </button>
                    @endif
                </div>

                <form action="{{ route('deliveries.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Batch Selection -->
                        <div class="col-span-2">
                            <label for="batch_id" class="block text-sm font-medium text-gray-700">Batch</label>
                            <select id="batch_id" name="batch_id" required
                                    class="mt-1 block w-full rounded-md @error('batch_id') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">
                                <option value="">Select a batch</option>
                                @foreach($batches as $batch)
                                    <option value="{{ $batch->id }}" @if(old('batch_id') == $batch->id) selected @endif>
                                        Batch #{{ $batch->id }} - {{ $batch->name ?? 'Unnamed Batch' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('batch_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Delivery Date -->
                        <div>
                            <label for="delivery_date" class="block text-sm font-medium text-gray-700">Delivery Date</label>
                            <input type="datetime-local" name="delivery_date" id="delivery_date" required
                                   value="{{ old('delivery_date') }}"
                                   class="mt-1 block w-full rounded-md @error('delivery_date') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">
                            @error('delivery_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Delivery Status -->
                        <div>
                            <label for="delivery_status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="delivery_status" name="delivery_status" required
                                    class="mt-1 block w-full rounded-md @error('delivery_status') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">
                                <option value="" disabled>Select status</option>
                                <option value="pending" @if(old('delivery_status') == 'pending') selected @endif>Pending</option>
                                <option value="in_transit" @if(old('delivery_status') == 'in_transit') selected @endif>In Transit</option>
                                <option value="delivered" @if(old('delivery_status') == 'delivered') selected @endif>Delivered</option>
                                <option value="failed" @if(old('delivery_status') == 'failed') selected @endif>Failed</option>
                            </select>
                            @error('delivery_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Delivery Person -->
                        <div>
                            <label for="delivery_person" class="block text-sm font-medium text-gray-700">Delivery Person</label>
                            <input type="text" name="delivery_person" id="delivery_person"
                                   value="{{ old('delivery_person') }}"
                                   class="mt-1 block w-full rounded-md @error('delivery_person') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">
                            @error('delivery_person')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Delivery Contact -->
                        <div>
                            <label for="delivery_contact" class="block text-sm font-medium text-gray-700">Contact Number</label>
                            <input type="text" name="delivery_contact" id="delivery_contact"
                                   value="{{ old('delivery_contact') }}"
                                   class="mt-1 block w-full rounded-md @error('delivery_contact') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">
                            @error('delivery_contact')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Delivery Address -->
                        <div class="col-span-2">
                            <label for="delivery_address" class="block text-sm font-medium text-gray-700">Delivery Address</label>
                            <textarea name="delivery_address" id="delivery_address" rows="3"
                                      class="mt-1 block w-full rounded-md @error('delivery_address') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">{{ old('delivery_address') }}</textarea>
                            @error('delivery_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Signature Recipient Name -->
                        <div class="col-span-2">
                            <label for="signature_recipient_name" class="block text-sm font-medium text-gray-700">Recipient Name (for signature)</label>
                            <input type="text" name="signature_recipient_name" id="signature_recipient_name"
                                   value="{{ old('signature_recipient_name') }}"
                                   class="mt-1 block w-full rounded-md @error('signature_recipient_name') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">
                            @error('signature_recipient_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Signature Data (hidden, will be filled by JS) -->
                        <input type="hidden" name="signature_data" id="signature_data">

                        <!-- Delivery Confirmation -->
                        <div class="flex items-center">
                            <input type="checkbox" name="delivery_confirmation" id="delivery_confirmation" value="1"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="delivery_confirmation" class="ml-2 block text-sm text-gray-700">
                                Delivery Confirmed
                            </label>
                        </div>

                        <!-- Temperature Check -->
                        <div class="flex items-center">
                            <input type="checkbox" name="temperature_check" id="temperature_check" value="1"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="temperature_check" class="ml-2 block text-sm text-gray-700">
                                Temperature Check Passed
                            </label>
                        </div>

                        <!-- Quality Check -->
                        <div class="flex items-center">
                            <input type="checkbox" name="quality_check" id="quality_check" value="1"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="quality_check" class="ml-2 block text-sm text-gray-700">
                                Quality Check Passed
                            </label>
                        </div>

                        <!-- Customer Rating -->
                        <div>
                            <label for="customer_rating" class="block text-sm font-medium text-gray-700">Customer Rating</label>
                            <select id="customer_rating" name="customer_rating"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select rating</option>
                                <option value="1">1 - Poor</option>
                                <option value="2">2 - Fair</option>
                                <option value="3">3 - Good</option>
                                <option value="4">4 - Very Good</option>
                                <option value="5">5 - Excellent</option>
                            </select>
                        </div>

                        <!-- Delivery Photos -->
                        <div class="col-span-2">
                            <label for="delivery_photos" class="block text-sm font-medium text-gray-700">Delivery Photos</label>
                            <input type="file" name="delivery_photos[]" id="delivery_photos" multiple
                                   class="mt-1 block w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-semibold
                                          @error('delivery_photos') file:bg-red-50 file:text-red-700 @else file:bg-indigo-50 file:text-indigo-700 @enderror
                                          hover:file:bg-indigo-100">
                            @error('delivery_photos')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-xs text-gray-500">Upload multiple photos if needed</p>
                            @enderror
                        </div>

                        <!-- Additional Notes -->
                        <div class="col-span-2">
                            <label for="additional_notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                            <textarea name="additional_notes" id="additional_notes" rows="3"
                                      class="mt-1 block w-full rounded-md @error('additional_notes') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">{{ old('additional_notes') }}</textarea>
                            @error('additional_notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Customer Comments -->
                        <div class="col-span-2">
                            <label for="customer_comments" class="block text-sm font-medium text-gray-700">Customer Comments</label>
                            <textarea name="customer_comments" id="customer_comments" rows="2"
                                      class="mt-1 block w-full rounded-md @error('customer_comments') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">{{ old('customer_comments') }}</textarea>
                            @error('customer_comments')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Customer Complaints -->
                        <div class="col-span-2">
                            <label for="customer_complaints" class="block text-sm font-medium text-gray-700">Customer Complaints</label>
                            <textarea name="customer_complaints" id="customer_complaints" rows="2"
                                      class="mt-1 block w-full rounded-md @error('customer_complaints') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">{{ old('customer_complaints') }}</textarea>
                            @error('customer_complaints')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Feedback Status -->
                        <div>
                            <label for="feedback_status" class="block text-sm font-medium text-gray-700">Feedback Status</label>
                            <select id="feedback_status" name="feedback_status"
                                    class="mt-1 block w-full rounded-md @error('feedback_status') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">
                                <option value="">Select status</option>
                                <option value="pending" @if(old('feedback_status') == 'pending') selected @endif>Pending</option>
                                <option value="submitted" @if(old('feedback_status') == 'acknowledged') selected @endif>Sumitted</option>
                                <option value="reviewed" @if(old('feedback_status') == 'resolved') selected @endif>Reviewed</option>
                                <option value="dismissed" @if(old('feedback_status') == 'dismissed') selected @endif>Dismissed</option>
                                <option value="resolved" @if(old('feedback_status') == 'dismissed') selected @endif>Resolved</option>
                            </select>
                            @error('feedback_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Feedback Submitted At -->
                        <div>
                            <label for="feedback_submitted_at" class="block text-sm font-medium text-gray-700">Feedback Date</label>
                            <input type="datetime-local" name="feedback_submitted_at" id="feedback_submitted_at"
                                   value="{{ old('feedback_submitted_at') }}"
                                   class="mt-1 block w-full rounded-md @error('feedback_submitted_at') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror">
                            @error('feedback_submitted_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3 pt-6">
                        <a href="{{ route('deliveries.index') }}"
                           class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save Delivery
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Function to fill form with test data
    function fillTestData() {
        // Set form values
        const now = new Date();
        const formattedDate = now.toISOString().slice(0, 16);
        const futureDate = new Date();
        futureDate.setDate(now.getDate() + 1);
        const futureFormattedDate = futureDate.toISOString().slice(0, 16);

        // Basic info
        document.getElementById('batch_id').value = '{{ $batches->first()->id ?? '1' }}';
        document.getElementById('delivery_date').value = formattedDate;
        document.getElementById('delivery_status').value = 'in_transit';
        document.getElementById('delivery_person').value = 'John Doe';
        document.getElementById('delivery_contact').value = '+1 (555) 123-4567';
        document.getElementById('delivery_address').value = '123 Main St, Anytown, AN 12345';

        // Signature and checks
        document.getElementById('signature_recipient_name').value = 'Jane Smith';
        document.getElementById('delivery_confirmation').checked = true;
        document.getElementById('temperature_check').checked = true;
        document.getElementById('quality_check').checked = true;

        // Customer feedback
        document.getElementById('customer_rating').value = '5';
        document.getElementById('additional_notes').value = 'Delivery was on time and in perfect condition.';
        document.getElementById('customer_comments').value = 'Great service, will order again!';
        document.getElementById('customer_complaints').value = 'None';
        document.getElementById('feedback_status').value = 'acknowledged';
        document.getElementById('feedback_submitted_at').value = futureFormattedDate;
    }

    // Add event listener for the test data button
    document.addEventListener('DOMContentLoaded', function() {
        const fillButton = document.getElementById('fillTestData');
        if (fillButton) {
            fillButton.addEventListener('click', fillTestData);
        }
        // Set default delivery date to now
        const now = new Date();
        // Format the date to YYYY-MM-DDTHH:MM
        const formattedDate = now.toISOString().slice(0, 16);
        document.getElementById('delivery_date').value = formattedDate;

        // Set feedback date to now if feedback status is being set
        const feedbackStatus = document.getElementById('feedback_status');
        if (feedbackStatus) {
            feedbackStatus.addEventListener('change', function() {
                if (this.value && !document.getElementById('feedback_submitted_at').value) {
                    document.getElementById('feedback_submitted_at').value = formattedDate;
                }
            });
        }
    });
</script>
@endpush
</x-app-layout>
