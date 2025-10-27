<!-- <template>
  <section
    v-if="filtered.length"
    class="featured-listings container-fluid section-spacing"
  >
    
    <div class="row align-items-center mb-3">
      <div class="col-8">
        <h2 class="headText">Featured Listings</h2>
      </div>
      <div class="col-4 text-end">
        <Link href="/listings" class="view-all-btn">
          View All
          <img src="/images/Vector.png" alt="→" class="arrow-icon" />
        </Link>
      </div>
    </div>

  
    <div class="row mb-4">
      <div class="col-12">
        <ul class="tabs d-flex mb-0">
          <li
            :class="{ active: activeTab==='inStock' }"
            @click="switchTab('inStock')"
          >
            In Stock
          </li>
          <li
            :class="{ active: activeTab==='newCars' }"
            @click="switchTab('newCars')"
          >
            New Cars
          </li>
          <li
            :class="{ active: activeTab==='usedCars' }"
            @click="switchTab('usedCars')"
          >
            Used Cars
          </li>
        </ul>
      </div>
    </div>

   
    <transition name="fade-slide" mode="out-in">
      <div :key="page" class="grid">
        <Link
          v-for="item in currentPageItems"
          :key="item.id"
          :href="route('product', {
            model: $root.stringToSlug(item.manufacture.title),
            slug:
              $root.stringToSlug(item.manufacture.title) +
              '-' +
              $root.stringToSlug(item.vehicle_model.title),
            id: item.id
          })"
          class="car-card text-decoration-none"
        >
          
          <div class="card-image">
            <img
              :src="item.media?.[0]?.original_url || '/placeholder.png'"
              :alt="item.vehicle_model?.name || 'Vehicle Image'"
            />
            <span class="year-pill">{{ item.year }}</span>
            <span class="bookmark-icon" @click.stop>
              <img src="/images/bookmark.png" alt="Bookmark" class="bookmark-img" />
            </span>
          </div>

      
          <div class="card-details">
            <h5 class="car-title">
              {{ item.manufacture.title }}
              {{ item.vehicle_model.title }} – {{ item.year }}
            </h5>
            <p class="car-subtitle">{{ item.vehicle_type?.title }}</p>

            <div class="specs">
              <div class="spec-item">
                <img src="/images/tachometer.png" alt="Mileage" class="spec-icon" />
                <span>{{ item.mileage }} Miles</span>
              </div>
              <div class="spec-item">
                <img src="/images/gasoline-pump.png" alt="Fuel" class="spec-icon" />
                <span>{{ item.FuelTypeString }}</span>
              </div>
              <div class="spec-item">
                <img src="/images/gearbox.png" alt="Transmission" class="spec-icon" />
                <span>{{ item.TransmissionString }}</span>
              </div>
            </div>

            <div class="card-footer">
              <div class="price">
                {{ item.monthly_price_currency === 'USD' ? '$' : 'LKR ' }}
                {{ (item.monthly_price || 0).toLocaleString() }}
              </div>
              <span class="view-details">
                View Details
                <img src="/images/Vector.png" alt="→" class="arrow-icon_1 ps-2" />
              </span>
            </div>
          </div>
        </Link>
      </div>
    </transition>


    <div class="pager">
      <button @click="prevPage" :disabled="page === 0">
        <i class="fas fa-chevron-left"></i>
      </button>
      <button @click="nextPage" :disabled="(page + 1) * perPage >= filtered.length">
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>
  </section>
</template> -->


<template>
  <section class="featured-listings my-5 mb-0 container-fluid px-5">
    <!-- HEADER ROW -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <!-- Title -->
      <h2 class="fw-bold mb-0">Featured Listings</h2>
      <!-- View All -->
      <!-- <Link href="/listings" class="view-all-btn d-inline-flex align-items-center mb-0">
      <span>View All</span>
      <img src="/images/Vector.png" alt="→" class="ms-1" style="width:1em; height:auto;" />
      </Link> -->
    </div>





    <!-- tabs -->
    <!-- <div class="row mb-4">
      <div class="col-12">
        <ul class="tabs d-flex mb-0">
          <li :class="{ active: activeTab === 'inStock' }" @click="switchTab('inStock')">
            In Stock
          </li>
          <li :class="{ active: activeTab === 'newCars' }" @click="switchTab('newCars')">
            New Cars
          </li>
          <li :class="{ active: activeTab === 'usedCars' }" @click="switchTab('usedCars')">
            Used Cars
          </li>
        </ul>
      </div>
    </div> -->

    <!-- animated 4×2 grid on desktop, 1×N on mobile -->
    <transition :name="transitionName" mode="out-in">
      <div :key="page" class="grid" @touchstart="onTouchStart" @touchend="onTouchEnd">

        <a v-for="item in currentPageItems" :key="item.id" :href="route('product', {
          model: $root.stringToSlug(item.manufacture.title),
          slug:
            $root.stringToSlug(item.manufacture.title) +
            '-' +
            $root.stringToSlug(item.vehicle_model.title),
          id: item.id
        })" class="car-card text-decoration-none">
        <!-- image + year/bookmark -->
        <div class="card-image position-relative">

          <div v-if="!imageLoaded[item.id]" class="skeleton-placeholder"></div>

          <img :src="item.media?.[0]?.original_url || '/placeholder.png'"
            :alt="item.vehicle_model?.name || 'Vehicle Image'" class="img-fluid auction-image"
            :class="{ 'is-loaded': imageLoaded[item.id] }" @load="markLoaded(item.id)" />
          <span class="year-pill">{{ item.year }}</span>
          <span class="bookmark-icon" @click.stop>
            <img src="/images/bookmark.png" alt="Bookmark" class="bookmark-img" />
          </span>

          <span class="image-counter">
            <img src="/images/image.png" alt="Photos" class="counter-icon" />
            {{ item.media?.length || 0 }}
          </span>

        </div>

        <!-- details -->
        <div class="card-details">
          <h5 class="car-title">
            {{ item.manufacture.title }}
            {{ item.vehicle_model.title }}
          </h5>
          <!-- <p class="car-subtitle">{{ item.vehicle_type?.title }}</p> -->

          <div class="specs">
            <!-- only render engine CC if grades[0] exists -->
            <div v-if="parseGrades(item.grades).length || item.engine_capacity" class="spec-item">
            <img src="/images/car-engine.png" alt="Engine" class="spec-icon" />
            <div class="spec-text">
              {{ rawList(getFirstGrade(item).engine_capacities) || item.engine_capacity || '–' }}
            </div>
          </div>


            <div v-if="parseGrades(item.grades).length" class="spec-item">
              <img src="/images/gasoline-pump.png" alt="Fuel" class="spec-icon" />
              <div class="spec-text">
                {{ getFuelSummary(getFirstGrade(item).fuel_types) || '–' }}
              </div>
            </div>

            <div v-if="parseGrades(item.grades).length" class="spec-item">

              <img src="/images/gearbox.png" alt="Transmission" class="spec-icon" />
              

              <div class="spec-text">
                {{ getFirstGrade(item).transmission || '–' }}
              </div>
            </div>
        </div>

          <div class="card-footer">
            <div class="price">
              <small class="upwards-label">Upwards</small>

              {{ item.monthly_price_currency === 'USD' ? '$' : 'LKR ' }}
              {{ (item.monthly_price || 0).toLocaleString() }}
            </div>
            <!-- <span class="view-details">
                View Details
                <img src="/images/Vector.png" alt="→" class="arrow-icon_1 ps-2" />
              </span> -->
          </div>
        </div>
      </a>
      </div>
    </transition>

    <!-- pager -->
    <div class="pager">
      <button @click="prevPage" :disabled="page === 0">
        <i class="fas fa-chevron-left"></i>
      </button>
      <button @click="nextPage" :disabled="(page + 1) * perPage >= filtered.length">
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>
  </section>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'

export default {
  name: 'FeaturedListings',
  components: { Link },
  props: {
    featuredVehicles: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      activeTab: 'inStock',
      page: 0,
      perPageDesktop: 10,
      transitionName: 'slide-left',
      width: 0,
      imageLoaded: {}
    }
  },
  computed: {
    
    perPage() {
      if (this.width < 576) {
        
        return 1;
      }
      if (this.width < 1050) {
       
        return 6;
      }
      if (this.width < 1800) {
        
        return 8;
      }
     
      return this.perPageDesktop; 
    },
    
    filtered() {
     
      if (this.activeTab === 'inStock') {
        return this.featuredVehicles;
      }
     
      if (this.activeTab === 'newCars') {
        return this.featuredVehicles.filter(v => v.vehicle_status === 'new');
      }
     
      if (this.activeTab === 'usedCars') {
        return this.featuredVehicles.filter(v => v.vehicle_status === 'used');
      }
    },
   
    currentPageItems() {
      const start = this.page * this.perPage
      return this.filtered.slice(start, start + this.perPage)
    }
  },
  methods: {

    rawList(val) {
     
      if (val == null) return '';
      if (Array.isArray(val)) return val.join(', ');
      const s = String(val).trim();
      if (!s) return '';
    
      try {
        const arr = JSON.parse(s);
        return Array.isArray(arr) ? arr.join(', ') : s;
      } catch {
        return s; 
      }
    },


  parseGrades(grades) {
    try {
      return Array.isArray(grades) 
        ? grades 
        : JSON.parse(grades || '[]')
    } catch {
      return []
    }
  },

   getFirstGrade(item) {
    const grades = this.parseGrades(item.grades)
    return grades.length ? grades[0] : {}
  },
     firstGrade(item) {
    return Array.isArray(item.grades) && item.grades.length
      ? item.grades[0]
      : null;
  },
     parseList(val) {
    if (Array.isArray(val)) return val;
    try {
      const arr = JSON.parse(val);
      return Array.isArray(arr) ? arr : [arr];
    } catch {
      return [];
    }
  },
    // get the smallest CC value
    getMinCapacity(capStr) {
      const arr = this.parseList(capStr)
        .map(Number)
        .filter(n => !isNaN(n))
        .sort((a, b) => a - b);
      return arr.length ? arr[0] : '';
    },
    // first fuel + “+N more”
     getFuelSummary(fuelInput) {
    let names;
    if (Array.isArray(fuelInput)) {
      // if objects: map to name, else assume array of strings
      names = fuelInput.map(f =>
        typeof f === 'object' && f.name ? f.name : String(f)
      );
    } else {
      names = this.parseList(fuelInput);
    }
    if (!names.length) return '';
    if (names.length === 1) return names[0];
    return `${names[0]} +${names.length - 1}`;
  },
    switchTab(tab) {
      this.activeTab = tab
      this.page = 0
    },
    prevPage() {
      if (this.page > 0)
        this.transitionName = 'slide-right'
      this.page--
    },
    nextPage() {
      if ((this.page + 1) * this.perPage < this.filtered.length) {
        this.transitionName = 'slide-left'
        this.page++
      }
    },
    onResize() {
      this.width = window.innerWidth
      // ensure page reset if perPage changes beneath you
      this.page = 0
    },
    onTouchStart(e) {
      this.touchStartX = e.changedTouches[0].clientX
    },
    onTouchEnd(e) {
      const delta = e.changedTouches[0].clientX - this.touchStartX
      if (delta < -50) this.nextPage()
      else if (delta > 50) this.prevPage()
    },
    markLoaded(id) {
      this.imageLoaded[id] = true;
    }

  },
  mounted() {
    // set initial width and start listening
    this.onResize()
    window.addEventListener('resize', this.onResize)
  },
  beforeUnmount() {
    // clean up the listener
    window.removeEventListener('resize', this.onResize)
  }
}
</script>


<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

.headText {
  font-size: clamp(1.5rem, 2vw, 2.25rem);
  /*added*/
  color: #333;
  line-height: 1.2;
  margin-bottom: 0;
  margin-bottom: var(--title-spacing) !important;
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
  gap: 0.4rem;
  /* small space between text & icon */
  white-space: nowrap;
  /*added*/
  font-size: clamp(0.875rem, 1vw, 1.125rem) !important;
  /*added*/
}


.view-all-btn:hover {
  text-decoration: underline;
}

.arrow-icon {
  /* width: 15px;    
  height: 15px;    
  margin-left: 1.2rem; */
  width: clamp(0.75rem, .5vw, 0.9rem);
  /*added*/
  height: clamp(0.75rem, .5vw, 0.9rem);
  /*added*/
  vertical-align: middle;
}

/* .arrow-icon {
  width: 15px;
  height: 15px;
  margin-left: 1.2rem;
  vertical-align: middle;
} */



/* === Featured Listings Header === */
.featured-listings {
  margin-top: 30px;
  position: relative;
}

.featured-listings .header {
  position: relative;
}

/* .featured-listings .view-all-btn {
  position: absolute;
  top: 0;
  right: 0;
  margin: 0;
} */

.header {
  margin-bottom: 1.5rem;
}

.header-top {
  display: flex;
  flex-direction: column;

}

.header h2 {
  /* font-size: 2rem; */
  font-size: clamp(1.5rem, 2vw, 2.25rem);
  /*added */
  font-weight: 700;
  margin-bottom: 0.5rem;
  font-family: 'Poppins', sans-serif;
  color: #050B20;
  line-height: 1.2;
  margin-bottom: 0;
  font-family: 'Poppins', sans-serif !important;
  font-weight: 700 !important;
  white-space: nowrap;
  /*added */
}

/* === Tabs === */
.tabs-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  border-bottom: 1px solid #ccc;
  position: relative;
}

.tabs {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  gap: 1.5rem;
  border-bottom: 1px solid #ccc;
}

.tabs-container::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 1px;
  background-color: #ccc;
}

.tabs li {
  padding: 0.5rem 0;
  cursor: pointer;
  padding: 0.5rem 0;
  font-weight: 500;
  position: relative;
}

.tabs .active {
  color: #04203d;
  border-bottom: 2px solid #0a3f79;
}

/* === Grid === */
.grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 2rem;
  margin-bottom: 1rem;
  /* padding: 0 1rem; */
  /* Add padding to match Live Auction */
}

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
}

.view-all-btn:hover {
  text-decoration: underline;
}



.card {
  box-shadow: 0px 0px 9px 0px rgb(81 97 140 / 72%);
  position: relative;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border-radius: 32px;
}

.card:hover {
  transform: scale(1.02);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

.card-image {
  position: relative;
  background: #fff;
  overflow: hidden;
  border-radius: 32px;
  z-index: 1;
}

.card-image img {
  display: block;
  width: 100%;
  height: 260px;
  object-fit: cover;

}

.badge {
  position: absolute;
  top: 0.75rem;
  left: 0.75rem;
  background: linear-gradient(90deg, #0D1B2A, #26384D);
  color: #fff;
  padding: 0.25rem 0.75rem;
  border-radius: 8px;
  font-size: 0.75rem;
}

.bookmark {
  position: absolute;
  top: 0.75rem;
  right: 0.75rem;
  background: rgba(255, 255, 255, 0.8);
  border: none;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.bookmark i {
  color: #333;
}

/* === Card Content === */
.card-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 1.5rem;
  background: #f2f1fb;
  color: #000000;
  border-bottom-left-radius: 32px;
  border-bottom-right-radius: 32px;
  position: relative;
  z-index: 0;
  margin-top: -60px;
  padding-top: 80px;
}

.title {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 600;

}

.subtitle {
  margin: 0 0 0.75rem;
  font-size: 0.875rem;
  opacity: 0.8;
}

.specs {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: auto;
}

.spec-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}

.spec-item span {
  font-size: 0.875rem;
}

.icons {
  font-size: 1.25rem;
  opacity: 0.85;
}

.footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 1rem;
}

.price {
  /* font-size: 1.25rem; */
  font-size: clamp(1rem, 2vw, 1.5rem);
  font-weight: 700;
}


.vehicle-card__cta-button {
  text-decoration: none;
  font-size: 0.775rem;
  font-weight: 500;
  color: #fff;
  display: flex;
  align-items: center;
}


.details-arrow {
  margin-left: 0.3rem;
  transform: rotate(-45deg);
  font-size: 1.3rem;

}

/* === Pager === */
.pager {
  display: flex;
  justify-content: left;
  gap: 1rem;
  padding: 2rem 0;
  margin-top: var(--bottom-spacing);
  margin-bottom: var(--bottom-spacing);
  padding-left: .75rem;
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

/* === Responsive === */
@media (max-width: 1024px) {
  .grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 576px) {
  .header-top {
    flex-wrap: wrap;
  }

  .header-top h2 {
    font-size: 1.5rem;
  }

  .view-all-btn {
    margin-top: 0;
    /* No need to push it below */

  }

}

@media (max-width: 768px) {

  .headText,
  .header h2 {
    font-size: 24px;
    font-weight: 700;
  }

  .categorySection {
    padding: 20px 0;
  }

  .grid {
    grid-template-columns: 1fr;
  }

  .tabs-container {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .tabs {
    width: 100%;
    overflow-x: auto;
    padding-bottom: 0.5rem;
  }

  .header-top {
    flex-direction: row;
    align-items: center;
    gap: 1rem;
    padding: 0 0.75rem;
  }

  .section-spacing {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
  }

}

@media (max-width: 480px) {
  .grid {
    grid-template-columns: 1fr;

  }

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
} */


/*—— fade-slide transition (20px) ——*/
/* .fade-slide-enter-active,
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

/* forward swipe: slide new page in from right */
.slide-left-enter-active,
.slide-left-leave-active {
  transition: all 0.4s ease;
}

.slide-left-enter-from {
  opacity: 0;
  transform: translateX(20px);
}

.slide-left-enter-to {
  opacity: 1;
  transform: translateX(0);
}

.slide-left-leave-from {
  opacity: 1;
  transform: translateX(0);
}

.slide-left-leave-to {
  opacity: 0;
  transform: translateX(-20px);
}

/* backward swipe: slide new page in from left */
.slide-right-enter-active,
.slide-right-leave-active {
  transition: all 0.4s ease;
}

.slide-right-enter-from {
  opacity: 0;
  transform: translateX(-20px);
}

.slide-right-enter-to {
  opacity: 1;
  transform: translateX(0);
}

.slide-right-leave-from {
  opacity: 1;
  transform: translateX(0);
}

.slide-right-leave-to {
  opacity: 0;
  transform: translateX(20px);
}


/* Ensure grid collapses on mobile */
@media (max-width: 576px) {
  .grid {
    grid-template-columns: 1fr !important;
  }
}




/* === Common Section Spacing === */
.section-spacing {
  --title-spacing: 2.5rem;
  --bottom-spacing: 3rem;
  margin-top: 2rem;
  margin-bottom: 0;
  padding-left: 2.3rem !important;
  padding-right: 2.3rem !important;
}



/* outer container */
.car-card {
  font-family: 'Poppins', sans-serif;
  background: #f0f3f7;
  border: .5px solid #a1bad6;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0px 4px 16px 0px #00000040;
  display: flex;
  flex-direction: column;
  min-height: 460px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.car-card:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
}

/* top “white cap” */
.card-image {
  position: relative;
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0px 4px 12px #79787840;
}

.card-image img {
  width: 100%;
  height: 240px;
  object-fit: cover;
}

/* year pill + bookmark */
.year-pill {
  position: absolute;
  top: 12px;
  left: 12px;
  background-image: linear-gradient(90deg, #415a77 0%, #0d1b2a 100%);
  color: #fff;
  padding: 6px 24px;
  border-radius: 22px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}

.bookmark-icon {
  position: absolute;
  top: 12px;
  right: 12px;
  background: #d2d4d6;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.bookmark-icon .bookmark-img {
  width: 15px;
  height: 15px;
  object-fit: contain;
}

/* grey details panel */
.card-details {
  padding: 20px 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  flex: 1;
}

.car-title {
  font-size: 1.125rem;
  font-weight: 500;
  margin: 0;
  color: #21252B;
}

.car-subtitle {
  font-size: 0.875rem;
  margin: 0;
  color: #21252B;
}

/* specs */
.specs {
  display: flex;
  justify-content: space-between;
}

.spec-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.spec-icon {
  width: 24px;
  height: 24px;
  object-fit: contain;
}

/* footer */
.card-footer {
  display: flex;
  justify-content: space-between;
  margin-top: auto;
}

.price {
  font-family: 'Poppins', sans-serif;
  font-size: 1.25rem;
  font-weight: 600;
  color: #050B20;
}

.view-details {
  font-size: 14px;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  color: #050B20 !important;
}

.view-details img {
  margin-left: 6px;
  width: 12px;
  height: 12px;
  object-fit: contain;

}

/* 1) Enlarge the “View Details” arrow */
.view-details img,
.arrow-icon_1 {
  width: 20px;
  /* or whatever feels right */
  height: 18px;
}

/* 2) Force your specs text to use the car-card color */
.spec-item span {
  color: #21252B !important;
}


/* @media (max-width: 1800px) {
  .grid {
    grid-template-columns: repeat(4, 1fr);
  }
} */

@media (min-width: 1025px) and (max-width: 1800px) {
  .grid {
    grid-template-columns: repeat(4, 1fr);
  }
}


/* shimmering placeholder */
.skeleton-placeholder {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to right,
      #e0e0e0 0%,
      #f8f8f8 50%,
      #e0e0e0 100%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: inherit;
  /* matches your existing card-image rounding */
}

@keyframes shimmer {
  0% {
    background-position: -200% 0;
  }

  100% {
    background-position: 200% 0;
  }
}

/* fade the real image in */
.auction-image {
  opacity: 0;
  transition: opacity 0.5s ease;
}

.auction-image.is-loaded {
  opacity: 1;
}



.image-counter {
  position: absolute;
  bottom: 12px;
  right: 12px;
  display: flex;
  align-items: center;
  background: rgba(204, 201, 201, 0.6);
  padding: 4px 6px;
  border-radius: 12px;
  font-size: 16px;
  color: #0e0d0d;
  pointer-events: none;

}

.image-counter .counter-icon {
  width: 17px;
  height: 17px;
  margin-right: 4px;
  font-weight: bolder;
}


.spec-text {
  color: #000;
}

.upwards-label {
  display: block;
  font-size: 0.75rem;
  color: #000;
  margin-top: 0.25rem;
}

.spec-text i.fa-arrow-up {
  font-size: 1rem;
  margin-left: 0.25rem;
  vertical-align: middle;
}

.upwards-label {
  display: block;
  font-size: 0.75rem;
  color: #000;
  margin-top: 0.25rem;
}
</style>
