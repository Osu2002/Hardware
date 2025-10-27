<template>
  <div
    class="carousel slide"
    role="region"
    aria-label="Featured Vehicle Stock"
  >
    <div class="carousel-inner">
      <div
        class="carousel-item d-block custom_slide_height"
        v-for="slide in slides"
        :key="slide.src"
      >
        <!-- shimmering placeholder -->
        <div v-if="!imageLoaded[slide.src]" class="skeleton-placeholder" />

        <!-- real image -->
        <img
          :src="slide.src"
          class="d-block w-100 custom_slide_height auction-image"
          :class="{ 'is-loaded': imageLoaded[slide.src] }"
          @load="markLoaded(slide.src)"
          alt="Available stock"
        />

        <!-- caption -->
        <div class="carousel-caption custom_caption_position" style="top:90%">
          <h1 class="main-topic-banner">{{ slide.title }}</h1>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BannerWithLoader',
  props: {
    /**
     * slides: Array of objects like:
     *   { src: '/images/Background (19).jpg', title: 'My Profile' }
     */
    slides: {
      type: Array,
      default: () => [
        { src: '/images/Background (19).jpg', title: 'My Profile' }
      ]
    }
  },
  data() {
    return {
      imageLoaded: {}
    }
  },
  mounted() {
    // initialize each slide as not-loaded
    this.slides.forEach(s => {
      this.$set(this.imageLoaded, s.src, false)
    })
  },
  methods: {
    markLoaded(src) {
      this.imageLoaded[src] = true
    }
  }
}
</script>

<style scoped>
.carousel-item {
  position: relative;
}
.custom_slide_height {
  height: 91vh !important;
  object-fit: cover;
}
.carousel-caption {
  position: absolute;
  right: 15%;
  bottom: 1.25rem;
  left: 3%;
  color: #fff;
  text-align: left;
}
.main-topic-banner {
  font-size: 2.4rem;
  font-weight: 700;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}
@media (max-width:768px) {
  .custom_slide_height { height:50vh!important; }
  .main-topic-banner { margin-top:-80px; }
}
@media (max-width:576px) {
  .custom_slide_height { height:40vh!important; }
  .main-topic-banner { font-size:1.8rem; margin-top:-60px; }
}

/* shimmer placeholder */
.skeleton-placeholder {
  position: absolute; top:0; left:0;
  width:100%; height:100%;
  background: linear-gradient(
    to right,
    #e0e0e0 0%, #f8f8f8 50%, #e0e0e0 100%
  );
  background-size:200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: inherit;
  z-index:1;
}
@keyframes shimmer {
  0%   { background-position: -200% 0; }
  100% { background-position:  200% 0; }
}

/* fade in real image */
.auction-image {
  opacity: 0;
  transition: opacity 0.5s ease;
}
.auction-image.is-loaded {
  opacity: 1;
  z-index: 2;
}
</style>
