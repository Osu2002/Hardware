<template>
  <AppLayout>
    <br><br>
    <div class="category-container">
      <h1 class="category-title">
        {{ category.title }}
      </h1>

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

export default {
  name: 'CategoryListIndex',

  components: {
    AppLayout,
    CategoryProductList,
  },

  props: {
    category: {
      type: Object,
      required: true,
    },
    products: {
      type: Object, // Laravel paginator
      required: true,
    },
  },
};
</script>

<style>
.category-container {
  max-width: 1320px;
  margin: 0 auto;
  padding: 0 12px 40px;
}

.category-title {
  font-size: 1.6rem;
  font-weight: 800;
  margin-bottom: 18px;
  color: #111827;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}
</style>
