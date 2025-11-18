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
            >
              <div class="offer-image-wrapper">
                <span
                  v-if="offer.discountLabel"
                  class="offer-discount"
                >
                  {{ offer.discountLabel }}
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
                <span class="current">
                  Rs. {{ formatCurrency(offer.price) }}
                </span>
                <span
                  v-if="offer.oldPrice"
                  class="old"
                >
                  Rs. {{ formatCurrency(offer.oldPrice) }}
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

  data() {
    return {
      visibleCount: 4, // how many cards visible at once (desktop)
      currentIndex: 0,
      // Hard-coded demo data for now
      offers: [
        {
          id: 1,
          name: 'Men Air Compressor 1HP-38L',
          image: 'https://via.placeholder.com/260x220?text=Offer+1',
          price: 50588,
          oldPrice: null,
          discountLabel: '',
        },
        {
          id: 2,
          name: 'Waterproofing Paint Brilliance',
          image: 'https://via.placeholder.com/260x220?text=Offer+2',
          price: 37950,
          oldPrice: 42000,
          discountLabel: 'DISCOUNT 10%',
        },
        {
          id: 3,
          name: 'Dulux Aquatech Flex Brilliance',
          image: 'https://via.placeholder.com/260x220?text=Offer+3',
          price: 31000,
          oldPrice: 34500,
          discountLabel: '',
        },
        {
          id: 4,
          name: 'Sayerlack WB EX Matt Top Coat',
          image: 'https://via.placeholder.com/260x220?text=Offer+4',
          price: 28567,
          oldPrice: 39676,
          discountLabel: 'DISCOUNT 28%',
        },
        {
          id: 5,
          name: 'Extra Product Example 5',
          image: 'https://via.placeholder.com/260x220?text=Offer+5',
          price: 14990,
          oldPrice: 17990,
          discountLabel: 'DISCOUNT 17%',
        },
      ],
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
      // Each step moves one card width (100 / visibleCount %)
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
  },
};
</script>

<style scoped>
.special-offers {
  margin: 40px 0;
}

.special-offers-inner {
  display: grid;
  grid-template-columns: 320px 18px minmax(0, 1fr);
  background: #f9fafb;
  border-radius: 8px;
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
  border: 2px solid #00c0ff;
  background: transparent;
  color: #ffffff;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: background 0.15s ease, color 0.15s ease;
}

.special-cta:hover {
  background: #00c0ff;
  color: #001c80;
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
  border-left: 18px solid #00a2ff;
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
  border-radius: 8px 8px 0 0;
  overflow: hidden;
}

.offer-image {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: contain;
  background: #ffffff;
}

.offer-discount {
  position: absolute;
  top: 10px;
  right: 10px;
  background: #ff1f2f;
  color: #ffffff;
  font-size: 0.7rem;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 4px;
  text-transform: uppercase;
}

.offer-name {
  margin-top: 10px;
  font-size: 0.9rem;
  font-weight: 500;
  color: #374151;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  text-align: center;
}

.offer-price {
  margin-top: 6px;
  text-align: center;
}

.offer-price .current {
  display: block;
  font-weight: 600;
  color: #111827;
}

.offer-price .old {
  display: block;
  margin-top: 2px;
  font-size: 0.8rem;
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
    flex: 0 0 50%; /* 2 cards visible on smaller screens (logic still works) */
  }
}

@media (max-width: 480px) {
  .offer-card {
    flex: 0 0 100%; /* 1 card visible on phones */
  }
}
</style>
