<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Eco Process') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('batches.eco-processes.update', [$batch, $ecoProcess]) }}">
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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
