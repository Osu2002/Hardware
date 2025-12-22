<template>
  <AppLayout>
    <br /><br />

    <div class="offers-container">
      <OfferBanner title="Special Offers" />

      <div class="offers-head">
        <!-- <h2 class="offers-title">{{ page?.title || "Special Offers" }}</h2>
        <p class="offers-sub">Browse only discounted items.</p> -->
      </div>

      <div class="offers-layout">
        <aside class="sidebar">
          <ProductFilter
            :subcategories="subcategories"
            :brands="brands"
            :filters="filters"
          />
        </aside>

        <main class="main">
          <OfferList :products="products" />
        </main>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from "@@/Layouts/AppLayout.vue";
import OfferBanner from "./Banner.vue";
import ProductFilter from "./ProductFilter.vue";
import OfferList from "./OfferList.vue";

export default {
  name: "SpecialOffersIndex",
  components: {
    AppLayout,
    OfferBanner,
    ProductFilter,
    OfferList,
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
.offers-container {
  max-width: 1320px;
  margin: 0 auto;
  padding: 0 12px 40px;
}

.offers-head {
  padding: 8px 0 10px;
}

.offers-title {
  margin: 0;
  font-size: 1.3rem;
  font-weight: 900;
  color: #111827;
}

.offers-sub {
  margin: 6px 0 0;
  color: #6b7280;
  font-size: 0.95rem;
}

.offers-layout {
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 16px;
  align-items: start;
}

@media (max-width: 1024px) {
  .offers-layout {
    grid-template-columns: 1fr;
  }
}
</style>
