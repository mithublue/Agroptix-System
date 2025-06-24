<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Agroptix') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 h-full" x-data="{ sidebarOpen: window.innerWidth > 1024, mobileSidebarOpen: false }">
    <div class="flex h-full">
        <!-- Sidebar Backdrop -->
        <div x-show="mobileSidebarOpen" @click="mobileSidebarOpen = false"
             class="fixed inset-0 z-20 bg-gray-900 bg-opacity-50 lg:hidden"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
        </div>

        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-30 w-64 transform bg-white shadow-lg lg:translate-x-0 lg:static lg:inset-0 transition-transform duration-300 ease-in-out"
             :class="{'-translate-x-full': !mobileSidebarOpen}">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-center h-16 px-4 bg-indigo-600">
                    <span class="text-xl font-semibold text-white">Agroptix</span>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md group {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>

                    <!-- Sources -->
                    <div x-data="{ open: {{ request()->is('sources*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2 text-sm font-medium text-left rounded-md hover:bg-gray-100 {{ request()->is('sources*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-700' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 {{ request()->is('sources*') ? 'text-indigo-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Sources
                            </div>
                            <svg :class="{'transform rotate-180': open}" class="w-4 h-4 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" class="mt-1 space-y-1" x-collapse>
                            <a href="{{ route('sources.index') }}" class="flex items-center px-4 py-2 pl-11 text-sm rounded-md {{ request()->routeIs('sources.index') ? 'bg-gray-100 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
                                View All
                            </a>
                            <a href="{{ route('sources.create') }}" class="flex items-center px-4 py-2 pl-11 text-sm rounded-md {{ request()->routeIs('sources.create') ? 'bg-gray-100 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
                                Add Source
                            </a>
                        </div>
                    </div>

                    <!-- Products -->
                    <div x-data="{ open: {{ request()->is('products*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2 text-sm font-medium text-left rounded-md hover:bg-gray-100 {{ request()->is('products*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-700' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4"></path>
                                </svg>
                                Products
                            </div>
                            <svg :class="{'transform rotate-180': open}" class="w-4 h-4 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" class="mt-1 space-y-1" x-collapse>
                            <a href="{{ route('products.index') }}" class="flex items-center px-4 py-2 pl-11 text-sm rounded-md {{ request()->routeIs('products.index') ? 'bg-gray-100 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
                                View All
                            </a>
                            <a href="{{ route('products.create') }}" class="flex items-center px-4 py-2 pl-11 text-sm rounded-md {{ request()->routeIs('products.create') ? 'bg-gray-100 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
                                Add Product
                            </a>
                        </div>
                    </div>

                    <!-- Batches -->
                    <div x-data="{ open: {{ request()->is('batches*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2 text-sm font-medium text-left rounded-md hover:bg-gray-100 {{ request()->is('batches*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-700' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Batches
                            </div>
                            <svg :class="{'transform rotate-180': open}" class="w-4 h-4 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" class="mt-1 space-y-1" x-collapse>
                            <a href="{{ route('batches.index') }}" class="flex items-center px-4 py-2 pl-11 text-sm rounded-md {{ request()->routeIs('batches.index') ? 'bg-gray-100 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
                                View All
                            </a>
                            <a href="{{ route('batches.create') }}" class="flex items-center px-4 py-2 pl-11 text-sm rounded-md {{ request()->routeIs('batches.create') ? 'bg-gray-100 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
                                Add Batch
                            </a>
                        </div>
                    </div>

                    <!-- Quality Tests -->
                    <div x-data="{ open: {{ request()->is('quality-tests*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2 text-sm font-medium text-left rounded-md hover:bg-gray-100 {{ request()->is('quality-tests*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-700' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 {{ request()->is('quality-tests*') ? 'text-indigo-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                Quality Tests
                            </div>
                            <svg :class="{'transform rotate-180': open}" class="w-4 h-4 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" class="mt-1 space-y-1" x-collapse>
                            <a href="{{ route('quality-tests.index') }}" class="flex items-center px-4 py-2 pl-11 text-sm rounded-md {{ request()->routeIs('quality-tests.index') ? 'bg-gray-100 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
                                View All
                            </a>
                            <a href="{{ route('quality-tests.create') }}" class="flex items-center px-4 py-2 pl-11 text-sm rounded-md {{ request()->routeIs('quality-tests.create') ? 'bg-gray-100 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
                                Add Quality Test
                            </a>
                        </div>
                    </div>

                    <!-- Shipments -->
                    <!-- Shipments -->
                    <div x-data="{ open: {{ request()->is('shipments*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2 text-sm font-medium text-left rounded-md hover:bg-gray-100 {{ request()->is('shipments*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-700' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 {{ request()->is('shipments*') ? 'text-indigo-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Shipments
                            </div>
                            <svg :class="{'transform rotate-180': open}" class="w-4 h-4 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" class="mt-1 space-y-1" x-collapse>
                            <a href="{{ route('shipments.index') }}" class="flex items-center px-4 py-2 pl-11 text-sm rounded-md {{ request()->routeIs('shipments.index') ? 'bg-gray-100 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
                                View All
                            </a>
                            <a href="{{ route('shipments.create') }}" class="flex items-center px-4 py-2 pl-11 text-sm rounded-md {{ request()->routeIs('shipments.create') ? 'bg-gray-100 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
                                Add Shipment
                            </a>
                        </div>
                    </div>

                    <!-- Admin Section -->
                    @can('create_source')
                    <div class="pt-4 mt-4 border-t border-gray-200">
                        <h3 class="px-4 text-xs font-semibold tracking-wider text-gray-500 uppercase">Administration</h3>

                        <!-- Users -->
                        <div x-data="{ open: {{ request()->is('admin/users*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2 mt-2 text-sm font-medium text-left rounded-md hover:bg-gray-100 {{ request()->is('admin/users*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-700' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 {{ request()->is('admin/users*') ? 'text-indigo-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    Users
                                </div>
                                <svg :class="{'transform rotate-180': open}" class="w-4 h-4 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" class="mt-1 space-y-1" x-collapse>
                                <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-2 pl-11 text-sm rounded-md {{ request()->routeIs('admin.users.index') ? 'bg-gray-100 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
                                    All Users
                                </a>
                                <a href="{{ route('admin.users.create') }}" class="flex items-center px-4 py-2 pl-11 text-sm rounded-md {{ request()->routeIs('admin.users.create') ? 'bg-gray-100 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
                                    Add User
                                </a>
                            </div>
                        </div>

                        <!-- Roles -->
                        @can('manage_roles')
                        <div x-data="{ open: {{ request()->is('admin/roles*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2 mt-1 text-sm font-medium text-left rounded-md hover:bg-gray-100 {{ request()->is('admin/roles*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-700' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 {{ request()->is('admin/roles*') ? 'text-indigo-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    Roles
                                </div>
                                <svg :class="{'transform rotate-180': open}" class="w-4 h-4 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" class="mt-1 space-y-1" x-collapse>
                                <a href="{{ route('admin.roles.index') }}" class="flex items-center px-4 py-2 pl-11 text-sm rounded-md {{ request()->routeIs('admin.roles.index') ? 'bg-gray-100 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
                                    All Roles
                                </a>
                                <a href="{{ route('admin.roles.create') }}" class="flex items-center px-4 py-2 pl-11 text-sm rounded-md {{ request()->routeIs('admin.roles.create') ? 'bg-gray-100 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
                                    Create Role
                                </a>
                            </div>
                        </div>
                        @endcan

                        <!-- Permissions -->
                        @can('manage_permissions')
                        <div x-data="{ open: {{ request()->is('admin/permissions*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2 mt-1 text-sm font-medium text-left rounded-md hover:bg-gray-100 {{ request()->is('admin/permissions*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-700' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 {{ request()->is('admin/permissions*') ? 'text-indigo-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    Permissions
                                </div>
                                <svg :class="{'transform rotate-180': open}" class="w-4 h-4 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" class="mt-1 space-y-1" x-collapse>
                                <a href="{{ route('admin.permissions.index') }}" class="flex items-center px-4 py-2 pl-11 text-sm rounded-md {{ request()->routeIs('admin.permissions.index') ? 'bg-gray-100 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
                                    All Permissions
                                </a>
                            </div>
                        </div>
                        @endcan
                    </div>
                    @endcan
                </nav>

                <!-- User Profile -->
                <div class="p-4 border-t border-gray-200">
                    <div class="flex items-center">
                        <img class="w-8 h-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=4f46e5&color=fff" alt="{{ auth()->user()->name }}">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</p>
                            <a href="{{ route('profile.edit') }}" class="text-xs text-gray-500 hover:text-gray-700">View profile</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Mobile header -->
            <header class="flex items-center justify-between px-4 py-3 bg-white shadow-sm lg:hidden">
                <button @click="mobileSidebarOpen = true" class="p-2 text-gray-500 rounded-md hover:text-gray-600 hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div class="text-xl font-semibold text-gray-800">
                    {{ config('app.name') }}
                </div>
                <div class="w-6"></div> <!-- Spacer for alignment -->
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Alpine.js for sidebar functionality -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('sidebar', () => ({
                open: false,
                toggle() {
                    this.open = !this.open;
                }
            }));
        });
    </script>
@turboScripts
</body>
</html>
