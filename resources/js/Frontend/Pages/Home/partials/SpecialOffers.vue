<template>
  <section class="special-offers" v-if="offers.length">
    <div class="special-offers-inner">
      <!-- LEFT STATIC PANEL -->
      <div class="special-panel">
        <span class="special-badge">Limited Time</span>

        <h2 class="special-title">
          EXCLUSIVE <br />
          DEALS
        </h2>

        <button class="special-cta">
         GRAB IT NOW
        </button>
      </div>

      <!-- LITTLE ARROW BETWEEN PANELS -->
      <div class="divider-arrow"></div>

      <!-- RIGHT SLIDER -->
      <div class="offers-slider">
        <button
          class="nav-btn prev"
          type="button"
          @click="prev"
          :disabled="atStart"
        >
          ‹
        </button>

       <div class="offers-viewport" ref="viewport" @scroll.passive="onViewportScroll">
  <div class="offers-track">

           <div
  v-for="offer in offers"
  :key="offer.id"
  class="offer-card"
  @click="goToProduct(offer.slug)"  
>

              <div class="offer-image-wrapper">
                <!-- DISCOUNT BADGE -->
                <span
                  v-if="discountBadgeFor(offer)"
                  class="offer-discount"
                >
                  {{ discountBadgeFor(offer) }}
                </span>

                <img
                  :src="offer.image"
                  :alt="offer.name"
                  class="offer-image"
                />
              </div>

              <div class="offer-name" :title="offer.name">
                {{ offer.name }}
              </div>

              <div class="offer-price">
                <!-- OLD PRICE (STRIKED) -->
                <span
                  v-if="oldPrice(offer) !== null"
                  class="old"
                >
                  Rs. {{ formatCurrency(oldPrice(offer)) }}
                </span>

                <!-- NEW / DISCOUNTED PRICE -->
                <span class="current">
                  Rs. {{ formatCurrency(currentPrice(offer)) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <button
          class="nav-btn next"
          type="button"
          @click="next"
          :disabled="atEnd"
        >
          ›
        </button>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'SpecialOffers',

  props: {
    // Each item can be like:
    // {
    //   id,
    //   name,
    //   image,
    //   price,         // final price
    //   oldPrice,      // original price
    //   regular_price,
    //   sale_price,
    //   discountLabel, // "DISCOUNT 80%" etc.
    //   discount_status,
    //   discount_type,
    //   discounted_amount
    // }
    offers: {
      type: Array,
      default: () => [],
    },
  },

  data() {
    return {
  canPrev: false,
  canNext: false,
  _raf: null,
};

  },

  computed: {
  atStart() {
    return !this.canPrev;
  },
  atEnd() {
    return !this.canNext;
  },
},

mounted() {
  this.$nextTick(() => {
    this.updateNavState();
    window.addEventListener('resize', this.updateNavState, { passive: true });
  });
},

beforeUnmount() {
  window.removeEventListener('resize', this.updateNavState);
  if (this._raf) cancelAnimationFrame(this._raf);
},


  methods: {
   next() {
  const vp = this.$refs.viewport;
  if (!vp) return;

  const card = vp.querySelector('.offer-card');
  const step = card ? card.getBoundingClientRect().width : vp.clientWidth;

  vp.scrollBy({ left: step, behavior: 'smooth' });
},

prev() {
  const vp = this.$refs.viewport;
  if (!vp) return;

  const card = vp.querySelector('.offer-card');
  const step = card ? card.getBoundingClientRect().width : vp.clientWidth;

  vp.scrollBy({ left: -step, behavior: 'smooth' });
},

onViewportScroll() {
  if (this._raf) cancelAnimationFrame(this._raf);
  this._raf = requestAnimationFrame(() => this.updateNavState());
},

updateNavState() {
  const vp = this.$refs.viewport;
  if (!vp) return;

  const left = vp.scrollLeft;
  const maxLeft = vp.scrollWidth - vp.clientWidth;

  this.canPrev = left > 2;
  this.canNext = left < maxLeft - 2;
},


    formatCurrency(value) {
      if (value == null) return '';
      return Number(value).toLocaleString('en-LK', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },

    /**
     * Final (discounted) price to display.
     */
    currentPrice(offer) {
      // Prefer explicit `price` from backend mapping
      if (offer.price != null) return Number(offer.price);

      // Fallbacks if you pass sale/regular prices instead
      if (offer.sale_price != null) return Number(offer.sale_price);
      if (offer.regular_price != null) return Number(offer.regular_price);

      return 0;
    },

    /**
     * Original price to show as striked-through.
     */
    oldPrice(offer) {
      const current = this.currentPrice(offer);

      // If backend already gives oldPrice, use it
      if (offer.oldPrice != null && Number(offer.oldPrice) > current) {
        return Number(offer.oldPrice);
      }

      // If you pass regular_price and it's > current, use that
      if (offer.regular_price != null && Number(offer.regular_price) > current) {
        return Number(offer.regular_price);
      }

      // If you pass price + sale_price (standard pattern)
      if (
        offer.price != null &&
        offer.sale_price != null &&
        Number(offer.price) > Number(offer.sale_price)
      ) {
        return Number(offer.price);
      }

      return null;
    },

    goToProduct(slug) {
    // Same style you used elsewhere
    this.$inertia.visit(`/products/${slug}`);
    // or, if Ziggy is available:
    // this.$inertia.visit(route('products.show', slug));
  },

    /**
     * Text inside the discount badge.
     * 1. offer.discountLabel (if backend already gave a label)
     * 2. Compute percent from oldPrice & currentPrice -> "-15%"
     * 3. discount_type + discounted_amount (percent/amount)
     */
    discountBadgeFor(offer) {
  // 1) Prefer computing % from old vs current price
  const original = this.oldPrice(offer);
  const current = this.currentPrice(offer);

  if (original && current && original > current) {
    const percent = Math.round(((original - current) / original) * 100);
    return percent > 0 ? `-${percent}%` : null;
  }

  // 2) Fallback: explicit percent from backend fields
  if (
    offer.discount_status &&
    offer.discount_type === 'percent' &&
    Number(offer.discounted_amount) > 0
  ) {
    return `-${Math.round(Number(offer.discounted_amount))}%`;
  }

  // 3) Last fallback: extract a number from "DISCOUNT 80%" and convert to "-80%"
  if (offer.discountLabel) {
    const m = String(offer.discountLabel).match(/(\d+(?:\.\d+)?)/);
    if (m) return `-${Math.round(Number(m[1]))}%`;
  }

  return null;
}
,
  },
};
</script>

<style scoped>
.special-offers {
  margin: 40px 0;
}

.special-offers .offer-price .old {
  text-decoration: line-through !important;
  text-decoration-thickness: 2px;
  text-decoration-color: currentColor;
}

.special-offers-inner {
  display: grid;
  grid-template-columns: 320px 18px minmax(0, 1fr);
  background: #f9fafb;
  border-radius: 16px;
  box-shadow: 0 8px 30px rgba(15, 23, 42, 0.08);
  overflow: hidden;
}

/* LEFT PANEL */
.special-panel {
  background: #041553;
  color: #ffffff;
  padding: 40px 32px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.special-badge {
  font-size: 0.8rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  opacity: 0.8;
  margin-bottom: 16px;
}

.special-title {
  font-size: 2.4rem;
  line-height: 1.1;
  font-weight: 700;
  margin-bottom: 32px;
}

/* ===== Typography to match TopNavbar (mitem / mtext-btn style) ===== */
.special-offers {
  font-family: inherit; /* same global font as navbar */
}

/* match navbar vibe: uppercase + tracking + bold */
.special-badge,
.special-cta,
.offer-name {
 
  letter-spacing: 0.04em;
  font-weight: 700;
}

/* navbar uses smaller uppercase label style */
.special-badge {
  font-size: 11px;
  letter-spacing: 0.06em;
  opacity: 0.85;
}

/* navbar-like button text */
.special-cta {
  font-size: 13px;
  letter-spacing: 0.06em;
}

/* product name in navbar style */
.offer-name {
  font-size: 13px;      /* similar to navbar menu size */
  font-weight: 700;
  letter-spacing: 0.04em;
}

/* prices: keep your colors, just align weight a bit */
.offer-price .current {
  font-weight: 500;
}
.offer-price .old {
  font-weight: 500;
}


.special-cta {
  align-self: flex-start;
  padding: 10px 28px;
  border-radius: 999px;
  border: 2px solid #ffcc00;
  background: #ffcc00;
  color: #1f2937;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: background 0.15s ease, color 0.15s ease, transform 0.15s ease;
}

.special-cta:hover {
  background: #fcd34d;
  transform: translateY(-1px);
}

/* ARROW DIVIDER */
.divider-arrow {
  position: relative;
}

.divider-arrow::after {
  content: '';
  position: absolute;
  top: 50%;
  left: -1px;
  transform: translateY(-50%);
  width: 0;
  height: 0;
  border-top: 18px solid transparent;
  border-bottom: 18px solid transparent;
  border-left: 18px solid #fbbf24;
}

/* SLIDER AREA */
.offers-slider {
  position: relative;
  padding: 26px 16px 30px; /* less padding on sides */
  background: #ffffff;
}


.offers-viewport {
  width: 100%;
  overflow-x: auto;
  overflow-y: hidden;

  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
  overscroll-behavior-x: contain;
  touch-action: pan-x;

  scrollbar-width: none;
}

.offers-viewport::-webkit-scrollbar {
  display: none;
}


.offers-track {
  display: flex;
  gap: 0; /* keep your padding spacing */
}
.offer-card {
  scroll-snap-align: start;
  scroll-snap-stop: always;
}
@media (max-width: 768px) {
  .nav-btn {
    width: 38px;
    height: 38px;
    font-size: 22px;
  }
}


/* CARD */
.offer-card {
  flex: 0 0 25%; /* 4 cards visible */
  padding: 0 10px;
  box-sizing: border-box;
}

.offer-image-wrapper {
  position: relative;
  padding-top: 70%;
  background: #f3f4f6;
  border-radius: 16px;
  overflow: hidden;
  border: 2px solid #fbbf24;
}

.offer-image {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: contain;
  background: #ffffff;
}

/* DISCOUNT BADGE */


/* NAME + PRICE */
.offer-name {
  margin-top: 10px;
  font-size: 0.9rem;
  font-weight: 500;
  color: #1f2937;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  text-align: center;
}

.offer-price {
  margin-top: 6px;
  text-align: center;
}

/* NEW PRICE (purple, bold) */
.offer-price .current {
  display: block;
  font-weight: 500;
  color: #4c1d95;
}

/* OLD PRICE (grey, striked) */
.offer-price .old {
  display: block;
  margin-bottom: 2px;
  font-size: 0.85rem;
  text-decoration: line-through;
  color: #9ca3af;
}

.nav-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 10;

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

  margin: 0; /* IMPORTANT: remove margin so it won't push layout */
}

.nav-btn.prev { left: 10px; }
.nav-btn.next { right: 10px; }


.nav-btn:hover:not(:disabled) {
  background: rgba(17, 24, 39, 0.15);
  transform: translateY(-1px);
}

.nav-btn:disabled {
  opacity: 0.35;
  cursor: default;
}

/* Responsive */
@media (max-width: 1024px) {
  .special-offers-inner {
    grid-template-columns: 260px 14px minmax(0, 1fr);
  }
}

@media (max-width: 768px) {
  .special-offers-inner {
    grid-template-columns: 1fr;
  }

  .divider-arrow {
    display: none;
  }

  .offers-slider {
    padding: 20px;
  }

  .offer-card {
    flex: 0 0 50%; /* 2 cards visible on smaller screens */
  }
}

@media (max-width: 768px) {
  .nav-btn { display: none; }

  .offers-slider { padding: 20px 0; }

  .offers-track { padding: 0 14px; }

  .offer-card {
    flex: 0 0 72%;       /* smaller than 85% */
    max-width: 320px;    /* stops being too big on tablets/large phones */
    padding: 0 8px;
    scroll-snap-align: center;
  }

  .offer-image-wrapper {
    padding-top: 58%;    /* reduce image height */
  }
}

@media (max-width: 480px) {
  .offer-card {
    flex: 0 0 78%;       /* phone size */
    max-width: 280px;    /* even smaller on phones */
  }

  .offer-image-wrapper {
    padding-top: 55%;
  }
}



.offer-image-wrapper {
  position: relative;
}

/* put image behind */
.offer-image {
  position: absolute;
  inset: 0;
  z-index: 1;
}

/* put badge above image */
.offer-discount {
  position: absolute;
  top: 10px;
  left: 10px;
  z-index: 2;

  width: 38px;
  height: 38px;
  border-radius: 50%;

  background: #1a175c; /* light red */
  color: #f9f7f7;      /* red text */

  display: flex;
  align-items: center;
  justify-content: center;

  font-size: 0.75rem;
  font-weight: 700;
  box-shadow: 0 4px 10px rgba(185, 28, 28, 0.18);
}



</style>
