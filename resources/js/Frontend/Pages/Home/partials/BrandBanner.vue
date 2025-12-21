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
          >
            ‹
          </button>
          <button
            type="button"
            class="nav-btn"
            @click="next"
          >
            ›
          </button>
        </div>
      </div>

      <!-- SWIPER SLIDER -->
       <div class="brand-slider-row">
      <Swiper
        class="brand-swiper"
        :modules="modules"
        :loop="true"
        :speed="600"
        :autoplay="{
          delay: 2000,
          disableOnInteraction: false
        }"
        :slides-per-view="6"
        :space-between="16"
        :breakpoints="breakpoints"
        @swiper="onSwiper"
      >
        <SwiperSlide
          v-for="brand in brands"
          :key="brand.id"
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
          </div>
        </SwiperSlide>
      </Swiper>
      </div>
    </div>
  </section>
</template>

<script>
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay } from 'swiper/modules';
import 'swiper/css';

export default {
  name: 'BrandBanner',

  components: {
    Swiper,
    SwiperSlide,
  },

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
      swiperInstance: null,
      modules: [Autoplay],
      breakpoints: {
        0: {
          slidesPerView: 2,
          spaceBetween: 8,
        },
        480: {
          slidesPerView: 3,
          spaceBetween: 10,
        },
        768: {
          slidesPerView: 4,
          spaceBetween: 12,
        },
        1024: {
          slidesPerView: 6,
          spaceBetween: 16,
        },
      },
    };
  },

  methods: {
    onSwiper(swiper) {
      this.swiperInstance = swiper;
    },
    next() {
      if (this.swiperInstance) {
        this.swiperInstance.slideNext();
      }
    },
    prev() {
      if (this.swiperInstance) {
        this.swiperInstance.slidePrev();
      }
    },
  },
};
</script>

<style scoped>
/* ✅ SAME FONT as FeaturedProductList.vue */
.brand-banner {
  margin: 40px 0;
  border-radius: 16px;
  overflow: hidden;

  font-family: "Abadi MT Condensed Light", "Abadi MT Condensed", "Abadi MT", Abadi,
    system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
}

/* ✅ Force every text inside to match FeaturedProductList.vue */
.brand-banner * {
  font-family: inherit;
  letter-spacing: 0.03rem !important;
}

/* ===== HEADER ===== */
.brand-banner-header {
  padding: 24px 3px 12px;

  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 4px;
}

.brand-banner-title {
  margin: 0;

  /* ✅ same title color as CategoryNav */
  color: var(--nav-text, #12355a);

  font-weight: 700;
  text-transform: uppercase;

  /* ✅ normal size */
  font-size: 1.2rem;
}

/* ✅ 768px size */
@media (max-width: 768px) {
  .brand-banner-title {
    font-size: 1.05rem;
  }
}

.brand-banner-controls {
  display: flex;
  gap: 8px;
}

/* ===== SLIDER ===== */
.brand-slider-row {
  padding: 0; /* touches left/right edges */
}

.brand-swiper {
  width: 100%;
  padding: 0 0 20px; /* only bottom padding */
}

/* Brand card */
.brand-card-inner {
  background: #ffffff;
  border-radius: 12px;
  padding: 16px 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  border: 1px solid #e5e7eb;
}

/* Brand logo */
.brand-logo-wrapper {
  width: 100%;
  height: 60px;
  border-radius: 10px;
  background: transparent;
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

/* Slider buttons */
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

.nav-btn:hover {
  background: rgba(17, 24, 39, 0.15);
  transform: translateY(-1px);
}
</style>

