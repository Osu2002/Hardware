<template>
    <AppLayout>

        <!-- <Banner :selected-country="selectedCountry"/> -->

        <StockFilter  :countries="countries" />

        <!-- <StockTable /> -->

    </AppLayout>
</template>

<script>
// import $ from 'jquery';
import AppLayout from '@@/Layouts/AppLayout.vue';
import Banner from './partials/Banner.vue';
import StockFilter from './partials/StockFilter.vue';
import StockTable from './partials/StockTable.vue';

export default {
    components: {
        AppLayout,
        Banner,
        StockFilter,
        StockTable
    },
    props: {
        countries: {
            type: Array,
            default: () => []
        }
    },
    mounted() {
        console.log(this.$root.getQueryParam('brand'))

    }
}
</script>

<style>
html, body {
  overflow-x: hidden;
}

</style>
