<template>
  <div class="product-page">
    <!-- Breadcrumb -->
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="/" class="crumb-link">Home</a>

      <template v-if="product.categories && product.categories.length">
        <template v-for="cat in product.categories" :key="cat.id">
          <span class="crumb-separator">/</span>
          <a :href="`/category/${cat.slug}`" class="crumb-link">
            {{ cat.title }}
          </a>
        </template>
      </template>

      <span class="crumb-separator">/</span>
      <span class="crumb-current" :title="product.name">
        {{ truncateName(product.name, 28) }}
      </span>
    </nav>

    <!-- Main -->
    <div class="product-main">
      <!-- Gallery -->
      <section class="product-gallery" aria-label="Product gallery">
        <div class="main-image-wrapper" role="button" tabindex="0" aria-label="Product image">
          <img
            v-if="activeImageUrl"
            :src="activeImageUrl"
            :alt="product.name"
            class="main-image"
            loading="eager"
            decoding="async"
          />

          <div class="badges" v-if="product.discountLabel">
            <span class="badge badge-discount">-{{ product.discountLabel }} OFF</span>
          </div>

          <div class="hover-hint" aria-hidden="true">Hover to zoom</div>
        </div>

        <div v-if="product.gallery && product.gallery.length" class="thumbs-row" aria-label="Gallery thumbnails">
          <button
            v-for="img in product.gallery"
            :key="img.id"
            type="button"
            class="thumb-btn"
            :class="{ active: img.id === activeImageId }"
            @click="activeImageId = img.id"
            :aria-label="`View image ${img.id}`"
          >
            <img :src="img.url" :alt="product.name" class="thumb-img" loading="lazy" decoding="async" />
          </button>
        </div>
      </section>

      <!-- Info -->
      <section class="product-info" aria-label="Product information">
        <header class="header">
          <h1 class="title" :title="product.name">
            {{ truncateName(product.name, 28) }}
          </h1>

          <div class="brand-row">
            <template v-if="product.brandLogo">
              <span class="brand-label">Brand</span>
              <img :src="product.brandLogo" :alt="product.brand || 'Brand'" class="brand-logo" />
            </template>
            <template v-else-if="product.brand">
              <span class="brand-label">Brand</span>
              <span class="brand-text">{{ product.brand }}</span>
            </template>
          </div>
        </header>

        <div class="price-area" aria-label="Pricing">
          <div class="price-line">
            <div class="price-main">Rs {{ formatPrice(product.price) }}</div>

            <div class="price-meta" v-if="product.discountLabel || product.oldPrice">
              <span v-if="product.discountLabel" class="pill pill-discount">Save {{ product.discountLabel }}</span>
              <del v-if="product.oldPrice" class="price-old">Rs {{ formatPrice(product.oldPrice) }}</del>
            </div>
          </div>
        </div>

        <p v-if="product.short_description" class="short-desc">
          {{ product.short_description }}
        </p>

        <div v-if="product.attributes && product.attributes.length" class="attrs" aria-label="Attributes">
          <div class="attrs-grid">
            <div class="attr-row" v-for="a in product.attributes" :key="a.code">
              <div class="attr-label">{{ a.label }}</div>

              <div class="attr-value">
                <template v-if="a.type === 'color'">
                  <span class="color-swatch" :style="{ background: a.value }" aria-hidden="true"></span>
                  <span class="attr-text">{{ a.value }}</span>
                </template>
                <template v-else>
                  <span class="attr-text">{{ a.value }}<span v-if="a.unit"> {{ a.unit }}</span></span>
                </template>
              </div>
            </div>
          </div>
        </div>

        <div class="meta" aria-label="Availability and warranty">
          <div v-if="product.inStock !== null && product.inStock !== undefined" class="meta-row">
            <div class="meta-label">Availability</div>
            <div class="meta-value" :class="product.inStock == 1 ? 'instock' : 'outstock'">
              {{ product.inStock == 1 ? 'In Stock' : 'Out of Stock' }}
              <span v-if="product.stockCount !== null && product.stockCount !== undefined" class="meta-sub">
                ({{ product.stockCount }})
              </span>
            </div>
          </div>

          <div v-if="product.warrantyPeriod || product.warrantyType" class="meta-row">
            <div class="meta-label">Warranty</div>
            <div class="meta-value">
              <span v-if="product.warrantyPeriod">{{ product.warrantyPeriod }}</span>
              <span v-if="product.warrantyPeriod && product.warrantyType" class="dot">•</span>
              <span v-if="product.warrantyType">{{ product.warrantyType }}</span>
            </div>
          </div>
        </div>

        <!-- Actions: WhatsApp only (Buy Now + Add to Cart removed/hidden) -->
        <div class="actions" aria-label="Purchase actions">
          <a
            class="btn-whatsapp"
            :href="whatsAppLink"
            target="_blank"
            rel="noopener"
            aria-label="Contact on WhatsApp"
          >
            <span class="wa-icon" aria-hidden="true">
              <!-- WhatsApp SVG -->
              <svg viewBox="0 0 32 32" width="20" height="20" fill="currentColor" focusable="false">
                <path
                  d="M19.11 17.34c-.28-.14-1.64-.81-1.9-.9-.26-.1-.45-.14-.64.14-.19.28-.74.9-.9 1.09-.17.19-.33.21-.61.07-.28-.14-1.18-.43-2.25-1.38-.83-.74-1.39-1.66-1.55-1.94-.17-.28-.02-.43.12-.57.12-.12.28-.33.43-.5.14-.17.19-.28.28-.47.1-.19.05-.36-.02-.5-.07-.14-.64-1.55-.88-2.12-.23-.55-.47-.48-.64-.49h-.55c-.19 0-.5.07-.76.36-.26.28-1 1-1 2.43 0 1.43 1.02 2.81 1.16 3 .14.19 2.01 3.07 4.87 4.3.68.29 1.21.47 1.62.6.68.22 1.3.19 1.79.12.55-.08 1.64-.67 1.87-1.31.23-.64.23-1.19.16-1.31-.07-.12-.26-.19-.55-.33z"
                />
                <path
                  d="M26.67 5.33A14.67 14.67 0 0 0 3.33 23.1L2 30l6.98-1.3A14.66 14.66 0 0 0 30.67 16c0-3.92-1.53-7.6-4-10.67zM16 28a11.9 11.9 0 0 1-6.06-1.65l-.43-.25-4.14.77.79-4.03-.28-.41A12 12 0 1 1 16 28z"
                />
              </svg>
            </span>
            <span class="wa-text">WhatsApp to Order</span>
            <span class="wa-sub">Chat with product details</span>
          </a>
        </div>
      </section>
    </div>

    <!-- Description -->
    <section v-if="product.description" class="product-description" aria-label="Description">
      <h2 class="section-title">Description</h2>
      <div class="description-body" v-html="product.description" />
    </section>

    <!-- Related -->
    <section v-if="relatedProducts && relatedProducts.length" class="related-section" aria-label="Related products">
      <h2 class="section-title">Related Products</h2>

      <!-- CategoryNav-style: 5 visible on desktop + swipe/scroll on smaller devices -->
      <div class="related-slider">
        <div class="slider-viewport" aria-label="Related products slider">
          <div class="slider-track">
            <article
              v-for="p in relatedProducts"
              :key="p.id"
              class="product-card"
              @click="goToProduct(p.slug)"
              role="button"
              tabindex="0"
            >
              <div class="card-inner">
                <div class="card-image-wrapper">
                  <span
                    v-if="hasDiscount(p)"
                    class="badge-discount"
                    :title="`Discount: ${discountPercent(p)}%`"
                  >
                    -{{ discountPercent(p) }}%
                  </span>

                  <img
                    :src="p.image"
                    :alt="p.name"
                    class="card-image"
                    loading="lazy"
                    decoding="async"
                  />
                </div>

                <div class="card-body">
                  <h3 class="card-title" :title="p.name">
                    {{ truncateName(p.name, 20) }}
                  </h3>

                  <div class="card-price-row">
                    <div class="card-price-stack">
                      <del v-if="hasDiscount(p)" class="card-old-price">
                        Rs{{ formatPrice(basePrice(p)) }}
                      </del>

                      <div class="card-price">
                        Rs{{ formatPrice(currentPrice(p)) }}
                      </div>
                    </div>
                  </div>
                </div>

                <div class="card-footer">
                  <button
                    type="button"
                    class="btn-heart"
                    :class="{ active: isWished(p) }"
                    @click.stop="toggleWish(p)"
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
                    @click.stop="goToProduct(p.slug)"
                  >
                    BUY
                  </button>
                </div>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>

    <!-- Mobile sticky action -->
    <div class="mobile-sticky">
      <a class="mobile-wa" :href="whatsAppLink" target="_blank" rel="noopener" aria-label="Contact on WhatsApp">
        <span class="wa-icon" aria-hidden="true">
          <svg viewBox="0 0 32 32" width="18" height="18" fill="currentColor" focusable="false">
            <path
              d="M19.11 17.34c-.28-.14-1.64-.81-1.9-.9-.26-.1-.45-.14-.64.14-.19.28-.74.9-.9 1.09-.17.19-.33.21-.61.07-.28-.14-1.18-.43-2.25-1.38-.83-.74-1.39-1.66-1.55-1.94-.17-.28-.02-.43.12-.57.12-.12.28-.33.43-.5.14-.17.19-.28.28-.47.1-.19.05-.36-.02-.5-.07-.14-.64-1.55-.88-2.12-.23-.55-.47-.48-.64-.49h-.55c-.19 0-.5.07-.76.36-.26.28-1 1-1 2.43 0 1.43 1.02 2.81 1.16 3 .14.19 2.01 3.07 4.87 4.3.68.29 1.21.47 1.62.6.68.22 1.3.19 1.79.12.55-.08 1.64-.67 1.87-1.31.23-.64.23-1.19.16-1.31-.07-.12-.26-.19-.55-.33z"
            />
            <path
              d="M26.67 5.33A14.67 14.67 0 0 0 3.33 23.1L2 30l6.98-1.3A14.66 14.66 0 0 0 30.67 16c0-3.92-1.53-7.6-4-10.67zM16 28a11.9 11.9 0 0 1-6.06-1.65l-.43-.25-4.14.77.79-4.03-.28-.41A12 12 0 1 1 16 28z"
            />
          </svg>
        </span>
        <span>WhatsApp to Order</span>
      </a>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProductView',

  props: {
    product: { type: Object, required: true },
    relatedProducts: { type: Array, default: () => [] },
  },

  data() {
    return {
      activeImageId:
        this.product.gallery && this.product.gallery.length
          ? this.product.gallery[0].id
          : null,
      pageUrl: '',

      // Related-products wishlist UI (CategoryNav-style)
      wished: new Set(),
    };
  },

  mounted() {
    if (typeof window !== 'undefined') {
      this.pageUrl = window.location.href || '';
    }
  },

  computed: {
    activeImageUrl() {
      if (!this.product.gallery || !this.product.gallery.length) {
        return this.product.primaryImage || '';
      }
      const img = this.product.gallery.find((g) => g.id === this.activeImageId);
      return (img && img.url) || this.product.primaryImage || '';
    },

    whatsAppLink() {
      // Sri Lanka: 071 552 6000 -> +94 71 552 6000 -> 94715526000
      const phone = '94715526000';

      const name = this.product && this.product.name ? String(this.product.name) : '';
      const price = this.product && this.product.price != null ? `Rs ${this.formatPrice(this.product.price)}` : '';
      const brand = this.product && this.product.brand ? String(this.product.brand) : '';

      const lines = [
        'Hi, I would like to buy this product.',
        name ? `Product: ${name}` : null,
        brand ? `Brand: ${brand}` : null,
        price ? `Price: ${price}` : null,
        this.pageUrl ? `Link: ${this.pageUrl}` : null,
      ].filter(Boolean);

      const text = encodeURIComponent(lines.join('\n'));
      return `https://wa.me/${phone}?text=${text}`;
    },
  },

  methods: {
    formatPrice(value) {
      if (value == null) return '';
      return Number(value).toLocaleString('en-LK', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },

    truncateName(text, max = 32) {
      if (!text) return '';
      const s = String(text).trim();
      return s.length > max ? s.slice(0, max) + '...' : s;
    },

    // ---------- Related: Discount logic (CategoryNav-style) ----------
    discountPercent(product) {
      const p = Number(product?.discount_percent);
      if (Number.isFinite(p) && p > 0) return Math.round(p);

      const oldP = this.basePrice(product);
      const nowP = this.currentPrice(product);

      if (Number.isFinite(oldP) && oldP > 0 && Number.isFinite(nowP) && nowP > 0 && nowP < oldP) {
        return Math.round(((oldP - nowP) / oldP) * 100);
      }

      return 0;
    },

    basePrice(product) {
      return (
        Number(
          product?.regular_price ??
            product?.oldPrice ??
            product?.old_price ??
            0
        ) || 0
      );
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
  },
};
</script>

<style scoped>
/* Design system */
.product-page {
  --bg: #ffffff;
  --text: #0f172a;
  --muted: #64748b;
  --line: rgba(15, 23, 42, 0.10);
  --soft: rgba(15, 23, 42, 0.06);
  --accent: #16a34a; /* WhatsApp / CTA */
  --accent-2: #22c55e;
  --warn: #ef4444;

  background: var(--bg);
  color: var(--text);
  padding: clamp(16px, 2.6vw, 36px) 0 clamp(28px, 4vw, 60px);
}

/* Breadcrumb */
.breadcrumb {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  align-items: center;
  font-size: 0.9rem;
  color: var(--muted);
  margin-bottom: clamp(14px, 2vw, 18px);
}

.crumb-link {
  color: var(--muted);
  text-decoration: none;
  transition: color 160ms ease, text-decoration-color 160ms ease;
}

.crumb-link:hover {
  color: var(--text);
  text-decoration: underline;
  text-decoration-color: var(--line);
  text-underline-offset: 3px;
}

.crumb-separator {
  color: rgba(100, 116, 139, 0.7);
}

.crumb-current {
  color: var(--text);
  font-weight: 700;
}

/* Layout */
.product-main {
  display: grid;
  grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
  gap: clamp(16px, 3vw, 46px);
  align-items: start;
  padding-bottom: clamp(14px, 2.2vw, 24px);
  border-bottom: 1px solid var(--line);
}

/* Gallery (no block/card background; only subtle borders) */
.product-gallery {
  min-width: 0;
}

.main-image-wrapper {
  position: relative;
  border-radius: 16px;
  border: 1px solid var(--line);
  background: radial-gradient(80% 80% at 50% 40%, rgba(34, 197, 94, 0.08), transparent 60%),
              linear-gradient(180deg, rgba(15, 23, 42, 0.02), transparent 55%);
  overflow: hidden;
  padding-top: 78%;
  isolation: isolate;
  cursor: zoom-in;
  outline: none;
  background: #fff !important;
}

.main-image-wrapper:focus-visible {
  box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.22);
}

.main-image {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: contain;
  transform: translateZ(0) scale(1);
  transition: transform 360ms ease, filter 360ms ease;
  will-change: transform, filter;
    background: #fff !important;
}

.main-image-wrapper:hover .main-image {
  transform: scale(1.07);
  filter: saturate(1.06) contrast(1.02);
}

.badges {
  position: absolute;
  left: 12px;
  top: 12px;
  display: flex;
  gap: 8px;
  z-index: 2;
}

.badge {
  font-size: 0.78rem;
  font-weight: 800;
  letter-spacing: 0.02em;
  padding: 7px 10px;
  border-radius: 999px;
  color: #fff;
}

.badge-discount {
  border-color: rgba(239, 68, 68, 0.25);
  background: rgba(239, 68, 68, 0.08);
  color: #b91c1c;
}

.hover-hint {
  position: absolute;
  right: 12px;
  bottom: 12px;
  font-size: 0.78rem;
  color: rgba(100, 116, 139, 0.9);
  background: rgba(255, 255, 255, 0.72);
  border: 1px solid var(--line);
  padding: 6px 10px;
  border-radius: 999px;
  backdrop-filter: blur(6px);
  transform: translateY(0);
  transition: opacity 220ms ease, transform 220ms ease;
  opacity: 0.92;
}

.main-image-wrapper:hover .hover-hint {
  opacity: 1;
  transform: translateY(-2px);
}

.thumbs-row {
  display: flex;
  gap: 10px;
  margin-top: 12px;
  overflow-x: auto;
  padding-bottom: 6px;
  scroll-snap-type: x mandatory;
}

.thumbs-row::-webkit-scrollbar {
  height: 8px;
}
.thumbs-row::-webkit-scrollbar-thumb {
  background: rgba(100, 116, 139, 0.25);
  border-radius: 999px;
}

.thumb-btn {
  flex: 0 0 auto;
  scroll-snap-align: start;
  border: 1px solid transparent;
  border-radius: 12px;
  padding: 0;
  background: transparent;
  transition: transform 180ms ease, border-color 180ms ease;
}

.thumb-btn:hover {
  transform: translateY(-1px);
}

.thumb-btn.active {
  border-color: rgba(34, 197, 94, 0.55);
}

.thumb-img {
  width: 72px;
  height: 72px;
  object-fit: contain;
  border-radius: 12px;
  border: 1px solid var(--line);
  background: rgba(15, 23, 42, 0.02);
}

/* Info (no background blocks; only structured spacing & dividers) */
.product-info {
  min-width: 0;
  padding-top: 6px;
}

.header {
  padding-bottom: 14px;
  border-bottom: 1px solid var(--line);
}

.title {
  font-size: clamp(1.2rem, 2.1vw, 1.85rem);
  line-height: 1.18;
  letter-spacing: -0.02em;
  font-weight: 850;
  margin: 0 0 10px;
}

.brand-row {
  display: flex;
  align-items: center;
  gap: 10px;
  min-height: 28px;
}

.brand-label {
  font-size: 0.92rem;
  color: var(--muted);
}

.brand-text {
  font-size: 0.92rem;
  font-weight: 700;
  color: var(--text);
}

.brand-logo {
  height: 26px;
  max-width: 160px;
  object-fit: contain;
  filter: saturate(1.05);
}

.price-area {
  padding: 14px 0 10px;
  border-bottom: 1px solid var(--line);
}

.price-line {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.price-main {
  font-size: clamp(1.25rem, 2.2vw, 1.8rem);
  font-weight: 900;
  letter-spacing: -0.015em;
  color: #f97316;
}

.price-meta {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 10px;
}

.price-old{
  text-decoration-line: line-through !important;
  text-decoration-thickness: 1px !important;   /* light line */
  text-decoration-color: rgba(14, 14, 15, 0.758) !important;
  text-decoration-skip-ink: auto;
}

.pill {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 6px 10px;
  font-size: 0.8rem;
  font-weight: 800;
  border: 1px solid var(--line);
  background: rgba(15, 23, 42, 0.02);
  color: var(--text);
}

.pill-discount {
  border-color: rgba(239, 68, 68, 0.25);
  background: rgba(239, 68, 68, 0.08);
  color: #b91c1c;
}

.short-desc {
  margin: 14px 0 0;
  font-size: 0.98rem;
  color: rgba(51, 65, 85, 0.95);
  line-height: 1.6;
}

/* Attributes */
.attrs {
  padding: 16px 0 6px;
  border-bottom: 1px solid var(--line);
}

.attrs-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 10px;
}

.attr-row {
  display: grid;
  grid-template-columns: minmax(0, 0.95fr) minmax(0, 1.05fr);
  gap: 12px;
  align-items: center;
}

.attr-label {
  font-size: 0.92rem;
  color: var(--muted);
}

.attr-value {
  display: inline-flex;
  justify-content: flex-end;
  gap: 8px;
  align-items: center;
  font-size: 0.92rem;
  font-weight: 750;
  color: var(--text);
  text-align: right;
}

.attr-text {
  word-break: break-word;
}

.color-swatch {
  width: 12px;
  height: 12px;
  border-radius: 4px;
  border: 1px solid rgba(100, 116, 139, 0.3);
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.35);
}

/* Meta */
.meta {
  padding: 14px 0 0;
}

.meta-row {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 12px;
  align-items: baseline;
  padding: 6px 0;
}

.meta-label {
  font-size: 0.92rem;
  color: var(--muted);
}

.meta-value {
  font-weight: 850;
  font-size: 0.92rem;
}

.meta-sub {
  font-weight: 750;
  opacity: 0.85;
}

.dot {
  margin: 0 8px;
  color: rgba(100, 116, 139, 0.6);
}

.instock {
  color: #16a34a;
}
.outstock {
  color: #dc2626;
}

/* Actions */
.actions {
  padding-top: 16px;
}

.btn-whatsapp {
  display: grid;
  grid-template-columns: 22px 1fr;
  grid-template-rows: auto auto;
  column-gap: 10px;
  row-gap: 2px;
  align-items: center;

  text-decoration: none;
  border-radius: 14px;
  padding: 12px 14px;
  border: 1px solid rgba(34, 197, 94, 0.35);
  background: linear-gradient(135deg, rgba(34, 197, 94, 0.16), rgba(34, 197, 94, 0.06));
  color: var(--text);

  transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
}

.btn-whatsapp:hover {
  transform: translateY(-1px);
  border-color: rgba(34, 197, 94, 0.55);
  box-shadow: 0 16px 40px rgba(34, 197, 94, 0.18);
}

.btn-whatsapp:focus-visible {
  outline: none;
  box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.22);
}

.wa-icon {
  grid-row: 1 / span 2;
  color: var(--accent);
}

.wa-text {
  font-weight: 900;
  letter-spacing: -0.01em;
}

.wa-sub {
  font-size: 0.85rem;
  color: var(--muted);
}

/* Description */
.product-description {
  padding-top: clamp(16px, 2.6vw, 26px);
}

.section-title {
  font-size: 1.15rem;
  font-weight: 900;
  letter-spacing: -0.01em;
  margin: 0 0 10px;
}

.description-body {
  color: rgba(51, 65, 85, 0.96);
  line-height: 1.75;
  font-size: 0.98rem;
  padding-bottom: 22px;
  border-bottom: 1px solid var(--line);
}

/* Related (CategoryNav card design + horizontal swipe) */
.related-section {
  padding-top: clamp(16px, 2.6vw, 26px);
}

.related-slider {
  position: relative;
  --gap: 12px;
  --card-basis: calc((100% - (var(--gap) * 4)) / 5); /* 5 visible */
}

.slider-viewport {
  overflow-x: auto;
  overflow-y: hidden;
  width: 100%;
  padding: 6px 0;
  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
  scroll-behavior: smooth;
  touch-action: pan-x pan-y;
  overscroll-behavior-x: contain;

  scroll-padding-left: 6px;
  scroll-padding-right: 6px;

  scrollbar-width: none;
  -ms-overflow-style: none;
}

.slider-viewport::-webkit-scrollbar {
  display: none;
}

.slider-track {
  display: flex;
  gap: var(--gap);
  padding: 0 6px;
}

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

/* OLD PRICE */
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
  .related-slider {
    --card-basis: calc((100% - (var(--gap) * 2)) / 3);
  }
}

/* mobile: ~2 visible + swipe */
@media (max-width: 768px) {
  .related-slider {
    --gap: 8px;
    --card-basis: clamp(150px, 44vw, 185px);
  }

  .slider-track {
    padding: 0 8px;
  }

  .product-card {
    scroll-snap-stop: always;
  }

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

/* very small: still swipeable but not oversized */
@media (max-width: 480px) {
  .related-slider {
    --card-basis: clamp(180px, 78vw, 260px);
  }
}

/* Mobile sticky CTA */
.mobile-sticky {
  display: none;
}

.mobile-wa {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  text-decoration: none;
  font-weight: 900;
  border-radius: 14px;
  padding: 12px 14px;
  color: #0b1f14;
  background: linear-gradient(135deg, rgba(34, 197, 94, 0.22), rgba(34, 197, 94, 0.10));
  border: 1px solid rgba(34, 197, 94, 0.38);
  box-shadow: 0 14px 34px rgba(34, 197, 94, 0.18);
}

/* Responsive */
@media (max-width: 900px) {
  .product-main {
    grid-template-columns: 1fr;
  }

  .attr-row {
    grid-template-columns: 1fr;
    gap: 6px;
  }

  .attr-value {
    justify-content: flex-start;
    text-align: left;
  }
}

@media (max-width: 640px) {
  .thumb-img {
    width: 64px;
    height: 64px;
  }

  .actions {
    display: none; /* replaced by sticky CTA on small screens */
  }

  .mobile-sticky {
    display: block;
    position: fixed;
    left: 12px;
    right: 12px;
    bottom: 12px;
    padding-bottom: env(safe-area-inset-bottom);
    z-index: 50;
  }
}
</style>
