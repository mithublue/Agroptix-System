<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Eco Process') }}
            </h2>
            <a href="{{ route('batches.eco-processes.index', $batch) }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Back to Eco Processes') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form id="ecoProcessForm" method="POST" action="{{ route('batches.eco-processes.update', [$batch, $ecoProcess]) }}" onsubmit="event.preventDefault(); submitEcoProcessForm(this);">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="stage" :value="__('Stage')" />
                            <x-text-input id="stage" class="block mt-1 w-full" type="text" name="stage" :value="old('stage', $ecoProcess->stage)" required />
                            <x-input-error :messages="$errors->get('stage')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach(['pending', 'in_progress', 'completed', 'failed'] as $status)
                                    <option value="{{ $status }}" {{ $ecoProcess->status === $status ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="start_time" :value="__('Start Time')" />
                            <x-text-input id="start_time" class="block mt-1 w-full" type="datetime-local"
                                        name="start_time"
                                        :value="old('start_time', optional($ecoProcess->start_time)->format('Y-m-d\TH:i'))"
                                        required />
                            <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="end_time" :value="__('End Time (Optional)')" />
                            <x-text-input id="end_time" class="block mt-1 w-full" type="datetime-local"
                                        name="end_time"
                                        :value="old('end_time', optional($ecoProcess->end_time)->format('Y-m-d\TH:i'))" />
                            <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('batches.show', $batch) }}" class="text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update') }}
                            </x-primary-button>
                        </div>
                    </form>

@push('scripts')
<script>
    function submitEcoProcessForm(form) {
        // Submit the form via fetch
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(new FormData(form))
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            // Show success toast
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            Toast.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Eco process has been updated successfully.'
            }).then(() => {
                // Redirect after toast is closed
                window.location.href = data.redirect || '{{ route("batches.eco-processes.index", $batch) }}';
            });
        })
        .catch(error => {
            // Show error toast
            console.error('Error:', error);
            const errorMessage = error.message || 'An error occurred while updating the eco process.';
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage,
                confirmButtonText: 'OK',
                confirmButtonColor: '#3b82f6'
            });
        });
    }
</script>
@endpush
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
