<template>
    <AppLayout>

        <!-- <Banner :vehicle="vehicle" :randomVehicles="randomVehicles" /> -->


        <product :vehicle="vehicle" :randomVehicles="randomVehicles" />
                <!-- <ContactUs :vehicle="vehicle" /> -->

        <!-- <StockTable /> -->
    <Featured_Listings :featuredVehicles="featuredVehicles" />
        <!-- <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1>Product View</h1>
                </div>
            </div>
        </div> -->
    </AppLayout>
</template>

<script>
// import $ from 'jquery';
import AppLayout from '@@/Layouts/AppLayout.vue';
import Banner from './partials/Banner.vue';
import product from './partials/product.vue';
import Featured_Listings from './partials/Featured_Listings.vue';
import ContactUs from './partials/ContactUs.vue';

export default {
    components: {
        AppLayout,
        Banner,
        product,
        Featured_Listings,
        ContactUs
    },
    props: {
        vehicle: Object,
        randomVehicles: Array,
        featuredVehicles: Array,
    },
    // mounted() {
    //     console.log(this.$root.getQueryParam('brand'))

    //     $(document).ready(function () {
    //         $("html, body").animate({ scrollTop: 0 }, "fastest");
    //         return false;
    //     });

    // }
}
</script>

<style>
* {
    font-family: poppins;
}



</style>