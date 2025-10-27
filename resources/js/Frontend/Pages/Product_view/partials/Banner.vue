<template>
    <div class="carousel slide" role="region" aria-label="Featured Vehicle Stock">
        <div class="carousel-inner">
            <div class="carousel-item d-block custom_slide_height">
                <div class=""></div>
                <img src="/images/live.png" class="d-block w-100  custom_slide_height" alt="Available stock" />
                <div class="carousel-caption custom_caption_position" style="top: 90%">
                    <div class="caption">
                        <div class="p-0">
                            <h1 class="main-topic-banner">
                                <a style="color: #ffffff" href="/">Home</a> /
                                <a style="color: #ffffff" href="/available-cars">Listings</a> /
                                <span class="current">
                                    {{ vehicle.manufacture.title }} {{ vehicle.vehicle_model.title }}
                                </span>
                            </h1>
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
    components: {
        Link,
    },
    props: {
        selectedCountry: {
            type: Object,
            default: null,
        },
        vehicle: Object,

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

.breadcrumb-item+.breadcrumb-item::before {
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
