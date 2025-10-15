<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{ tab: '{{ request()->get('tab','general') }}' }">
            <!-- Tabs -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="#" @click.prevent="tab='general'"
                       :class="tab==='general' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                       class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        {{ __('General') }}
                    </a>
                    @if(!empty($showProductsTab))
                    <a href="#" @click.prevent="tab='products'"
                       :class="tab==='products' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                       class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        {{ __('Products') }}
                    </a>
                    @endif
                </nav>
            </div>

            <!-- General Tab Content -->
            <div x-show="tab==='general'" x-cloak class="space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

            @if(!empty($showProductsTab))
            <!-- Products Tab Content -->
            <div x-show="tab==='products'" x-cloak class="space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-2xl">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">{{ __('Manage Products') }}</h2>
                            <p class="mt-1 text-sm text-gray-600">{{ __('Select the products you supply or produce.') }}</p>
                        </header>

                        @if (session('status') === 'products-updated')
                            <div class="mt-4 rounded-md bg-green-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.364 7.364a1 1 0 01-1.414 0L3.293 10.434a1 1 0 011.414-1.414l3.05 3.05 6.657-6.657a1 1 0 011.293-.12z" clip-rule="evenodd"/></svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-800">{{ __('Products updated successfully.') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form method="post" action="{{ route('profile.products.update') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('patch')

                            <div>
                                <x-input-label for="products_select" :value="__('Products')" />
                                <select id="products_select" name="products[]" multiple class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}" {{ in_array($p->id, $userProductIds ?? []) ? 'selected' : '' }}>{{ $p->name }}</option>
                                    @endforeach
                                </select>
                                @error('products')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('products.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save') }}</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            @push('scripts')
            <script>
                (function() {
                    function initProfileProductsTS() {
                        const el = document.getElementById('products_select');
                        if (!el || el.dataset.tsInit==='1') return;
                        el.dataset.tsInit='1';
                        try {
                            new TomSelect(el, {
                                plugins: ['remove_button'],
                                maxOptions: 500,
                                searchField: 'text',
                                closeAfterSelect: false,
                            });
                        } catch (e) {
                            // no-op
                        }
                    }
                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', initProfileProductsTS);
                    } else {
                        initProfileProductsTS();
                    }
                    document.addEventListener('turbo:load', initProfileProductsTS);
                })();
            </script>
            @endpush
        </div>
    </div>
</x-app-layout>
