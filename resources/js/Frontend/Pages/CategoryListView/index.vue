<template>
  <AppLayout>
    <br /><br />

    <div class="category-container">
      <CategoryBanner :category="category" />

      <div class="category-layout">
        <aside class="sidebar">
          <CategoryFilters
            :category="category"
            :subcategories="subcategories"
            :brands="brands"
            :filters="filters"
          />
        </aside>

        <main class="main">
          <CategoryProductList
            :category="category"
            :products="products"
          />
        </main>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from "@@/Layouts/AppLayout.vue";
import CategoryBanner from "./CategoryBanner.vue";
import CategoryFilters from "./CategoryFilters.vue";
import CategoryProductList from "./CategoryProductList.vue";

export default {
  name: "CategoryListIndex",
  components: {
    AppLayout,
    CategoryBanner,
    CategoryFilters,
    CategoryProductList,
  },
  props: {
    category: { type: Object, required: true },
    products: { type: Object, required: true },
    subcategories: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
  },
};
</script>

<style>
.category-container {
  max-width: 1320px;
  margin: 0 auto;
  padding: 0 12px 40px;
}

/* sidebar + main */
.category-layout {
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 16px;
  align-items: start;
}

/* responsive: sidebar goes top */
@media (max-width: 1024px) {
  .category-layout {
    grid-template-columns: 1fr;
  }
}
</style>
