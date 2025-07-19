import { Country, State, City } from 'country-state-city';

// Initialize country and state dropdowns
document.addEventListener('alpine:init', () => {
    Alpine.data('countryState', () => ({
        countries: [],
        states: [],
        selectedCountry: '',
        selectedState: '',

        init() {
            // Load countries
            this.countries = Country.getAllCountries().map(country => ({
                code: country.isoCode,
                name: country.name
            }));

            // Sort countries alphabetically
            this.countries.sort((a, b) => a.name.localeCompare(b.name));

            // Set default country if needed
            const defaultCountry = this.$el.querySelector('[name="country"]')?.value;
            if (defaultCountry) {
                this.selectedCountry = defaultCountry;
                this.loadStates();
            }
        },

        loadStates() {
            if (!this.selectedCountry) {
                this.states = [];
                return;
            }

            // Get states for selected country
            this.states = State.getStatesOfCountry(this.selectedCountry)
                .map(state => ({
                    code: state.isoCode,
                    name: state.name
                }))
                .sort((a, b) => a.name.localeCompare(b.name));

            // Clear state selection when country changes
            this.selectedState = '';
        },

        // Format country/state for display
        formatOption(option) {
            return option ? `${option.name} (${option.code})` : '';
        }
    }));
});
