<template>
  <section
    class="banner-slider"
    ref="viewport"
    :style="viewportInlineStyle"
    tabindex="0"
    role="region"
    aria-label="Banner slider"
    @mouseenter="onMouseEnter"
    @mouseleave="onMouseLeave"
    @keydown.left.prevent="prev"
    @keydown.right.prevent="next"
  >
    <div class="track" :style="trackStyle" aria-live="polite">
      <div
        v-for="(b, i) in safeBanners"
        :key="i"
        class="slide"
        :aria-hidden="i !== currentIndex"
      >
        <img
          class="slide-img"
          :src="b.src"
          :alt="b.alt || `Banner ${i + 1}`"
          :fetchpriority="i === 0 ? 'high' : 'auto'"
          loading="eager"
          decoding="async"
          draggable="false"
          @load="onImgLoad(i, $event)"
        />

        <div v-if="b.caption?.title || b.caption?.text" class="caption" aria-hidden="true">
          <div v-if="b.caption?.title" class="caption-title">{{ b.caption.title }}</div>
          <div v-if="b.caption?.text" class="caption-text">{{ b.caption.text }}</div>
        </div>
      </div>
    </div>

    <!-- Arrows -->
    <button
      v-if="showArrows && safeBanners.length > 1"
      class="nav nav-left"
      type="button"
      aria-label="Previous banner"
      @click="prev"
    >
      ‹
    </button>

    <button
      v-if="showArrows && safeBanners.length > 1"
      class="nav nav-right"
      type="button"
      aria-label="Next banner"
      @click="next"
    >
      ›
    </button>

    <!-- Dots -->
    <div v-if="showDots && safeBanners.length > 1" class="dots" aria-label="Banner navigation">
      <button
        v-for="(_, i) in safeBanners"
        :key="`dot-${i}`"
        class="dot"
        type="button"
        :class="{ active: i === currentIndex }"
        :aria-label="`Go to banner ${i + 1}`"
        :aria-current="i === currentIndex ? 'true' : 'false'"
        @click="goTo(i)"
      />
    </div>
  </section>
</template>

<script>
export default {
  name: "BannerSlider",
  props: {
    banners: {
      type: Array,
      default: () => [],
    },
    autoplay: {
      type: Boolean,
      default: true,
    },
    intervalMs: {
      type: Number,
      default: 4500,
    },
    pauseOnHover: {
      type: Boolean,
      default: true,
    },
    showArrows: {
      type: Boolean,
      default: true,
    },
    showDots: {
      type: Boolean,
      default: true,
    },
    /**
     * Optional max height in px (keeps very tall images from taking the whole page).
     * Set to null to disable.
     */
    maxHeight: {
      type: Number,
      default: 520,
    },
    /**
     * Minimum height before images load (prevents layout collapse).
     */
    minHeight: {
      type: Number,
      default: 260,
    },
  },
  data() {
    return {
      currentIndex: 0,
      timer: null,
      isHovering: false,

      // aspect ratios (w/h) per slide index
      ratios: {},

      // viewport width (used to compute stable height)
      viewportWidth: 0,
      resizeObserver: null,
    };
  },
  computed: {
    safeBanners() {
      return Array.isArray(this.banners)
        ? this.banners.filter(b => b && b.src)
        : [];
    },
    trackStyle() {
      const x = this.currentIndex * 100;
      return {
        transform: `translateX(-${x}%)`,
      };
    },
    viewportInlineStyle() {
      // Compute a stable height from the largest (scaled) image height:
      // height = viewportWidth / (w/h) = viewportWidth / ratio
      const width = this.viewportWidth || 0;
      const ratios = Object.values(this.ratios).filter(r => r && r > 0);

      let h = this.minHeight;

      if (width > 0 && ratios.length > 0) {
        const heights = ratios.map(r => width / r);
        h = Math.max(...heights, this.minHeight);
      }

      if (this.maxHeight && this.maxHeight > 0) {
        h = Math.min(h, this.maxHeight);
      }

      return {
        height: `${Math.round(h)}px`,
      };
    },
  },
  watch: {
    safeBanners(newVal) {
      // keep index valid
      if (this.currentIndex >= newVal.length) this.currentIndex = 0;

      // restart autoplay if needed
      this.restartAutoplay();
    },
    autoplay() {
      this.restartAutoplay();
    },
    intervalMs() {
      this.restartAutoplay();
    },
  },
  mounted() {
    this.setupResizeObserver();
    this.startAutoplay();
  },
  beforeUnmount() {
    this.stopAutoplay();
    this.teardownResizeObserver();
  },
  methods: {
    setupResizeObserver() {
      this.$nextTick(() => {
        const el = this.$refs.viewport;
        if (!el || typeof ResizeObserver === "undefined") return;

        this.resizeObserver = new ResizeObserver((entries) => {
          const entry = entries[0];
          const w = entry?.contentRect?.width || el.clientWidth || 0;
          this.viewportWidth = w;
        });

        this.resizeObserver.observe(el);

        // initial width
        this.viewportWidth = el.clientWidth || 0;
      });
    },
    teardownResizeObserver() {
      if (this.resizeObserver) {
        this.resizeObserver.disconnect();
        this.resizeObserver = null;
      }
    },
    onImgLoad(index, evt) {
      const img = evt?.target;
      if (!img) return;

      const w = img.naturalWidth || 0;
      const h = img.naturalHeight || 0;
      if (w > 0 && h > 0) {
        this.$set ? this.$set(this.ratios, index, w / h) : (this.ratios[index] = w / h);
      }
    },
    startAutoplay() {
      if (!this.autoplay) return;
      if (this.safeBanners.length <= 1) return;

      this.stopAutoplay();
      this.timer = window.setInterval(() => {
        if (this.pauseOnHover && this.isHovering) return;
        this.next();
      }, Math.max(1200, this.intervalMs));
    },
    stopAutoplay() {
      if (this.timer) {
        window.clearInterval(this.timer);
        this.timer = null;
      }
    },
    restartAutoplay() {
      this.stopAutoplay();
      this.startAutoplay();
    },
    onMouseEnter() {
      this.isHovering = true;
    },
    onMouseLeave() {
      this.isHovering = false;
    },
    next() {
      const n = this.safeBanners.length;
      if (n <= 1) return;
      this.currentIndex = (this.currentIndex + 1) % n;
    },
    prev() {
      const n = this.safeBanners.length;
      if (n <= 1) return;
      this.currentIndex = (this.currentIndex - 1 + n) % n;
    },
    goTo(i) {
      if (i < 0 || i >= this.safeBanners.length) return;
      this.currentIndex = i;
    },
  },
};
</script>

<style scoped>
.banner-slider {
  position: relative;
  width: 100%;
  overflow: hidden;
  border-radius: 14px;
  background: #f6f7f9;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
  outline: none;
}

.track {
  height: 100%;
  display: flex;
  transition: transform 420ms ease;
  will-change: transform;
}

.slide {
  min-width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  user-select: none;
}

.slide-img {
  width: 100%;
  height: 100%;
  object-fit: cover; /* FULL IMAGE, NO CROPPING */
  image-rendering: auto;
  -webkit-user-drag: none;
}

.caption {
  position: absolute;
  left: 14px;
  bottom: 12px;
  max-width: min(80%, 680px);
  padding: 10px 12px;
  border-radius: 12px;
  background: rgba(0, 0, 0, 0.55);
  color: #fff;
  backdrop-filter: blur(2px);
}

.caption-title {
  font-weight: 800;
  font-size: 16px;
  line-height: 1.2;
}

.caption-text {
  margin-top: 4px;
  font-size: 13px;
  line-height: 1.35;
  opacity: 0.95;
}

.nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 44px;
  height: 44px;
  border: none;
  border-radius: 999px;
  cursor: pointer;
  font-size: 28px;
  line-height: 44px;
  text-align: center;
  background: rgba(255, 255, 255, 0.75);
  box-shadow: 0 8px 18px rgba(0, 0, 0, 0.18);
  transition: transform 120ms ease, background 120ms ease;
}

.nav:hover {
  background: rgba(255, 255, 255, 0.92);
  transform: translateY(-50%) scale(1.04);
}

.nav-left {
  left: 12px;
}

.nav-right {
  right: 12px;
}

.dots {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 10px;
  display: flex;
  justify-content: center;
  gap: 8px;
  padding: 0 12px;
}

.dot {
  width: 10px;
  height: 10px;
  border-radius: 999px;
  border: none;
  cursor: pointer;
  background: rgba(0, 0, 0, 0.25);
  transition: transform 120ms ease, background 120ms ease;
}

.dot.active {
  background: rgba(0, 0, 0, 0.7);
  transform: scale(1.15);
}
</style>
