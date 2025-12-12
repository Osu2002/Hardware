<template>
  <section class="catnav-section" v-if="normalizedCategories.length">
    <!-- ONE BLOCK PER CATEGORY -->
    <article
      v-for="category in normalizedCategories"
      :key="category.id"
      class="catnav-block"
    >
      <!-- CATEGORY HEADER -->
      <div class="catnav-header">
        <h2 class="catnav-heading">
          {{ category.title }}
        </h2>

        <div
          class="catnav-arrows"
          v-if="displayProducts(category).length > visibleCount"
        >
          <button
            type="button"
            class="nav-btn"
            @click="prevSlide(category)"
            :disabled="atStart(category)"
          >
            «
          </button>
          <button
            type="button"
            class="nav-btn"
            @click="nextSlide(category)"
            :disabled="atEnd(category)"
          >
            »
          </button>
        </div>
      </div>

      <!-- PRODUCT SLIDER FOR THIS CATEGORY -->
      <div class="catnav-slider">
        <div class="slider-viewport">
          <div
            class="slider-track"
            :style="trackStyle(category)"
          >
            <article
              v-for="product in displayProducts(category)"
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
    </article>
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
      visibleCount: 5,   // ⬅️ 5 cards visible at once on desktop
      maxPerCategory: 8, // ⬅️ show up to 8 products per category
      sliderIndex: {},   // per-category index map: { [categoryId]: number }
    };
  },

  computed: {
    // Only categories that actually have at least 1 product
    normalizedCategories() {
      return (this.categories || []).filter((cat) => {
        return Array.isArray(cat.products) && cat.products.length > 0;
      });
    },
  },

  watch: {
    categories: {
      handler() {
        this.ensureIndices();
      },
      immediate: true,
    },
  },

  methods: {
    ensureIndices() {
      const next = {};
      (this.categories || []).forEach((cat) => {
        const id = cat.id;
        next[id] = this.sliderIndex[id] || 0;
      });
      this.sliderIndex = next;
    },

    // Up to maxPerCategory recent products per category
    displayProducts(category) {
      const list = Array.isArray(category.products) ? category.products : [];
      return list.slice(0, this.maxPerCategory);
    },

    getIndex(category) {
      return this.sliderIndex[category.id] || 0;
    },

    maxIndex(category) {
      const len = this.displayProducts(category).length;
      return Math.max(0, len - this.visibleCount);
    },

    atStart(category) {
      return this.getIndex(category) === 0;
    },

    atEnd(category) {
      return this.getIndex(category) >= this.maxIndex(category);
    },

    trackStyle(category) {
      const products = this.displayProducts(category);
      const total = products.length || 1;
      const index = this.getIndex(category);

      // Move exactly 1 card per step
      const step = 100 / total;
      const offset = index * step;

      return {
        transform: `translateX(-${offset}%)`,
      };
    },

    nextSlide(category) {
      const current = this.getIndex(category);
      const max = this.maxIndex(category);
      if (current < max) {
        this.sliderIndex = {
          ...this.sliderIndex,
          [category.id]: current + 1,
        };
      }
    },

    prevSlide(category) {
      const current = this.getIndex(category);
      if (current > 0) {
        this.sliderIndex = {
          ...this.sliderIndex,
          [category.id]: current - 1,
        };
      }
    },

    formatPrice(value) {
      if (value == null) return '';
      return Number(value).toLocaleString('en-LK', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },

    goToProduct(slug) {
      this.$inertia.visit(`/products/${slug}`);
    },
  },
};
</script>

<style scoped>
.catnav-section {
  margin: 50px 0;
}

/* each category block */
.catnav-block {
  margin-bottom: 40px;
}

/* header / category title row */
.catnav-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 18px;
}

.catnav-heading {
  font-size: 1.2rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #111827;
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
  width: 28px;
  height: 28px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
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
  flex: 0 0 20%; /* ⬅️ 5 cards per row */
  padding: 0 6px;
  box-sizing: border-box;
  cursor: pointer;
}

.card-inner {
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 3px 10px rgba(15, 23, 42, 0.06);
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
  left: 8px;
  top: 8px;
  background: #1d4ed8;
  color: #ffffff;
  font-size: 0.7rem;
  font-weight: 700;
  padding: 3px 7px;
  border-radius: 4px;
}

.card-body {
  padding: 10px 10px 6px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.card-title {
  font-size: 0.85rem;
  font-weight: 600;
  color: #111827;
  text-align: center;
  line-height: 1.3;
  min-height: 2.8em;
}

.card-price-row {
  margin-top: 6px;
  text-align: center;
}

.card-price {
  color: #f97316;
  font-weight: 700;
  font-size: 0.9rem;
}

.card-footer {
  display: flex;
  justify-content: flex-end;
  padding: 8px 10px 10px;
  border-top: 1px solid #e5e7eb;
}

.btn-buy {
  background: #22c55e;
  color: #ffffff;
  border: none;
  padding: 4px 14px;
  border-radius: 6px;
  font-size: 0.8rem;
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
    flex-direction: row;
    align-items: center;
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
