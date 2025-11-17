<template>
  <AppLayout>
    <br><br>
    <div class="home-container">
      <ImageSlider />
      <CategoryNav :categories="categories" />
      <CategoriesBox :categories="categories" />
      <!-- SpecialOffers if you added it -->
      <SpecialOffers />

      <!-- Category nav + product slider -->
     
    </div>
  </AppLayout>
</template>

<script>
import AppLayout     from '@@/Layouts/AppLayout.vue';
import ImageSlider   from './partials/ImageSlider.vue';
import CategoriesBox from './partials/Categoriesbox.vue';
import CategoryNav   from './partials/Categorynav.vue';
import SpecialOffers from './partials/SpecialOffers.vue';

export default {
  components: { AppLayout, ImageSlider, CategoriesBox, CategoryNav,SpecialOffers},
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
.home-container {
  max-width: 1320px;
  margin: 0 auto;
  padding: 0 12px;
}
</style>
