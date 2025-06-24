import './bootstrap';
import Alpine from 'alpinejs';
import { Turbo } from '@hotwired/turbo-rails';

// Initialize Alpine.js
window.Alpine = Alpine;

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
