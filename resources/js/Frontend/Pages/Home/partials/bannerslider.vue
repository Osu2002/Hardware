<template>
  <section
    class="banner-slider"
    ref="viewport"
    :style="viewportInlineStyle"
    tabindex="0"
    role="region"
    aria-label="Image carousel"
    aria-roledescription="carousel"
    @mouseenter="onMouseEnter"
    @mouseleave="onMouseLeave"
    @keydown.left.prevent="prev"
    @keydown.right.prevent="next"
  >
    <!-- Slide Track -->
    <div class="track" :style="trackStyle" aria-live="polite">
      <div
        v-for="(b, i) in safeBanners"
        :key="i"
        class="slide"
        :aria-hidden="i !== currentIndex"
        role="group"
        aria-roledescription="slide"
        :aria-label="`Slide ${i + 1} of ${safeBanners.length}`"
      >
        <!-- Option C: blurred background behind a contain image -->
        <div
          v-if="fitMode === 'contain-blur'"
          class="img-bg"
          :style="bgStyle(b.src)"
          aria-hidden="true"
        />

        <img
          class="slide-img"
          :src="b.src"
          :alt="b.alt || `Banner ${i + 1}`"
          :fetchpriority="i === 0 ? 'high' : 'auto'"
          :loading="i === 0 ? 'eager' : 'lazy'"
          decoding="async"
          draggable="false"
          :style="imgInlineStyle"
        />
      </div>
    </div>

    <!-- Arrows -->
    <!-- <button
      v-if="showArrows && safeBanners.length > 1"
      class="arrow arrow--prev"
      type="button"
      aria-label="Previous slide"
      @click="prev"
    >
      <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
        <path
          d="M15 18l-6-6 6-6"
          fill="none"
          stroke="currentColor"
          stroke-width="2.25"
          stroke-linecap="round"
          stroke-linejoin="round"
        />
      </svg>
    </button>

    <button
      v-if="showArrows && safeBanners.length > 1"
      class="arrow arrow--next"
      type="button"
      aria-label="Next slide"
      @click="next"
    >
      <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
        <path
          d="M9 6l6 6-6 6"
          fill="none"
          stroke="currentColor"
          stroke-width="2.25"
          stroke-linecap="round"
          stroke-linejoin="round"
        />
      </svg>
    </button> -->

    <!-- Dots -->
    <div
      v-if="showDots && safeBanners.length > 1"
      class="dots"
      role="tablist"
      aria-label="Slide navigation"
    >
      <button
        v-for="(_, i) in safeBanners"
        :key="`dot-${i}`"
        type="button"
        class="dot"
        :class="{ active: i === currentIndex }"
        role="tab"
        :aria-selected="i === currentIndex"
        :aria-label="`Go to slide ${i + 1}`"
        @click="goTo(i)"
      />
    </div>
  </section>
</template>

<script>
export default {
  name: "BannerSlider",
  props: {
    banners: { type: Array, default: () => [] },

    /**
     * FIT OPTIONS:
     * - "cover"        => fills container, MAY CROP (no empty space)
     * - "contain"      => NO CROP, may letterbox (empty space)
     * - "contain-blur" => NO CROP, letterbox replaced by blurred background
     */
    fitMode: {
      type: String,
      default: "contain-blur",
      validator: (v) => ["cover", "contain", "contain-blur"].includes(v),
    },

    /**
     * ASPECT OPTIONS:
     * - forceAspect = true  => container uses CSS aspect-ratio (best for “true 16:9”)
     * - forceAspect = false => JS height calc + clamp (can break 16:9 when clamped)
     */
    forceAspect: { type: Boolean, default: true },
    aspectW: { type: Number, default: 16 },
    aspectH: { type: Number, default: 9 },

    // Only used when forceAspect = false (JS clamp mode)
    maxHeight: { type: Number, default: 520 },
    minHeight: { type: Number, default: 260 },

    // Letterbox background (shows for "contain", also used under blur layer)
    letterboxColor: { type: String, default: "#ffffff" },

    // Blur controls (only for "contain-blur")
    blurPx: { type: Number, default: 16 },
    blurScale: { type: Number, default: 1.08 },

    autoplay: { type: Boolean, default: true },
    intervalMs: { type: Number, default: 4500 },
    pauseOnHover: { type: Boolean, default: true },
    showArrows: { type: Boolean, default: true },
    showDots: { type: Boolean, default: true },
  },
  data() {
    return {
      currentIndex: 0,
      timer: null,
      isHovering: false,

      // only needed for JS clamp mode
      viewportWidth: 0,
      resizeObserver: null,

      reducedMotion: false,
      _mq: null,
      _mqChange: null,
    };
  },
  computed: {
    safeBanners() {
      return Array.isArray(this.banners)
        ? this.banners.filter((b) => b && b.src)
        : [];
    },
    trackStyle() {
      const x = this.currentIndex * 100;
      return { transform: `translateX(-${x}%)` };
    },

    // image fit behavior
    imgFit() {
      return this.fitMode === "cover" ? "cover" : "contain";
    },
    imgInlineStyle() {
      return {
        objectFit: this.imgFit,
      };
    },

    viewportInlineStyle() {
      // Always include CSS vars (letterbox + aspect ratio)
      const vars = {
        "--letterbox": this.letterboxColor,
        "--aspect-w": String(this.aspectW),
        "--aspect-h": String(this.aspectH),
      };

      // If CSS aspect-ratio is enabled, do NOT force height inline.
      if (this.forceAspect) return vars;

      // JS clamp mode (may break 16:9 when clamped)
      const width = this.viewportWidth || 0;
      const aspectRatio = this.aspectW / this.aspectH;

      let h = this.maxHeight;
      if (width > 0) {
        h = width / aspectRatio;
        h = Math.max(this.minHeight, Math.min(this.maxHeight, h));
      } else {
        h = Math.max(this.minHeight, Math.min(this.maxHeight, this.maxHeight));
      }

      return { ...vars, height: `${Math.round(h)}px` };
    },
  },
  watch: {
    safeBanners(newVal) {
      if (this.currentIndex >= newVal.length) this.currentIndex = 0;
      this.restartAutoplay();
    },
    autoplay() {
      this.restartAutoplay();
    },
    intervalMs() {
      this.restartAutoplay();
    },
    reducedMotion() {
      this.restartAutoplay();
    },
    forceAspect(newVal) {
      // toggle resize observer depending on mode
      if (newVal) this.teardownResizeObserver();
      else this.setupResizeObserver();
    },
  },
  mounted() {
    this.setupReducedMotion();
    if (!this.forceAspect) this.setupResizeObserver();
    this.startAutoplay();
  },
  beforeUnmount() {
    this.stopAutoplay();
    this.teardownResizeObserver();
    this.teardownReducedMotion();
  },
  methods: {
    // background blur style for contain-blur
    bgStyle(src) {
      return {
        backgroundImage: `url("${src}")`,
        filter: `blur(${this.blurPx}px)`,
        transform: `scale(${this.blurScale})`,
      };
    },

    setupReducedMotion() {
      if (typeof window === "undefined" || !window.matchMedia) return;
      const mq = window.matchMedia("(prefers-reduced-motion: reduce)");
      this._mq = mq;
      this.reducedMotion = !!mq.matches;

      const onChange = (e) => {
        this.reducedMotion = !!e.matches;
      };

      if (mq.addEventListener) mq.addEventListener("change", onChange);
      else if (mq.addListener) mq.addListener(onChange);

      this._mqChange = onChange;
    },
    teardownReducedMotion() {
      const mq = this._mq;
      const onChange = this._mqChange;
      if (!mq || !onChange) return;

      if (mq.removeEventListener) mq.removeEventListener("change", onChange);
      else if (mq.removeListener) mq.removeListener(onChange);

      this._mq = null;
      this._mqChange = null;
    },

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
        this.viewportWidth = el.clientWidth || 0;
      });
    },
    teardownResizeObserver() {
      if (this.resizeObserver) {
        this.resizeObserver.disconnect();
        this.resizeObserver = null;
      }
    },

    startAutoplay() {
      if (!this.autoplay) return;
      if (this.reducedMotion) return;
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
      if (this.pauseOnHover) this.isHovering = true;
    },
    onMouseLeave() {
      if (this.pauseOnHover) this.isHovering = false;
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
  border-radius: 18px;
  background: var(--letterbox, #ffffff);
  box-shadow: 0 14px 40px rgba(0, 0, 0, 0.14);
  outline: none;

  /* Aspect option (enabled by forceAspect=true) */
  aspect-ratio: calc(var(--aspect-w, 16) / var(--aspect-h, 9));

  /* You can keep this if you want it not too tall on large screens */
  max-height: 520px;
}

.banner-slider:focus-visible {
  box-shadow: 0 14px 40px rgba(0, 0, 0, 0.14), 0 0 0 3px rgba(59, 130, 246, 0.35);
}

.track {
  height: 100%;
  display: flex;
  will-change: transform;
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

@media (prefers-reduced-motion: reduce) {
  .track {
    transition: none;
  }
}

.slide {
  min-width: 100%;
  height: 100%;
  position: relative;
  user-select: none;
  background: var(--letterbox, #ffffff);
}

/* Option C: blur background */
.img-bg {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  opacity: 0.95;
  pointer-events: none;
}

/* Foreground image */
.slide-img {
  width: 100%;
  height: 100%;
  /* object-fit is controlled inline via :style for all modes */
  -webkit-user-drag: none;
  user-select: none;
  display: block;
  position: relative; /* keep above blur */
}

.arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 56px;
  height: 56px;
  border: none;
  border-radius: 999px;
  cursor: pointer;
  display: grid;
  place-items: center;
  background: rgba(255, 255, 255, 0.95);
  box-shadow: 0 10px 24px rgba(0, 0, 0, 0.22);
  transition: transform 140ms ease, background 140ms ease;
  color: #111827;
}

.arrow svg {
  width: 22px;
  height: 22px;
}

.arrow:hover {
  background: #ffffff;
  transform: translateY(-50%) scale(1.05);
}

.arrow:active {
  transform: translateY(-50%) scale(0.98);
}

.arrow--prev {
  left: 22px;
}

.arrow--next {
  right: 22px;
}

.dots {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 16px;
  display: flex;
  justify-content: center;
  gap: 10px;
  padding: 0 16px;
}

.dot {
  width: 10px;
  height: 10px;
  border: none;
  border-radius: 999px;
  cursor: pointer;
  background: rgba(255, 255, 255, 0.55);
  box-shadow: 0 6px 14px rgba(0, 0, 0, 0.18);
  transition: width 180ms ease, transform 180ms ease, background 180ms ease;
}

.dot.active {
  width: 34px;
  background: rgba(255, 255, 255, 0.95);
  transform: scale(1.02);
}

.dot:focus-visible {
  outline: none;
  box-shadow: 0 6px 14px rgba(0, 0, 0, 0.18), 0 0 0 3px rgba(59, 130, 246, 0.35);
}
</style>
