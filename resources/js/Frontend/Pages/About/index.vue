<template>
   <AppLayout>

    <Banner />

    <!-- <Content /> -->

    <!-- <Description /> -->
   <!-- <ServicesSection /> -->
   
    <Abouttext :image_url = image_url />
    
    <br><br>
    <ourMission/>

    <br>
    <ourValues/>

   </AppLayout>
</template>

<script>
// import $ from 'jquery';
import AppLayout from '@@/Layouts/AppLayout.vue';
import Banner from './partials/Banner.vue';
import Content from './partials/content.vue';
import Description from './partials/Description.vue';
import Abouttext from './partials/Abouttext.vue';
import ServicesSection from './partials/ServicesSection.vue';
import ourMission from './partials/ourMission.vue';
import ourValues from './partials/ourValues.vue';


export default {
    components:{
        AppLayout,
        Banner,
        Content,
        Description,
        Abouttext,
        ServicesSection,
        ourMission,
        ourValues
    },
    props:{
        image_url:Object
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

/* somewhere in your main/app.css or in index.vue (non-scoped) */
html, body {
  overflow-x: hidden;
}


</style>