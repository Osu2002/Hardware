<template>
  <section class="brand-banner" v-if="brands.length">
    <div class="brand-banner-inner">
      <!-- HEADER -->
      <div class="brand-banner-header">
        <h2 class="brand-banner-title">
          Popular Brands
        </h2>

        <div class="brand-banner-controls">
          <button
            type="button"
            class="nav-btn"
            @click="prev"
            :disabled="atStart"
          >
            ‹
          </button>
          <button
            type="button"
            class="nav-btn"
            @click="next"
            :disabled="atEnd"
          >
            ›
          </button>
        </div>
      </div>

      <!-- SLIDER -->
      <div class="brand-viewport">
        <div class="brand-track" :style="trackStyle">
          <div
            v-for="brand in brands"
            :key="brand.id"
            class="brand-card"
          >
            <div class="brand-card-inner">
              <div class="brand-logo-wrapper">
                <img
                  v-if="brand.logo"
                  :src="brand.logo"
                  :alt="brand.title"
                  class="brand-logo"
                />
                <div v-else class="brand-logo placeholder">
                  {{ brand.title }}
                </div>
              </div>

              <div class="brand-name">
                {{ brand.title }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'BrandBanner',

  props: {
    // from HomeController:
    // id, title, slug, website_url, logo, banner, ...
    brands: {
      type: Array,
      default: () => [],
    },
  },

  data() {
    return {
      visibleCount: 6, // will be adjusted in mounted()
      currentIndex: 0,
    };
  },

  computed: {
    maxIndex() {
      return Math.max(0, this.brands.length - this.visibleCount);
    },
    atStart() {
      return this.currentIndex === 0;
    },
    atEnd() {
      return this.currentIndex >= this.maxIndex;
    },
    trackStyle() {
      // width shift in percentage steps
      const step = 100 / this.visibleCount;
      return {
        transform: `translateX(-${this.currentIndex * step}%)`,
      };
    },
  },

  mounted() {
    this.updateVisibleCount();
    window.addEventListener('resize', this.updateVisibleCount);
  },

  beforeUnmount() {
    window.removeEventListener('resize', this.updateVisibleCount);
  },

  methods: {
    next() {
      if (!this.atEnd) {
        this.currentIndex += 1;
      }
    },
    prev() {
      if (!this.atStart) {
        this.currentIndex -= 1;
      }
    },

    updateVisibleCount() {
      const width = window.innerWidth || document.documentElement.clientWidth;

      if (width < 480) {
        this.visibleCount = 2;
      } else if (width < 768) {
        this.visibleCount = 3;
      } else if (width < 1024) {
        this.visibleCount = 4;
      } else {
        this.visibleCount = 6;
      }

      // keep index in range when resizing
      if (this.currentIndex > this.maxIndex) {
        this.currentIndex = this.maxIndex;
      }
    },
  },
};
</script>

<style scoped>
.brand-banner {
  margin: 40px 0;
  padding: 24px 32px;
  background: #f9fafb;
  border-radius: 16px;
  box-shadow: 0 8px 30px rgba(15, 23, 42, 0.06);
}

.brand-banner-inner {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

/* Header */
.brand-banner-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 4px;
}

.brand-banner-title {
  font-size: 1.4rem;
  font-weight: 700;
  color: #111827;
  margin: 0;
}

.brand-banner-controls {
  display: flex;
  gap: 8px;
}

/* Slider viewport + track */
.brand-viewport {
  overflow: hidden;
  width: 100%;
}

.brand-track {
  display: flex;
  transition: transform 0.3s ease;
}

/* Brand card */
.brand-card {
  flex: 0 0 calc(100% / 6);
  padding: 0 8px;
  box-sizing: border-box;
}

.brand-card-inner {
  background: #ffffff;
  border-radius: 12px;
  padding: 16px 12px;
  box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

/* Brand logo */
.brand-logo-wrapper {
  width: 100%;
  height: 60px;
  border-radius: 10px;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 8px;
}

.brand-logo {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.brand-logo.placeholder {
  font-size: 0.8rem;
  font-weight: 600;
  color: #6b7280;
  text-align: center;
}

/* Brand name */
.brand-name {
  font-size: 0.85rem;
  font-weight: 500;
  color: #374151;
  text-align: center;
}

/* Slider buttons (same style as other sliders) */
.nav-btn {
  border: none;
  background: rgba(17, 24, 39, 0.06);
  width: 32px;
  height: 32px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  cursor: pointer;
  transition: background 0.15s ease, transform 0.15s ease;
}

.nav-btn:hover:not(:disabled) {
  background: rgba(17, 24, 39, 0.15);
  transform: translateY(-1px);
}

.nav-btn:disabled {
  opacity: 0.35;
  cursor: default;
}

/* Responsive card widths – must match visibleCount logic */
@media (max-width: 1023.98px) {
  .brand-card {
    flex: 0 0 calc(100% / 4);
  }
}

@media (max-width: 767.98px) {
  .brand-card {
    flex: 0 0 calc(100% / 3);
  }
}

@media (max-width: 479.98px) {
  .brand-card {
    flex: 0 0 calc(100% / 2);
  }
}
</style>
