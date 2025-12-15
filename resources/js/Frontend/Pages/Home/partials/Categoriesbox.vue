<template>
  <section v-if="categories && categories.length" class="shop-categories">
    <div class="container">
      <h2 class="section-title">Shop by Category</h2>

      <div class="categories-wrapper">
        <!-- MODE: Slider (Active if > 8 items) -->
        <div 
          v-if="isSliderActive" 
          class="slider-container"
        >
          <div 
            class="slider-track"
            :style="{ transform: `translateX(-${currentPage * 100}%)` }"
          >
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
          
          <!-- Controls -->
          <button 
            class="nav-button prev"
            :disabled="currentPage === 0"
            @click="prevPage"
            aria-label="Previous categories"
          >
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
          </button>
          
          <button 
            class="nav-button next"
            :disabled="currentPage === totalPages - 1"
            @click="nextPage"
            aria-label="Next categories"
          >
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </button>
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
    };
  },
  computed: {
    isSliderActive() {
      // Per requirements: only switch to slider if > 8 items
      return this.categories && this.categories.length > 8;
    },
    columns() {
      if (this.windowWidth <= 480) return 1;
      if (this.windowWidth <= 768) return 2;
      if (this.windowWidth <= 1024) return 3;
      return 4;
    },
    itemsPerPage() {
      // 2 rows visible per page
      return this.columns * 2;
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
      // Reset to page 0 if layout drastically changes to prevent empty index
      // Alternatively, try to keep the same first item visible, but page 0 is safer
      this.currentPage = 0;
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
    },
    nextPage() {
      if (this.currentPage < this.totalPages - 1) {
        this.currentPage++;
      }
    },
    prevPage() {
      if (this.currentPage > 0) {
        this.currentPage--;
      }
    },
    goToCategory(category) {
      this.$inertia.visit(route('category.list', category.id));
    },
  },
};
</script>

<style scoped>
/* 
  Design Tokens (internal usage)
  Colors: Dark, White, Gray
*/

.shop-categories {
  padding: 40px 0 60px;
  background-color: #fff; /* Ensure neutral background if placed in blocks */
}

/* Container limits width but allows grid to expand */
.container {
  max-width: 1400px; /* Large enough for 4 cols */
  margin: 0 auto;
  padding: 0 20px;
  position: relative;
}

.section-title {
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 24px;
  color: #111827;
}

.categories-wrapper {
  position: relative;
}

/* Slider Frame */
.slider-container {
  overflow: hidden;
  position: relative;
  /* Add padding for hover overflow/shadows if needed, or keeping tight */
  margin: -10px; /* Offset padding for shadows */
  padding: 10px;
}

.slider-track {
  display: flex;
  width: 100%;
  transition: transform 0.5s cubic-bezier(0.25, 1, 0.5, 1);
  will-change: transform;
}

.slider-page {
  width: 100%;
  flex-shrink: 0;
}

/* GRID LAYOUT */
.categories-grid {
  display: grid;
  gap: 20px;
  /* logic matching JS break points */
  grid-template-columns: repeat(4, 1fr);
}

@media (max-width: 1024px) {
  .categories-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}
@media (max-width: 768px) {
  .categories-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media (max-width: 480px) {
  .categories-grid {
    grid-template-columns: 1fr;
  }
}

/* CARD COMPONENT */
.category-card {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  background-color: #f3f4f6; /* Skeleton/Loading bg */
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  outline: none;
  /* Aspect Ratio Hack or native? Native is widely supported now */
  aspect-ratio: 4 / 3; 
}

/* Hover Interaction over Card */
.category-card:hover, .category-card:focus-visible {
  transform: translateY(-5px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  z-index: 1; /* Lift above siblings */
}

/* Focus Ring */
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
  transform: scale(1.12); /* Smooth zoom */
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

/* Overlay */
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

/* Title */
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
  font-size: 1.25rem;
  font-weight: 700;
  text-shadow: 0 2px 4px rgba(0,0,0,0.5);
  transition: transform 0.3s ease;
  pointer-events: none; /* Let clicks pass through */
}

.category-card:hover .card-title {
  transform: scale(1.05); /* Slight text scale */
}

/* Navigation Buttons */
.nav-button {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  color: #111827;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
  transition: all 0.2s ease;
  z-index: 10;
  opacity: 0; /* Hidden by default until hover wrapper or always visible? */
  /* UX decision: Always visible if slider active, or fade in. 
     Requirements say "Provide Next/Prev buttons", implies visibility.
     Let's keep them visible but offset cleanly. 
  */
  opacity: 1;
}

.nav-button:hover:not(:disabled) {
  background: #f9fafb;
  transform: translateY(-50%) scale(1.1);
  box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
}

.nav-button:active:not(:disabled) {
  transform: translateY(-50%) scale(0.95);
}

.nav-button:disabled {
  opacity: 0.4;
  cursor: not-allowed;
  background: #f3f4f6;
  border-color: #f3f4f6;
  color: #9ca3af;
  box-shadow: none;
}

/* Positioning buttons - Outside or Inside? 
   If full width, inside overlaps content.
   Let's place them slightly outside if container allows, or overlay on edges.
   Cleanest is usually overlaying the edges or sitting on top.
   Let's overlay on vertical center of the slider window. 
*/
.nav-button.prev {
  left: -20px;
}
.nav-button.next {
  right: -20px;
}

/* On smaller screens, move buttons inside or below? */
@media (max-width: 768px) {
  .nav-button {
    width: 36px;
    height: 36px;
  }
  .nav-button.prev {
    left: -10px;
  }
  .nav-button.next {
    right: -10px;
  }
}

/* If screen is too narrow for outside buttons, push them in */
@media (max-width: 600px) {
  .nav-button.prev {
    left: 4px;
  }
  .nav-button.next {
    right: 4px;
  }
  /* Ensure they don't block too much content - maybe slightly transparent? */
  .nav-button {
    background: rgba(255,255,255,0.9);
  }
}
</style>
