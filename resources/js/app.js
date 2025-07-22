import './bootstrap';
import Alpine from 'alpinejs';
import { Turbo } from '@hotwired/turbo-rails';
import Swal from 'sweetalert2';
import './country-state';

// Make Swal available globally
window.Swal = Swal;

// Initialize Alpine.js
window.Alpine = Alpine;

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
    // Initialize Alpine.js
    Alpine.start();
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
