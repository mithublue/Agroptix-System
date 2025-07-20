<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Permissions') }}
        </h2>
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

                    <!-- Search Box -->
                    <div class="mb-6">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                id="permissionSearch" 
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                placeholder="Search permissions..."
                                onkeyup="filterPermissions()">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guard</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                                </tr>
                            </thead>
                            <tbody id="permissionsTableBody" class="bg-white divide-y divide-gray-200">
                                @foreach($permissions as $permission)
                                    <tr class="permission-row" data-name="{{ strtolower($permission->name) }}" data-guard="{{ strtolower($permission->guard_name) }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $permission->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $permission->guard_name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $permission->created_at->format('M d, Y') }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4" id="paginationContainer">
                        {{ $permissions->links() }}
                    </div>
                    
                    @push('scripts')
                    <script>
                        function filterPermissions() {
                            const searchTerm = document.getElementById('permissionSearch').value.toLowerCase();
                            const rows = document.querySelectorAll('.permission-row');
                            let visibleRows = 0;
                            
                            rows.forEach(row => {
                                const name = row.getAttribute('data-name');
                                const guard = row.getAttribute('data-guard');
                                
                                if (name.includes(searchTerm) || guard.includes(searchTerm)) {
                                    row.style.display = '';
                                    visibleRows++;
                                } else {
                                    row.style.display = 'none';
                                }
                            });
                            
                            // Show/hide pagination based on search
                            const paginationContainer = document.getElementById('paginationContainer');
                            if (searchTerm.length > 0) {
                                paginationContainer.style.display = 'none';
                            } else {
                                paginationContainer.style.display = 'block';
                            }
                            
                            // Show no results message if no rows are visible
                            const noResults = document.getElementById('noResults');
                            if (visibleRows === 0) {
                                if (!noResults) {
                                    const tbody = document.getElementById('permissionsTableBody');
                                    const tr = document.createElement('tr');
                                    tr.id = 'noResults';
                                    tr.innerHTML = `
                                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            No permissions found matching your search.
                                        </td>
                                    `;
                                    tbody.appendChild(tr);
                                }
                            } else if (noResults) {
                                noResults.remove();
                            }
                        }
                        
                        // Clear search when clicking the 'x' in the search input (for browsers that support it)
                        document.getElementById('permissionSearch').addEventListener('search', function() {
                            if (this.value === '') {
                                filterPermissions();
                            }
                        });
                    </script>
                    @endpush
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
