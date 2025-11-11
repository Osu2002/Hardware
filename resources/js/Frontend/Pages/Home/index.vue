<template>
  <AppLayout>
    
 <div class="container px-3">
  <br>
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
