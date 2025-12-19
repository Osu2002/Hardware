<template>
  <div class="product-page">
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
      <a href="/" class="crumb-link">Home</a>

      <template v-for="cat in product.categories" :key="cat.id">
        <span class="crumb-separator">/</span>
        <!-- Adjust URL if your category route is different -->
        <a :href="`/category/${cat.slug}`" class="crumb-link">
          {{ cat.title }}
        </a>
      </template>

      <span class="crumb-separator">/</span>
     <span class="crumb-current" :title="product.name">
  {{ truncateName(product.name) }}
</span>

    </nav>

    <!-- Main layout -->
    <div class="product-main">
      <!-- Gallery -->
      <div class="product-gallery">
        <div class="main-image-wrapper">
          <img
            v-if="activeImageUrl"
            :src="activeImageUrl"
            :alt="product.name"
            class="main-image"
          />

          <div class="badges">
            <!-- <span v-if="product.is_new" class="badge badge-new">NEW</span> -->
            <span
              v-if="product.discountLabel"
              class="badge badge-discount"
            >
              -{{ product.discountLabel }}  OFF
            </span>
          </div>
        </div>

        <div
          v-if="product.gallery && product.gallery.length"
          class="thumbs-row"
        >
          <button
            v-for="img in product.gallery"
            :key="img.id"
            type="button"
            class="thumb-btn"
            :class="{ active: img.id === activeImageId }"
            @click="activeImageId = img.id"
          >
            <img
              :src="img.url"
              :alt="product.name"
              class="thumb-img"
            />
          </button>
        </div>
      </div>

      <!-- Info -->
      <div class="product-info">
      <h1 class="title" :title="product.name">
  {{ truncateName(product.name) }}
</h1>


        <div v-if="product.brandLogo" class="brand-logo-row">
  <span class="brand-label">Brand:</span>
  <img :src="product.brandLogo" :alt="product.brand || 'Brand'" class="brand-logo" />
</div>

<p v-else-if="product.brand" class="brand">
  Brand: <span>{{ product.brand }}</span>
</p>

<div v-if="product.attributes && product.attributes.length" class="attrs">
  <div class="attr-row" v-for="a in product.attributes" :key="a.code">
    <div class="attr-label">{{ a.label }}</div>

    <div class="attr-value">
      <template v-if="a.type === 'color'">
        <span class="color-swatch" :style="{ background: a.value }"></span>
        {{ a.value }}
      </template>
      <template v-else>
        {{ a.value }}<span v-if="a.unit"> {{ a.unit }}</span>
      </template>
    </div>
  </div>
</div>

<div v-if="product.inStock !== null && product.inStock !== undefined" class="meta-row">
  <div class="meta-label">Availability</div>
  <div class="meta-value" :class="product.inStock == 1 ? 'instock' : 'outstock'">
    {{ product.inStock == 1 ? 'In Stock' : 'Out of Stock' }}
    <span v-if="product.stockCount !== null && product.stockCount !== undefined">
      ({{ product.stockCount }})
    </span>
  </div>
</div>

<div v-if="product.warrantyPeriod || product.warrantyType" class="meta-row">
  <div class="meta-label">Warranty</div>
  <div class="meta-value">
    <span v-if="product.warrantyPeriod">{{ product.warrantyPeriod }}</span>
    <span v-if="product.warrantyPeriod && product.warrantyType"> • </span>
    <span v-if="product.warrantyType">{{ product.warrantyType }}</span>
  </div>
</div>

<div class="price-block">

  <!-- <span v-if="product.discountLabel" class="badge badge-discount">
  -{{ product.discountLabel }}
</span> -->


 <del v-if="product.oldPrice" class="price-old">
  Rs{{ formatPrice(product.oldPrice) }}
</del>


  <div class="price-main">
    Rs{{ formatPrice(product.price) }}
  </div>

 
</div>


        <p
          v-if="product.short_description"
          class="short-desc"
        >
          {{ product.short_description }}
        </p>

        <div class="actions">
          <button type="button" class="btn-primary">
            BUY NOW
          </button>
          <button type="button" class="btn-outline">
            ADD TO CART
          </button>
        </div>
      </div>
    </div>

    <!-- Description -->
    <section
      v-if="product.description"
      class="product-description"
    >
      <h2>Description</h2>
      <!-- assuming HTML stored in DB -->
      <div class="description-body" v-html="product.description" />
    </section>

    <!-- Related products -->
    <section
      v-if="relatedProducts && relatedProducts.length"
      class="related-section"
    >
      <h2>Related Products</h2>

      <div class="related-grid">
        <article
          v-for="p in relatedProducts"
          :key="p.id"
          class="related-card"
          @click="goToProduct(p.slug)"
        >
          <div class="related-image-wrapper">
            <img
              :src="p.image"
              :alt="p.name"
              class="related-image"
            />
          </div>
          <div class="related-body">
            <h3 class="related-title">
              {{ p.name }}
            </h3>
            <div class="related-price">
              Rs{{ formatPrice(p.price) }}
            </div>
          </div>
        </article>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'ProductView',

  props: {
    product: {
      type: Object,
      required: true,
    },
    relatedProducts: {
      type: Array,
      default: () => [],
    },
  },

  data() {
    return {
      activeImageId:
        this.product.gallery && this.product.gallery.length
          ? this.product.gallery[0].id
          : null,
    };
  },

  computed: {
    activeImageUrl() {
      if (!this.product.gallery || !this.product.gallery.length) {
        return this.product.primaryImage || '';
      }

      const img = this.product.gallery.find(
        (g) => g.id === this.activeImageId,
      );

      return (img && img.url) || this.product.primaryImage || '';
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


    goToProduct(slug) {
      // Reuse the same route when clicking related products
      this.$inertia.visit(`/products/${slug}`);
      // or, if Ziggy is enabled:
      // this.$inertia.visit(route('products.show', slug));
    },
  },
};
</script>

<style scoped>
.product-page {
  padding: 40px 0 60px;
  /* background: #f3f4f6; */
}

/* breadcrumb */
.breadcrumb {
  font-size: 0.85rem;
  color: #6b7280;
  margin-bottom: 18px;
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  align-items: center;
}

.crumb-link {
  color: #4b5563;
  text-decoration: none;
}

.crumb-link:hover {
  text-decoration: underline;
}

.crumb-current {
  font-weight: 600;
  color: #111827;
}

.crumb-separator {
  color: #9ca3af;
}

/* main layout */
.product-main {
  display: grid;
  grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr);
  gap: 32px;
  align-items: flex-start;
  margin-bottom: 32px;
}

/* gallery */
.product-gallery {
  background: #ffffff;
  border-radius: 10px;
  padding: 16px;
  box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
}

.main-image-wrapper {
  position: relative;
  padding-top: 70%;
  background: #f9fafb;
  border-radius: 8px;
  overflow: hidden;
}

.main-image {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: contain;
  background: #ffffff;
}

.badges {
  position: absolute;
  left: 10px;
  top: 10px;
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

.badge {
  font-size: 0.7rem;
  font-weight: 700;
  padding: 4px 9px;
  border-radius: 4px;
  color: #ffffff;
}

.badge-new {
  background: #1d4ed8;
}

.badge-discount {
  background: #dc2626;
}

.thumbs-row {
  display: flex;
  gap: 8px;
  margin-top: 10px;
  overflow-x: auto;
  padding-bottom: 4px;
}

.thumb-btn {
  border: 1px solid transparent;
  border-radius: 6px;
  padding: 0;
  background: transparent;
  flex: 0 0 auto;
}

.thumb-btn.active {
  border-color: #22c55e;
}

.thumb-img {
  display: block;
  width: 64px;
  height: 64px;
  object-fit: contain;
  background: #ffffff;
  border-radius: 6px;
}

/* info */
.product-info {
  background: #ffffff;
  border-radius: 10px;
  padding: 20px 18px;
  box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
}

.title {
  font-size: 1.3rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 6px;
}

.brand {
  font-size: 0.9rem;
  color: #6b7280;
  margin-bottom: 14px;
}

.brand span {
  font-weight: 600;
  color: #111827;
}

.price-block {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 6px;
  margin-bottom: 12px;
}

.price-main {
  font-size: 1.3rem;
  font-weight: 800;
  color: #f97316;
}

.price-old {
  display: inline-block;
  font-size: 0.95rem;
  color: #9ca3af;
  padding-top: 8px;
  text-decoration: line-through !important;
  text-decoration-thickness: 2px;
}


.price-label {
  font-size: 0.8rem;
  font-weight: 700;
  color: #dc2626;
  background: #fee2e2;
  padding: 8px 8px 2px; /* top padding added */
  border-radius: 999px;
}

.short-desc {
  font-size: 0.95rem;
  color: #4b5563;
  margin-bottom: 20px;
}

.actions {
  display: flex;
  gap: 10px;
}

.btn-primary,
.btn-outline {
  border-radius: 6px;
  font-size: 0.9rem;
  font-weight: 700;
  padding: 8px 20px;
  cursor: pointer;
  border: 1px solid transparent;
}

.btn-primary {
  background: #22c55e;
  color: #ffffff;
  border-color: #22c55e;
}

.btn-primary:hover {
  background: #16a34a;
}

.btn-outline {
  background: #ffffff;
  color: #111827;
  border-color: #d1d5db;
}

.btn-outline:hover {
  background: #f3f4f6;
}

/* description */
.product-description {
  background: #ffffff;
  border-radius: 10px;
  padding: 18px 18px 20px;
  box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
  margin-bottom: 28px;
}

.product-description h2 {
  font-size: 1.1rem;
  font-weight: 700;
  margin-bottom: 10px;
}

.description-body {
  font-size: 0.95rem;
  color: #4b5563;
}

/* related */
.related-section h2 {
  font-size: 1.1rem;
  font-weight: 700;
  margin-bottom: 12px;
}

.related-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 14px;
}

.related-card {
  background: #ffffff;
  border-radius: 8px;
  cursor: pointer;
  box-shadow: 0 3px 10px rgba(15, 23, 42, 0.05);
  overflow: hidden;
  transition: transform 0.12s ease, box-shadow 0.12s ease;
}

.related-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(15, 23, 42, 0.18);
}

.related-image-wrapper {
  padding-top: 70%;
  background: #f9fafb;
  position: relative;
}

.related-image {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: contain;
  background: #ffffff;
}

.related-body {
  padding: 8px 10px 10px;
  text-align: center;
}

.related-title {
  font-size: 0.9rem;
  font-weight: 600;
  color: #111827;
  min-height: 2.5em;
  margin-bottom: 4px;
}

.related-price {
  font-size: 0.9rem;
  font-weight: 700;
  color: #f97316;
}

/* responsive */
@media (max-width: 768px) {
  .product-main {
    grid-template-columns: minmax(0, 1fr);
  }
}
.brand-logo-row{
  display:flex;
  align-items:center;
  gap:10px;
  margin-bottom:14px;
}
.brand-label{ font-size:0.9rem; color:#6b7280; }
.brand-logo{
  height:26px;
  max-width:140px;
  object-fit:contain;
}

.attrs{
  border-top:1px solid #eee;
  padding-top:12px;
  margin-top:10px;
  margin-bottom:12px;
}
.attr-row{
  display:flex;
  justify-content:space-between;
  gap:12px;
  padding:6px 0;
  border-bottom:1px dashed #eee;
}
.attr-label{ color:#6b7280; font-size:0.9rem; }
.attr-value{ color:#111827; font-weight:600; font-size:0.9rem; text-align:right; }

.color-swatch{
  display:inline-block;
  width:12px;height:12px;
  border-radius:3px;
  border:1px solid #ddd;
  vertical-align:middle;
  margin-right:6px;
}

.meta-row{
  display:flex;
  justify-content:space-between;
  gap:12px;
  padding:6px 0;
}
.meta-label{ color:#6b7280; font-size:0.9rem; }
.meta-value{ font-weight:700; font-size:0.9rem; }
.instock{ color:#16a34a; }
.outstock{ color:#dc2626; }


.badge-discount{
  padding-top: 6px;
}

</style>
