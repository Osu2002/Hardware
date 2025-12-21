<template>
  <section v-if="categories && categories.length" class="shop-categories">
    <div class="container">
      <!-- Header row -->
      <div class="categories-header">
        <h2 class="section-title">Shop by Category</h2>

        <div
          v-if="isSliderActive"
          class="header-controls"
          aria-label="Category navigation"
        >
          <button
            class="header-nav-button"
            :disabled="currentPage === 0"
            @click="prevPage"
            aria-label="Previous categories"
            type="button"
          >
            <svg
              viewBox="0 0 24 24"
              width="20"
              height="20"
              stroke="currentColor"
              stroke-width="2"
              fill="none"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
          </button>

          <button
            class="header-nav-button"
            :disabled="currentPage === totalPages - 1"
            @click="nextPage"
            aria-label="Next categories"
            type="button"
          >
            <svg
              viewBox="0 0 24 24"
              width="20"
              height="20"
              stroke="currentColor"
              stroke-width="2"
              fill="none"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </button>
        </div>
      </div>

      <div class="categories-wrapper">
        <!-- MODE: Slider (Active if > 8 items) -->
        <div
          v-if="isSliderActive"
          class="slider-container"
          ref="sliderViewport"
          @scroll.passive="onViewportScroll"
        >
          <div class="slider-track">
            <div
              v-for="(page, pageIndex) in paginatedCategories"
              :key="pageIndex"
              class="slider-page"
            >
              <div class="categories-grid">
                <div
                  v-for="category in page"
                  :key="category.id"
                  class="category-card"
                  tabindex="0"
                  role="button"
                  :aria-label="'View category ' + category.title"
                  @click="goToCategory(category)"
                  @keydown.enter.prevent="goToCategory(category)"
                  @keydown.space.prevent="goToCategory(category)"
                >
                  <div class="card-image-container">
                    <img
                      v-if="category.image"
                      :src="category.image"
                      :alt="category.title"
                      class="card-image"
                      loading="lazy"
                    />
                    <div v-else class="card-image placeholder">
                      {{ category.title.charAt(0) }}
                    </div>
                    <div class="card-overlay"></div>
                    <h3 class="card-title">{{ category.title }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- MODE: Simple Grid (Active if <= 8 items) -->
        <div v-else class="categories-grid">
          <div
            v-for="category in categories"
            :key="category.id"
            class="category-card"
            tabindex="0"
            role="button"
            :aria-label="'View category ' + category.title"
            @click="goToCategory(category)"
            @keydown.enter.prevent="goToCategory(category)"
            @keydown.space.prevent="goToCategory(category)"
          >
            <div class="card-image-container">
              <img
                v-if="category.image"
                :src="category.image"
                :alt="category.title"
                class="card-image"
                loading="lazy"
              />
              <div v-else class="card-image placeholder">
                {{ category.title.charAt(0) }}
              </div>
              <div class="card-overlay"></div>
              <h3 class="card-title">{{ category.title }}</h3>
            </div>
          </div>
        </div>

        <!-- Progress dots -->
        <div
          v-if="isSliderActive && totalPages > 1"
          class="slider-dots"
          aria-label="Category pages"
        >
          <button
            v-for="n in totalPages"
            :key="n - 1"
            type="button"
            class="dot"
            :class="{ 'is-active': currentPage === (n - 1) }"
            @click="goToPage(n - 1)"
            :aria-label="`Go to page ${n}`"
            :aria-current="currentPage === (n - 1) ? 'page' : null"
          ></button>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "CategoriesBox",
  props: {
    categories: { type: Array, default: () => [] },
  },
  data() {
    return {
      currentPage: 0,
      windowWidth: typeof window !== "undefined" ? window.innerWidth : 1200,
      viewportWidth: 0,
      pageGap: 24, // must match CSS --page-gap
      justDragged: false,
      _raf: null,
      _dragTimer: null,
      _lastLeft: 0,
    };
  },
  computed: {
    isSliderActive() {
      return this.categories && this.categories.length > 8;
    },
    columns() {
      // mobile: 2 columns => 2x2 = 4 categories per page (what you wanted)
      if (this.windowWidth <= 768) return 2;
      if (this.windowWidth <= 1024) return 3;
      return 4;
    },
    itemsPerPage() {
      return this.columns * 2; // 2 rows always
    },
    totalPages() {
      if (!this.isSliderActive) return 1;
      return Math.ceil(this.categories.length / this.itemsPerPage);
    },
    paginatedCategories() {
      if (!this.isSliderActive) return [this.categories];

      const pages = [];
      for (let i = 0; i < this.categories.length; i += this.itemsPerPage) {
        pages.push(this.categories.slice(i, i + this.itemsPerPage));
      }
      return pages;
    },
  },
  watch: {
    itemsPerPage() {
      this.currentPage = 0;
      this.$nextTick(() => {
        this.measureViewport();
        this.goToPage(0, false);
      });
    },
    categories: {
      deep: true,
      handler() {
        if (this.currentPage > this.totalPages - 1) this.currentPage = 0;
        this.$nextTick(() => {
          this.measureViewport();
          this.goToPage(this.currentPage, false);
        });
      },
    },
  },
  mounted() {
    window.addEventListener("resize", this.handleResize, { passive: true });
    this.handleResize();
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.handleResize);
    if (this._raf) cancelAnimationFrame(this._raf);
    if (this._dragTimer) clearTimeout(this._dragTimer);
  },
  // Vue 2 fallback
  beforeDestroy() {
    window.removeEventListener("resize", this.handleResize);
    if (this._raf) cancelAnimationFrame(this._raf);
    if (this._dragTimer) clearTimeout(this._dragTimer);
  },
  methods: {
    handleResize() {
      this.windowWidth = window.innerWidth;
      this.$nextTick(() => {
        this.measureViewport();
        const el = this.$refs.sliderViewport;
        if (!el) return;
        el.scrollTo({ left: this.currentPage * this.stepSize(), behavior: "auto" });
      });
    },
    measureViewport() {
      this.viewportWidth = this.$refs.sliderViewport?.clientWidth || 0;
    },
    stepSize() {
      return (this.viewportWidth || 0) + this.pageGap;
    },

    nextPage() {
      this.goToPage(this.currentPage + 1);
    },
    prevPage() {
      this.goToPage(this.currentPage - 1);
    },

    goToPage(i, smooth = true) {
      const el = this.$refs.sliderViewport;
      if (!el) return;

      const idx = Math.max(0, Math.min(this.totalPages - 1, i));
      const left = idx * this.stepSize();

      el.scrollTo({ left, behavior: smooth ? "smooth" : "auto" });
      this.currentPage = idx;
    },

    onViewportScroll() {
      if (!this.isSliderActive) return;

      const el = this.$refs.sliderViewport;
      if (!el) return;

      // mark as "dragged" if it actually moved (prevents accidental click after swipe)
      const moved = Math.abs(el.scrollLeft - this._lastLeft) > 2;
      this._lastLeft = el.scrollLeft;
      if (moved) {
        this.justDragged = true;
        if (this._dragTimer) clearTimeout(this._dragTimer);
        this._dragTimer = setTimeout(() => (this.justDragged = false), 220);
      }

      if (this._raf) cancelAnimationFrame(this._raf);
      this._raf = requestAnimationFrame(() => {
        const step = this.stepSize();
        if (!step) return;
        const idx = Math.round(el.scrollLeft / step);
        this.currentPage = Math.max(0, Math.min(this.totalPages - 1, idx));
      });
    },

    goToCategory(category) {
      if (this.justDragged) return;
      this.$inertia.visit(route("category.list", category.id));
    },
  },
};
</script>

<style scoped>
.shop-categories {
  padding: 70px 0;
  background-color: #fff;
  --nav-text: #12355a;
  --page-gap: 24px; /* must match pageGap in JS */

  /* ✅ SAME FONT as FeaturedProductList.vue */
  font-family: "Abadi MT Condensed Light", "Abadi MT Condensed", "Abadi MT", Abadi,
    system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
}

/* ✅ Force every text inside to match (same as FeaturedProductList.vue) */
.shop-categories * {
  font-family: inherit;
  letter-spacing: 0.03rem !important;
}

.container {
  max-width: none;
  width: 100%;
  margin: 0;
  padding: 0;
  position: relative;
}

/* Header row */
.categories-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 24px;
}

.section-title {
  font-size: 1.2rem;
  font-weight: 700;
  margin: 0;
  text-transform: uppercase;
  /* ✅ removed letter-spacing overrides (handled by .shop-categories *) */
  color: var(--nav-text);
}

@media (max-width: 768px) {
  .section-title {
    font-size: 1.05rem;
  }
}
@media (max-width: 480px) {
  .section-title {
    font-size: 0.95rem;
  }
}

.header-controls {
  display: flex;
  align-items: center;
  gap: 10px;
}

.header-nav-button {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  color: #111827;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
  transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
}

.header-nav-button:hover:not(:disabled) {
  background: #f9fafb;
  transform: translateY(-1px);
  box-shadow: 0 8px 18px rgba(0, 0, 0, 0.1);
}

.header-nav-button:disabled {
  opacity: 0.45;
  cursor: not-allowed;
  box-shadow: none;
}

.categories-wrapper {
  position: relative;
  padding: 6px 0;
}

/* ✅ Slider viewport (native touch swipe) */
.slider-container {
  overflow-x: auto;
  overflow-y: hidden;
  width: 100%;

  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
  overscroll-behavior-x: contain;

  /* allow vertical page scroll + horizontal swipe */
  touch-action: pan-x pan-y;

  scrollbar-width: none;
  -ms-overflow-style: none;

  padding: 14px 0;
}

.slider-container::-webkit-scrollbar {
  display: none;
}

/* Track + pages */
.slider-track {
  display: flex;
  gap: var(--page-gap);
  align-items: stretch;
}

.slider-page {
  flex: 0 0 100%;
  width: 100%;
  scroll-snap-align: start;
  scroll-snap-stop: always;
  box-sizing: border-box;
}

/* GRID */
.categories-grid {
  display: grid;
  gap: 20px;
  grid-template-columns: repeat(4, 1fr);
}

@media (max-width: 1024px) {
  .categories-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 768px) {
  .categories-grid {
    grid-template-columns: repeat(2, 1fr); /* ✅ 4 per screen (2x2) */
  }
}

@media (max-width: 480px) {
  .categories-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
}

/* CARD */
.category-card {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  background-color: #f3f4f6;
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  outline: none;
  aspect-ratio: 4 / 3;
}

.category-card:hover,
.category-card:focus-visible {
  transform: translateY(-5px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
    0 4px 6px -2px rgba(0, 0, 0, 0.05);
  z-index: 1;
}

.category-card:focus-visible {
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.6),
    0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.card-image-container {
  width: 100%;
  height: 100%;
  position: relative;
  overflow: hidden;
}

.card-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.7s cubic-bezier(0.25, 1, 0.5, 1);
  display: block;
}

.category-card:hover .card-image {
  transform: scale(1.12);
}

.card-image.placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  font-weight: 800;
  color: rgba(255, 255, 255, 0.4);
  background: linear-gradient(135deg, #374151 0%, #111827 100%);
}

.card-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.2) 100%);
  transition: background 0.3s ease, opacity 0.3s ease;
  opacity: 0.8;
}

.category-card:hover .card-overlay {
  background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.4) 100%);
  opacity: 1;
}

.card-title {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0;
  padding: 1rem;
  text-align: center;
  color: #ffffff;
  /* ✅ removed letter-spacing override (handled by .shop-categories *) */
  font-weight: 700;
  font-size: 1.25rem;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
  transition: transform 0.3s ease;
  pointer-events: none;
}

@media (max-width: 768px) {
  .card-title {
    font-size: 1.05rem;
    padding: 0.85rem;
  }
}
@media (max-width: 480px) {
  .card-title {
    font-size: 0.95rem;
    padding: 0.75rem;
  }
}

/* Dots (no wrapper background) */
.slider-dots {
  margin: 14px auto 0;
  width: fit-content;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 0;
  background: transparent;
  box-shadow: none;
}

.slider-dots .dot {
  width: 10px;
  height: 10px;
  border: 0;
  border-radius: 999px;
  background: rgba(18, 53, 90, 0.25); /* light version of active */
  cursor: pointer;
  transition: transform 0.18s ease, background 0.18s ease, width 0.18s ease;
}

.slider-dots .dot.is-active {
  width: 34px;
  background: #12355a;
}

.slider-dots .dot:hover {
  transform: translateY(-1px);
  background: rgba(18, 53, 90, 0.4);
}

.slider-dots .dot:focus-visible {
  outline: 2px solid rgba(18, 53, 90, 0.55);
  outline-offset: 3px;
}
</style>
