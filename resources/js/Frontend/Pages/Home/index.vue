<template>
  <AppLayout>
    <br><br>
    <div class="home-container">
    <ImageSlider :images="banners" />


       <CategoriesBox :categories="categories" />

        <SpecialOffers :offers="specialOffers" />
      <CategoryNav :categories="categories" />
     
      <!-- SpecialOffers if you added it -->
    


      <!-- Category nav + product slider -->
        <BrandBanner :brands="brands" />
     
    </div>
  </AppLayout>
</template>

<script>
import AppLayout     from '@@/Layouts/AppLayout.vue';
import ImageSlider   from './partials/ImageSlider.vue';
import CategoriesBox from './partials/Categoriesbox.vue';
import CategoryNav   from './partials/Categorynav.vue';
import SpecialOffers from './partials/SpecialOffers.vue';
import BrandBanner from './partials/BrandBanner.vue';

export default {
  components: { AppLayout, ImageSlider, CategoriesBox, CategoryNav,SpecialOffers,  BrandBanner},
  props: {
    site_url: Object,
    categories: {
      type: Array,
      default: () => [],
    },
    specialOffers: {                      // ← NEW
      type: Array,
      default: () => [],
    },
    brands: Array,

      banners: {           // ← NEW
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
