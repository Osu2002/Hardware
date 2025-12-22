<template>
  <section class="featured-banner">
    <div class="banner-wrap">
      <img
        v-if="DEFAULT_BANNER"
        :src="DEFAULT_BANNER"
        :alt="title || 'Featured banner'"
        class="banner-img"
        loading="lazy"
      />

      <div v-else class="banner-fallback">
        <span>No featured banner image</span>
      </div>

      <div class="banner-overlay"></div>

      <div class="banner-content">
        <h1 class="banner-title">{{ title }}</h1>
      </div>
    </div>
  </section>
</template>

<script>
const DEFAULT_BANNER = "/images/featured-banner.jpg"; // ✅ change only here

export default {
  name: "FeaturedBanner",
  data() {
    return { DEFAULT_BANNER };
  },
  props: {
    title: { type: String, default: "Featured Products" },
  },
};
</script>

<style scoped>
/* ✅ EXACT same rules as CategoryBanner */

.featured-banner {
  font-family: "Abadi MT Condensed Light", "Abadi MT Condensed", "Abadi MT", Abadi,
    system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
}

.featured-banner * {
  font-family: inherit;
}

.featured-banner {
  margin: 10px 0 18px;
}

.banner-wrap {
  position: relative;
  border-radius: 14px;
  overflow: hidden;
  background: #f3f4f6;
  min-height: 160px;
}

/* ✅ IMPORTANT: image + fallback MUST share same height */
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
