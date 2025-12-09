<template>
  <section class="catnav-section" v-if="categories && categories.length">
    <!-- NAV / TABS -->
    <div class="catnav-header">
      <div class="catnav-tabs">
        <button
          v-for="cat in categories"
          :key="cat.id"
          type="button"
          class="catnav-tab"
          :class="{ active: cat.id === activeCategoryId }"
          @click="setActive(cat.id)"
        >
          {{ cat.title }}
        </button>
      </div>

      <div class="catnav-arrows">
        <button
          type="button"
          class="nav-btn"
          @click="prevSlide"
          :disabled="atStart"
        >
          «
        </button>
        <button
          type="button"
          class="nav-btn"
          @click="nextSlide"
          :disabled="atEnd"
        >
          »
        </button>
      </div>
    </div>

    <!-- PRODUCT SLIDER -->
    <div class="catnav-slider">
      <div class="slider-viewport">
        <div class="slider-track" :style="trackStyle">
          <article
            v-for="product in activeProducts"
            :key="product.id"
            class="product-card"
              @click="goToProduct(product.slug)"
          >
            <div class="card-inner">
              <div class="card-image-wrapper">
                <span v-if="product.is_new" class="badge-new">NEW</span>

                <img
                  :src="product.image"
                  :alt="product.name"
                  class="card-image"
                />
              </div>

              <div class="card-body">
                <h3 class="card-title" :title="product.name">
                  {{ product.name }}
                </h3>

                <div class="card-price-row">
                  <div class="card-price">
                    Rs{{ formatPrice(product.price) }}
                  </div>
                </div>
              </div>

              <div class="card-footer">
                <button type="button" class="btn-buy">
                  BUY
                </button>
              </div>
            </div>
          </article>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'CategoryNav',

  props: {
    categories: {
      type: Array,
      default: () => [],
    },
  },

  data() {
    return {
      activeCategoryId: null,
      visibleCount: 4,  // 4 cards visible at once
      currentIndex: 0,
    };
  },

  computed: {
    // products for the currently active category
    activeProducts() {
      const active = this.categories.find(
        (c) => c.id === this.activeCategoryId
      );
      // always return an array
      return active && Array.isArray(active.products)
        ? active.products
        : [];
    },

    maxIndex() {
      return Math.max(0, this.activeProducts.length - this.visibleCount);
    },

    atStart() {
      return this.currentIndex === 0;
    },

    atEnd() {
      return this.currentIndex >= this.maxIndex;
    },

    trackStyle() {
      // If we show 4 cards in viewport, each step is 25%
      const step = 100 / this.visibleCount;
      return {
        transform: `translateX(-${this.currentIndex * step}%)`,
      };
    },
  },

  mounted() {
    if (this.categories.length) {
      this.activeCategoryId = this.categories[0].id;
    }
  },

  methods: {
    setActive(id) {
      this.activeCategoryId = id;
      this.currentIndex = 0; // reset slider when switching category
    },

    nextSlide() {
      if (!this.atEnd) this.currentIndex += 1;
    },

    prevSlide() {
      if (!this.atStart) this.currentIndex -= 1;
    },

    formatPrice(value) {
      if (value == null) return '';
      return Number(value).toLocaleString('en-LK', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },

     goToProduct(slug) {
    // Using Inertia directly.
    // If you have Ziggy route(), you could do: this.$inertia.visit(route('products.show', slug))
    this.$inertia.visit(`/products/${slug}`);
  },
  },
};
</script>

<style scoped>
.catnav-section {
  margin: 50px 0;
}

/* header / tabs */
.catnav-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 18px;
}

.catnav-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 28px;
}

.catnav-tab {
  border: none;
  background: transparent;
  font-size: 1.1rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #9ca3af;
  cursor: pointer;
  padding: 0;
  position: relative;
}

.catnav-tab.active {
  color: #111827;
}

.catnav-tab.active::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: -6px;
  width: 100%;
  height: 3px;
  border-radius: 2px;
  background: #22c55e;
}

/* arrows */
.catnav-arrows {
  display: flex;
  align-items: center;
  gap: 6px;
}

.nav-btn {
  border: none;
  background: #e5e7eb;
  width: 32px;
  height: 32px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  cursor: pointer;
  transition: background 0.15s ease, transform 0.15s ease;
}

.nav-btn:hover:not(:disabled) {
  background: #d1d5db;
  transform: translateY(-1px);
}

.nav-btn:disabled {
  opacity: 0.4;
  cursor: default;
}

/* slider */
.catnav-slider {
  position: relative;
}

.slider-viewport {
  overflow: hidden;
  width: 100%;
}

.slider-track {
  display: flex;
  transition: transform 0.3s ease;
}

/* card */
.product-card {
  flex: 0 0 25%; /* 4 cards visible */
  padding: 0 10px;
  box-sizing: border-box;
  cursor: pointer;
}

.card-inner {
  background: #ffffff;
  border-radius: 10px;
  box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  min-height: 100%;
}

.card-image-wrapper {
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

.badge-new {
  position: absolute;
  left: 10px;
  top: 10px;
  background: #1d4ed8;
  color: #ffffff;
  font-size: 0.7rem;
  font-weight: 700;
  padding: 4px 9px;
  border-radius: 4px;
}

.card-body {
  padding: 12px 12px 8px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.card-title {
  font-size: 0.92rem;
  font-weight: 600;
  color: #111827;
  text-align: center;
  line-height: 1.3;
  min-height: 3.1em;
}

.card-price-row {
  margin-top: 8px;
  text-align: center;
}

.card-price {
  color: #f97316;
  font-weight: 700;
}

.card-footer {
  display: flex;
  justify-content: flex-end;
  padding: 10px 12px 12px;
  border-top: 1px solid #e5e7eb;
}

.btn-buy {
  background: #22c55e;
  color: #ffffff;
  border: none;
  padding: 6px 18px;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.15s ease;
}

.btn-buy:hover {
  background: #16a34a;
}

/* responsive */
@media (max-width: 1024px) {
  .product-card {
    flex: 0 0 33.333%; /* 3 per row */
  }
}

@media (max-width: 768px) {
  .catnav-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .product-card {
    flex: 0 0 50%; /* 2 per row */
  }
}

@media (max-width: 480px) {
  .product-card {
    flex: 0 0 100%; /* 1 per row */
  }
}
</style>
