<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Users') }}
            </h2>
            @can('manage_users')
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Add New User
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                                    @can('manage_users')
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Activity Status</th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Approval Status</th>
                                    @endcan
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4f46e5&color=fff" alt="{{ $user->name }}">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($user->roles as $role)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        @can('manage_users')
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div x-data="{ isActive: {{ $user->is_active ? 'true' : 'false' }}, isLoading: false }">
                                                <button :class="isActive ? 'bg-green-500' : 'bg-gray-300'" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none" @click="
                                                    if (isLoading) return;
                                                    isLoading = true;
                                                    axios.post('{{ route('admin.users.status', $user) }}', {
                                                        field: 'is_active',
                                                        value: isActive ? 0 : 1
                                                    }, {
                                                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') }
                                                    }).then(res => {
                                                        isActive = !isActive;
                                                    }).catch(() => { alert('Failed to update status'); }).finally(() => { isLoading = false; });
                                                ">
                                                    <span :class="isActive ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                                                </button>
                                                <span class="ml-2 text-xs" x-text="isActive ? 'Active' : 'Inactive'"></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div x-data="{ isApproved: {{ is_null($user->is_approved) ? 'null' : ($user->is_approved ? 'true' : 'false') }}, isLoading: false }">
                                                <template x-if="isApproved === null">
                                                    <div class="flex items-center justify-center space-x-2">
                                                        <button class="bg-green-500 text-white px-3 py-1 rounded transition-colors" :disabled="isLoading" @click="
                                                            if (isLoading) return;
                                                            isLoading = true;
                                                            axios.post('{{ route('admin.users.status', $user) }}', {
                                                                field: 'is_approved',
                                                                value: 1
                                                            }, {
                                                                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') }
                                                            }).then(res => {
                                                                isApproved = true;
                                                            }).catch(() => { alert('Failed to approve'); }).finally(() => { isLoading = false; });
                                                        ">Approve</button>
                                                        <button class="bg-red-500 text-white px-3 py-1 rounded transition-colors" :disabled="isLoading" @click="
                                                            if (isLoading) return;
                                                            isLoading = true;
                                                            axios.post('{{ route('admin.users.status', $user) }}', {
                                                                field: 'is_approved',
                                                                value: 0
                                                            }, {
                                                                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') }
                                                            }).then(res => {
                                                                isApproved = false;
                                                            }).catch(() => { alert('Failed to reject'); }).finally(() => { isLoading = false; });
                                                        ">Reject</button>
                                                    </div>
                                                </template>
                                                <template x-if="isApproved === true">
                                                    <button :class="'bg-green-500'" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none" @click="
                                                        if (isLoading) return;
                                                        isLoading = true;
                                                        axios.post('{{ route('admin.users.status', $user) }}', {
                                                            field: 'is_approved',
                                                            value: 0
                                                        }, {
                                                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') }
                                                        }).then(res => {
                                                            isApproved = false;
                                                        }).catch(() => { alert('Failed to update approval'); }).finally(() => { isLoading = false; });
                                                    ">
                                                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform translate-x-6"></span>
                                                    </button>
                                                    <span class="ml-2 text-xs">Approved</span>
                                                </template>
                                                <template x-if="isApproved === false">
                                                    <button :class="'bg-red-500'" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none" @click="
                                                        if (isLoading) return;
                                                        isLoading = true;
                                                        axios.post('{{ route('admin.users.status', $user) }}', {
                                                            field: 'is_approved',
                                                            value: 1
                                                        }, {
                                                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') }
                                                        }).then(res => {
                                                            isApproved = true;
                                                        }).catch(() => { alert('Failed to update approval'); }).finally(() => { isLoading = false; });
                                                    ">
                                                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform translate-x-1"></span>
                                                    </button>
                                                    <span class="ml-2 text-xs">Rejected</span>
                                                </template>
                                                <template x-if="isApproved === null">
                                                    <span class="ml-2 text-xs">Pending</span>
                                                </template>
                                            </div>
                                        </td>
                                        @endcan
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
