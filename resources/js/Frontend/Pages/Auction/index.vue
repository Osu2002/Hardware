<template>
  <AppLayout>

    <Banner />

    <Auction />

  </AppLayout>
</template>

<script>
// import $ from 'jquery';
import AppLayout from '@@/Layouts/AppLayout.vue';
import Banner from '../Auction/partials/Banner.vue';
import Auction from './partials/auction.vue';


export default {
    components:{
        AppLayout,
        Banner,
        Auction
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