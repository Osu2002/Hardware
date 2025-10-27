<template>

  <Topnavigationbar style="top: 0;" />

  <slot />


  <Footer />

</template>

<script setup>
import { useRecaptchaProvider } from 'vue-recaptcha'
import Topnavigationbar from './partials/Topnavigationbar.vue';
import Footer from './partials/Footer.vue';

useRecaptchaProvider();
</script>

<style>
.navbar-second.navbar-live-auction .nav-link {
  color: #000 !important;
}

.navbar-second.navbar-live-auction .nav-link:hover {
  color: #333 !important;
}
</style>