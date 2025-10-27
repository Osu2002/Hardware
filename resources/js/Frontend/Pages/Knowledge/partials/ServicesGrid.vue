<template>
    <section class="container-fluid my-5">
        <!-- Heading -->
        <div class="text-center mb-4">
            <h2 class="fw-bold">How To Order Step By Step</h2>
        </div>

        <!-- Steps Grid -->
        <div class="row g-4">
           <div v-for="step in steps" :key="step.number" class="col-6 col-md-3">
  <div class="step-card rounded overflow-hidden position-relative">
    <div class="image-loader-container position-relative w-100 h-100">
      <!-- shimmer placeholder -->
      <div v-if="!step.loaded" class="skeleton-placeholder"></div>

      <!-- actual image -->
      <img
        :src="step.img"
        :alt="step.title"
        class="w-100 h-100 object-fit-cover"
        :class="{ 'is-loaded': step.loaded }"
        @load="step.loaded = true"
      />
    </div>

    <!-- gradient + text overlay -->
    <div class="overlay position-absolute top-0 start-0 w-100 h-100"></div>
    <div class="step-info position-absolute top-0 start-0 p-3 text-white">
      <div class="step-number fw-bold">{{ step.number }}</div>
      <div class="step-title">{{ step.title }}</div>
    </div>
  </div>
</div>

        </div>
    </section>
</template>

<script>
export default {
    name: "OrderSteps",
    data() {
        return {
            steps: [
                 { number: "01", title: "Dream to buy your dreaming car", img: "/images/Link.png", loaded: false },
                { number: "02", title: "Select From Auction",        img: "/images/Link (1).png", loaded: false },
                { number: "03", title: "Generate Enquiry",           img: "/images/3.png",       loaded: false },
                { number: "04", title: "Complete Deposit",           img: "/images/4 (2).png",   loaded: false },
                { number: "05", title: "Successful Bid",             img: "/images/5.png",       loaded: false },
                { number: "06", title: "Vehicle Shipment",           img: "/images/6.png",       loaded: false },
                { number: "07", title: "Vehicle Delivery",           img: "/images/7.png",       loaded: false },
                { number: "08", title: "Drive With Pleasure",         img: "/images/8.png",       loaded: false },
            ],
        };
    },
};
</script>

<style scoped>
.step-card {
    /* keep cards square */
    aspect-ratio: 1 / 1;
}

/* make the img cover the card */
.object-fit-cover {
    object-fit: cover;
}

/* the dark gradient from bottom up */
.overlay {
    background: linear-gradient(180deg,
            rgba(0, 0, 0, 0.2) 0%,
            rgba(0, 0, 0, 0.7) 100%);
}

/* number and title styling */
.step-info .step-number {
    font-size: 1.25rem;
    letter-spacing: 0.05em;
}

.step-info .step-title {
    margin-top: 0.25rem;
    font-size: 0.9rem;
}


.image-loader-container {
  width: 100%;
  height: 100%;
  overflow: hidden;
}

/* shimmer placeholder */
.skeleton-placeholder {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: linear-gradient(
    to right,
    #e0e0e0 0%, #f8f8f8 50%, #e0e0e0 100%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}

/* fade-in the actual image */
.object-fit-cover {
  transition: opacity 0.5s ease;
  opacity: 0;
}
.object-fit-cover.is-loaded {
  opacity: 1;
}

@keyframes shimmer {
  0%   { background-position: -200% 0; }
  100% { background-position:  200% 0; }
}

</style>
