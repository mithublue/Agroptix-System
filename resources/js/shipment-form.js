document.addEventListener('alpine:init', () => {
    Alpine.data('shipmentForm', (initialState = {}) => ({
        loading: false,
        error: null,
        success: null,
        form: null,

        init() {
            this.form = this.$el;
            
            // Listen for form submission
            this.form.addEventListener('submit', (e) => this.handleSubmit(e));
            
            // Listen for the drawer open event to reset the form
            window.addEventListener('shipment-form-drawer:show', () => {
                this.resetForm();
            });
        },
        
        async handleSubmit(e) {
            e.preventDefault();
            this.loading = true;
            this.error = null;
            this.success = null;

            try {
                const formData = new FormData(this.form);
                const url = this.form.getAttribute('action');
                const method = this.form.getAttribute('method');
                const isEdit = method.toLowerCase() !== 'post';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'An error occurred');
                }

                // Show success message
                this.success = data.message || (isEdit ? 'Shipment updated successfully!' : 'Shipment created successfully!');
                
                // Dispatch success event
                window.dispatchEvent(new CustomEvent('shipment-created', {
                    detail: {
                        message: this.success,
                        shipment: data.shipment
                    }
                }));
                
                // Close the drawer after a short delay
                setTimeout(() => {
                    window.dispatchEvent(new CustomEvent('shipment-form-drawer:close'));
                }, 1500);
                
            } catch (error) {
                console.error('Error submitting form:', error);
                this.error = error.message || 'An error occurred while saving the shipment.';
                
                // Show error notification
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: {
                        type: 'error',
                        message: this.error
                    }
                }));
            } finally {
                this.loading = false;
            }
        },
        
        resetForm() {
            if (this.form) {
                this.form.reset();
                this.error = null;
                this.success = null;
                this.loading = false;
            }
        }
    }));
});
