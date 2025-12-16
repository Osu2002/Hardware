<template>
  <section class="catnav-section" v-if="normalizedCategories.length">
    <article
      v-for="category in normalizedCategories"
      :key="category.id"
      class="catnav-block"
    >
      <div class="catnav-header">
        <h2 class="catnav-heading">{{ category.title }}</h2>

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

      <div class="catnav-slider">
        <div class="slider-viewport">
          <div class="slider-track" :style="trackStyle(category)">
            <article
              v-for="product in displayProducts(category)"
              :key="product.id"
              class="product-card"
              @click="goToProduct(product.slug)"
            >
              <div class="card-inner">
                <div class="card-image-wrapper">
                  <!-- <span v-if="product.is_new" class="badge-new">NEW</span> -->

                  <!-- DISCOUNT BADGE -->
                  <span
                    v-if="hasDiscount(product)"
                    class="badge-discount"
                    :title="`Discount: ${discountPercent(product)}%`"
                  >
                    -{{ discountPercent(product) }}%
                  </span>

                  <img
                    :src="product.image"
                    :alt="product.name"
                    class="card-image"
                  />
                </div>

                <div class="card-body">
                  <h3 class="card-title" :title="product.name">
                    {{ truncateName(product.name) }}
                  </h3>

                  <div class="card-price-row">
                    <div class="card-price-stack">
                      <!-- OLD PRICE -->
                      <div v-if="hasDiscount(product)" class="card-old-price">
                        Rs{{ formatPrice(basePrice(product)) }}
                      </div>

                      <!-- NEW / CURRENT PRICE -->
                      <div class="card-price">
                        Rs{{ formatPrice(currentPrice(product)) }}
                      </div>
                    </div>
                  </div>
                </div>

                <div class="card-footer">
                  <!-- HEART BUTTON (LEFT) -->
                  <button
                    type="button"
                    class="btn-heart"
                    :class="{ active: isWished(product) }"
                    @click.stop="toggleWish(product)"
                    aria-label="Add to wishlist"
                    title="Wishlist"
                  >
                    <svg viewBox="0 0 24 24" class="heart-icon" aria-hidden="true">
                     <path
  d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
/>

                    </svg>
                  </button>

                  <button type="button" class="btn-buy" @click.stop="goToProduct(product.slug)">
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
      visibleCount: 5,
      maxPerCategory: 8,
      sliderIndex: {},
      wished: new Set(), // local wishlist state (optional)
    };
  },

  computed: {
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
      const step = 100 / total;
      const offset = index * step;

      return { transform: `translateX(-${offset}%)` };
    },

    nextSlide(category) {
      const current = this.getIndex(category);
      const max = this.maxIndex(category);
      if (current < max) {
        this.sliderIndex = { ...this.sliderIndex, [category.id]: current + 1 };
      }
    },

    prevSlide(category) {
      const current = this.getIndex(category);
      if (current > 0) {
        this.sliderIndex = { ...this.sliderIndex, [category.id]: current - 1 };
      }
    },

   // ---------- DISCOUNT LOGIC ----------
// ---------- DISCOUNT LOGIC ----------
discountPercent(product) {
  // backend computed percent (best)
  const p = Number(product?.discount_percent);
  if (Number.isFinite(p) && p > 0) return Math.round(p);

  // fallback compute from prices
  const oldP = Number(product?.regular_price);
  const nowP = Number(product?.price);
  if (Number.isFinite(oldP) && oldP > 0 && Number.isFinite(nowP) && nowP > 0 && nowP < oldP) {
    return Math.round(((oldP - nowP) / oldP) * 100);
  }

  return 0;
},

basePrice(product) {
  return Number(product?.regular_price ?? 0) || 0;
},

currentPrice(product) {
  return Number(product?.price ?? 0) || 0;
},

hasDiscount(product) {
  const base = this.basePrice(product);
  const now = this.currentPrice(product);
  return base > 0 && now > 0 && now < base && this.discountPercent(product) > 0;
},


    // ---------- UI HELPERS ----------
    truncateName(name) {
      const s = String(name ?? '');
      if (s.length <= 20) return s;
      return s.slice(0, 20) + '...';
    },

    formatPrice(value) {
      if (value == null) return '';
      return Number(value).toLocaleString('en-LK', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },

    toggleWish(product) {
      const id = product?.id;
      if (id == null) return;

      const next = new Set(this.wished);
      if (next.has(id)) next.delete(id);
      else next.add(id);

      this.wished = next;

      // If you want to tell parent:
      // this.$emit('toggle-wishlist', { product, wished: next.has(id) });
    },

    isWished(product) {
      const id = product?.id;
      return id != null && this.wished.has(id);
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

.catnav-block {
  margin-bottom: 40px;
}

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

.catnav-slider {
  position: relative;
}

.slider-viewport {
  overflow: hidden;
  width: 100%;
  padding: 6px 0;
}

.slider-track {
  display: flex;
  transition: transform 0.3s ease;
}

/* card */
.product-card {
  flex: 0 0 20%;
  padding: 0 6px;
  box-sizing: border-box;
  cursor: pointer;
}

.card-inner {
  background: #ffffff;
  border-radius: 8px;
  border: 1px solid #e5e7eb; /* light ash border */
  overflow: hidden;
  display: flex;
  flex-direction: column;
  min-height: 100%;
  transition: transform 0.18s ease, box-shadow 0.18s ease;
}


.product-card:hover .card-inner {
  transform: translateY(-2px);
  /* border-color: #373a3d; */
}


.card-image-wrapper {
  position: relative;
  padding-top: 70%;
  background: #f9fafb;
  overflow: hidden;
}

.card-image {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: contain;
  background: #ffffff;
  transition: transform 0.28s ease;
}

.product-card:hover .card-image {
  transform: scale(1.08);
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
  z-index: 2;
}

/* discount badge (circle) */
.badge-discount {
  position: absolute;
  right: 8px;
  top: 8px;
  width: 44px;
  height: 44px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: #ef4444;
  color: #ffffff;
  font-weight: 800;
  font-size: 0.78rem;
  z-index: 2;
  box-shadow: 0 6px 14px rgba(239, 68, 68, 0.25);
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

.card-price-stack {
  display: inline-flex;
  flex-direction: column;
  gap: 2px;
  align-items: center;
}

.card-old-price {
  color: #6b7280;
  font-weight: 600;
  font-size: 0.78rem;
  text-decoration: line-through;
}

.card-price {
  color: #f97316;
  font-weight: 800;
  font-size: 0.92rem;
}

.card-footer {
  display: flex;
  align-items: center;
  justify-content: space-between; /* heart left, buy right */
  gap: 10px;
  padding: 8px 10px 10px;
  border-top: 1px solid #e5e7eb;
}

.btn-heart {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  background: #ffffff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: transform 0.15s ease, border-color 0.15s ease, background 0.15s ease;
}

.btn-heart:hover {
  transform: translateY(-1px);
  border-color: #fca5a5;
  background: #fff1f2;
}

.btn-heart.active {
  border-color: #ef4444;
  background: #fee2e2;
}

.heart-icon {
  width: 18px;
  height: 18px;
  fill: #ef4444;
  display: block;
}

.heart-icon {
  width: 18px;
  height: 18px;
  display: block; /* prevents weird inline spacing */
}

/* default = outline heart */
.btn-heart {
  color: #9ca3af; /* grey */
}

.btn-heart .heart-icon {
  fill: none;
  stroke: currentColor;
  stroke-width: 2;
}

/* active = filled heart */
.btn-heart.active {
  color: #ef4444; /* red */
}

.btn-heart.active .heart-icon {
  fill: currentColor;
  stroke: none;
}


.btn-buy {
  background: #22c55e;
  color: #ffffff;
  border: none;
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 800;
  cursor: pointer;
  transition: background 0.15s ease, transform 0.15s ease;
}

.btn-buy:hover {
  background: #16a34a;
  transform: translateY(-1px);
}

/* responsive */
@media (max-width: 1024px) {
  .product-card {
    flex: 0 0 33.333%;
  }
}

@media (max-width: 768px) {
  .catnav-header {
    flex-direction: row;
    align-items: center;
    gap: 12px;
  }

  .product-card {
    flex: 0 0 50%;
  }
}

@media (max-width: 480px) {
  .product-card {
    flex: 0 0 100%;
  }
}
</style>
