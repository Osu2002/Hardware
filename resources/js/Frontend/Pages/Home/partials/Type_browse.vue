<template>
  <section class="categorySection my-5 mb-0 pt-2 container-fluid">
    <div class="row text-left w-100">
      <div class="col-md-12 categorySectionColumn ps-0 mb-0">
        <!-- Header -->
        <div class="row align-items-center ps-0">
          <div class="col-8 ps-3">
            <h2 class="headText secondFontStyle text-left fw-semibold fs-2 fs-md-1">
              Browse by Type
            </h2>
          </div>
         
        </div>

        <!-- Desktop Grid -->
        <div class="container-fluid mt-4 d-none d-sm-block">
          <div class="row">
            <div
              v-for="(vehicleType, idx) in vehicle_types.slice(0, 2)"
              :key="vehicleType.id"
              class="col-12 col-sm-6 mb-4"
            >
              <Link
                method="post"
                :href="route('available')"
                :data="{ _method: 'GET', type: vehicleType.id }"
              >
                <div
                  class="category-card big-card "
                  :style="{ '--delay': idx * 150 + 'ms' }"
                >
                  <div class="img-container position-relative">
                     <div
                      v-if="!imageLoaded[vehicleType.id]"
                      class="skeleton-placeholder"
                    ></div>
                    <img
                      :src="vehicleType.media?.[0]?.original_url"
                      alt="Vehicle Type Image"
                      class="vehicle-image"
                      :class="{ 'is-loaded': imageLoaded[vehicleType.id] }"
                      @load="imageLoaded[vehicleType.id] = true"
                    />
                    <div class="button-overlay">
                      <button class="category-button">
                        {{ vehicleType.title }}
                      </button>
                    </div>
                  </div>
                </div>
              </Link>
            </div>
          </div>
          <div class="row mt-4">
            <div
              v-for="(vehicleType, idx) in vehicle_types.slice(2, 5)"
              :key="vehicleType.id"
              class="col-12 col-sm-6 col-md-4 mb-4"
            >
              <Link
                method="post"
                :href="route('available')"
                :data="{ _method: 'GET', type: vehicleType.id }"
              >
                <div
                  class="category-card big-card "
                  :style="{ '--delay': (idx + 2) * 150 + 'ms' }"
                >
                  
                <div class="img-container position-relative">
                  <!-- skeleton placeholder while loading -->
                  <div
                    v-if="!imageLoaded[vehicleType.id]"
                    class="skeleton-placeholder"
                  ></div>

                  <img
                    :src="vehicleType.media?.[0]?.original_url"
                    alt="Vehicle Type Image"
                    class="vehicle-image"
                    :class="{ 'is-loaded': imageLoaded[vehicleType.id] }"
                    @load="imageLoaded[vehicleType.id] = true"
                  />

                  <div class="button-overlay">
                    <button class="category-button">
                      {{ vehicleType.title }}
                    </button>
                  </div>
                </div>

                </div>
              </Link>
            </div>
          </div>
        </div>

        <!-- MOBILE CAROUSEL: one card per view -->
        <div class="d-block d-sm-none mt-4">
          <transition :name="transitionName" mode="out-in">
            <div
              class="row"
              :key="currentPage"
              @touchstart="onTouchStart"
              @touchend="onTouchEnd"
            >
              <div class="col-12">
                <Link
                  v-for="(vehicleType, idx) in paginatedVehicleTypes"
                  :key="vehicleType.id"
                  method="post"
                  :href="route('available')"
                  :data="{ _method: 'GET', type: vehicleType.id }"
                >
                  <div
                    class="category-card big-card "
                    :style="{ '--delay': idx * 150 + 'ms' }"
                  >
                    <div class="img-container position-relative">
                      <div 
                        v-if="!imageLoaded[vehicleType.id]" 
                        class="skeleton-placeholder"
                      ></div>
                       <img
                        :src="vehicleType.media?.[0]?.original_url"
                        alt="Vehicle Type Image"
                        class="vehicle-image"
                        :class="{ 'is-loaded': imageLoaded[vehicleType.id] }"
                        @load="markLoaded(vehicleType.id)"
                      />
                      <div class="button-overlay">
                        <button class="category-button">
                          {{ vehicleType.title }}
                        </button>
                      </div>
                    </div>
                  </div>
                </Link>
              </div>
            </div>
          </transition>
          <div class="pager mt-3">
            <button @click="prevPage" :disabled="currentPage === 0">
              <i class="fas fa-chevron-left"></i>
            </button>
            <button
              @click="nextPage"
              :disabled="(currentPage + 1) * itemsPerPage >= vehicle_types.length"
            >
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
        </div>

        <!--
        <div class="row justify-content-center">
          <div
            v-for="(vehicleType, idx) in vehicle_types.slice(0, 4)"
            :key="vehicleType.id"
            class="col-lg-3 col-md-6 col-sm-6 col-12 mt-4 d-flex p-0 justify-content-center"
          >
            <Link
              method="post"
              as="button"
              :href="route('live.auction')"
              :data="{ _method: 'GET', type: vehicleType.id, search: true }"
              class="text-decoration-none text-reset border-0 bg-white"
            >
              <div
                class="category-card big-card animated-item"
                :style="{ '--delay': idx * 150 + 'ms' }"
              >
                <div class="img-container">
                  <img
                    :src="vehicleType.media?.[0]?.original_url"
                    alt="Vehicle Type Image"
                    class="vehicle-image"
                  />
                  <div class="button-overlay">
                    <button class="category-button">
                      {{ vehicleType.title }}
                    </button>
                  </div>
                </div>
              </div>
            </Link>
          </div>
        </div>

        <div class="row justify-content-center">
          <div
            v-for="(vehicleType, idx) in vehicle_types.slice(4, 9)"
            :key="vehicleType.id"
            class="five-per-row col-md-4 col-sm-6 col-12 mt-4 d-flex p-0 justify-content-center"
          >
            <Link
              method="post"
              as="button"
              :href="route('live.auction')"
              :data="{ _method: 'GET', type: vehicleType.id, search: true }"
              class="text-decoration-none text-reset border-0 bg-white"
            >
              <div
                class="category-card small-card animated-item"
                :style="{ '--delay': (idx + 2) * 150 + 'ms' }"
              >
                <div class="img-container">
                  <img
                    :src="vehicleType.media?.[0]?.original_url"
                    alt="Vehicle Type Image"
                    class="vehicle-image"
                  />
                  <div class="button-overlay">
                    <button class="category-button">
                      {{ vehicleType.title }}
                    </button>
                  </div>
                </div>
              </div>
            </Link>
          </div>
        </div>
        -->
      </div>
    </div>
  </section>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';

export default {
  components: { Link },
  props: {
    vehicle_types: { type: Array, required: true },
  },

  data() {
  return {
    currentPage: 0,       // which “page” we’re on
    itemsPerPage: 1,      // show one card per page on mobile
    touchStartX: 0,       // for swipe detection
    touchEndX: 0,
    transitionName: 'slide-left', // which slide animation to use
    observer: null,
    imageLoaded: {},
  };
},

computed: {
  paginatedVehicleTypes() {
    const start = this.currentPage * this.itemsPerPage;
    return this.vehicle_types.slice(start, start + this.itemsPerPage);
  }
},

  mounted() {
     this.initObserver();     // make the observer
     this.observeItems();  

    const observer = new IntersectionObserver(
      (entries, obs) => {
        entries.forEach((e) => {
          if (e.isIntersecting) {
            e.target.classList.add('in-view');
            obs.unobserve(e.target);
          }
        });
      },
      { threshold: 0.15 }
    );
    this.$el.querySelectorAll('.animated-item').forEach((el) => {
      observer.observe(el);
    });


    this.vehicle_types.forEach(v => {
      this.imageLoaded[v.id] = false;
    });

    // 2) catch any already‐cached images
    this.$nextTick(() => {
      this.vehicle_types.forEach(v => {
        const img = this.$el.querySelector(`img[alt="Vehicle Type Image"][src="${v.media?.[0]?.original_url}"]`);
        if (img && img.complete) {
          this.markLoaded(v.id);
        }
      });
    });
  },

  

  methods: {
  nextPage() {
    if ((this.currentPage + 1) * this.itemsPerPage < this.vehicle_types.length) {
      this.transitionName = 'slide-left';
      this.currentPage++;
      // this.$nextTick(() => this.observeItems());
    }
  },
  prevPage() {
    if (this.currentPage > 0) {
      this.transitionName = 'slide-right';
      this.currentPage--;
      // this.$nextTick(() => this.observeItems());
    }
  },
  onTouchStart(e) {
    this.touchStartX = e.changedTouches[0].clientX;
  },
  onTouchEnd(e) {
    this.touchEndX = e.changedTouches[0].clientX;
    const delta = this.touchEndX - this.touchStartX;
    if (delta < -50) this.nextPage();
    else if (delta > 50) this.prevPage();
  },

  initObserver() {
    // create it once
    this.observer = new IntersectionObserver(
      (entries, obs) => {
        entries.forEach(e => {
          if (e.isIntersecting) {
            e.target.classList.add('in-view');
            obs.unobserve(e.target);
          }
        });
      },
      { threshold: 0.15 }
    );
  },
  observeItems() {
    // watch every .animated-item on the page
    this.$el
      .querySelectorAll('.animated-item')
      .forEach(el => this.observer.observe(el));
  },

   markLoaded(id) {
      this.$set     // if Vue 2; or simply `this.imageLoaded[id]=true;` in Vue 3
        ? this.$set(this.imageLoaded, id, true)
        : (this.imageLoaded[id] = true);
    }
},

};
</script>

<style scoped>
/* force exactly 5 columns on large screens */
.five-per-row {
  flex: 0 0 20%;
  max-width: 20%;
}

/* height for the first 4 “big” cards */
.big-card {
  height: 350px;
  gap: 30px !important;

  /* adjust as needed */
}

/* height for the next 5 “small” cards */
.small-card {
  height: 350px;
}

.row {
  --bs-gutter-x: 1.5rem;
  --bs-gutter-y: 0;
  display: flex;
  flex-wrap: wrap;
  margin-top: calc(-1 * var(--bs-gutter-y));
  margin-right: 0 !important;
  margin-left: 0 !important;
}

.categorySection {
  /* padding: 85px 40px; */
  background-color: #fff;
}

.headText {
  font-size: clamp(1.5rem, 2vw, 2.25rem) !important;
  font-family: 'Poppins', sans-serif !important;
  color: #050B20 !important;
  font-weight: 700 !important;

}


.view-all-btn {
  background: none;
  color: #000;
  /* font-size: 18px; */
  font-weight: 600;
  border: none;
  display: inline-flex;
  align-items: center;
  cursor: pointer;
   gap: 0.4rem;                   /* small space between text & icon */
  white-space: nowrap; 
  font-size: clamp(0.875rem, 1vw, 1.125rem) !important;
}

.view-all-btn:hover {
  text-decoration: underline;
}

.arrow-icon {
  width:  clamp(0.75rem, .5vw, 0.9rem);
  height: clamp(0.75rem, .5vw, 0.9rem);
  margin-left: 0;            

}

/* Card basics */
.category-card {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  width: 100%;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.img-container {
  position: relative;
}

.vehicle-image {
  width: 100%;
  height: 350px;
  object-fit: cover;
  gap: 30px !important;
}

.button-overlay {
  position: absolute;
  bottom: 10px;
  left: 10px;
}

.category-button {
  background: linear-gradient(90deg, #0d1b2a 0%, #273b51 50%, #222b35 100%);
  color: #fff;
  border: none;
  padding: 8px 30px;
  border-radius: 20px;
  cursor: pointer;
}

@keyframes beautifulIn {
  0% {
    opacity: 0;
    transform: translateY(40px) rotateX(25deg) scale(0.85);
    filter: blur(8px);
  }

  60% {
    opacity: 1;
    transform: translateY(-10px) rotateX(0deg) scale(1.08);
    filter: blur(2px);
  }

  100% {
    transform: translateY(0) rotateX(0deg) scale(1);
    filter: blur(0);
  }
}

/* .animated-item {
  transform: translateY(40px);
  filter: blur(8px);
} */

.animated-item.in-view {
  animation: beautifulIn 0.8s ease-out var(--delay) forwards;
}

.category-card:hover {
  transform: translateY(-8px);    /* lift the card up by 8px */
  box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

@media (max-width: 768px) {

  .col-md-6,
  .col-md-4 {
    flex: 0 0 100%;
    max-width: 100%;
  }
}


/* Slide animations */
.slide-left-enter-active,
.slide-left-leave-active,
.slide-right-enter-active,
.slide-right-leave-active {
  transition: all 0.4s ease;
}
.slide-left-enter-from { opacity: 0; transform: translateX(20px); }
.slide-left-leave-to   { opacity: 0; transform: translateX(-20px); }
.slide-right-enter-from{ opacity: 0; transform: translateX(-20px); }
.slide-right-leave-to  { opacity: 0; transform: translateX(20px); }

/* Pager buttons */
.pager {
  display: flex; gap: 1rem; padding: 2rem 0;
}
.pager button {
  width: 60px; height: 40px; border-radius: 25px;
  border: 1.5px solid #3f3f3fd0; display: flex;
  align-items: center; justify-content:center;
  background: #fff; cursor: pointer;
}
.pager button:disabled { opacity: 0.4; cursor: default; }
.pager button:hover { background: #f8f9fa; }


/* shimmering placeholder */
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
  border-radius: 10px; /* match .category-card rounding */
}

@keyframes shimmer {
  0%   { background-position: -200% 0; }
  100% { background-position:  200% 0; }
}

/* fade the real image in */
.vehicle-image {
  width: 100%;
  height: 350px; /* keep your existing height */
  object-fit: cover;
  opacity: 0;
  transition: opacity 0.5s ease;
}

.vehicle-image.is-loaded {
  opacity: 1;
}
/* add at the bottom of your <style> */
.animated-item.in-view {
  animation: none !important;
  transform: none !important;
  filter: none !important;
}

</style>
