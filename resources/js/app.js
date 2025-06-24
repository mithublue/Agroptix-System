import './bootstrap';
import Alpine from 'alpinejs';

// Initialize Alpine.js
window.Alpine = Alpine;

// Start Alpine.js when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
});
