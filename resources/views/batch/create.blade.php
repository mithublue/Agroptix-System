<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Batch') }}
            </h2>
            <a href="{{ route('batches.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Back to Batches') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('batches.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Batch Code -->
                            <div>
                                <x-label for="batch_code" :value="__('Batch Code')" />
                                <x-input id="batch_code" name="batch_code" type="text" class="mt-1 block w-full"
                                    :value="old('batch_code')" />

                            </div>

                            <!-- Producer -->
                            <div>
                                <x-label for="producer_id" :value="__('Producer')" required />
                                <select id="producer_id" name="producer_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @if(old('producer_id'))
                                        <option value="{{ old('producer_id') }}" selected>Loading...</option>
                                    @else
                                        <option value="">-- Select Producer --</option>
                                    @endif
                                </select>
                                <x-input-error :messages="$errors->get('producer_id')" class="mt-2" />
                                <p id="producer_help" class="mt-1 text-xs text-gray-500"></p>
                            </div>

                            <!-- Source -->
                            <div>
                                <x-label for="source_id" :value="__('Source')" required />
                                <select id="source_id" name="source_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- Select Source --</option>
                                    @foreach($sources as $id => $source)
                                        <option value="{{ $id }}" {{ old('source_id') == $id ? 'selected' : '' }}>{{ $source }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('source_id')" class="mt-2" />
                            </div>

                            <!-- Product -->
                            <div>
                                <x-label for="product_id" :value="__('Product')" required />
                                <select id="product_id" name="product_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- Select Product --</option>
                                    @foreach($products as $id => $product)
                                        <option value="{{ $id }}" {{ old('product_id') == $id ? 'selected' : '' }}>{{ $product }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                            </div>

                            <!-- Harvest Time with Custom Datepicker -->
                            <div>
                                <x-label for="harvest_time" :value="__('Harvest Time')" required />
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input type="date" 
                                           id="harvest_time" 
                                           name="harvest_time" 
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                           value="{{ old('harvest_time') ? \Carbon\Carbon::parse(old('harvest_time'))->format('Y-m-d') : '' }}"
                                           required
                                           max="{{ now()->format('Y-m-d') }}">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div id="harvest_time_calendar" class="datepicker-calendar hidden absolute z-10 mt-1 w-64 bg-white rounded-md shadow-lg p-4 border border-gray-200">
                                    <div class="flex justify-between items-center mb-4">
                                        <button type="button" class="prev-month p-1 rounded-full hover:bg-gray-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                        </button>
                                        <h3 class="month-year text-lg font-medium"></h3>
                                        <button type="button" class="next-month p-1 rounded-full hover:bg-gray-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-7 gap-1 text-center text-sm">
                                        <div class="font-medium py-1">Su</div>
                                        <div class="font-medium py-1">Mo</div>
                                        <div class="font-medium py-1">Tu</div>
                                        <div class="font-medium py-1">We</div>
                                        <div class="font-medium py-1">Th</div>
                                        <div class="font-medium py-1">Fr</div>
                                        <div class="font-medium py-1">Sa</div>
                                    </div>
                                    <div class="days grid grid-cols-7 gap-1 mt-1"></div>
                                </div>
                                <x-input-error :messages="$errors->get('harvest_time')" class="mt-2" />
                            </div>
                            
                            <style>
                                .datepicker-container {
                                    position: relative;
                                }
                                
                                .datepicker-calendar {
                                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                                }
                                
                                .days {
                                    min-height: 160px;
                                }
                                
                                .day {
                                    @apply p-1.5 rounded-md text-center cursor-pointer;
                                }
                                
                                .day:hover {
                                    @apply bg-indigo-100;
                                }
                                
                                .day.today {
                                    @apply font-semibold text-indigo-700;
                                }
                                
                                .day.selected {
                                    @apply bg-indigo-600 text-white;
                                }
                                
                                .day.other-month {
                                    @apply text-gray-400;
                                }
                                
                                .day.disabled {
                                    @apply text-gray-300 cursor-not-allowed;
                                }
                            </style>
                            
                            @push('scripts')
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    console.log('DOM fully loaded, initializing datepicker...');
                                    
                                    const displayInput = document.getElementById('harvest_time_display');
                                    const hiddenInput = document.getElementById('harvest_time');
                                    const calendar = document.getElementById('harvest_time_calendar');
                                    
                                    if (!displayInput) console.error('displayInput not found');
                                    if (!hiddenInput) console.error('hiddenInput not found');
                                    if (!calendar) console.error('calendar not found');
                                    
                                    if (!displayInput || !hiddenInput || !calendar) {
                                        console.error('Required datepicker elements not found - aborting initialization');
                                        return;
                                    }
                                    
                                    console.log('All datepicker elements found, proceeding with initialization');
                                    
                                    const monthYearElement = calendar.querySelector('.month-year');
                                    const daysContainer = calendar.querySelector('.days');
                                    const prevMonthBtn = calendar.querySelector('.prev-month');
                                    const nextMonthBtn = calendar.querySelector('.next-month');
                                    
                                    console.log('Datepicker UI elements found, starting initialization...');
                                    
                                    let currentDate = new Date();
                                    let selectedDate = hiddenInput.value ? new Date(hiddenInput.value) : null;
                                    
                                    // Initialize the calendar
                                    function initCalendar() {
                                        renderCalendar();
                                        
                                        // Toggle calendar on input click
                                        displayInput.addEventListener('click', function(e) {
                                            e.stopPropagation();
                                            calendar.classList.toggle('hidden');
                                        });
                                        
                                        // Close calendar when clicking outside
                                        document.addEventListener('click', function(e) {
                                            if (!calendar.contains(e.target) && e.target !== displayInput) {
                                                calendar.classList.add('hidden');
                                            }
                                        });
                                        
                                        // Navigation
                                        prevMonthBtn.addEventListener('click', function(e) {
                                            e.stopPropagation();
                                            currentDate.setMonth(currentDate.getMonth() - 1);
                                            renderCalendar();
                                        });
                                        
                                        nextMonthBtn.addEventListener('click', function(e) {
                                            e.stopPropagation();
                                            currentDate.setMonth(currentDate.getMonth() + 1);
                                            renderCalendar();
                                        });
                                    }
                                    
                                    // Render the calendar for the current month
                                    function renderCalendar() {
                                        const year = currentDate.getFullYear();
                                        const month = currentDate.getMonth();
                                        
                                        // Set month and year in header
                                        monthYearElement.textContent = new Intl.DateTimeFormat('en-US', { 
                                            year: 'numeric', 
                                            month: 'long' 
                                        }).format(currentDate);
                                        
                                        // Get first and last day of month
                                        const firstDay = new Date(year, month, 1);
                                        const lastDay = new Date(year, month + 1, 0);
                                        
                                        // Get days in month
                                        const daysInMonth = lastDay.getDate();
                                        
                                        // Get day of week for first day of month (0-6, where 0 is Sunday)
                                        const startingDay = firstDay.getDay();
                                        
                                        // Clear previous days
                                        daysContainer.innerHTML = '';
                                        
                                        // Add empty cells for days before first day of month
                                        for (let i = 0; i < startingDay; i++) {
                                            const prevMonthDay = new Date(year, month, -startingDay + i + 1);
                                            const dayElement = createDayElement(prevMonthDay, true);
                                            daysContainer.appendChild(dayElement);
                                        }
                                        
                                        // Add days of current month
                                        for (let i = 1; i <= daysInMonth; i++) {
                                            const dayDate = new Date(year, month, i);
                                            const dayElement = createDayElement(dayDate);
                                            
                                            // Check if this is the selected date
                                            if (selectedDate && isSameDay(dayDate, selectedDate)) {
                                                dayElement.classList.add('selected');
                                            }
                                            
                                            // Check if this is today
                                            if (isToday(dayDate)) {
                                                dayElement.classList.add('today');
                                            }
                                            
                                            daysContainer.appendChild(dayElement);
                                        }
                                        
                                        // Add empty cells for remaining days in the last week
                                        const remainingDays = 7 - ((startingDay + daysInMonth) % 7);
                                        if (remainingDays < 7) { // Only add if not already a complete week
                                            for (let i = 1; i <= remainingDays; i++) {
                                                const nextMonthDay = new Date(year, month + 1, i);
                                                const dayElement = createDayElement(nextMonthDay, true);
                                                daysContainer.appendChild(dayElement);
                                            }
                                        }
                                    }
                                    
                                    // Create a day element
                                    function createDayElement(date, isOtherMonth = false) {
                                        const dayElement = document.createElement('div');
                                        dayElement.className = 'day' + (isOtherMonth ? ' other-month' : '');
                                        dayElement.textContent = date.getDate();
                                        
                                        dayElement.addEventListener('click', function(e) {
                                            e.stopPropagation();
                                            
                                            // Update selected date
                                            selectedDate = date;
                                            
                                            // Update display input
                                            const formattedDate = formatDate(date);
                                            displayInput.value = formattedDate;
                                            
                                            // Update hidden input (ISO format)
                                            hiddenInput.value = date.toISOString().split('T')[0];
                                            
                                            // Update UI
                                            document.querySelectorAll('.day').forEach(day => {
                                                day.classList.remove('selected');
                                            });
                                            dayElement.classList.add('selected');
                                            
                                            // Close calendar after selection
                                            setTimeout(() => {
                                                calendar.classList.add('hidden');
                                            }, 200);
                                        });
                                        
                                        return dayElement;
                                    }
                                    
                                    // Helper functions
                                    function isSameDay(date1, date2) {
                                        return date1.getDate() === date2.getDate() &&
                                               date1.getMonth() === date2.getMonth() &&
                                               date1.getFullYear() === date2.getFullYear();
                                    }
                                    
                                    function isToday(date) {
                                        const today = new Date();
                                        return isSameDay(date, today);
                                    }
                                    
                                    function formatDate(date) {
                                        return date.toLocaleDateString('en-GB', {
                                            day: '2-digit',
                                            month: '2-digit',
                                            year: 'numeric'
                                        }).replace(/\//g, '/');
                                    }
                                    
                                    // Initialize the calendar
                                    initCalendar();
                                });
                            </script>
                            @endpush

                            @push('scripts')
                            <script>
                                (function () {
                                    function initBatchTomSelectCreate() {
                                        const producerSel = document.getElementById('producer_id');
                                        const sourceSel = document.getElementById('source_id');
                                        const productSel = document.getElementById('product_id');
                                        if (!producerSel || producerSel.dataset.tsInit === '1') return;
                                        producerSel.dataset.tsInit = '1';

                                        const producersUrl = @json(route('ajax.producers'));
                                        const sourcesUrl = @json(route('ajax.sources.by-owner'));
                                        const productsUrl = @json(route('ajax.products.by-owner'));

                                        const initial = {
                                            producerId: @json((int) old('producer_id')),
                                            sourceId: @json((int) old('source_id')),
                                            productId: @json((int) old('product_id')),
                                        };

                                        const producerTS = new TomSelect(producerSel, {
                                            valueField: 'value',
                                            labelField: 'text',
                                            searchField: 'text',
                                            options: [],
                                            load: function (query, callback) {
                                                const url = producersUrl + (query ? ('?q=' + encodeURIComponent(query)) : '');                            
                                                fetch(url)
                                                    .then(r => r.json())
                                                    .then(json => {
                                                        const data = json && json.success ? (json.data || []) : [];                                                        
                                                        callback(data);
                                                    })
                                                    .catch(err => {
                                                        console.error('Error loading producers:', err);
                                                        callback();
                                                    });
                                            },
                                            onFocus: function() {
                                                console.log('Producer field focused');
                                                if (!this.loading && !this.options || Object.keys(this.options).length === 0) {
                                                    console.log('Loading producers on focus...');
                                                    this.load('');
                                                }
                                            }
                                        });

                                        const sourceTS = new TomSelect(sourceSel, {
                                            valueField: 'value',
                                            labelField: 'text',
                                            searchField: 'text',
                                            options: [],
                                            persist: false,
                                            create: false,
                                            closeAfterSelect: true,
                                            maxOptions: 100
                                        });

                                        const productTS = new TomSelect(productSel, {
                                            valueField: 'value',
                                            labelField: 'text',
                                            searchField: 'text',
                                            options: [],
                                            persist: false,
                                            create: false,
                                            closeAfterSelect: true,
                                            maxOptions: 100
                                        });

                                        async function ensureProducerOption(id) {
                                            if (!id) return;
                                            try {
                                                const res = await fetch(producersUrl + '?id=' + id);
                                                const json = await res.json();
                                                if (json && json.success && Array.isArray(json.data)) {
                                                    json.data.forEach(o => producerTS.addOption(o));
                                                }
                                            } catch (e) { }
                                        }

                                        async function reloadSources(ownerId, productId) {
                                            if (!ownerId) return;
                                            try {
                                                const u = new URL(sourcesUrl, window.location.origin);
                                                u.searchParams.set('owner_id', ownerId);
                                                if (productId) u.searchParams.set('product_id', productId);
                                                const res = await fetch(u.toString());
                                                const json = await res.json();
                                                sourceTS.clearOptions();
                                                if (json && json.success) {
                                                    sourceTS.addOptions(json.data || []);
                                                    if (initial.sourceId && (json.data || []).some(i => i.value == initial.sourceId)) {
                                                        sourceTS.setValue(initial.sourceId, true);
                                                    }
                                                }
                                            } catch (e) { }
                                        }

                                        async function reloadProducts(ownerId, sourceId) {
                                            if (!ownerId) return;
                                            try {
                                                const u = new URL(productsUrl, window.location.origin);
                                                u.searchParams.set('owner_id', ownerId);
                                                if (sourceId) u.searchParams.set('source_id', sourceId);
                                                const res = await fetch(u.toString());
                                                const json = await res.json();
                                                productTS.clearOptions();
                                                if (json && json.success) {
                                                    productTS.addOptions(json.data || []);
                                                    if (initial.productId && (json.data || []).some(i => i.value == initial.productId)) {
                                                        productTS.setValue(initial.productId, true);
                                                    }
                                                }
                                            } catch (e) { }
                                        }

                                        producerTS.on('change', (val) => {
                                            sourceTS.clear(true);
                                            productTS.clear(true);
                                            initial.sourceId = null;
                                            initial.productId = null;
                                            const ownerId = parseInt(val || 0);
                                            if (ownerId) {
                                                reloadSources(ownerId);
                                                reloadProducts(ownerId);
                                            }
                                        });

                                        productTS.on('change', (val) => {
                                            const productId = parseInt(val || 0);
                                            const ownerId = parseInt(producerTS.getValue() || 0);
                                            if (ownerId) reloadSources(ownerId, productId || null);
                                        });

                                        sourceTS.on('change', (val) => {
                                            const sourceId = parseInt(val || 0);
                                            const ownerId = parseInt(producerTS.getValue() || 0);
                                            if (ownerId) reloadProducts(ownerId, sourceId || null);
                                        });

                                        // Preselect initial values
                                        if (initial.producerId) {
                                            ensureProducerOption(initial.producerId).then(() => {
                                                producerTS.setValue(initial.producerId, true);
                                                reloadSources(initial.producerId, initial.productId || null);
                                                reloadProducts(initial.producerId, initial.sourceId || null);
                                            });
                                        }
                                    }

                                    if (document.readyState === 'loading') {
                                        document.addEventListener('DOMContentLoaded', initBatchTomSelectCreate);
                                    } else {
                                        initBatchTomSelectCreate();
                                    }
                                    document.addEventListener('turbo:load', initBatchTomSelectCreate);
                                })();
                            </script>
                            @endpush

                            <!-- Status -->
                            <div>
                                <x-label for="status" :value="__('Status')" required />
                                <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @foreach(\App\Models\Batch::STATUSES as $value => $label)
                                        <option value="{{ $value }}" {{ old('status', 'pending') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <!-- Weight -->
                            <div>
                                <x-label for="weight" :value="__('Weight (kg)')" />
                                <x-input id="weight" name="weight" type="number" step="0.01" class="mt-1 block w-full"
                                    :value="old('weight')" />
                                <x-input-error :messages="$errors->get('weight')" class="mt-2" />
                            </div>

                            <!-- Grade -->
                            <div>
                                <x-label for="grade" :value="__('Grade')" />
                                <select id="grade" name="grade" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- Select Grade --</option>
                                    @foreach(\App\Models\Batch::GRADES as $value => $label)
                                        <option value="{{ $value }}" {{ old('grade') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('grade')" class="mt-2" />
                            </div>

                            <!-- Has Defect -->
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="has_defect" name="has_defect" type="checkbox"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        {{ old('has_defect') ? 'checked' : '' }}
                                        value="1">
                                </div>
                                <div class="ml-3 text-sm">
                                    <x-label for="has_defect" :value="__('Has Defect')" class="font-medium text-gray-700" />
                                    <p class="text-gray-500">Check if the batch has any defects</p>
                                </div>
                                <x-input-error :messages="$errors->get('has_defect')" class="mt-2" />
                            </div>

                            <!-- Remark -->
                            <div class="md:col-span-2">
                                <x-label for="remark" :value="__('Remarks')" />
                                <textarea id="remark" name="remark" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('remark') }}</textarea>
                                <x-input-error :messages="$errors->get('remark')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('batches.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
                            </a>
                            <x-button type="submit" class="ml-3">
                                {{ __('Create Batch') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
