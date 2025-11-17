<template>
  <AppLayout>
    <br><br>
    <div class="home-container">
      <ImageSlider />
      <CategoriesBox :categories="categories" />
      <br><br>
      <SpecialOffers />
    </div>

   
  </AppLayout>
</template>

<script>
import AppLayout     from '@@/Layouts/AppLayout.vue';
import CategoriesBox from './partials/Categoriesbox.vue';
import ImageSlider   from './partials/ImageSlider.vue';
import SpecialOffers from './partials/SpecialOffers.vue';


export default {
  components: { AppLayout, ImageSlider, CategoriesBox, SpecialOffers  },
  props: {
    site_url: Object,
    categories: {
      type: Array,
      default: () => [],
    },
  },
};
</script>


<style>
.home-container{ max-width: 1320px; margin: 0 auto; padding: 0 12px; }
</style>
