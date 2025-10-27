<template>
  <AppLayout>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&display=swap" rel="stylesheet" />

    <TopBanner :category_manufactures="Object.values(category_manufactures)" />

    <!-- <Filterration :reload="false" /> -->
    <Type_browse :vehicle_types="vehicle_types" />
    <Browse_by_Brand_Section :vehicle_types="vehicle_types" :category_manufactures="category_manufactures" />

    <AuctionVehicles :live_auction_vehicles_list="live_auction_vehicles_list" />

    <!-- <featuredListing /> -->

    <!-- <exploreVehicles /> -->

    <!-- <Vehicle_Leasing_Facilities /> -->

    <Featured_Listings :featuredVehicles="featuredVehicles" />

    <!-- <thirdBanner /> -->

    <Testimonials :reviews="reviews" />

    <News_Section :reviews="reviews" />

  </AppLayout>
</template>

<script>
// import $ from 'jquery';
import Filterration from '../Live_auction/partials/Filter.vue';
import AppLayout from '@@/Layouts/AppLayout.vue';
import TopBanner from './partials/TopBanner.vue'
import Browse_by_Brand_Section from './partials/Browse_by_Brand_Section.vue';
import featuredListing from './partials/featuredListing.vue';
import exploreVehicles from './partials/exploreVehicles.vue';
import Featured_Listings from './partials/Featured_Listings.vue';
import thirdBanner from './partials/thirdBanner.vue';
import Testimonials from './partials/Testimonials.vue';
import News_Section from './partials/News_Section.vue';
import AuctionVehicles from './partials/auctionVehicles.vue';
import Vehicle_Leasing_Facilities from './partials/Vehicle_Leasing_Facilities.vue';
import Type_browse from './partials/Type_browse.vue';

export default {
  name: 'HomePage',
  components: {
    AppLayout,
    TopBanner,
    Browse_by_Brand_Section,
    featuredListing,
    exploreVehicles,
    thirdBanner,
    Testimonials,
    News_Section,
    Filterration,
    AuctionVehicles,
    Type_browse,
    Vehicle_Leasing_Facilities,
    Featured_Listings

  },
  props: {
    vehicle_types: Object,
    category_manufactures: Object,
    models: Object,
    years: Array,
    minMileage: null | Number,
    maxMileage: null | Number,
    featuredVehicles: Object,
    in_stock: Object,
    latest_cars: Object,
    live_auction_vehicles_list: Object,
    reviews: Array,

    requestQuery: Object,
    auctionDates: Array,
    manufactures: Array,
    models: Array,
    years: Array,
    chassisNumbers: Array,
    engineCapacity: Array,
    colorQuery: Array,
    vehiclesList: Array,
  },
  mounted() {
    console.log("category_manufactures:", this.category_manufactures);

  },
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

* {
  font-family: "Poppins", sans-serif;
}
</style>
