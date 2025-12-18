<template>
  <section class="catnav-section" v-if="normalizedCategories.length">
    <article
      v-for="category in normalizedCategories"
      :key="category.id"
      class="catnav-block"
    >
      <div class="catnav-header">
        <h2 class="catnav-heading">{{ category.title }}</h2>

        <div class="catnav-arrows" v-if="shouldShowArrows(category)">
          <button
            type="button"
            class="nav-btn"
            @click="prevSlide(category)"
            :disabled="atStart(category)"
            aria-label="Previous"
            title="Previous"
          >
            «
          </button>
          <button
            type="button"
            class="nav-btn"
            @click="nextSlide(category)"
            :disabled="atEnd(category)"
            aria-label="Next"
            title="Next"
          >
            »
          </button>
        </div>
      </div>

      <div class="catnav-slider">
        <div
          class="slider-viewport"
          :ref="(el) => setViewportRef(category.id, el)"
        >
          <div class="slider-track">
            <article
              v-for="product in displayProducts(category)"
              :key="product.id"
              class="product-card"
              @click="goToProduct(product.slug)"
            >
              <div class="card-inner">
                <div class="card-image-wrapper">
                  <!-- <span v-if="product.is_new" class="badge-new">NEW</span> -->

                  <!-- DISCOUNT BADGE -->
                  <span
                    v-if="hasDiscount(product)"
                    class="badge-discount"
                    :title="`Discount: ${discountPercent(product)}%`"
                  >
                    -{{ discountPercent(product) }}%
                  </span>

                  <img
                    :src="product.image"
                    :alt="product.name"
                    class="card-image"
                    loading="lazy"
                  />
                </div>

                <div class="card-body">
                  <h3 class="card-title" :title="product.name">
                    {{ truncateName(product.name) }}
                  </h3>

                  <div class="card-price-row">
                    <div class="card-price-stack">
                      <!-- OLD PRICE -->
                      <del v-if="hasDiscount(product)" class="card-old-price">
                        Rs{{ formatPrice(basePrice(product)) }}
                      </del>

                      <!-- NEW / CURRENT PRICE -->
                      <div class="card-price">
                        Rs{{ formatPrice(currentPrice(product)) }}
                      </div>
                    </div>
                  </div>
                </div>

                <div class="card-footer">
                  <!-- HEART BUTTON (LEFT) -->
                  <button
                    type="button"
                    class="btn-heart"
                    :class="{ active: isWished(product) }"
                    @click.stop="toggleWish(product)"
                    aria-label="Add to wishlist"
                    title="Wishlist"
                  >
                    <svg viewBox="0 0 24 24" class="heart-icon" aria-hidden="true">
                      <path
                        d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
                      />
                    </svg>
                  </button>

                  <button
                    type="button"
                    class="btn-buy"
                    @click.stop="goToProduct(product.slug)"
                  >
                    BUY
                  </button>
                </div>
              </div>
            </article>
          </div>
        </div>
      </div>
    </article>
  </section>
</template>

<script>
export default {
  name: 'CategoryNav',

  props: {
    categories: {
      type: Array,
      default: () => [],
    },
  },

  data() {
    return {
      maxPerCategory: 8,
      wished: new Set(),

      // viewport + scroll state per category
      viewportRefs: {}, // { [categoryId]: HTMLElement }
      scrollMeta: {}, // { [categoryId]: { canScroll, atStart, atEnd } }

      // mobile flag (also backed by CSS media query)
      isMobile: false,

      // internals
      _mql: null,
      _mqlHandler: null,
      _scrollRaf: {}, // { [categoryId]: rafId }
      _scrollHandlers: {}, // { [categoryId]: fn }
    };
  },

  computed: {
    normalizedCategories() {
      return (this.categories || []).filter((cat) => {
        return Array.isArray(cat.products) && cat.products.length > 0;
      });
    },
  },

  watch: {
    categories: {
      handler() {
        this.$nextTick(() => this.refreshAllScrollMeta());
      },
      immediate: true,
    },
  },

  mounted() {
    this.setupMobileWatcher();
    this.$nextTick(() => this.refreshAllScrollMeta());

    // keep arrow state accurate on resize (card basis changes with breakpoints)
    window.addEventListener('resize', this.refreshAllScrollMeta, { passive: true });
  },

  beforeUnmount() {
    this.teardown();
  },

  // Vue 2 fallback
  beforeDestroy() {
    this.teardown();
  },

  methods: {
    // ---- reactive helpers (Vue 2 + Vue 3 safe) ----
    _set(obj, key, val) {
      if (this.$set) this.$set(obj, key, val);
      else obj[key] = val;
    },
    _del(obj, key) {
      if (this.$delete) this.$delete(obj, key);
      else delete obj[key];
    },

    // ---- category products ----
    displayProducts(category) {
      const list = Array.isArray(category.products) ? category.products : [];
      return list.slice(0, this.maxPerCategory);
    },

    // ---- viewport refs (required approach) ----
    setViewportRef(categoryId, el) {
      const id = String(categoryId);

      // unmount / v-if cleanup
      if (!el) {
        const prev = this.viewportRefs[id];
        if (prev && this._scrollHandlers[id]) {
          prev.removeEventListener('scroll', this._scrollHandlers[id]);
        }
        this._del(this.viewportRefs, id);
        this._del(this.scrollMeta, id);
        delete this._scrollHandlers[id];
        if (this._scrollRaf[id]) cancelAnimationFrame(this._scrollRaf[id]);
        delete this._scrollRaf[id];
        return;
      }

      // ignore same element
      if (this.viewportRefs[id] === el) return;

      // detach old
      const prev = this.viewportRefs[id];
      if (prev && this._scrollHandlers[id]) {
        prev.removeEventListener('scroll', this._scrollHandlers[id]);
      }

      // store ref
      this._set(this.viewportRefs, id, el);

      // attach scroll listener (throttled with rAF)
      const onScroll = () => {
        if (this._scrollRaf[id]) return;
        this._scrollRaf[id] = requestAnimationFrame(() => {
          this._scrollRaf[id] = null;
          this.updateScrollMeta(id);
        });
      };
      this._scrollHandlers[id] = onScroll;
      el.addEventListener('scroll', onScroll, { passive: true });

      // initial state
      this.$nextTick(() => this.updateScrollMeta(id));
    },

    refreshAllScrollMeta() {
      (this.normalizedCategories || []).forEach((cat) => {
        this.updateScrollMeta(String(cat.id));
      });
    },

    updateScrollMeta(categoryId) {
      const id = String(categoryId);
      const el = this.viewportRefs[id];
      if (!el) return;

      const maxLeft = Math.max(0, el.scrollWidth - el.clientWidth);
      const left = el.scrollLeft;

      // small thresholds for fractional scrollLeft / snap
      const EPS = 2;

      const canScroll = maxLeft > EPS;
      const atStart = !canScroll || left <= EPS;
      const atEnd = !canScroll || left >= maxLeft - EPS;

      this._set(this.scrollMeta, id, { canScroll, atStart, atEnd });
    },

    // ---- arrows + scrolling ----
    shouldShowArrows(category) {
      if (this.isMobile) return false;

      const id = String(category.id);
      const meta = this.scrollMeta[id];

      // fallback heuristic until meta is computed
      if (!meta) return this.displayProducts(category).length > 5;

      return !!meta.canScroll;
    },

    atStart(category) {
      const meta = this.scrollMeta[String(category.id)];
      return meta ? meta.atStart : true;
    },

    atEnd(category) {
      const meta = this.scrollMeta[String(category.id)];
      return meta ? meta.atEnd : true;
    },

    nextSlide(category) {
      const el = this.viewportRefs[String(category.id)];
      if (!el) return;

      const delta = Math.max(220, Math.floor(el.clientWidth * 0.9));
      el.scrollBy({ left: delta, behavior: 'smooth' });
    },

    prevSlide(category) {
      const el = this.viewportRefs[String(category.id)];
      if (!el) return;

      const delta = Math.max(220, Math.floor(el.clientWidth * 0.9));
      el.scrollBy({ left: -delta, behavior: 'smooth' });
    },

    // ---- mobile watcher (optional, plus CSS hides arrows too) ----
    setupMobileWatcher() {
      if (typeof window === 'undefined' || !window.matchMedia) return;

      this._mql = window.matchMedia('(max-width: 768px)');
      const apply = (matches) => {
        this.isMobile = !!matches;
        this.$nextTick(() => this.refreshAllScrollMeta());
      };

      apply(this._mql.matches);

      this._mqlHandler = (e) => apply(e.matches);

      if (this._mql.addEventListener) this._mql.addEventListener('change', this._mqlHandler);
      else if (this._mql.addListener) this._mql.addListener(this._mqlHandler);
    },

    teardown() {
      window.removeEventListener('resize', this.refreshAllScrollMeta);

      if (this._mql && this._mqlHandler) {
        if (this._mql.removeEventListener) this._mql.removeEventListener('change', this._mqlHandler);
        else if (this._mql.removeListener) this._mql.removeListener(this._mqlHandler);
      }

      Object.keys(this.viewportRefs || {}).forEach((id) => {
        const el = this.viewportRefs[id];
        const fn = this._scrollHandlers[id];
        if (el && fn) el.removeEventListener('scroll', fn);
      });

      Object.keys(this._scrollRaf || {}).forEach((id) => {
        if (this._scrollRaf[id]) cancelAnimationFrame(this._scrollRaf[id]);
      });
    },

    // ---------- DISCOUNT LOGIC ----------
    discountPercent(product) {
      const p = Number(product?.discount_percent);
      if (Number.isFinite(p) && p > 0) return Math.round(p);

      const oldP = Number(product?.regular_price);
      const nowP = Number(product?.price);
      if (
        Number.isFinite(oldP) &&
        oldP > 0 &&
        Number.isFinite(nowP) &&
        nowP > 0 &&
        nowP < oldP
      ) {
        return Math.round(((oldP - nowP) / oldP) * 100);
      }

      return 0;
    },

    basePrice(product) {
      return Number(product?.regular_price ?? 0) || 0;
    },

    currentPrice(product) {
      return Number(product?.price ?? 0) || 0;
    },

    hasDiscount(product) {
      const base = this.basePrice(product);
      const now = this.currentPrice(product);
      return base > 0 && now > 0 && now < base && this.discountPercent(product) > 0;
    },

    // ---------- UI HELPERS ----------
    truncateName(name) {
      const s = String(name ?? '');
      if (s.length <= 20) return s;
      return s.slice(0, 20) + '...';
    },

    formatPrice(value) {
      if (value == null) return '';
      return Number(value).toLocaleString('en-LK', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },

    toggleWish(product) {
      const id = product?.id;
      if (id == null) return;

      const next = new Set(this.wished);
      if (next.has(id)) next.delete(id);
      else next.add(id);

      this.wished = next;
    },

    isWished(product) {
      const id = product?.id;
      return id != null && this.wished.has(id);
    },

    goToProduct(slug) {
      this.$inertia.visit(`/products/${slug}`);
    },
  },
};
</script>

<style scoped>
.catnav-section {
  margin: 50px 0;
}

.catnav-block {
  margin-bottom: 40px;
}

.catnav-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 18px;
}
/* EXACT same style as CategoryBox .section-title */
.catnav-heading {
  font-size: 1.2rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  color: var(--nav-text, #12355a);
}

.catnav-arrows {
  display: flex;
  align-items: center;
  gap: 6px;
}

.nav-btn {
  border: none;
  background: #e5e7eb;
  width: 28px;
  height: 28px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  cursor: pointer;
  transition: background 0.15s ease, transform 0.15s ease;
}

.nav-btn:hover:not(:disabled) {
  background: #d1d5db;
  transform: translateY(-1px);
}

.nav-btn:disabled {
  opacity: 0.4;
  cursor: default;
}

.catnav-slider {
  position: relative;

  /* responsive basis via CSS only (no translate math) */
  --gap: 12px;
  --card-basis: calc((100% - (var(--gap) * 4)) / 5); /* ~5 visible */
}

/* REQUIRED: real scroll container + snap + momentum */
.slider-viewport {
  overflow-x: auto;
  overflow-y: hidden;
  width: 100%;
  padding: 6px 0;
  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
  scroll-behavior: smooth;
  touch-action: pan-x;
  overscroll-behavior-x: contain;

  /* makes end-of-list fully reachable + nicer snap offsets */
  scroll-padding-left: 6px;
  scroll-padding-right: 6px;

  /* keep UI clean while still scrollable */
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE/Edge legacy */
}

.slider-viewport::-webkit-scrollbar {
  display: none; /* Chrome/Safari */
}

.slider-track {
  display: flex;
  gap: var(--gap);
  padding: 0 6px; /* ensures last/first card can snap fully into view */
}

/* card */
.product-card {
  flex: 0 0 var(--card-basis);
  box-sizing: border-box;
  cursor: pointer;

  scroll-snap-align: start;
}

.card-inner {
  background: #ffffff;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  min-height: 100%;
  transition: transform 0.18s ease, box-shadow 0.18s ease;
}

.product-card:hover .card-inner {
  transform: translateY(-2px);
}

.card-image-wrapper {
  position: relative;
  padding-top: 70%;
  background: #f9fafb;
  overflow: hidden;
}

.card-image {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: contain;
  background: #ffffff;
  transition: transform 0.28s ease;
}

.product-card:hover .card-image {
  transform: scale(1.08);
}

.badge-new {
  position: absolute;
  left: 8px;
  top: 8px;
  background: #1d4ed8;
  color: #ffffff;
  font-size: 0.7rem;
  font-weight: 700;
  padding: 3px 7px;
  border-radius: 4px;
  z-index: 2;
}

/* discount badge (circle) */
.badge-discount {
  position: absolute;
  right: 8px;
  top: 8px;
  width: 44px;
  height: 44px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: #d51010;
  color: #ffffff;
  font-weight: 800;
  font-size: 0.78rem;
  z-index: 2;
  box-shadow: 0 6px 14px rgba(239, 68, 68, 0.25);
}

.card-body {
  padding: 10px 10px 6px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

/* ===== Typography to match SpecialOffers / TopNavbar (mitem style) ===== */
.catnav-section {
  font-family: inherit;
}

/* headings + key text use navbar vibe */
.catnav-heading,
.card-title,
.btn-buy {
  /* text-transform: uppercase; */
  letter-spacing: 0.04em;
  font-weight: 500;
}

/* heading a bit more premium */
.catnav-heading {
  letter-spacing: 0.06em;
  font-weight: 500;
}

/* EXACT same style as CategoryBox .section-title */
.catnav-heading {
  font-size: 1.2rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  color: var(--nav-text, #12355a);
}


/* product title like menu text */
.card-title {
  font-size: 13px; /* similar to navbar */
}

/* price weights consistent */
.card-price {
  font-weight: 500;
}
.card-old-price {
  font-weight: 500;
}


.card-title {
  font-size: 0.85rem;
  font-weight: 500;
  color: #111827;
  text-align: center;
  line-height: 1.3;
  min-height: 2.8em;
}

.card-price-row {
  margin-top: 6px;
  text-align: center;
}

.card-price-stack {
  display: inline-flex;
  flex-direction: column;
  gap: 2px;
  align-items: center;
}

/* OLD PRICE (keep intact) */
.card-old-price {
  position: relative;
  display: inline-block;
  color: #9ca3af;
  font-weight: 500;
  font-size: 0.82rem;
}

.card-old-price::after {
  content: "";
  position: absolute;
  left: 0;
  right: 0;
  top: 50%;
  height: 2px;
  background: currentColor;
  transform: translateY(-50%);
}

.card-price {
  color: #4c1d95;
  font-weight: 500;
  font-size: 0.92rem;
}

.card-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  padding: 8px 10px 10px;
  border-top: 1px solid #e5e7eb;
}

.btn-heart {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  background: #ffffff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: transform 0.15s ease, border-color 0.15s ease, background 0.15s ease;
}

.btn-heart:hover {
  transform: translateY(-1px);
  border-color: #fca5a5;
  background: #fff1f2;
}

.btn-heart.active {
  border-color: #ef4444;
  background: #fee2e2;
}

.heart-icon {
  width: 18px;
  height: 18px;
  display: block;
}

/* default = outline heart */
.btn-heart {
  color: #9ca3af;
}

.btn-heart .heart-icon {
  fill: none;
  stroke: currentColor;
  stroke-width: 2;
}

/* active = filled heart */
.btn-heart.active {
  color: #ef4444;
}

.btn-heart.active .heart-icon {
  fill: currentColor;
  stroke: none;
}

.btn-buy {
  background: #071c61;
  color: #ffffff;
  border: none;
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.15s ease, transform 0.15s ease;
}

.btn-buy:hover {
  background: #1165b8b2;
  transform: translateY(-4px);
}

/* tablet: ~3 visible */
@media (max-width: 1024px) {
  .catnav-slider {
    --card-basis: calc((100% - (var(--gap) * 2)) / 3);
  }
}

/* mobile: hide arrows + smaller cards + swipe + snap */
@media (max-width: 768px) {
  .catnav-arrows {
    display: none !important;
  }

  .catnav-slider {
    --gap: 10px;
    --card-basis: calc((100% - var(--gap)) / 2); /* ~2 visible */
  }

  .product-card {
    scroll-snap-stop: always;
  }
}

/* very small: 1 visible */
@media (max-width: 480px) {
  .catnav-slider {
    --card-basis: 100%;
  }
}

/* mobile: smaller cards + swipe + snap */
@media (max-width: 768px) {
  .catnav-arrows {
    display: none !important;
  }

  .catnav-slider {
    --gap: 8px;

    /* keep ~2 visible but make them feel smaller (slightly narrower + tighter spacing) */
    --card-basis: clamp(150px, 44vw, 185px);
  }

  .slider-track {
    padding: 0 8px;
  }

  .product-card {
    scroll-snap-stop: always;
  }

  /* tighten vertical size */
  .card-image-wrapper {
    padding-top: 62%;
  }

  .card-body {
    padding: 8px 8px 4px;
  }

  .card-title {
    font-size: 0.78rem;
    min-height: 2.4em;
  }

  .card-price {
    font-size: 0.86rem;
  }

  .card-old-price {
    font-size: 0.72rem;
  }

  .card-footer {
    padding: 6px 8px 8px;
    gap: 8px;
  }

  .btn-heart {
    width: 30px;
    height: 30px;
    border-radius: 9px;
  }

  .heart-icon {
    width: 16px;
    height: 16px;
  }

  .btn-buy {
    padding: 5px 10px;
    border-radius: 8px;
    font-size: 0.75rem;
  }

  .badge-discount {
    width: 38px;
    height: 38px;
    font-size: 0.72rem;
    right: 6px;
    top: 6px;
  }
}

/* very small: still effectively 1 at a time, but not oversized */
@media (max-width: 480px) {
  .catnav-slider {
    --card-basis: clamp(180px, 78vw, 260px);
  }
}

</style>
