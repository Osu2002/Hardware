<template>
    <div>
        <div v-for="country in countries" :key="country.id" class="form-check">
            <input class="form-check-input" type="checkbox" :value="country.id" v-model="selectedCountries"
                @change="updateFilter" />
            <label class="form-check-label">
                {{ country.name }}
                <!-- <img :src="country?.media?.length > 0 ? country?.media[0].original_url : ''" height="15" class="ps-4" alt=""> -->
            </label>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        countries: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            selectedCountries: this.$page.props.requestQuery.country
                ? Array.isArray(this.$page.props.requestQuery.country)
                    ? this.$page.props.requestQuery.country
                    : [this.$page.props.requestQuery.country]
                : [],
        };
    },
    methods: {
        updateFilter() {
            // Get current query parameters
            const query = { ...this.$page.props.requestQuery };
            
            // Update the country parameter
            if (this.selectedCountries.length > 0) {
                query.country = this.selectedCountries;
            } else {
                delete query.country;
            }

            // Use Inertia to update with the new query
            this.$inertia.reload({
                method: "POST",
                data: {
                    "_method": "GET",
                    ...query
                },
                preserveState: true,
                preserveScroll: true,
            });
        },
    },
};
</script>