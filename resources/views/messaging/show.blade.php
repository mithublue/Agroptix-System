<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Conversation') }} #{{ $conversation->id }}
        </h2>
        <div class="text-sm text-gray-600">
            {{ $conversation->customer->name ?? ('Customer #'.$conversation->customer_id) }}
            <span class="text-gray-400">↔</span>
            {{ $conversation->supplier->name ?? ('Supplier #'.$conversation->supplier_id) }}
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="p-3 bg-red-100 text-red-800 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="space-y-4">
                        @forelse ($messages as $message)
                            <div class="border rounded p-3">
                                <div class="flex items-center justify-between">
                                    <div class="font-medium text-gray-900">
                                        @php
                                            $displayUser = $message->sentAs ?: $message->author;
                                        @endphp
                                        {{ $displayUser?->name ?? 'User #'.($displayUser?->id ?? '—') }}
                                        <span class="text-xs text-gray-500">({{ $message->author?->name ?? 'User #'.$message->author_id }})</span>
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $message->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="mt-2 prose max-w-none">{!! $message->body !!}</div>
                                @if (!empty($message->attachments))
                                    <div class="mt-3 space-y-2">
                                        <div class="text-xs text-gray-500">Attachments:</div>
                                        <ul class="list-disc list-inside text-sm">
                                            @foreach($message->attachments as $att)
                                                <li>
                                                    @php $mime = $att['mime'] ?? ''; @endphp
                                                    @if (str_starts_with($mime, 'image/'))
                                                        <a href="{{ $att['url'] ?? '#' }}" target="_blank" class="inline-flex items-center space-x-2">
                                                            <img src="{{ $att['url'] ?? '' }}" alt="attachment" class="w-16 h-16 object-cover rounded border">
                                                            <span>{{ $att['original_name'] ?? 'image' }}</span>
                                                        </a>
                                                    @else
                                                        <a href="{{ $att['url'] ?? '#' }}" target="_blank" class="text-indigo-600 hover:underline">
                                                            {{ $att['original_name'] ?? ($att['url'] ?? 'file') }}
                                                        </a>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500">No messages yet.</p>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $messages->withQueryString()->links() }}
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('conversations.messages.store', $conversation) }}" class="space-y-4" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Message</label>
                            <div id="editor" class="mt-1 border border-gray-300 rounded-md"></div>
                            <input type="hidden" id="body" name="body" value="{{ old('body') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Attachments</label>
                            <input type="file" name="attachments[]" multiple class="mt-1 block w-full border-gray-300 rounded-md">
                            <p class="text-xs text-gray-500 mt-1">Up to 10 MB per file. Images, PDF, MP4/WEBM, and DOC/DOCX supported.</p>
                        </div>
                        @php
                            $isAdmin = auth()->user()->getRoleNames()->map(fn($r) => strtolower($r))->contains('admin');
                        @endphp
                        @if ($isAdmin)
                            <div class="flex items-center">
                                <input id="send_as_supplier" name="send_as_supplier" type="checkbox" value="1" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                <label for="send_as_supplier" class="ml-2 block text-sm text-gray-700">Send as supplier (impersonate)</label>
                            </div>
                        @endif
                        <div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                                onclick="document.getElementById('body').value = window.quill ? window.quill.root.innerHTML : document.getElementById('body').value;">
                                Send
                            </button>
                            <a href="{{ route('conversations.index') }}" class="ml-3 text-sm text-gray-600 hover:text-gray-900">Back to conversations</a>
                        </div>
                    </form>
                </div>
            </div>
            @push('styles')
            <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet" />
            @endpush
            @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function(){
                    const quill = new Quill('#editor', {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'header': 1 }, { 'header': 2 }],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                [{ 'indent': '-1'}, { 'indent': '+1' }],
                                [{ 'color': [] }, { 'background': [] }],
                                ['link']
                            ]
                        }
                    });
                    window.quill = quill;
                    const oldBody = document.getElementById('body').value;
                    if (oldBody) quill.root.innerHTML = oldBody;
                });
            </script>
            @endpush
        </div>
    </div>
</x-app-layout>
