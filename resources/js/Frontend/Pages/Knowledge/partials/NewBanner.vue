<template>
  <div class="carousel slide" role="region" aria-label="Featured Vehicle Stock">
    <div class="carousel-inner">
      <div class="carousel-item d-block custom_slide_height">
        <!-- shimmer placeholder until the image loads -->
        <div v-if="!bannerLoaded" class="skeleton-placeholder"></div>

        <!-- the banner image will fade in once loaded -->
        <img
          src="/images/Background (7).png"
          class="d-block w-100 custom_slide_height banner-image"
          :class="{ 'is-loaded': bannerLoaded }"
          @load="onBannerLoad"
          alt="Available stock"
        />

        <div class="carousel-caption custom_caption_position" style="top: 90%">
          <div class="caption">
            <div class="p-0">
              <h1 class="main-topic-banner">Knowledge Center</h1>
            </div>
            <!--
            <div class="hero-content-about">
              <div class="breadcrumb-wrapper">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                      <Link :href="route('index')">Home</Link>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                      Available stock
                    </li>
                    <span
                      class="breadcrumb-item active"
                      aria-current="page"
                      v-if="selectedCountry"
                    >
                      {{ selectedCountry.name }}
                    </span>
                  </ol>
                </nav>
              </div>
            </div>
            -->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";

export default {
  components: { Link },
  props: {
    selectedCountry: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      // tracks whether the banner image has finished loading
      bannerLoaded: false,
    };
  },
  methods: {
    // called when the <img> emits its load event
    onBannerLoad() {
      this.bannerLoaded = true;
    },
  },
  mounted() {
    console.log("Selected Country in Banner:", this.selectedCountry);
  },
};
</script>

<style scoped>
/* —————————————— Loader/Shimmer —————————————— */
.skeleton-placeholder {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 91vh; /* match your .custom_slide_height */
  background: linear-gradient(
    to right,
    #e0e0e0 0%,
    #f8f8f8 50%,
    #e0e0e0 100%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  z-index: 1;
}
@keyframes shimmer {
  0%   { background-position: -200% 0; }
  100% { background-position:  200% 0; }
}

/* fade-in the real banner once loaded */
.banner-image {
  opacity: 0;
  transition: opacity 0.5s ease;
  z-index: 2;
}
.banner-image.is-loaded {
  opacity: 1;
}

/* —————————————— Your existing styles —————————————— */
.carousel-item {
  position: relative;
}

.custom_slide_height {
  height: 91vh !important;
  object-fit: cover;
}

.carousel-caption {
  position: absolute;
  right: 15%;
  bottom: 1.25rem;
  left: 3%;
  padding-top: 0;
  padding-bottom: 1.25rem;
  color: #fff;
  text-align: left;
}

.main-topic-banner {
  font-size: 2.4rem;
  font-weight: 700;
  color: #ffffff;
  line-height: 1.2;
  /* margin-left left blank intentionally */
  display: block;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

@media (max-width: 768px) {
  .main-topic-banner {
    font-size: 2.4rem;
    margin-top: -80px;
  }
  .custom_slide_height {
    height: 50vh !important;
  }
}

@media (max-width: 576px) {
  .main-topic-banner {
    font-size: 1.8rem;
    margin-top: -60px;
  }
  .custom_slide_height {
    height: 40vh !important;
  }
}

/* kept commented styles for your future use */
/*
.hero-content-about { … }
.about-us-text { … }
.breadcrumb-wrapper { … }
.breadcrumb { … }
*/
</style>
