<x-app-layout>
    <x-slot name="header">
        <div class="container mx-auto px-6 py-4" x-data>
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Monitoring Dashboard</h1>
                <div class="text-sm" x-text="$store.monitoring?.currentDate ?? ''"></div>
            </div>
        </div>
    </x-slot>
    <div x-data="dashboard()" x-init="init()" class="min-h-screen">
        <!-- Main Content -->
        <main class="container mx-auto px-6 py-8">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Dashboard Overview</h2>

                <!-- Dropdown Selector -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Source</label>
                    <select
                        x-model="selectedSource"
                        @change="onSourceChange()"
                        class="w-full md:w-1/2 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-900"
                    >
                        <option value="">-- Select a source --</option>
                        <template x-for="source in sources" :key="source.id">
                            <option :value="source.id" x-text="source.name"></option>
                        </template>
                    </select>
                </div>

                <!-- Details Section -->
                <div x-show="selectedSource" class="space-y-6">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-4 gap-6">
                        <!-- Type Card -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center space-x-4">
                                <div class="bg-blue-500 rounded-xl p-3">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Type</p>
                                    <p class="text-2xl font-bold text-gray-900" x-text="currentData.type"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Temperature Card -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center space-x-4">
                                <div class="bg-orange-500 rounded-xl p-3">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Temperature</p>
                                    <p class="text-2xl font-bold" :class="currentData.temperature > 30 ? 'text-red-600' : 'text-green-600'" x-text="currentData.temperature + '°C'"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Capacity Card -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center space-x-4">
                                <div class="bg-green-500 rounded-xl p-3">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Capacity</p>
                                    <p class="text-2xl font-bold text-gray-900" x-text="currentData.capacity"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Quantity Card -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center space-x-4">
                                <div class="bg-yellow-500 rounded-xl p-3">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Quantity Filled</p>
                                    <p class="text-2xl font-bold text-gray-900" x-text="currentData.quantity"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Card (for Vehicles) -->
                    <div x-show="currentData.type === 'Vehicle'" class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Location</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center space-x-3">
                                <span class="text-sm font-medium text-gray-600">Latitude:</span>
                                <span class="text-lg font-semibold text-gray-900" x-text="currentData.latitude"></span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm font-medium text-gray-600">Longitude:</span>
                                <span class="text-lg font-semibold text-gray-900" x-text="currentData.longitude"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Temperature Chart -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Temperature Over Time</h3>
                        <div class="relative h-80">
                            <canvas id="tempChart"></canvas>
                        </div>
                    </div>

                    <!-- Status Indicators -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Overview</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                                <div class="flex justify-center mb-2">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <p class="text-xs text-green-700 font-medium uppercase">Active</p>
                                <p class="text-2xl font-bold text-green-700" x-text="currentData.capacity > currentData.quantity ? '✓' : '-'"></p>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                                <div class="flex justify-center mb-2">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-xs text-blue-700 font-medium uppercase">Monitoring</p>
                                <p class="text-2xl font-bold text-blue-700">ON</p>
                            </div>
                            <div :class="currentData.temperature > 30 ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200'" class="border rounded-lg p-4 text-center">
                                <div class="flex justify-center mb-2">
                                    <svg class="w-6 h-6" :class="currentData.temperature > 30 ? 'text-red-600' : 'text-green-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-xs font-medium uppercase" :class="currentData.temperature > 30 ? 'text-red-700' : 'text-green-700'">Temp Status</p>
                                <p class="text-2xl font-bold" :class="currentData.temperature > 30 ? 'text-red-700' : 'text-green-700'" x-text="currentData.temperature > 30 ? 'HIGH' : 'OK'"></p>
                            </div>
                            <div :class="currentData.quantity >= currentData.capacity ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200'" class="border rounded-lg p-4 text-center">
                                <div class="flex justify-center mb-2">
                                    <svg class="w-6 h-6" :class="currentData.quantity >= currentData.capacity ? 'text-red-600' : 'text-yellow-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                <p class="text-xs font-medium uppercase" :class="currentData.quantity >= currentData.capacity ? 'text-red-700' : 'text-yellow-700'">Fill Level</p>
                                <p class="text-xl font-bold" :class="currentData.quantity >= currentData.capacity ? 'text-red-700' : 'text-yellow-700'" x-text="Math.round((currentData.quantity / currentData.capacity) * 100) + '%'"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    @push('scripts')
        <script>
            function dashboard() {
                return {
                    sources: [
                        { id: 1, name: 'Room 1', type: 'Room', capacity: 50, quantity: 32 },
                        { id: 2, name: 'Room 2', type: 'Room', capacity: 40, quantity: 28 },
                        { id: 3, name: 'Room 3', type: 'Room', capacity: 60, quantity: 45 },
                        { id: 4, name: 'Lab 1', type: 'Lab', capacity: 30, quantity: 15 },
                        { id: 5, name: 'Lab 2', type: 'Lab', capacity: 35, quantity: 20 },
                        { id: 6, name: 'Vehicle 1', type: 'Vehicle', capacity: 1000, quantity: 650, latitude: 40.7128, longitude: -74.0060 },
                        { id: 7, name: 'Vehicle 2', type: 'Vehicle', capacity: 1200, quantity: 800, latitude: 34.0522, longitude: -118.2437 },
                        { id: 8, name: 'Storage A', type: 'Storage', capacity: 500, quantity: 320 }
                    ],
                    selectedSource: '',
                    currentData: {
                        type: '',
                        temperature: 0,
                        capacity: 0,
                        quantity: 0,
                        latitude: 0,
                        longitude: 0
                    },
                    currentDate: '',
                    chart: null,
                    temperatureHistory: {},
                    tempUpdateInterval: null,
                    locationUpdateInterval: null,
                    currentSession: [],
                    chartSyncScheduled: false,

                    init() {
                        this.temperatureHistory = this.loadHistory();
                        this.updateDate();
                        setInterval(() => this.updateDate(), 1000);
                    },

                    loadHistory() {
                        try {
                            const stored = localStorage.getItem('temperatureHistory');
                            if (!stored) {
                                return {};
                            }

                            const parsed = JSON.parse(stored);
                            return typeof parsed === 'object' && parsed !== null ? parsed : {};
                        } catch (error) {
                            console.warn('Failed to parse temperature history', error);
                            return {};
                        }
                    },

                    saveHistory() {
                        localStorage.setItem('temperatureHistory', JSON.stringify(this.temperatureHistory));
                    },

                    randomTemperature() {
                        return Math.floor(Math.random() * 21) + 20;
                    },

                    ensureStore() {
                        if (window.Alpine?.store('monitoring')) {
                            Alpine.store('monitoring').currentDate = this.currentDate;
                        } else if (window.Alpine) {
                            Alpine.store('monitoring', {
                                currentDate: this.currentDate,
                            });
                        }
                    },

                    clearIntervals() {
                        if (this.tempUpdateInterval) {
                            clearInterval(this.tempUpdateInterval);
                            this.tempUpdateInterval = null;
                        }

                        if (this.locationUpdateInterval) {
                            clearInterval(this.locationUpdateInterval);
                            this.locationUpdateInterval = null;
                        }
                    },

                    resetCurrentData() {
                        this.currentData = {
                            type: '',
                            temperature: 0,
                            capacity: 0,
                            quantity: 0,
                            latitude: 0,
                            longitude: 0
                        };
                    },

                    updateDate() {
                        const now = new Date();
                        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                        this.currentDate = now.toLocaleDateString('en-US', options);
                        this.ensureStore();
                    },

                    onSourceChange() {
                        this.clearIntervals();

                        if (!this.selectedSource) {
                            this.currentSession = [];
                            this.resetCurrentData();

                            if (this.chart) {
                                this.chart.destroy();
                                this.chart = null;
                            }

                            this.scheduleChartSync();
                            return;
                        }

                        const source = this.sources.find((s) => String(s.id) === String(this.selectedSource));
                        if (!source) {
                            return;
                        }

                        const history = Array.isArray(this.temperatureHistory[this.selectedSource])
                            ? [...this.temperatureHistory[this.selectedSource]]
                            : [];

                        this.currentSession = history.slice(-30);

                        const lastReading = this.currentSession.length
                            ? this.currentSession[this.currentSession.length - 1]
                            : null;

                        const startingTemperature = lastReading ? lastReading.temperature : this.randomTemperature();

                        this.currentData = {
                            type: source.type,
                            temperature: startingTemperature,
                            capacity: source.capacity,
                            quantity: source.quantity,
                            latitude: source.latitude ?? 0,
                            longitude: source.longitude ?? 0
                        };

                        this.$nextTick(() => {
                            this.initChart();

                            if (this.currentSession.length) {
                                this.scheduleChartSync();
                            } else {
                                this.appendReading(startingTemperature, { skipChartSync: true });
                                this.scheduleChartSync();
                            }

                            this.startTemperatureUpdates();

                            if (source.type === 'Vehicle') {
                                this.startLocationUpdates();
                            }
                        });
                    },

                    initChart() {
                        const ctx = document.getElementById('tempChart');
                        if (!ctx) return;

                        if (this.chart) {
                            this.chart.destroy();
                            this.chart = null;
                            this.chartSyncScheduled = false;
                        }

                        const chartInstance = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: this.currentSession.map((reading) => reading.time),
                                datasets: [{
                                    label: 'Temperature (°C)',
                                    data: this.currentSession.map((reading) => reading.temperature),
                                    borderColor: 'rgb(99, 102, 241)',
                                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: false,
                                        min: 15,
                                        max: 45,
                                        ticks: {
                                            callback: function(value) {
                                                return value + '°C';
                                            }
                                        }
                                    },
                                    x: {
                                        display: true
                                    }
                                }
                            }
                        });

                        this.chart = Alpine?.raw ? Alpine.raw(chartInstance) : chartInstance;
                    },

                    syncChart() {
                        if (!this.chart) {
                            return;
                        }

                        this.chart.data.labels = this.currentSession.map((reading) => reading.time);
                        this.chart.data.datasets[0].data = this.currentSession.map((reading) => reading.temperature);
                        this.chart.update('none');
                    },

                    startTemperatureUpdates() {
                        this.tempUpdateInterval = setInterval(() => {
                            const newTemperature = this.randomTemperature();
                            this.currentData.temperature = newTemperature;
                            this.appendReading(newTemperature);
                        }, 2000);
                    },

                    startLocationUpdates() {
                        this.locationUpdateInterval = setInterval(() => {
                            const latChange = (Math.random() - 0.5) * 0.01;
                            const lonChange = (Math.random() - 0.5) * 0.01;

                            this.currentData.latitude = parseFloat((this.currentData.latitude + latChange).toFixed(4));
                            this.currentData.longitude = parseFloat((this.currentData.longitude + lonChange).toFixed(4));
                        }, 3000);
                    },

                    appendReading(temperature, options = {}) {
                        if (!this.selectedSource) {
                            return;
                        }

                        const { skipChartSync = false, timestamp = new Date() } = options;
                        const timeLabel = typeof timestamp === 'string'
                            ? timestamp
                            : timestamp.toLocaleTimeString();

                        const reading = {
                            time: timeLabel,
                            temperature
                        };

                        this.currentSession.push(reading);
                        if (this.currentSession.length > 30) {
                            this.currentSession.shift();
                        }

                        if (!Array.isArray(this.temperatureHistory[this.selectedSource])) {
                            this.temperatureHistory[this.selectedSource] = [];
                        }

                        this.temperatureHistory[this.selectedSource].push(reading);
                        if (this.temperatureHistory[this.selectedSource].length > 200) {
                            this.temperatureHistory[this.selectedSource].shift();
                        }

                        this.saveHistory();

                        if (!skipChartSync) {
                            this.scheduleChartSync();
                        }
                    },

                    scheduleChartSync() {
                        if (!this.chart || this.chartSyncScheduled) {
                            return;
                        }

                        this.chartSyncScheduled = true;
                        requestAnimationFrame(() => {
                            this.chartSyncScheduled = false;
                            this.syncChart();
                        });
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
