<template>
  <!-- Full-bleed “World Auto Dealers” section -->
  <section class="world-auto-dealers-section py-5">
    <div class="container-fluid p-0">
      <!-- use align-items-stretch so both cols match height -->
      <div class="row g-0 align-items-stretch rounded-4">
        <!-- Left: Content with padding -->
        <div class="col-12 col-lg-6 px-4 px-md-5 py-4 d-flex flex-column">
          <h2 class="section-title mb-3">World Auto Dealers</h2>

          <p>
            Bearing the corporate name <strong>World Auto Dealers</strong> on our
            humble threshold, we stepped into the arena of the
            automobile industry in the year 2003 and specialized
            ourselves in importing Japanese vehicles to elevate life
            style of our fellow Sri Lankans. Becoming one of the
            dominant forces in the automobile market we have built
            ourselves on the trust capital earned through our valued
            customers. Capitalizing on clean transparent business deals,
            we provide the auction sheet to the client that encloses the
            grades and A–Z details of the vehicle in topic.
          </p>

          <p>
            As an ever-growing organization we are honour-bound to
            enhance our services provided to all our much-treasured
            clients, therefore since 2010 we have given our customers the
            option to select their most preferred vehicle from the Japan
            auto auction by showing them the real condition of the vehicle.
            This facility is now further enhanced with a direct bidding
            link to live auto auctions in Japan. With this crystal-clear
            transparency, buyers know exactly:
          </p>

          <ul class="list-unstyled mt-4">
            <li class="d-flex align-items-start mb-2">
              <i class="fa-solid fa-circle-check me-2 check-icon"></i>
              Auction grade (exterior & interior)
            </li>
            <li class="d-flex align-items-start mb-2">
              <i class="fa-solid fa-circle-check me-2 check-icon"></i>
              Final auction selling price
            </li>
            <li class="d-flex align-items-start mb-2">
              <i class="fa-solid fa-circle-check me-2 check-icon"></i>
              Original mileage
            </li>
            <li class="d-flex align-items-start mb-2">
              <i class="fa-solid fa-circle-check me-2 check-icon"></i>
              Condition options
            </li>
            <li class="d-flex align-items-start">
              <i class="fa-solid fa-circle-check me-2 check-icon"></i>
              Color details
            </li>
          </ul>
        </div>

        <!-- Right: Image flush with same padding, flex to fill -->
        <div class="col-12 col-lg-6 px-4 px-md-5 p-0 d-flex flex-column">
          <div class="flex-fill position-relative">
            <!-- skeleton placeholder -->
            <div v-if="!worldImageLoaded" class="skeleton-placeholder"></div>

            <img src="/images/new1.png" alt="World Auto Dealers" class="w-100 h-100 object-fit-cover rounded-4"
              :class="{ 'is-loaded': worldImageLoaded }" @load="onWorldImageLoad" />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "WorldAutoDealersSection",

  data() {
    return {
      worldImageLoaded: false,   // ← new
    };
  },
  methods: {
    onWorldImageLoad() {
      this.worldImageLoaded = true;
    },
  },
};
</script>

<style>
/* break out to full viewport width */
.world-auto-dealers-section {
  position: relative;
  left: 50%;
  margin-left: -50vw;
  width: 100vw;
  overflow-x: hidden;
}

/* match “Who We Are” header style */
.section-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #171b4c;
}

/* Icon styling */
.check-icon {
  color: #405ef2;
  font-size: 1.25rem;
  line-height: 1;
}

.object-fit-cover {
  object-fit: cover;
  width: 100%;
  height: 100%;
  border-radius: 15px;
  opacity: 0;
  transition: opacity 0.5s ease;
}

.object-fit-cover.is-loaded {
  opacity: 1;
}

.skeleton-placeholder {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to right,
      #e0e0e0 0%, #f8f8f8 50%, #e0e0e0 100%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 15px;
  /* match the img rounding */
}

@keyframes shimmer {
  0% {
    background-position: -200% 0;
  }

  100% {
    background-position: 200% 0;
  }
}
</style>
