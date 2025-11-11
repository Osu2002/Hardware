<template>
  <AppLayout>
    <div class="home-container">
      <ImageSlider />
    </div>

   
  </AppLayout>
</template>

<script>
import AppLayout   from '@@/Layouts/AppLayout.vue';

import ImageSlider from './partials/ImageSlider.vue';

export default {
  components: { AppLayout, ImageSlider },
  props: { site_url: Object },
};
</script>

<style>
.home-container{ max-width: 1320px; margin: 0 auto; padding: 0 12px; }
</style>
