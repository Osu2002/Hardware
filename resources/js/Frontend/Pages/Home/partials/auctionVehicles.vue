<template>
  <section class="categorySection my-5 container-fluid px-3 px-md-4 main-container">
    <!-- Section header -->
    <div class="row text-left w-100">
      <div class="col-md-12 categorySectionColumn ps-0 mb-0">
        <div class="row align-items-center ps-0">
          <div class="col-8">
            <h2 class="headText secondFontStyle mb-4">
              Live Auction In Japan
            </h2>
          </div>
          <div class="col-4 ps-3 text-end">
            <Link as="button" :href="route('live.auction')" class="view-all-btn">
              View All
              <img src="/images/Vector.png" alt="→" class="arrow-icon" />
            </Link>
          </div>
        </div>

        <!-- spacing -->
        <div class="spacing-divider"></div>

        <!-- direction-aware transition + swipe -->
        <transition :name="transitionName" mode="out-in">
          <div
            :key="page"
            class="grid"
            @touchstart="onTouchStart"
            @touchend="onTouchEnd"
          >
            <div
              class="auction-card"
              v-for="vehicle in currentPageItems"
              :key="vehicle.id"
            >
              <div class="image-panel position-relative">
                <!-- skeleton until the imageLoaded flag flips -->
                <div
                  v-if="!imageLoaded[vehicle.id]"
                  class="skeleton-placeholder"
                ></div>

                <img
                  :src="vehicle.image"
                  alt="Vehicle"
                  class="img-fluid auction-image"
                  :class="{ 'is-loaded': imageLoaded[vehicle.id] }"
                  @load="markLoaded(vehicle.id)"
                />
              </div>



              <div class="content-panel d-flex flex-column">
                <h5 class="model-title">{{ vehicle.name }}</h5>
                <div class="vehicle-info mb-4">
                  <span><i class="fas fa-paint-brush"></i> {{ vehicle.color }}</span>
                  <span><i class="fas fa-car-side"></i> {{ vehicle.type }}</span>
                  <span><i class="fas fa-cogs"></i> {{ vehicle.engine }} CC</span>
                  <span><i class="fas fa-cogs"></i>
                    {{ vehicle.year_from }} - {{ vehicle.year_to }}
                  </span>
                </div>
                <div class="mt-auto d-flex justify-content-between align-items-center">
                  <div class="price"></div>
                  <Link
                    v-if="$root.getUser()"
                    class="view-details-btn"
                    method="post"
                    :href="route('live.auction')"
                    :data="{
                      _method: 'GET',
                      model: vehicle.modal,
                      engine: vehicle.engine,
                      year_from: vehicle.year_from,
                      year_to: vehicle.year_to,
                      color: vehicle.color,
                      search: true
                    }"
                  >
                    View Details
                    <img src="/images/Vector.png" alt="→" class="arrow-icon" />
                  </Link>
                  <Link
                    v-else
                    class="view-details-btn"
                    :href="route('user.login')"
                  >
                    View Details
                    <img src="/images/Vector.png" alt="→" class="arrow-icon" />
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </transition>

      </div>
    </div>

    <!-- Pager controls -->
    <div class="pager ps-3">
      <button
        @click="prevPage"
        :disabled="page === 0"
      >
        <i class="fas fa-chevron-left"></i>
      </button>
      <button
        @click="nextPage"
        :disabled="(page + 1) * perPage >= live_auction_vehicles_list.length"
      >
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>
  </section>
</template>


<script>
import { Link } from '@inertiajs/inertia-vue3';

export default {
  components: { Link },

  data() {
    return {
      page: 0,
      perPage: window.innerWidth < 576 ? 1 : 2,
      transitionName: 'slide-left',   // default when first mounting
      touchStartX: 0,
      touchEndX: 0,
      live_auction_vehicles_list: [
        {
          id: 1,
          name: 'Honda Vezel',
          modal: 'VEZEL',
          color: 'Black',
          type: 'Subcompact SUV',
          engine: '1500',
          year_from: '2022',
          year_to: '2025',
          image: '/images/433589708_402146669123656_5856198691501882829_n-876x535.jpg'

        },
        {
          id: 2,
          name: 'Toyota Yaris',
          modal: 'YARIS',
          color: 'White',
          type: 'Compact Sedan',
          engine: '1000',
          year_from: '2022',
          year_to: '2025',
          image: '/images/2024_Yaris_GRSport_062-scaled.jpg'

        },
        {
          id: 3,
          name: 'Toyota Corolla',
          modal: 'COROLLA',
          color: 'White',
          type: 'Compact Sedan',
          engine: '1800',
          year_from: '2022',
          year_to: '2025',
          image: '/images/2023_toyota_corolla_sedan_xse_fq_oem_1_1280.avif'

        },
        {
          id: 4,
          name: 'Honda Civic',
          modal: 'CIVIC',
          color: 'White',
          type: 'Compact Sedan',
          engine: '2000',
          year_from: '2022',
          year_to: '2025',
          image: '/images/403433-2024-honda-civic.jpg'

        }
      ],
      imageLoaded: {} 
    };
  },
  computed: {
    currentPageItems() {
      const start = this.page * this.perPage;
      return this.live_auction_vehicles_list.slice(start, start + this.perPage);
    }
  },
  methods: {
    prevPage() {
      if (this.page > 0) {
        this.transitionName = 'slide-right'
        this.page--
      }
    },
    nextPage() {
      if ((this.page + 1) * this.perPage < this.live_auction_vehicles_list.length) {
        this.transitionName = 'slide-left'
        this.page++
      }
    },
    onTouchStart(e) {
      this.touchStartX = e.changedTouches[0].clientX
    },
    onTouchEnd(e) {
      this.touchEndX = e.changedTouches[0].clientX
      const delta = this.touchEndX - this.touchStartX

      if (delta < -50) {
        this.nextPage()
      } else if (delta > 50) {
        this.prevPage()
      }
    },
     markLoaded(id) {
    this.imageLoaded[id] = true;
    }
  },
  mounted() {
  // before anything else, mark all as not-loaded
  this.live_auction_vehicles_list.forEach(v => {
    this.imageLoaded[v.id] = false;
  });

 
  },
};
</script>

<style scoped>
/* Section Styles */
.categorySection {
  background-color: #ffffff;
}

.categorySectionColumn {
  margin-top: 30px;
}

.headText {
  /* font-size: 30px; */
  font-size: clamp(1.5rem, 2vw, 2.25rem);  /*added*/
  color: #333;
  line-height: 1.2;
  margin-bottom: 0;
  margin-bottom: var(--title-spacing) !important;
  font-family: 'Poppins', sans-serif !important;
  color: #050B20 !important;
  font-weight: 700 !important;
}

/* Row Styles */
.row {
  --bs-gutter-x: 1.5rem;
  --bs-gutter-y: 0;
  display: flex;
  flex-wrap: wrap;
  margin-top: calc(-1 * var(--bs-gutter-y));
  margin-right: 0 !important;
  margin-left: 0 !important;
}

/* View All Button Styles */
.view-all-btn {
  background: none;
  color: #000000;
  text-decoration: none;
  font-weight: 600;
  cursor: pointer;
  border: none;
  display: inline-flex;
  align-items: center;
  justify-content: flex-end;
  gap: 0.4rem;                   /* small space between text & icon */
  white-space: nowrap; /*added*/
  font-size: clamp(0.875rem, 1vw, 1.125rem) !important;  /*added*/
}

/* .view-details-btn .arrow-icon {
  width: 15px;
  height: 15px;
  margin-left: 1.2rem;
  vertical-align: middle;
  


} */
.view-all-btn:hover {
  text-decoration: underline;
}

.arrow-icon {
  /* width: 15px;
  height: 15px;
  margin-left: 1.2rem; */
  width:  clamp(0.75rem, .5vw, 0.9rem); /*added*/
  height: clamp(0.75rem, .5vw, 0.9rem); /*added*/
  vertical-align: middle;
}

.arrow-icon_1 {
  width: 15px;
  height: 15px;
  margin-left: 1.2rem;
  vertical-align: middle;
}


/* Grid layout */
.grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
  margin-bottom: 2rem;
  padding: 0 0.75rem;
  /* Add this line to match the padding */
  margin-bottom: var(--bottom-spacing);
}

.auction-card {
  display: flex;
  height: 300px;
  border-radius: 16px;
  overflow: hidden;
  box-shadow:0px 0px 9px 0px rgb(81 97 140 / 72%);
  background: #fff;
}

/* Left image */
.image-panel {
  flex: 1;
}

.image-panel img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Right content */
.content-panel {
  flex: 1;
  padding: 1.5rem;
  background: #f0f3f7;

  color: #000000;
  display: flex;
  flex-direction: column;

}

.model-title {
  font-size: clamp(1rem, 2.5vw, 1.25rem);
  margin-bottom: 0.25rem;
  font-family: 'Poppins', sans-serif !important;
}

.model-subtitle {
  font-size: clamp(0.75rem, 2vw, 0.875rem);
  opacity: 0.8;
  margin-bottom: 1rem;
  font-family: 'Poppins', sans-serif !important;
}

.vehicle-info {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 0.75rem;
  font-size: clamp(0.75rem, 2vw, 0.875rem);
  opacity: 0.85;
}

.vehicle-info i {
  margin-right: 0.25rem;

}

.price {
  font-size: clamp(1.25rem, 3vw, 1.5rem);
  font-weight: 600;
}

.view-details-btn {
  /* make it show up by default */
  opacity: 1 !important;
  visibility: visible !important;

  /* basic button styling */
  display: inline-flex;
  align-items: center;
  gap: 1.2rem;
  padding: 0.5rem 1rem;
  color: black;
  border-radius: 0.25rem;
  font-size: 15px;
  font-weight: 200;
  text-decoration: none;
  font-family: 'Poppins', sans-serif !important;
}

/* rotate the arrow up-and-right */
.view-details-btn .fa-arrow-right {
  transform: rotate(-45deg);
  font-size: 1.2rem;
}

.view-details i {
  margin-left: 0.5rem;
}

.pager {
  display: flex;
  justify-content: left;
  gap: 1rem;
  padding: 2rem 0;
  margin-top: var(--bottom-spacing);
  margin-bottom: var(--bottom-spacing);
}

.pager button {
  width: 60px;
  height: 40px;
  background: #fff;
  border: 1.5px solid #3f3f3fd0;
  border-radius: 25px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.pager button:hover {
  background: #f8f9fa;
  border-color: #3f3f3fd0;
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.08);
}

.pager button:disabled {
  opacity: 0.4;
  cursor: default;
  box-shadow: none;
}

.pager button i {
  font-size: 16px;
  color: #333;
}

/* Responsive adjustments */
@media (max-width: 992px) {
  .grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .auction-card {
    height: 280px;
  }

  .content-panel {
    padding: 1rem;
  }
}

@media (max-width: 768px) {
  .headText {
    font-size: 24px;
    font-weight: 700;
  }

  .categorySection {
    padding: 20px 0;
  }

  .grid {
    grid-template-columns: 1fr;
  }

  .auction-card {
    flex-direction: column;
    height: auto;
    min-height: 400px;
  }

  .image-panel {
    height: 200px;
  }

  .content-panel {
    flex: none;
  }

  .vehicle-info {
    gap: 0.5rem;
  }
}

@media (max-width: 576px) {
  .auction-card {
    min-height: 350px;
  }

  .image-panel {
    height: 180px;
  }

  .content-panel {
    padding: 0.75rem;
  }
}

/* Add these CSS rules in the <style> section */
.spacing-divider {
  height: 2rem;
}

/* fade-slide transition */
/* .fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.5s ease;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.fade-slide-enter-to {
  opacity: 1;
  transform: translateX(0);
}

.fade-slide-leave-from {
  opacity: 1;
  transform: translateX(0);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateX(-100%);
}


.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.4s ease;
}
.fade-slide-enter-from {
  opacity: 0;
  transform: translateX(20px);
}
.fade-slide-enter-to {
  opacity: 1;
  transform: translateX(0);
}
.fade-slide-leave-from {
  opacity: 1;
  transform: translateX(0);
}
.fade-slide-leave-to {
  opacity: 0;
  transform: translateX(-20px);
} */

/* forward (next): incoming from right */
.slide-left-enter-active,
.slide-left-leave-active {
  transition: all 0.4s ease;
}
.slide-left-enter-from { opacity: 0; transform: translateX(20px); }
.slide-left-enter-to   { opacity: 1; transform: translateX(0); }
.slide-left-leave-from { opacity: 1; transform: translateX(0); }
.slide-left-leave-to   { opacity: 0; transform: translateX(-20px); }

/* backward (prev): incoming from left */
.slide-right-enter-active,
.slide-right-leave-active {
  transition: all 0.4s ease;
}
.slide-right-enter-from { opacity: 0; transform: translateX(-20px); }
.slide-right-enter-to   { opacity: 1; transform: translateX(0); }
.slide-right-leave-from { opacity: 1; transform: translateX(0); }
.slide-right-leave-to   { opacity: 0; transform: translateX(20px); }



.skeleton-placeholder {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: linear-gradient(
    to right,
    #e0e0e0 0%,
    #f8f8f8 50%,
    #e0e0e0 100%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: inherit; /* match whatever rounding .auction-card or .image-panel already has */
}

@keyframes shimmer {
  0%   { background-position: -200% 0; }
  100% { background-position:  200% 0; }
}

/* fade-in on top of your existing .img-fluid rules */
.auction-image {
  opacity: 0;
  transition: opacity 0.5s ease;
}

.auction-image.is-loaded {
  opacity: 1;
}
</style>
