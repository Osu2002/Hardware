<template>
  <div class="slideshow-container">
    <div
      v-for="(src, i) in images"
      :key="i"
      class="slide"
      :class="{ active: currentIndex === i }"
    >
      <!-- only for the first image: show skeleton until it loads -->
      <div
        v-if="i === 0 && !imageLoaded"
        class="skeleton-placeholder"
      ></div>

      <img
        :src="src"
        class="slider-img"
        @load="onImageLoad(i)"
        :class="{ 'is-loaded': i !== 0 || imageLoaded }"
      />
    </div>
  </div>
</template>

<script>
export default {
  name: 'ImageSlider',
  props: {
    images: { type: Array, required: true }
  },
  data() {
    return {
      currentIndex: 0,
      imageLoaded: false,    // track first-image load
    };
  },
  mounted() {
    setInterval(() => {
      this.currentIndex = (this.currentIndex + 1) % this.images.length;
    }, 7000);
  },
  methods: {
    onImageLoad(i) {
      // only care about the first image
      if (i === 0) this.imageLoaded = true;
    }
  }
};
</script>

<style scoped>
.slideshow-container {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  overflow: hidden;
}

.slide {
  position: absolute;
  width: 100%; height: 100%;
  opacity: 0;
  transition: opacity 1s ease-in-out;
}

.slide.active {
  opacity: 1;
}

.slider-img {
  width: 100%; height: 100%;
  object-fit: cover;
  opacity: 0;                     /* start hidden */
  transition: opacity 0.5s ease;
}

/* once loaded (either non-first images or first after load) */
.slider-img.is-loaded {
  opacity: 1;
}

/* reuse your skeleton styles */
.skeleton-placeholder {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: linear-gradient(
    to right,
    #e0e0e0 0%,
    #f8f8f8 50%,
    #e0e0e0 100%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  0%   { background-position: -200% 0; }
  100% { background-position: 200%  0; }
}
</style>
