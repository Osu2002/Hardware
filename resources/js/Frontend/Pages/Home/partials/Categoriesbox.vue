<template>
  <section v-if="categories && categories.length" class="shop-categories">
    <div class="container">
      <!-- Header row: title left, controls top-right -->
      <div class="categories-header">
        <h2 class="section-title">Shop by Category</h2>

        <div v-if="isSliderActive" class="header-controls" aria-label="Category navigation">
          <button
            class="header-nav-button"
            :disabled="currentPage === 0"
            @click="prevPage"
            aria-label="Previous categories"
            type="button"
          >
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
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
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
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
  @pointerdown="onPointerDown"
  @pointermove="onPointerMove"
  @pointerup="onPointerUp"
  @pointercancel="onPointerUp"
>
  

          <div class="slider-track" :class="{ dragging: isDragging }" :style="trackStyle">
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
        <!-- Progress dots (only for slider mode) -->
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
  name: 'CategoriesBox',
  props: {
    categories: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      currentPage: 0,
      windowWidth: typeof window !== 'undefined' ? window.innerWidth : 1200,

      // NEW: pixel-based slider math
      viewportWidth: 0,

      // NEW: creates “space between page 1 and page 2” (your 8th vs 9th/10th)
      pageGap: 24,
      isDragging: false,
dragX: 0,
startX: 0,
startY: 0,
dragAxis: null,       // 'x' | 'y' | null
pointerId: null,
justDragged: false,
    
    };
  },
  computed: {
    isSliderActive() {
      return this.categories && this.categories.length > 8;
    },
    columns() {
      if (this.windowWidth <= 480) return 1;
      if (this.windowWidth <= 768) return 2;
      if (this.windowWidth <= 1024) return 3;
      return 4;
    },
    itemsPerPage() {
      return this.columns * 2; // 2 rows
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

    // NEW: correct track movement (no peeking)
    trackStyle() {
  const step = (this.viewportWidth || 0) + this.pageGap;
  const baseOffset = this.currentPage * step;

  // while dragging, apply live drag offset
  const live = this.isDragging ? this.dragX : 0;

  return {
    transform: `translateX(-${baseOffset - live}px)`,
    gap: `${this.pageGap}px`,
  };
},

  },
  watch: {
    itemsPerPage() {
      this.currentPage = 0;
      this.$nextTick(() => this.measureViewport());
    },
    categories: {
      deep: true,
      handler() {
        if (this.currentPage > this.totalPages - 1) this.currentPage = 0;
        this.$nextTick(() => this.measureViewport());
      }
    }
  },
  mounted() {
    window.addEventListener('resize', this.handleResize);
    this.handleResize();
  },
  beforeUnmount() {
    window.removeEventListener('resize', this.handleResize);
  },
  methods: {
    handleResize() {
      this.windowWidth = window.innerWidth;
      this.$nextTick(() => this.measureViewport());
    },
    measureViewport() {
      this.viewportWidth = this.$refs.sliderViewport?.clientWidth || 0;
    },
    nextPage() {
      if (this.currentPage < this.totalPages - 1) this.currentPage++;
    },
    prevPage() {
      if (this.currentPage > 0) this.currentPage--;
    },
   goToCategory(category) {
  if (this.justDragged) return; // stop clicks after swipe
  this.$inertia.visit(route('category.list', category.id));
},

    onPointerDown(e) {
  // left click or touch
  if (e.pointerType === 'mouse' && e.button !== 0) return;

  this.isDragging = true;
  this.pointerId = e.pointerId;
  this.startX = e.clientX;
  this.startY = e.clientY;
  this.dragX = 0;
  this.dragAxis = null;

  // capture pointer so move/up still fires even if finger leaves area
  e.currentTarget.setPointerCapture?.(e.pointerId);
},

goToPage(i) {
  const idx = Math.max(0, Math.min(this.totalPages - 1, i));
  this.currentPage = idx;
},


onPointerMove(e) {
  if (!this.isDragging) return;

  const dx = e.clientX - this.startX;
  const dy = e.clientY - this.startY;

  // decide direction once (prevents breaking vertical page scroll)
  if (!this.dragAxis) {
    if (Math.abs(dx) < 6 && Math.abs(dy) < 6) return;

    this.dragAxis = Math.abs(dx) > Math.abs(dy) ? 'x' : 'y';

    // if it’s vertical scroll, stop our drag immediately
    if (this.dragAxis === 'y') {
      this.isDragging = false;
      this.dragX = 0;
      return;
    }
  }

  if (this.dragAxis !== 'x') return;

  // prevent page from scrolling sideways while swiping slider
  e.preventDefault?.();

  const step = (this.viewportWidth || 0) + this.pageGap;
  const max = step; // limit one page worth of drag

  this.dragX = Math.max(-max, Math.min(max, dx));
},

onPointerUp(e) {
  if (!this.isDragging) return;

  const step = (this.viewportWidth || 0) + this.pageGap;

  // swipe threshold: 20% of viewport or at least 60px
  const threshold = Math.max(60, (this.viewportWidth || 0) * 0.2);

  const moved = Math.abs(this.dragX) > 10;
  if (moved) {
    this.justDragged = true;
    setTimeout(() => (this.justDragged = false), 250);
  }

  if (this.dragAxis === 'x' && Math.abs(this.dragX) >= threshold) {
    if (this.dragX < 0) this.nextPage();
    else this.prevPage();
  }

  this.isDragging = false;
  this.dragX = 0;
  this.dragAxis = null;

  e.currentTarget.releasePointerCapture?.(this.pointerId);
  this.pointerId = null;
},

  },

};
</script>

<style scoped>
.shop-categories {
  padding: 40px 0 60px;
  background-color: #fff;
}

/* Full width container */
.container {
  max-width: none;
  width: 100%;
  margin: 0;
  padding: 0;
  position: relative;
}

/* Header row (title + buttons on the right) */
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
  letter-spacing: 0.04em;
  color: var(--nav-text);
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
  box-shadow: 0 2px 6px rgba(0,0,0,0.08);
  transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
}

.header-nav-button:hover:not(:disabled) {
  background: #f9fafb;
  transform: translateY(-1px);
  box-shadow: 0 8px 18px rgba(0,0,0,0.10);
}

.header-nav-button:disabled {
  opacity: 0.45;
  cursor: not-allowed;
  box-shadow: none;
}

.categories-wrapper {
  position: relative;
}

/* Slider viewport */
.slider-container {
  overflow: hidden;     /* IMPORTANT: prevents previous page “peeking” */
  position: relative;
  width: 100%;
}

.slider-track {
  display: flex;
  transition: transform 0.5s cubic-bezier(0.25, 1, 0.5, 1);
  will-change: transform;
  align-items: stretch;
}

.slider-page {
  flex: 0 0 100%;
  width: 100%;
  box-sizing: border-box;
}

/* GRID LAYOUT */
.categories-grid {
  display: grid;
  gap: 20px;
  grid-template-columns: repeat(4, 1fr);
}

@media (max-width: 1024px) {
  .categories-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 768px) {
  .categories-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 480px) {
  .categories-grid { grid-template-columns: 1fr; }
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
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  z-index: 1;
}

.category-card:focus-visible {
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.6), 0 10px 15px -3px rgba(0, 0, 0, 0.1);
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
  color: rgba(255,255,255,0.4);
  background: linear-gradient(135deg, #374151 0%, #111827 100%);
}

.card-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.2) 100%);
  transition: background 0.3s ease, opacity 0.3s ease;
  opacity: 0.8;
}

.category-card:hover .card-overlay {
  background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.4) 100%);
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
  letter-spacing: 0.04em;
  font-weight: 700;
  font-size: 1.25rem;
  text-shadow: 0 2px 4px rgba(0,0,0,0.5);
  transition: transform 0.3s ease;
  pointer-events: none;
}

.shop-categories {
  padding: 70px 0;          /* more space top & bottom */
  background-color: #fff;
}

/* Slider viewport */
.slider-container {
  overflow: hidden;          /* keep this, prevents peeking */
  position: relative;
  width: 100%;

  padding: 14px 0;           /* <-- IMPORTANT: space for hover lift */
}

/* Optional: if you still feel tiny cut on top/bottom */
.categories-wrapper {
  padding: 6px 0;            /* extra breathing room */
}


.category-card:hover .card-title {
  transform: scale(1.05);
}

.shop-categories {
  --nav-text: #12355a;
  --nav-muted: #6b7280;
  --nav-primary: #0b3c80;
  font-family: inherit;
  color: var(--nav-text);
}

/* enables smooth touch swiping without fighting page scroll */
.slider-container {
  touch-action: pan-y;     /* allow vertical scrolling, handle horizontal ourselves */
  user-select: none;
  -webkit-user-select: none;
}

/* remove transition during drag so it follows finger */
.slider-track.dragging {
  transition: none !important;
  cursor: grabbing;
}

/* progress dots bar */
/* dots row (no background wrapper) */
.slider-dots {
  margin: 14px auto 0;
  width: fit-content;

  display: flex;
  align-items: center;
  gap: 10px;
  padding: 0;              /* no wrapper padding */
  background: transparent; /* no wrapper background */
  box-shadow: none;        /* no wrapper shadow */
}


/* inactive dots */
/* inactive dots */
.slider-dots .dot {
  width: 10px;
  height: 10px;
  border: 0;
  border-radius: 999px;

  background: rgba(18, 53, 90, 0.25); /* light version of #12355a */
  cursor: pointer;

  transition: transform 0.18s ease, background 0.18s ease, width 0.18s ease;
}

/* active pill */
.slider-dots .dot.is-active {
  width: 34px;
  background: #12355a; /* same active color */
}

/* hover */
.slider-dots .dot:hover {
  transform: translateY(-1px);
  background: rgba(18, 53, 90, 0.4);
}

.slider-dots .dot:focus-visible {
  outline: 2px solid rgba(18, 53, 90, 0.55);
  outline-offset: 3px;
}


</style>
