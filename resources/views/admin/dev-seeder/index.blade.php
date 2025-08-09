<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Development Seeder') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6" x-data="devSeeder()" x-init="init()">
                <p class="text-sm text-gray-600 mb-4">
                    This tool is only available in the local environment and allows admins to run specific seeders for development.
                </p>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Select Seeders to Run</label>
                    <div class="mt-2 border rounded p-3 max-h-64 overflow-auto">
                        <div class="flex items-center justify-between mb-2">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded" :checked="all" @change="toggleAll()">
                                <span class="text-sm font-medium text-gray-700">Select All</span>
                            </label>
                            <span class="text-xs text-gray-500" x-text="selected.length + ' selected'"></span>
                        </div>
                        <hr class="my-2">
                        <template x-if="seeders.length === 0">
                            <div class="text-gray-500 text-sm">No seeders found in database/seeders.</div>
                        </template>
                        <template x-for="(seeder, idx) in seeders" :key="seeder.class">
                            <label class="flex items-center space-x-2 py-1">
                                <input type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded" x-model="selected" :value="seeder.class">
                                <span x-text="seeder.name + ' (' + seeder.class + ')'" class="text-sm"></span>
                            </label>
                        </template>
                    </div>
                </div>

                <div class="flex items-center space-x-3 mb-6">
                    <button @click="runSelected" :disabled="isRunning || selected.length === 0" class="px-4 py-2 bg-blue-600 text-white rounded disabled:opacity-50">
                        Run Seeders
                    </button>
                    <button @click="refresh" :disabled="isRunning" class="px-4 py-2 bg-gray-100 text-gray-800 rounded border disabled:opacity-50">
                        Refresh
                    </button>
                    <button @click="clearLogs" :disabled="isRunning" class="px-4 py-2 bg-gray-100 text-gray-800 rounded border disabled:opacity-50">
                        Clear Logs
                    </button>
                </div>

                <!-- Progress Bar -->
                <div class="mb-4" x-show="isRunning">
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-blue-700">Progress</span>
                        <span class="text-sm font-medium text-blue-700" x-text="Math.round((progress/currentTotal)*100) + '%' "></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" :style="'width: ' + ((progress/currentTotal)*100) + '%'">
                        </div>
                    </div>
                </div>

                <!-- Log Output -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Logs</label>
                    <div class="mt-2 border rounded p-3 bg-gray-50 h-48 overflow-auto text-sm" x-html="logs"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function devSeeder() {
        return {
            seeders: [],
            selected: [],
            isRunning: false,
            progress: 0,
            currentTotal: 0,
            logs: '',
            all: false,

            async init() {
                await this.refresh();
            },

            async refresh() {
                try {
                    const res = await fetch('{{ route('admin.dev.seeder.list') }}');
                    const data = await res.json();
                    this.seeders = data.seeders || [];
                    // If Select All was checked, keep everything selected
                    if (this.all) {
                        this.selected = this.seeders.map(s => s.class);
                    }
                } catch (e) {
                    this.appendLog('Failed to load seeders: ' + (e?.message || e));
                }
            },

            appendLog(line) {
                const time = new Date().toLocaleTimeString();
                this.logs += `<div>[${time}] ${line}</div>`;
                this.$nextTick(() => {
                    const box = this.$root.querySelector('.bg-gray-50.h-48');
                    if (box) box.scrollTop = box.scrollHeight;
                });
            },

            async runSelected() {
                if (this.selected.length === 0) return;
                this.isRunning = true;
                this.progress = 0;
                this.currentTotal = this.selected.length;
                this.logs = '';

                for (const cls of this.selected) {
                    this.appendLog('Seeding: ' + cls + ' ...');
                    try {
                        const res = await fetch('{{ route('admin.dev.seeder.run') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ class: cls })
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.appendLog('Done: ' + (data.message || cls));
                        } else {
                            this.appendLog('Failed: ' + (data.message || 'Unknown error'));
                        }
                    } catch (e) {
                        this.appendLog('Error: ' + (e?.message || e));
                    }
                    this.progress++;
                }

                this.isRunning = false;
                this.appendLog('All selected seeders finished.');
            },

            toggleAll() {
                this.all = !this.all;
                if (this.all) {
                    this.selected = this.seeders.map(s => s.class);
                } else {
                    this.selected = [];
                }
            },

            clearLogs() {
                this.logs = '';
            }
        }
    }
    </script>
</x-app-layout>
