<template>
  <section class="about-section py-5 px-0 overflow-hidden">
    <div class="container-fluid p-0">
      <div class="row g-0 align-items-stretch">
        <!-- Left side with skeleton loader -->
        <div class="col-12 col-md-6 px-4 px-md-5 d-flex flex-column">
          <div class="flex-fill position-relative">
            <!-- placeholder -->
            <div
              v-if="!aboutImageLoaded"
              class="skeleton-placeholder"
            ></div>

            <!-- actual image -->
            <img
              src="/images/about1.png"
              alt="company vehicle"
              class="img-fluid rounded about-img"
              :class="{ 'is-loaded': aboutImageLoaded }"
              @load="onAboutImageLoad"
            />
          </div>
        </div>

        <!-- Right: content drives the height -->
        <div class="col-12 col-md-6 px-4 px-md-5">
          <h2 class="about-title mb-4">Who We Are</h2>
          <p class="about-text">
            In the year 2003 World Auto Dealers revved up the corporate engines
            with a mission to be the dominant force in the automobile
            industry in Sri Lanka. As any organization, World Auto Dealers grew,
            accumulating experience and then stepped onto the World Wide Web
            in 2005. Success kept knocking at our doorstep, with occasional
            downs shaping us into conscience-driven dealers. Now christened
            Nikoba.com, trust capital is still the engine that drives our
            commitment to give customers an all-deserving service.
          </p>

          <!-- <div class="stats row text-center mt-5 g-0">
            <div class="col-4">
              <h3 class="stat-number">836M</h3>
              <p class="stat-label">CARS FOR SALE</p>
            </div>
            <div class="col-4">
              <h3 class="stat-number">738M</h3>
              <p class="stat-label">DEALER REVIEWS</p>
            </div>
            <div class="col-4">
              <h3 class="stat-number">100M</h3>
              <p class="stat-label">VISITORS PER DAY</p>
            </div>
          </div> -->
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "AboutSection",
  data() {
    return {
      aboutImageLoaded: false,
    };
  },
  methods: {
    onAboutImageLoad() {
      this.aboutImageLoaded = true;
    },
  },
};
</script>

<style scoped>
/* reuse skeleton CSS from your banner */
.skeleton-placeholder {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    to right,
    #e0e0e0 0%,
    #f8f8f8 50%,
    #e0e0e0 100%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 16px; /* match img rounding */
}
@keyframes shimmer {
  0% {
    background-position: -200% 0;
  }
  100% {
    background-position: 200% 0;
  }
}

/* fade-in the image once loaded */
.about-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  opacity: 0;
  transition: opacity 0.5s ease;
  border-radius: 16px !important; 
}
.about-img.is-loaded {
  opacity: 1;
}

/* rest of your existing styles */
.about-section {
  overflow-x: hidden;
}
.about-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #171b4c;
}
.about-text {
  color: #555;
  line-height: 1.6;
  font-size: 1rem;
}
.stat-number {
  font-size: 2rem;
  font-weight: 700;
  color: #171b4c;
  margin-bottom: 0.25rem;
}
.stat-label {
  font-size: 0.85rem;
  color: #777;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}
</style>
