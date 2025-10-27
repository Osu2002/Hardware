<template>
  <AppLayout>

    <Banner />

    <Dealer />

  </AppLayout>
</template>

<script>
// import $ from 'jquery';
import AppLayout from '@@/Layouts/AppLayout.vue';
import Banner from '../Dealer/partials/Banner.vue';
import Dealer from './partials/dealer.vue';


export default {
    components:{
        AppLayout,
        Banner,
        Dealer
    },
    mounted(){
        $(document).ready(function() {
      $("html, body").animate({ scrollTop: 0 }, "fastest");
      return false;
    });
    }
}
</script>

<style>

</style>