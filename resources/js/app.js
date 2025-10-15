import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
Alpine.plugin(collapse);
import { Turbo } from '@hotwired/turbo-rails';
import Swal from 'sweetalert2';
import './country-state';
import axios from 'axios';
import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";
import Chart from 'chart.js/auto';
window.TomSelect = TomSelect;
window.Chart = Chart;

// Ensure Alpine.data is always registered after Turbo navigation
window.Alpine = Alpine;

// Register qualityTests Alpine component globally
Alpine.data('qualityTests', () => ({
    openBatch: null,
    loadingTests: false,
    tests: {},
    loadedBatches: new Set(),
    error: null,
    axiosInstance: axios.create({
        baseURL: window.APP_URL || '/',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    }),
    async loadTests(batchId) {
        if (this.loadedBatches.has(batchId)) return;
        this.loadingTests = true;
        this.error = null;
        try {
            const testsUrl = `${window.APP_URL || ''}/batches/${batchId}/qualitytests`;
            const response = await this.axiosInstance.get(testsUrl);
            if (!response.data || response.data.success === false) throw new Error(response.data?.message || 'Failed to load tests');
            this.tests = { ...this.tests, [batchId]: Array.isArray(response.data.data) ? response.data.data : [] };
            this.loadedBatches = new Set([...this.loadedBatches, batchId]);
        } catch (error) {
            this.error = 'Failed to load quality tests. ' + (error.message || 'Please try again.');
            this.tests = { ...this.tests, [batchId]: [] };
            alert(`Error loading tests: ${error.message}`);
        } finally {
            this.loadingTests = false;
        }
    },
    async toggleBatch(batchId) {
        if (this.openBatch === batchId) {
            this.openBatch = null;
        } else {
            this.openBatch = batchId;
            await this.loadTests(batchId);
        }
    },
    formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    },
}));

// Re-register Alpine components after Turbo navigation
if (window.Turbo) {
    document.addEventListener('turbo:load', () => {
        Alpine.initTree(document.body);
    });
}

// Alpine.js click-outside directive
Alpine.directive('click-outside', (el, { expression }, { evaluateLater, effect }) => {
    const handleClick = (event) => {
        if (!el.contains(event.target) && !event.defaultPrevented) {
            const method = evaluateLater(expression);
            method();
        }
    };

    document.addEventListener('click', handleClick);

    // Cleanup
    el._clickOutsideCleanup = () => {
        document.removeEventListener('click', handleClick);
    };
});

// Make Swal available globally
window.Swal = Swal;

// Initialize Alpine.js
Alpine.start();

// Configure Toast
window.Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

// Start Alpine.js when Turbo has loaded the page
document.addEventListener('turbo:load', () => {
    // Initialize any Alpine.js plugins or custom directives here
});

// Persist Alpine stores across Turbo navigation
document.addEventListener('turbo:before-render', () => {
    const alpinePersisted = {};

    // Save all Alpine stores
    document.querySelectorAll('[x-data]').forEach(el => {
        try {
            // Check if Alpine is initialized and has the component
            if (el.__x && el.__x.$data) {
                const alpineComponent = el.__x.$data;
                const componentId = el.getAttribute('x-id') || el.getAttribute('id');

                if (componentId && alpineComponent.$data) {
                    // Only save serializable data
                    alpinePersisted[componentId] = JSON.parse(JSON.stringify(alpineComponent.$data));
                }
            }
        } catch (error) {
            console.warn('Error saving Alpine state:', error);
        }
    });

    // Restore Alpine stores after navigation
    const restoreAlpineState = () => {
        document.querySelectorAll('[x-data]').forEach(el => {
            try {
                const componentId = el.getAttribute('x-id') || el.getAttribute('id');
                if (componentId && alpinePersisted[componentId]) {
                    // Wait for Alpine to be initialized
                    const checkAlpine = setInterval(() => {
                        if (el.__x && el.__x.$data && el.__x.$data.$data) {
                            clearInterval(checkAlpine);
                            Object.assign(el.__x.$data.$data, alpinePersisted[componentId]);
                        }
                    }, 10);

                    // Timeout after 1 second if Alpine doesn't initialize
                    setTimeout(() => clearInterval(checkAlpine), 1000);
                }
            } catch (error) {
                console.warn('Error restoring Alpine state:', error);
            }
        });
    };

    // Use requestAnimationFrame to ensure DOM is ready
    requestAnimationFrame(() => {
        // Small delay to ensure Alpine has processed the new page
        setTimeout(restoreAlpineState, 10);
    });
});

// Start Turbo
Turbo.start();
