<template>
  <div class="carousel slide" role="region" aria-label="Featured Vehicle Stock">
    <div class="carousel-inner">
      <div class="carousel-item active d-block custom_slide_height position-relative">
        <div v-if="!imageLoaded" class="skeleton-placeholder"></div>

        <img
          :src="imageSrc"
          :class="['d-block', 'w-100', 'custom_slide_height', 'banner-image', { 'is-loaded': imageLoaded }]"
          alt="Available stock"
          @load="onImageLoad"
        />

        <div class="carousel-caption custom_caption_position" style="top: 90%;">
          <div class="caption">
            <h1 class="main-topic-banner">
              Local Stock
              <span v-if="selectedCountry"> - {{ selectedCountry.name }} </span>
            </h1>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';

export default {
  name: 'Banner',
  components: { Link },
  props: {
    selectedCountry: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      imageLoaded: false,
      imageSrc: '/images/Carbackground.png',
    };
  },
  methods: {
    onImageLoad() {
      console.log('Banner image loaded');
      this.imageLoaded = true;
    },
  },
  mounted() {
    console.log('Selected Country in Banner:', this.selectedCountry);
  },
};
</script>

<style scoped>
.carousel-item {
  position: relative;
}

.custom_slide_height {
  height: 91vh !important;
  object-fit: cover;
}

.skeleton-placeholder {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 91vh;
  background: linear-gradient(
    to right,
    #e0e0e0 0%,
    #f8f8f8 50%,
    #e0e0e0 100%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  z-index: 10;
}

@keyframes shimmer {
  0% {
    background-position: -200% 0;
  }
  100% {
    background-position: 200% 0;
  }
}

.banner-image {
  opacity: 0;
  transition: opacity 0.5s ease;
}

.banner-image.is-loaded {
  opacity: 1;
}

.carousel-caption {
  position: absolute;
  right: 15%;
  bottom: 1.25rem;
  left: 3%;
  padding: 0;
  color: #fff;
  text-align: left;
  z-index: 20;
}

.main-topic-banner {
  font-size: 2.4rem;
  font-weight: 700;
  line-height: 1.2;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

@media (max-width: 768px) {
  .main-topic-banner {
    font-size: 2.4rem;
    margin-top: -80px;
  }
  .custom_slide_height {
    height: 50vh !important;
  }
}

@media (max-width: 576px) {
  .main-topic-banner {
    font-size: 1.8rem;
    margin-top: -60px;
  }
  .custom_slide_height {
    height: 40vh !important;
  }
}
</style>