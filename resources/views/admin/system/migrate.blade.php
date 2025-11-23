<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>System Migration - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-4xl mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <h2 class="font-semibold text-2xl text-gray-800 mb-6 text-center">
                System Migration
            </h2>
                <!-- Warning Alert -->
                <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Warning:</strong> This action will run database migrations. Make sure you have a backup before proceeding.
                            </p>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('output'))
                    <div class="mb-4 bg-gray-100 border border-gray-300 rounded p-4">
                        <h3 class="font-semibold mb-2">Migration Output:</h3>
                        <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ session('output') }}</pre>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.system.migrate.run') }}" x-data="{ createAdmin: false }" onsubmit="return confirm('Are you sure you want to run database migrations? This action cannot be undone.');">
                    @csrf
                    
                    <!-- System Key -->
                    <div class="mb-6">
                        <label for="system_key" class="block font-medium text-gray-700 text-lg mb-2">
                            System Key <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="system_key" 
                               id="system_key" 
                               required
                               value="{{ old('system_key') }}"
                               class="w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('system_key') border-red-500 @enderror"
                               placeholder="Enter system key from .env file">
                        @error('system_key')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-sm mt-1">Enter the SYSTEM_KEY value from your .env file to proceed.</p>
                    </div>

                    <!-- Run Composer Install -->
                    <div class="mb-6 p-4 bg-purple-50 border border-purple-200 rounded-md">
                        <label class="flex items-center">
                            <input type="checkbox" name="run_composer" value="1" class="form-checkbox text-indigo-600">
                            <span class="ml-2 font-medium text-gray-700">Run Composer Install</span>
                        </label>
                        <p class="text-gray-600 text-sm mt-2 ml-6">
                            Install/update PHP dependencies before running migrations (recommended for fresh deployments)
                        </p>
                    </div>

                    <!-- Migration Type -->
                    <div class="mb-6">
                        <label class="block font-medium text-gray-700 text-lg mb-2">
                            Migration Type <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="migration_type" value="migrate" checked class="form-radio text-indigo-600">
                                <span class="ml-2">
                                    <strong>Migrate</strong> - Run pending migrations only (safe, recommended)
                                </span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="migration_type" value="refresh" class="form-radio text-indigo-600">
                                <span class="ml-2">
                                    <strong>Migrate Fresh</strong> - Drop all tables and re-run all migrations (⚠️ destructive)
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Run Seeders -->
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-md">
                        <label class="flex items-center">
                            <input type="checkbox" name="run_seeders" value="1" class="form-checkbox text-indigo-600">
                            <span class="ml-2 font-medium text-gray-700">Run Database Seeders</span>
                        </label>
                        <p class="text-gray-600 text-sm mt-2 ml-6">
                            Populate database with initial data (roles, permissions, test data, etc.)
                        </p>
                    </div>

                    <!-- Create Admin User -->
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                        <label class="flex items-center mb-3">
                            <input type="checkbox" name="create_admin" value="1" x-model="createAdmin" class="form-checkbox text-indigo-600">
                            <span class="ml-2 font-medium text-gray-700">Create Admin User</span>
                        </label>
                        
                        <div x-show="createAdmin" x-transition class="space-y-4 mt-4">
                            <div>
                                <label for="admin_email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Admin Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       name="admin_email" 
                                       id="admin_email" 
                                       value="{{ old('admin_email') }}"
                                       class="w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('admin_email') border-red-500 @enderror"
                                       placeholder="admin@example.com">
                                @error('admin_email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="admin_password" class="block text-sm font-medium text-gray-700 mb-1">
                                    Admin Password <span class="text-red-500">*</span>
                                </label>
                                <input type="password" 
                                       name="admin_password" 
                                       id="admin_password" 
                                       class="w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('admin_password') border-red-500 @enderror"
                                       placeholder="Minimum 8 characters">
                                @error('admin_password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-xs mt-1">Password must be at least 8 characters</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 text-sm">
                            Secured by system key authentication
                        </span>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded shadow">
                            Run Migrations
                        </button>
                    </div>
                </form>

                <!-- Information Section -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="font-semibold text-gray-800 mb-2">About This Feature</h3>
                    <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                        <li>This route allows running database migrations via web interface</li>
                        <li>Requires a valid system key configured in .env file (SYSTEM_KEY)</li>
                        <li>All migration attempts are logged for security</li>
                        <li>Useful for deployment automation and fresh installations</li>
                        <li>No authentication required - secured by system key only</li>
                    </ul>
                </div>
            </div>
        </div>
    </body>
</html>
