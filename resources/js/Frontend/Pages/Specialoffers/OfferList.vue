<template>
  <section class="category-products">
    <p v-if="!items.length" class="empty">No special offers found.</p>

    <div v-else class="grid">
      <article
        v-for="product in items"
        :key="product.id"
        class="product-card"
        @click="goToProduct(product.slug)"
      >
        <div class="card-inner">
          <div class="card-image-wrapper">
            <span
              v-if="hasDiscount(product)"
              class="badge-discount"
              :title="`Discount: ${discountPercent(product)}%`"
            >
              -{{ discountPercent(product) }}%
            </span>

            <img
              v-if="product.image"
              :src="product.image"
              :alt="product.name"
              class="card-image"
              loading="lazy"
            />

            <div v-else class="card-placeholder">No image</div>
          </div>

          <div class="card-body">
            <h3 class="card-title" :title="product.name">
              {{ truncateName(product.name) }}
            </h3>

            <div class="card-price-row">
              <div class="card-price-stack">
                <del v-if="hasDiscount(product)" class="card-old-price">
                  Rs{{ formatPrice(basePrice(product)) }}
                </del>

                <div class="card-price">
                  Rs{{ formatPrice(currentPrice(product)) }}
                </div>
              </div>
            </div>
          </div>

          <div class="card-footer">
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

    <nav
      v-if="products.links && products.links.length > 1"
      class="pagination"
      aria-label="Pagination"
    >
      <button
        v-for="link in products.links"
        :key="link.label + (link.url || '')"
        type="button"
        class="page-link"
        :class="{ active: link.active, disabled: !link.url }"
        :disabled="!link.url"
        @click.prevent="goToPage(link)"
      >
        <span v-html="link.label" />
      </button>
    </nav>
  </section>
</template>

<script>
export default {
  name: "OfferList",
  props: {
    products: { type: Object, required: true },
  },

  data() {
    return {
      wished: new Set(),
      _mql: null,
      _mqlHandler: null,
      isMobile: false,
    };
  },

  computed: {
    items() {
      return this.products?.data ? this.products.data : [];
    },
  },

  mounted() {
    if (typeof window !== "undefined" && window.matchMedia) {
      this._mql = window.matchMedia("(max-width: 768px)");

      const applyMode = (matches) => {
        this.isMobile = !!matches;
        this.ensurePerPage();
      };

      applyMode(this._mql.matches);
      this._mqlHandler = (e) => applyMode(e.matches);

      if (this._mql.addEventListener) this._mql.addEventListener("change", this._mqlHandler);
      else if (this._mql.addListener) this._mql.addListener(this._mqlHandler);
    }
  },

  beforeUnmount() {
    if (this._mql && this._mqlHandler) {
      if (this._mql.removeEventListener) this._mql.removeEventListener("change", this._mqlHandler);
      else if (this._mql.removeListener) this._mql.removeListener(this._mqlHandler);
    }
  },

  methods: {
    ensurePerPage() {
      const desired = this.isMobile ? 8 : 16;

      const url = new URL(window.location.href);
      const current = Number(url.searchParams.get("per_page") || 0);

      if (!current || current !== desired) {
        url.searchParams.set("per_page", String(desired));
        url.searchParams.delete("page");

        this.$inertia.get(url.toString(), {}, {
          preserveState: true,
          preserveScroll: true,
          replace: true,
        });
      }
    },

    truncateName(name) {
      const s = String(name ?? "");
      if (s.length <= 20) return s;
      return s.slice(0, 20) + "...";
    },

    formatPrice(value) {
      if (value == null) return "";
      return Number(value).toLocaleString("en-LK", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },

    discountPercent(product) {
      const p = Number(product?.discount_percent);
      if (Number.isFinite(p) && p > 0) return Math.round(p);

      const oldP = Number(product?.regular_price);
      const nowP = Number(product?.price);
      if (Number.isFinite(oldP) && oldP > 0 && Number.isFinite(nowP) && nowP > 0 && nowP < oldP) {
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

    goToPage(link) {
      if (!link.url || link.active) return;

      const desired = this.isMobile ? 8 : 16;
      const url = new URL(link.url, window.location.origin);
      url.searchParams.set("per_page", String(desired));

      this.$inertia.get(url.toString(), {}, { preserveState: true, preserveScroll: true });
    },
  },
};
</script>

<style scoped>
/* ✅ EXACT SAME STYLES as FeaturedProductList.vue */
.category-products { margin-top: 10px; }
.empty { padding: 24px 0; font-size: 0.95rem; color: #6b7280; }

.grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 12px;
  margin-bottom: 20px;
}
@media (max-width: 1024px) {
  .grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 768px) {
  .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
}

.product-card { box-sizing: border-box; cursor: pointer; }
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
.product-card:hover .card-inner { transform: translateY(-2px); }

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
.product-card:hover .card-image { transform: scale(1.08); }
.card-placeholder {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  color: #9ca3af;
}

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
.card-title {
  font-size: 0.85rem;
  font-weight: 500;
  color: #111827;
  text-align: center;
  line-height: 1.3;
  min-height: 2.8em;
}
.card-price-row { margin-top: 6px; text-align: center; }
.card-price-stack {
  display: inline-flex;
  flex-direction: column;
  gap: 2px;
  align-items: center;
}
.card-old-price {
  position: relative;
  display: inline-block;
  color: #9ca3af;
  font-weight: 500;
  font-size: 0.82rem;
  text-decoration: none;
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
.card-price { color: #4c1d95; font-weight: 500; font-size: 0.92rem; }

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
  color: #9ca3af;
}
.btn-heart:hover { transform: translateY(-1px); border-color: #fca5a5; background: #fff1f2; }
.btn-heart.active { border-color: #ef4444; background: #fee2e2; color: #ef4444; }
.heart-icon { width: 18px; height: 18px; display: block; fill: none; stroke: currentColor; stroke-width: 2; }
.btn-heart.active .heart-icon { fill: currentColor; stroke: none; }

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
.btn-buy:hover { background: #1165b8b2; transform: translateY(-4px); }

@media (max-width: 768px) {
  .card-image-wrapper { padding-top: 62%; }
  .card-body { padding: 8px 8px 4px; }
  .card-title { font-size: 0.78rem; min-height: 2.4em; }
  .card-price { font-size: 0.86rem; }
  .card-old-price { font-size: 0.72rem; }
  .card-footer { padding: 6px 8px 8px; gap: 8px; }
  .btn-heart { width: 30px; height: 30px; border-radius: 9px; }
  .heart-icon { width: 16px; height: 16px; }
  .btn-buy { padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; }
  .badge-discount { width: 38px; height: 38px; font-size: 0.72rem; right: 6px; top: 6px; }
}

.pagination {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  justify-content: center;
  padding-top: 10px;
}
.page-link {
  min-width: 34px;
  height: 34px;
  padding: 0 10px;
  border-radius: 999px;
  border: 1px solid #d1d5db;
  background: #ffffff;
  font-size: 0.85rem;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.page-link.active {
  background: #22c55e;
  border-color: #22c55e;
  color: #ffffff;
  font-weight: 700;
}
.page-link.disabled { opacity: 0.4; cursor: default; }
</style>
