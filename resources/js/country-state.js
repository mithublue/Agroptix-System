import { Country, State, City } from 'country-state-city';

// Make sure Alpine is available
window.Alpine = window.Alpine || {};

// Initialize country and state dropdowns
document.addEventListener('alpine:init', () => {
    Alpine.data('countryState', () => ({
        countries: [],
        states: [],
        selectedCountry: '',
        selectedState: '',

        init() {
            // Load countries
            this.$nextTick(() => {
                this.countries = Country.getAllCountries().map(country => ({
                    code: country.isoCode,
                    name: country.name
                }));

                // Sort countries alphabetically
                this.countries.sort((a, b) => a.name.localeCompare(b.name));

                // If we have a selected country from old input, load its states
                if (this.selectedCountry) {
                    this.loadStates();
                }
            });
        },

        loadStates() {
            if (!this.selectedCountry) {
                this.states = [];
                this.selectedState = '';
                return;
            }

            // Get states for selected country
            this.states = State.getStatesOfCountry(this.selectedCountry)
                .map(state => ({
                    code: state.isoCode,
                    name: state.name
                }))
                .sort((a, b) => a.name.localeCompare(b.name));

            // If the previously selected state exists in the new states, keep it selected
            if (this.selectedState && !this.states.some(s => s.name === this.selectedState)) {
                this.selectedState = '';
            }
        },

        // Format country/state for display
        formatOption(option) {
            return option ? `${option.name} (${option.code})` : '';
        }
    }));
});
