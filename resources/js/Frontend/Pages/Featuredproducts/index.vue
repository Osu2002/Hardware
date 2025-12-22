<template>
  <AppLayout>
    <br /><br />

    <div class="featured-container">
      <!-- ✅ Banner must be INSIDE container (same as Category) -->
      <FeaturedBanner title="Featured Products" />

      <div class="featured-head">
        <!-- <h2 class="featured-title">{{ page?.title || "Featured Products" }}</h2>
        <p class="featured-sub">Browse all featured items.</p> -->
      </div>

      <div class="featured-layout">
        <aside class="sidebar">
          <ProductFilter
            :subcategories="subcategories"
            :brands="brands"
            :filters="filters"
          />
        </aside>

        <main class="main">
          <FeaturedProductList :products="products" />
        </main>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from "@@/Layouts/AppLayout.vue";
import ProductFilter from "./ProductFilter.vue";
import FeaturedProductList from "./FeaturedProductList.vue";
import FeaturedBanner from "./Banner.vue";

export default {
  name: "FeaturedProductsIndex",
  components: {
    AppLayout,
    ProductFilter,
    FeaturedProductList,
    FeaturedBanner,
  },
  props: {
    page: { type: Object, default: () => ({}) },
    products: { type: Object, required: true },
    subcategories: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
  },
};
</script>

<style>
.featured-container {
  max-width: 1320px;
  margin: 0 auto;
  padding: 0 12px 40px;
}

.featured-head {
  padding: 8px 0 10px;
}

.featured-title {
  margin: 0;
  font-size: 1.3rem;
  font-weight: 900;
  color: #111827;
}

.featured-sub {
  margin: 6px 0 0;
  color: #6b7280;
  font-size: 0.95rem;
}

.featured-layout {
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 16px;
  align-items: start;
}

@media (max-width: 1024px) {
  .featured-layout {
    grid-template-columns: 1fr;
  }
}
</style>
