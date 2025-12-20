<template>
  <section class="offers-banner">
    <div class="banner-wrap">
      <img
        v-if="imgOk"
        :src="DEFAULT_BANNER"
        :alt="title || 'Special offers banner'"
        class="banner-img"
        loading="lazy"
        @error="imgOk = false"
      />

      <div v-else class="banner-fallback">
        <span>No banner image</span>
      </div>

      <div class="banner-overlay"></div>

      <div class="banner-content">
        <h1 class="banner-title">{{ title }}</h1>
      </div>
    </div>
  </section>
</template>

<script>
const DEFAULT_BANNER = "/images/special-offers-banner.jpg"; // ✅ change only here (public/images)

export default {
  name: "OfferBanner",
  props: {
    title: { type: String, default: "Special Offers" },
  },
  data() {
    return {
      DEFAULT_BANNER,
      imgOk: true,
    };
  },
};
</script>

<style scoped>
/* ✅ EXACT same rules as CategoryBanner */
.offers-banner {
  margin: 10px 0 18px;
}

.banner-wrap {
  position: relative;
  border-radius: 14px;
  overflow: hidden;
  background: #f3f4f6;
  min-height: 160px;
}

.banner-img,
.banner-fallback {
  width: 100%;
  height: clamp(260px, 32vw, 520px);
}

.banner-img {
  display: block;
  object-fit: cover;
  object-position: center;
  background: #ffffff;
}

.banner-fallback {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  font-size: 0.95rem;
}

.banner-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to top,
    rgba(0, 0, 0, 0.60),
    rgba(0, 0, 0, 0.15),
    rgba(0, 0, 0, 0.00)
  );
}

.banner-content {
  position: absolute;
  left: 16px;
  right: 16px;
  bottom: 16px;
}

.banner-title {
  margin: 0;
  color: #ffffff;
  font-weight: 900;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  font-size: clamp(1.05rem, 2.2vw, 1.7rem);
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.35);
}

@media (max-width: 480px) {
  .banner-wrap {
    border-radius: 12px;
  }
  .banner-content {
    left: 12px;
    right: 12px;
    bottom: 12px;
  }
}
</style>
