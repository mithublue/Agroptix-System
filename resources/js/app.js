import './bootstrap';
import Alpine from 'alpinejs';
import { Turbo } from '@hotwired/turbo-rails';
import Swal from 'sweetalert2';

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
        const alpineComponent = el.__x.$data;
        if (alpineComponent) {
            const componentId = el.getAttribute('x-id');
            if (componentId) {
                alpinePersisted[componentId] = { ...alpineComponent.$data };
            }
        }
    });

    // Restore Alpine stores after navigation
    document.addEventListener('turbo:render', () => {
        document.querySelectorAll('[x-data]').forEach(el => {
            const componentId = el.getAttribute('x-id');
            if (componentId && alpinePersisted[componentId]) {
                const alpineComponent = el.__x.$data;
                if (alpineComponent) {
                    Object.assign(alpineComponent.$data, alpinePersisted[componentId]);
                }
            }
        });
    });
});

// Start Turbo
Turbo.start();
