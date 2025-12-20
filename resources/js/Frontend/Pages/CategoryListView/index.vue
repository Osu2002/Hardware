<template>
  <AppLayout>
    <br><br>
    <div class="category-container">
      <CategoryBanner :category="category" />

      <CategoryProductList
        :category="category"
        :products="products"
      />
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@@/Layouts/AppLayout.vue';
import CategoryProductList from './CategoryProductList.vue';
import CategoryBanner from './CategoryBanner.vue';

export default {
  name: 'CategoryListIndex',
  components: {
    AppLayout,
    CategoryProductList,
    CategoryBanner,
  },
  props: {
    category: { type: Object, required: true },
    products: { type: Object, required: true },
  },
};
</script>

<style>
.category-container {
  max-width: 1320px;
  margin: 0 auto;
  padding: 0 12px 40px;
}
</style>
