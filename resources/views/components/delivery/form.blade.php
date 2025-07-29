<form x-data="deliveryForm" id="delivery-form" method="POST" action="{{ isset($delivery) ? route('deliveries.update', $delivery) : route('deliveries.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if(isset($delivery))
        @method('PUT')
    @endif

    <!-- Batch Selection -->
    <div>
        <label for="batch_id" class="block text-sm font-medium text-gray-700">Batch</label>
        <select id="batch_id" name="batch_id" required
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            <option value="">Select a batch</option>
            @foreach($batches as $batch)
                <option value="{{ $batch->id }}" {{ (old('batch_id', $delivery->batch_id ?? '') == $batch->id) ? 'selected' : '' }}>
                    {{ $batch->batch_code ?? 'Batch #' . $batch->id }}
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
        <input type="datetime-local" id="delivery_date" name="delivery_date" required
               value="{{ old('delivery_date', isset($delivery) ? $delivery->delivery_date?->format('Y-m-d\TH:i') : '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        @error('delivery_date')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Delivery Person -->
    <div>
        <label for="delivery_person" class="block text-sm font-medium text-gray-700">Delivery Person</label>
        <input type="text" id="delivery_person" name="delivery_person" required
               value="{{ old('delivery_person', $delivery->delivery_person ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        @error('delivery_person')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Delivery Contact -->
    <div>
        <label for="delivery_contact" class="block text-sm font-medium text-gray-700">Contact Number</label>
        <input type="tel" id="delivery_contact" name="delivery_contact" required
               value="{{ old('delivery_contact', $delivery->delivery_contact ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        @error('delivery_contact')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Delivery Address -->
    <div>
        <label for="delivery_address" class="block text-sm font-medium text-gray-700">Delivery Address</label>
        <textarea id="delivery_address" name="delivery_address" rows="3" required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('delivery_address', $delivery->delivery_address ?? '') }}</textarea>
        @error('delivery_address')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Delivery Status -->
    <div>
        <label for="delivery_status" class="block text-sm font-medium text-gray-700">Status</label>
        <select id="delivery_status" name="delivery_status" required
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            <option value="pending" {{ old('delivery_status', $delivery->delivery_status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="in_transit" {{ old('delivery_status', $delivery->delivery_status ?? '') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
            <option value="delivered" {{ old('delivery_status', $delivery->delivery_status ?? '') == 'delivered' ? 'selected' : '' }}>Delivered</option>
            <option value="cancelled" {{ old('delivery_status', $delivery->delivery_status ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        @error('delivery_status')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Delivery Photos -->
    <div>
        <label for="delivery_photos" class="block text-sm font-medium text-gray-700">Delivery Photos</label>
        <input type="file" id="delivery_photos" name="delivery_photos[]" multiple
               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        @error('delivery_photos')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Notes -->
    <div>
        <label for="delivery_notes" class="block text-sm font-medium text-gray-700">Notes</label>
        <textarea id="delivery_notes" name="delivery_notes" rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('delivery_notes', $delivery->delivery_notes ?? '') }}</textarea>
        @error('delivery_notes')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end space-x-3 pt-4">
        <button type="button" @click="$dispatch('close-drawer')" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Cancel
        </button>
        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ isset($delivery) ? 'Update' : 'Create' }} Delivery
        </button>
    </div>
</form>
<script>
    function deliveryForm() {
        return {
        }
    }
</script>
