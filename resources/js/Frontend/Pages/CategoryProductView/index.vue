<template>
  <AppLayout>
    <br><br>
    <div class="product-container">
      <ProductView
        :product="product"
        :related-products="relatedProducts"
      />
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@@/Layouts/AppLayout.vue';
import ProductView from './partials/ProductView.vue';

export default {
  name: 'CategoryProductPage',

  components: {
    AppLayout,
    ProductView,
  },

  props: {
    product: {
      type: Object,
      required: true,
    },
    relatedProducts: {
      type: Array,
      default: () => [],
    },
  },
};
</script>

<style>
.product-container {
  max-width: 1320px;
  margin: 0 auto;
  padding: 0 12px;
}


</style>
