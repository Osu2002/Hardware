<template>
  <section class="category-products">
    <!-- Empty state -->
    <p v-if="!items.length" class="empty">
      No products found in this category.
    </p>

    <!-- Grid -->
    <div v-else class="grid">
      <article
        v-for="p in items"
        :key="p.id"
        class="card"
        @click="goToProduct(p.slug)"
      >
        <div class="card-image-wrap">
          <img
            v-if="p.image"
            :src="p.image"
            :alt="p.name"
            class="card-image"
          />
          <div v-else class="card-placeholder">
            No image
          </div>
        </div>

        <div class="card-body">
          <h3 class="card-title" :title="p.name">
            {{ p.name }}
          </h3>

          <div class="card-price-row">
            <span class="price-main">
              Rs{{ formatPrice(p.price) }}
            </span>

            <span
              v-if="p.sale_price && p.sale_price < p.regular_price"
              class="price-old"
            >
              Rs{{ formatPrice(p.regular_price) }}
            </span>
          </div>
        </div>
      </article>
    </div>

    <!-- Pagination -->
    <nav
      v-if="products.links && products.links.length > 1"
      class="pagination"
      aria-label="Pagination"
    >
      <button
        v-for="link in products.links"
        :key="link.label + (link.url || '')"
        type="button"
        class="page-link"
        :class="{
          active: link.active,
          disabled: !link.url,
        }"
        :disabled="!link.url"
        @click.prevent="goToPage(link)"
      >
        <span v-html="link.label" />
      </button>
    </nav>
  </section>
</template>

<script>
export default {
  name: 'CategoryProductList',

  props: {
    category: {
      type: Object,
      required: true,
    },
    products: {
      type: Object, // paginator with data, links, meta
      required: true,
    },
  },

  computed: {
    items() {
      return this.products && this.products.data
        ? this.products.data
        : [];
    },
  },

  methods: {
    formatPrice(value) {
      if (value == null) return '';
      return Number(value).toLocaleString('en-LK', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },

    goToProduct(slug) {
      // reuse your existing product detail route
      this.$inertia.visit(`/products/${slug}`);
      // or, if Ziggy: this.$inertia.visit(route('products.show', slug));
    },

    goToPage(link) {
      if (!link.url || link.active) return;

      this.$inertia.get(link.url, {}, {
        preserveState: true,
        preserveScroll: true,
      });
    },
  },
};
</script>

<style scoped>
.category-products {
  margin-top: 10px;
}

.empty {
  padding: 24px 0;
  font-size: 0.95rem;
  color: #6b7280;
}

/* 3 cards per row on desktop */
.grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 18px;
  margin-bottom: 20px;
}

.card {
  background: #ffffff;
  border-radius: 10px;
  box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
  display: flex;
  flex-direction: column;
  cursor: pointer;
  overflow: hidden;
  transition: transform 0.12s ease, box-shadow 0.12s ease;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 22px rgba(15, 23, 42, 0.18);
}

.card-image-wrap {
  position: relative;
  padding-top: 70%;
  background: #f9fafb;
}

.card-image {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: contain;
  background: #ffffff;
}

.card-placeholder {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  color: #9ca3af;
}

.card-body {
  padding: 10px 12px 12px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.card-title {
  font-size: 0.95rem;
  font-weight: 600;
  color: #111827;
  line-height: 1.3;
  min-height: 2.6em;
  margin-bottom: 6px;
}

.card-price-row {
  display: flex;
  align-items: baseline;
  gap: 6px;
}

.price-main {
  color: #f97316;
  font-weight: 800;
  font-size: 1rem;
}

.price-old {
  font-size: 0.82rem;
  text-decoration: line-through;
  color: #9ca3af;
}

/* Pagination */
.pagination {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  justify-content: center;
  padding-top: 10px;
}

.page-link {
  min-width: 34px;
  height: 34px;
  padding: 0 10px;
  border-radius: 999px;
  border: 1px solid #d1d5db;
  background: #ffffff;
  font-size: 0.85rem;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.page-link span {
  display: inline-block;
}

.page-link.active {
  background: #22c55e;
  border-color: #22c55e;
  color: #ffffff;
  font-weight: 700;
}

.page-link.disabled {
  opacity: 0.4;
  cursor: default;
}

/* Responsive: 2 per row on tablet, 1 on mobile */
@media (max-width: 1024px) {
  .grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 640px) {
  .grid {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }
}
</style>
