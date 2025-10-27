<template>
    <AppLayout>

        <Banner />

        <!-- <Slider :testimonials="testimonials" /> -->
        <!-- <CurrentVehicleForm @current-ready="handleCurrentReady" /> -->

        
    
    <LeasingWizard
      :vehicles="vehicles"
      :makes="makes"
      :models="models"
      :fuel-options="fuelPrices"
    />

    </AppLayout>
</template>

<script>
// import $ from 'jquery';
import AppLayout from '@@/Layouts/AppLayout.vue';
import Banner from '../Testimonials/partials/Banner.vue';
import Slider from './partials/Slider.vue';
// import CurrentVehicleForm from './partials/CurrentVehicleForm.vue'
import LeasingWizard from './partials/LeasingWizard.vue'



Slider


export default {
    components: {
        AppLayout,
        Banner,
        Slider,
        LeasingWizard 
        // CurrentVehicleForm,
        
    },
    props:{
        testimonials: Array,
  makes: Array,
  models: Array,
  fuelPrices: Array,
  vehicles:     Array,
    },
    mounted() {
        $(document).ready(function () {
            $("html, body").animate({ scrollTop: 0 }, "fastest");
            return false;
        });
    }
    ,
   


}
</script>

<style></style>