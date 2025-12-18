<template>
  <section
    class="slider"
    :style="{ height }"
    role="region"
    aria-roledescription="carousel"
    :aria-label="ariaLabel"
    @mouseenter="pauseOnHover && stop()"
    @mouseleave="pauseOnHover && start()"
    @touchstart.passive="onTouchStart"
    @touchend.passive="onTouchEnd"
    @keydown.left.prevent="prev"
    @keydown.right.prevent="next"
    tabindex="0"
  >
   
    <div class="track" :style="trackStyle" @transitionend="onTransitionEnd">
      <div
        v-for="(s, i) in slidesExtended"
        :key="i + '-' + (s?.src || i)"
        class="slide"
        :style="{ backgroundImage: `url('${s?.src || ''}')` }"
        role="img"
        :aria-label="s?.alt || ''"
      >
        <a v-if="s?.link" :href="s.link" class="fill-link" aria-label="Open slide"></a>

        <div v-if="s?.caption" class="caption">
          <h3 class="cap-title">{{ s.caption.title }}</h3>
          <p v-if="s.caption.text" class="cap-text">{{ s.caption.text }}</p>
        </div>

        <div class="overlay"></div>
      </div>
    </div>

    <!-- Arrows -->
    <button v-if="showArrows && count > 1" class="nav prev" @click="prev" aria-label="Previous">
      <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
        <path d="M15 6L9 12l6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
    <button v-if="showArrows && count > 1" class="nav next" @click="next" aria-label="Next">
      <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
        <path d="M9 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>

    <!-- Dots -->
  <div v-if="showDots && count > 1" class="dots" aria-hidden="true">
  <button
    v-for="(_, i) in computedImages"
    :key="'dot-' + i"
    :class="['dot', { active: realIndex === i }]"
    @click="goTo(i)"
    :aria-label="'Go to slide ' + (i+1)"
  />
</div>

  </section>
</template>

<script>
export default {
  name: 'ImageSlider',
  props: {
    // Default images (public/images/*)
     images: {
    type: Array,
    default: () => [],
  },

   fallbackImages: {
    type: Array,
    default: () => ([
      {
        src: '/images/asianpaints.png',
        alt: 'Slide 1',
        caption: { title: 'Pro-grade Power Tools', text: 'Makita • Bosch • DeWalt' },
      },
      {
        src: '/images/asianpaints.png',
        alt: 'Slide 2',
        caption: { title: 'Paint & Finishes', text: 'Refresh your space' },
      },
      {
        src: '/images/asianpaints.png',
        alt: 'Slide 3',
        caption: { title: 'Plumbing Essentials', text: 'PVC • GI • Fittings' },
      },
    ]),
  },
    interval: { type: Number, default: 4500 },
    speed:    { type: Number, default: 700 },
    height:   { type: String, default: '420px' },
    pauseOnHover: { type: Boolean, default: true },
    showArrows:   { type: Boolean, default: true },
    showDots:     { type: Boolean, default: true },
    ariaLabel:    { type: String, default: 'Promotional slider' },
  },
  data() {
    return { current: 1, transitioning: true, timer: null, touchStartX: null };
  },
  computed: {

    computedImages() {
      // use backend banners if present, otherwise defaults
      return (this.images && this.images.length)
        ? this.images
        : this.fallbackImages;
    },
    count() { return this.images?.length || 0; },
    slidesExtended() {
      if (!this.count) return [];
      return [ this.images[this.count - 1], ...this.images, this.images[0] ];
    },
    trackStyle() {
      const translate = `translate3d(-${this.current * 100}%, 0, 0)`;
      const transition = this.transitioning
        ? `transform ${this.speed}ms cubic-bezier(0.22, 0.61, 0.36, 1)`
        : 'none';
      return { transform: translate, transition, width: `${this.slidesExtended.length * 100}%` };
    },
    realIndex() {
      if (this.current === 0) return this.count - 1;
      if (this.current === this.count + 1) return 0;
      return this.current - 1;
    },
  },
  mounted() {
    if (this.count > 1) this.start();
    document.addEventListener('visibilitychange', this.onVis, { passive: true });
  },
  beforeUnmount() {
    this.stop();
    document.removeEventListener('visibilitychange', this.onVis);
  },
  methods: {
    start() { this.stop(); this.timer = setInterval(this.next, this.interval); },
    stop()  { if (this.timer) { clearInterval(this.timer); this.timer = null; } },
    next()  { if (this.count > 1) { this.transitioning = true; this.current += 1; } },
    prev()  { if (this.count > 1) { this.transitioning = true; this.current -= 1; } },
    goTo(i) { this.transitioning = true; this.current = i + 1; },
    onTransitionEnd() {
      if (this.current === this.count + 1) {
        this.transitioning = false; this.current = 1;
        requestAnimationFrame(() => { this.transitioning = true; });
      } else if (this.current === 0) {
        this.transitioning = false; this.current = this.count;
        requestAnimationFrame(() => { this.transitioning = true; });
      }
    },
    onTouchStart(e) { this.touchStartX = e.changedTouches?.[0]?.clientX ?? null; if (this.pauseOnHover) this.stop(); },
    onTouchEnd(e) {
      const endX = e.changedTouches?.[0]?.clientX ?? null;
      if (this.touchStartX != null && endX != null) {
        const dx = endX - this.touchStartX;
        if (Math.abs(dx) > 30) (dx < 0 ? this.next() : this.prev());
      }
      if (this.pauseOnHover) this.start();
      this.touchStartX = null;
    },
    onVis() { if (document.hidden) this.stop(); else if (this.count > 1) this.start(); },
  }
};
</script>

<style scoped>
/* Frame */
.slider{
  position:relative; width:100%; overflow:hidden;
  border-radius:14px; border:1px solid rgba(0,0,0,.08);
  box-shadow: 0 12px 28px rgba(0,0,0,.10);
}
.track{ height:100%; display:flex; will-change: transform; }
.slide{ position:relative; flex:0 0 100%; height:100%; background-size:cover; background-position:center; }
.fill-link{ position:absolute; inset:0; }

/* Overlay + caption */
.overlay{ position:absolute; inset:0; background: linear-gradient(180deg, rgba(0,0,0,.10) 0%, rgba(0,0,0,.46) 85%); pointer-events:none; }
.caption{ position:absolute; left:24px; bottom:24px; right:24px; color:#fff; text-shadow:0 2px 12px rgba(0,0,0,.45); }
.cap-title{ font-weight:800; font-size:clamp(18px,3vw,28px); line-height:1.1; margin:0 0 6px; }
.cap-text{ font-size:clamp(12px,2.2vw,15px); margin:0; }

/* Arrows (glass/blur buttons) */
.nav{
  position:absolute; top:50%; transform:translateY(-50%);
  width:44px; height:44px; border-radius:50%;
  border:1px solid rgba(255,255,255,.25);
  background: rgba(15,23,42,.45);
  backdrop-filter: blur(6px);
  color:#fff; display:flex; align-items:center; justify-content:center;
  cursor:pointer; transition: background .18s, transform .12s, box-shadow .18s;
  box-shadow: 0 6px 14px rgba(0,0,0,.25);
}
.nav:hover{ background: rgba(15,23,42,.65); }
.nav:active{ transform: translateY(-50%) scale(.98); }
.nav:focus-visible{ outline: none; box-shadow: 0 0 0 3px rgba(255,255,255,.45); }

.prev{ left:12px; } .next{ right:12px; }

/* Dots (perfect circles, crisp) */
.dots{
  position:absolute; left:0; right:0; bottom:12px;
  display:flex; gap:10px; justify-content:center; align-items:center;
}
.dot{
  width:11px; height:11px; border-radius:50%;
  background: transparent; border:2px solid #ffffffd0;
  box-shadow: 0 1px 2px rgba(0,0,0,.25);
  cursor:pointer; transition: transform .15s, background .15s, border-color .15s;
}
.dot:hover{ transform: scale(1.08); }
.dot.active{ background:#fff; border-color:#fff; transform: scale(1.15); }
.dot:focus-visible{ outline:none; box-shadow: 0 0 0 3px rgba(255,255,255,.4); }

@media (max-width:640px){
  .nav{ width:40px; height:40px; }
  .caption{ left:14px; right:14px; bottom:14px; }
}
@media (prefers-reduced-motion: reduce){ .track{ transition:none !important; } }
</style>
