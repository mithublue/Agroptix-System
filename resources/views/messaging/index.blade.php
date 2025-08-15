<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Conversations') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @php
                        $isAdmin = auth()->user()->getRoleNames()->map(fn($r) => strtolower($r))->contains('admin');
                    @endphp
                    @if ($isAdmin)
                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-800 mb-2">Start a new conversation (Admin)</h3>
                            <form method="POST" action="{{ route('conversations.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @csrf
                                <div>
                                    <label class="block text-sm text-gray-700">Customer</label>
                                    <select id="customer_id" name="customer_id" placeholder="Search user..." class="mt-1 block w-full border-gray-300 rounded" required></select>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700">Supplier</label>
                                    <select id="supplier_id" name="supplier_id" placeholder="Search user..." class="mt-1 block w-full border-gray-300 rounded" required></select>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-sm text-gray-700">First message (optional)</label>
                                    <textarea name="body" rows="2" class="mt-1 block w-full border-gray-300 rounded"></textarea>
                                </div>
                                <div class="md:col-span-3">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">Create</button>
                                </div>
                            </form>
                        </div>
                        @push('styles')
                        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet" />
                        @endpush
                        @push('scripts')
                        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
                        <script>
                            (function(){
                                const usersUrl = @json(route('ajax.users'));
                                const baseTomSelect = {
                                    valueField: 'value',
                                    labelField: 'text',
                                    searchField: 'text',
                                    loadThrottle: 300,
                                    maxOptions: 50,
                                    preload: false,
                                    render: {
                                        option: function(item, escape) {
                                            return '<div>' + escape(item.text) + '</div>';
                                        },
                                        item: function(item, escape) {
                                            return '<div>' + escape(item.text) + '</div>';
                                        }
                                    },
                                    load: function(query, callback) {
                                        const url = usersUrl + (query ? ('?q=' + encodeURIComponent(query)) : '');
                                        fetch(url, {headers: {'X-Requested-With':'XMLHttpRequest'}})
                                            .then(res => res.json())
                                            .then(json => callback(json.data || []))
                                            .catch(() => callback());
                                    }
                                };
                                new TomSelect('#customer_id', baseTomSelect);
                                new TomSelect('#supplier_id', baseTomSelect);
                            })();
                        </script>
                        @endpush
                    @endif
                    @if($conversations->count() === 0)
                        <p class="text-gray-500">No conversations found.</p>
                    @else
                        <div class="divide-y">
                            @foreach($conversations as $conv)
                                <a href="{{ route('conversations.show', $conv) }}" class="block py-3 hover:bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-medium text-gray-900">
                                                {{ $conv->customer->name ?? 'Customer #'.$conv->customer_id }}
                                                <span class="text-gray-400">↔</span>
                                                {{ $conv->supplier->name ?? 'Supplier #'.$conv->supplier_id }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Last message: {{ optional($conv->last_message_at)->diffForHumans() ?? '—' }}
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-400">#{{ $conv->id }}</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-4">{{ $conversations->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
