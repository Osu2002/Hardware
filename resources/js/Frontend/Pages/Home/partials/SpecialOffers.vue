<template>
  <section class="special-offers" v-if="offers.length">
    <div class="special-offers-inner">
      <!-- LEFT STATIC PANEL -->
      <div class="special-panel">
        <span class="special-badge">SALE</span>

        <h2 class="special-title">
          SPECIAL<br />
          OFFERS
        </h2>

        <button class="special-cta">
          SHOP NOW
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

        <div class="offers-viewport">
          <div class="offers-track" :style="trackStyle">
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
      visibleCount: 4, // how many cards visible at once (desktop)
      currentIndex: 0,
    };
  },

  computed: {
    maxIndex() {
      return Math.max(0, this.offers.length - this.visibleCount);
    },
    atStart() {
      return this.currentIndex === 0;
    },
    atEnd() {
      return this.currentIndex >= this.maxIndex;
    },
    trackStyle() {
      const step = 100 / this.visibleCount;
      return {
        transform: `translateX(-${this.currentIndex * step}%)`,
      };
    },
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
      // 1. Direct label from backend
      if (offer.discountLabel) {
        return offer.discountLabel;
      }

      // 2. Calculate from prices
      const original = this.oldPrice(offer);
      const current = this.currentPrice(offer);

      if (original && current && original > current) {
        const diff = original - current;
        const percent = original > 0 ? Math.round((diff / original) * 100) : 0;
        if (percent > 0) {
          return `-${percent}%`;
        }
      }

      // 3. Fallback using discount_type / discounted_amount
      if (
        offer.discount_status &&
        offer.discount_type &&
        offer.discounted_amount > 0
      ) {
        if (offer.discount_type === 'percent') {
          return `-${offer.discounted_amount}%`;
        }
        if (offer.discount_type === 'amount') {
          return `-Rs. ${this.formatCurrency(offer.discounted_amount)}`;
        }
      }

      return null;
    },
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
  background: #001c80;
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
  padding: 26px 40px 30px;
  background: #ffffff;
  display: flex;
  align-items: center;
}

.offers-viewport {
  overflow: hidden;
  width: 100%;
}

.offers-track {
  display: flex;
  transition: transform 0.3s ease;
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
.offer-discount {
  position: absolute;
  top: 10px;
  left: 10px;
  background: #7c3aed; /* purple */
  color: #ffffff;
  font-size: 0.8rem;
  font-weight: 700;
  padding: 8px 12px;
  border-radius: 999px;
  box-shadow: 0 4px 10px rgba(76, 29, 149, 0.4);
}

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
  font-weight: 700;
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

/* NAV BUTTONS */
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
  margin: 0 6px;
}

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
}


@media (max-width: 480px) {
  .offer-card {
    flex: 0 0 100%; /* 1 card visible on phones */
  }
}
</style>
