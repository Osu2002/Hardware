<template>
    <div
        class="carousel slide"
        role="region"
        aria-label="Featured Vehicle Stock"
    >
        <div class="carousel-inner">
            <div class="carousel-item d-block custom_slide_height">
        <div v-if="!imageLoaded" class="skeleton-placeholder"></div>
                <img
                    src="/images/about.png"
          :class="['d-block', 'w-100', 'custom_slide_height', 'banner-image', { 'is-loaded': imageLoaded }]"
                    alt="Available stock"
                    @load="onImageLoad"
                />
                <div
                    class="carousel-caption custom_caption_position"
                    style="top: 90%"
                >
                    <div class="caption">
                        <div class="p-0">
                            <h1 class="main-topic-banner">About Us</h1>
                        </div>
                        <!-- <div class="hero-content-about">
                            <div class="breadcrumb-wrapper">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <Link :href="route('index')">Home</Link>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Available stock
                                </li>
                                <span class="breadcrumb-item active" aria-current="page" v-if="selectedCountry">{{ selectedCountry.name }}</span>

                                </ol>
                            </nav>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";

export default {
     data() {
    return {
      imageLoaded: false,
    };
  },
   methods: {
    onImageLoad() {
      this.imageLoaded = true;
    },
  },
    components: {
        Link,
    },
    props: {
        selectedCountry: {
            type: Object,
            default: null,
        },
    },
    mounted() {
        console.log("Selected Country in Banner:", this.selectedCountry);
    },
};
</script>

<style scoped>
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
    padding-top: 0rem;
    padding-bottom: 1.25rem;
    color: #fff;
    text-align: left;
}

.skeleton-placeholder {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 91vh;
  background: linear-gradient(
    to right,
    #e0e0e0 0%,
    #f8f8f8 50%,
    #e0e0e0 100%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  0% {
    background-position: -200% 0;
  }
  100% {
    background-position: 200% 0;
  }
}

.banner-image {
  opacity: 0;
  transition: opacity 0.5s ease;
}

.banner-image.is-loaded {
  opacity: 1;
}

.main-topic-banner {
    font-size: 2.4rem;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
    margin-left: 1;
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

.hero-content-about {
    margin-top: 10px;
    padding: 0px;
    position: relative;
}

.about-us-text {
    font-size: 1.2rem;
    color: #ffffff;
    margin: 0 auto 15px;
    max-width: 90%;
}

.breadcrumb-wrapper {
    display: flex;
    justify-content: center;
}

.breadcrumb {
    background: transparent;
    padding: 0px 15px;
    margin: 0;
    display: flex;
    align-items: center;
}

.breadcrumb-item {
    font-size: 1rem;
    color: #ffffff;
}

.breadcrumb-item a {
    color: #ffffff;
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb-item a:hover {
    color: #cccccc;
}

.breadcrumb-item a:focus {
    outline: 2px solid #ffbf00;
    outline-offset: 2px;
}

.breadcrumb-item.active {
    color: #cccccc;
}

.breadcrumb-item + .breadcrumb-item::before {
    color: #ffffff;
}

@media (max-width: 768px) {
    .main-topic-banner {
        font-size: 2.4rem;
    }

    .custom_slide_height {
        height: 50vh !important;
    }

    .hero-content-about {
        margin-top: 15px;
    }

    .about-us-text {
        font-size: 1rem;
    }

    .breadcrumb-item {
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .main-topic-banner {
        font-size: 1.8rem;
    }

    .custom_slide_height {
        height: 40vh !important;
    }

    .hero-content-about {
        margin-top: 10px;
    }

    .about-us-text {
        font-size: 0.9rem;
    }

    .breadcrumb-item {
        font-size: 0.8rem;
    }
}
</style>
