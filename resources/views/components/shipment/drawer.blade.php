@props(['show' => false, 'title' => 'Add New Shipment', 'formAction' => route('shipments.store'), 'method' => 'POST', 'shipment' => null])

<div x-data="{
    show: @js($show),
    close() {
        this.show = false;
        // Reset the URL without page reload
        window.history.pushState({}, '', '{{ route('shipments.index') }}');
    }
}"
     x-show="show"
     x-cloak
     x-on:keydown.escape.window="close()"
     class="fixed inset-0 overflow-hidden z-50"
     aria-labelledby="slide-over-title"
     role="dialog"
     aria-modal="true">

    <!-- Background overlay -->
    <div x-show="show"
         x-transition:enter="ease-in-out duration-500"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in-out duration-500"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
         aria-hidden="true"
         x-on:click="close()">
    </div>

    <div class="fixed inset-y-0 right-0 max-w-full flex">
        <div x-show="show"
             x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="w-screen max-w-2xl">

            <div class="h-full flex flex-col bg-white shadow-xl overflow-y-scroll">
                <div class="flex-1 overflow-y-auto py-6">
                    <!-- Header -->
                    <div class="px-4 sm:px-6 border-b border-gray-200 pb-4">
                        <div class="flex items-start justify-between">
                            <h2 class="text-lg font-medium text-gray-900" id="slide-over-title">
                                {{ $title }}
                            </h2>
                            <div class="ml-3 h-7 flex items-center">
                                <button type="button"
                                        x-on:click="close()"
                                        class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <span class="sr-only">Close panel</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <div class="mt-6 px-4 sm:px-6">
                        <form id="shipment-form"
                              action="{{ $formAction }}"
                              method="POST"
                              x-on:submit.prevent="
                                  fetch($el.action, {
                                      method: '{{ $method === 'PUT' ? 'PUT' : 'POST' }}',
                                      headers: {
                                          'Content-Type': 'application/json',
                                          'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content,
                                          'Accept': 'application/json',
                                          'X-Requested-With': 'XMLHttpRequest'
                                      },
                                      body: new FormData($el)
                                  })
                                  .then(response => response.json())
                                  .then(data => {
                                      if (data.redirect) {
                                          window.location.href = data.redirect;
                                      } else if (data.errors) {
                                          // Handle validation errors
                                          const errorMessages = Object.values(data.errors).flat();
                                          alert('Validation errors: ' + errorMessages.join('\n'));
                                      }
                                  })
                                  .catch(error => {
                                      console.error('Error:', error);
                                      alert('An error occurred. Please try again.');
                                  });
                              "
                              class="space-y-6">
                            @if($method === 'PUT')
                                @method('PUT')
                            @endif
                            @csrf

                            {{ $slot }}

                            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                                <button type="button"
                                        x-on:click="close()"
                                        class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    {{ $method === 'PUT' ? 'Update' : 'Create' }} Shipment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
