<template>
  <!-- Remove my-5 pt-2 classes -->
  <section class="news-section container-fluid section-spacing">
    <!-- header -->
    <div class="row text-left w-100">
      <div class="col-md-12 ps-0 mb-0">
        <div class="row align-items-center ps-0">
          <div class="col-8 ps-3">
            <h2 class="headText secondFontStyle fw-semibold">News</h2>
          </div>
          <!-- <div class="col-4 ps-3 text-end">
            <Link method="post" as="button" :href="route('live.auction')" class="view-all-btn">
              View All <span class="arrow-icon">→</span>
            </Link>
          </div> -->
        </div>
      </div>
    </div>

    <!-- paginated grid with transitions -->
    <transition :name="transitionName" mode="out-in">
      <div :key="page" class="row row-cols-1 row-cols-md-4 g-4" @touchstart="onTouchStart" @touchend="onTouchEnd">
        <div class="col" v-for="item in currentPageItems" :key="item.id">
          <div class="card news-card h-100">
            <!-- image + category badge -->
              <div class="position-relative">
               
                <div
                  v-if="!imageLoaded[item.id]"
                  class="skeleton-placeholder"
                ></div>

                
                <img
                  :src="item.image"
                  class="card-img-top news-image"
                  alt="News image"
                  :class="{ 'is-loaded': imageLoaded[item.id] }"
                  @load="markLoaded(item.id)"
                />

                
                <span class="category-badge">{{ item.category }}</span>
              </div>


            <!-- author/date + title -->
            <div class="card-body pt-3 px-0">
              <p class="text-muted mb-1 small">
                {{ item.author }} • {{ formatDate(item.date) }}
              </p>
              <h5 class="card-title mb-0">{{ item.title }}</h5>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- pager controls -->
    <div class="pager">
      <button @click="prevPage" :disabled="page === 0">
        <i class="fas fa-chevron-left"></i>
      </button>
      <button @click="nextPage" :disabled="(page + 1) * perPage >= newsItems.length">
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>
  </section>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'

export default {
  name: 'NewsSection',
  components: { Link },
  data() {
    return {
      page: 0,
      perPage: window.innerWidth < 768 ? 1 : 4,
      touchStartX: 0,
      imageLoaded: {},
      touchEndX: 0,
      transitionName: 'slide-left',
      newsItems: [
        {
          id: 1,
          image: '/images/volvo.png',
          category: 'Sound',
          author: 'Admin',
          date: '2023-11-22',
          title: '2024 BMW ALPINA XB7 with exclusive details, extraordinary'
        },
        {
          id: 2,
          image: '/images/bentley.png',
          category: 'Accessories',
          author: 'Admin',
          date: '2023-11-22',
          title: 'BMW X6 M50i is designed to exceed your sportiest.'
        },
        {
          id: 3,
          image: '/images/audi.png',
          category: 'Exterior',
          author: 'Admin',
          date: '2023-11-22',
          title: 'BMW X5 Gold 2024 Sport Review: Light on Sport'
        },
        {
          id: 4,
          image: '/images/mercedes.jpg',
          category: 'Interior',
          author: 'Editor',
          date: '2024-01-10',
          title: 'Mercedes-Benz GLE 2024 Review: Luxury Meets Comfort'
        },
        {
          id: 5,
          image: '/images/tesla1.png',
          category: 'Electric',
          author: 'Staff Writer',
          date: '2024-03-05',
          title: 'Tesla Model S Plaid: Breaking the 200 MPH Barrier'
        },
        {
          id: 6,
          image: '/images/ford1.png',
          category: 'Safety',
          author: 'AutoReview',
          date: '2024-04-12',
          title: '2024 Ford F-150 Fights Fatigue with Next-Gen Lane Assist'
        },
        // {
        //   id: 7,
        //   image: '/images/audi1.png',
        //   category: 'Technology',
        //   author: 'TechCars',
        //   date: '2024-02-20',
        //   title: 'Audi e-tron’s Virtual Mirror Tech Arrives in U.S. Models'
        // },
        // {
        //   id: 8,
        //   image: '/images/chevy-corvette.png',
        //   category: 'Performance',
        //   author: 'TrackDayMag',
        //   date: '2024-05-18',
        //   title: 'Corvette Stingray’s New Z51 Package Shaves Seconds Off Lap Time'
        // },
        // {
        //   id: 9,
        //   image: '/images/land-rover-defender.png',
        //   category: 'Lifestyle',
        //   author: 'Outdoorsy',
        //   date: '2024-06-01',
        //   title: 'Land Rover Defender Tackles the Rubicon Trail in Style'
        // }

      ]
    }
  },
  computed: {
    currentPageItems() {
      const start = this.page * this.perPage
      return this.newsItems.slice(start, start + this.perPage)
    }
  },
  methods: {
    formatDate(dateStr) {
      const d = new Date(dateStr)
      return d.toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric'
      })
    },
    prevPage() {
      if (this.page > 0) {
        this.transitionName = 'slide-right'
        this.page--
      }
    },
    nextPage() {
      if ((this.page + 1) * this.perPage < this.newsItems.length) {
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
      if (delta < -50) this.nextPage()
      else if (delta > 50) this.prevPage()
    },
    handleResize() {
      const newPer = window.innerWidth < 768 ? 1 : 4
      if (newPer !== this.perPage) {
        this.perPage = newPer
        this.page = 0
      }
    },
    markLoaded(id) {
      this.imageLoaded[id] = true;
    }
  },
  mounted() {
    window.addEventListener('resize', this.handleResize)

    this.newsItems.forEach(item => {
      this.imageLoaded[item.id] = false
    });
  },
  beforeUnmount() {
    window.removeEventListener('resize', this.handleResize)
  }
}
</script>

<style scoped>
.view-all-btn {
  background: none;
  color: #000000;
  /* font-size: 18px; */
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

.arrow-icon {
  font-size: 20px;
  margin-left: 10px;
  transform: rotate(320deg);
}

.news-card {
  border: none;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  margin-bottom: 1.5rem;
  /* Space between cards */
}

.card-img-top {
  width: 100%;
  height: 300px;
  object-fit: cover;
}

.category-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  background: linear-gradient(90deg,
      #0D1B2A 0%,
      #273B51 50%,
      #232E3B 67%,
      #1E1E1E 89%,
      #222B35 95%,
      #26384D 100%);
  color: #fff;
  padding: 0.25rem 0.75rem;
  border-radius: 8px;
  font-size: 0.75rem;
  text-transform: capitalize;
}

.text-muted.small {
  font-size: 0.85rem;
}

.card-title {
  font-size: 1rem;
  font-weight: 600;
  line-height: 1.3;
  color: #050B20;
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

.headText {
  font-size: 30px;
  color: #333;
  line-height: 1.2;
  margin-bottom: 1.5rem !important;
  padding: 0 0.75rem;
  font-family: 'Poppins', sans-serif !important;
  color: #050B20 !important;
  font-weight: 700 !important;
}

@media (max-width: 768px) {
  .headText {
    font-size: 24px;
    font-weight: 700;
  }
}

/* Update row spacing */
.row-cols-1 {
  margin-top: 0;
  margin-bottom: var(--bottom-spacing);
  padding: 0 0.75rem;
}

/* Ensure section spacing variables are present */
.section-spacing {
  --title-spacing: 2.5rem;
  --bottom-spacing: 3rem;
  margin-top: 2rem;
}

.pager {
  display: flex;
  justify-content: left;
  gap: 1rem;
  padding: 2rem 0;
  margin-top: var(--bottom-spacing);
  margin-bottom: var(--bottom-spacing);
   padding-left: 1.2rem;
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
  border-radius: inherit; /* matches your .card-img-top rounding */
}

@keyframes shimmer {
  0%   { background-position: -200% 0; }
  100% { background-position:  200% 0; }
}

/* fade-in the real image */
.news-image {
  opacity: 0;
  transition: opacity 0.5s ease;
}

.news-image.is-loaded {
  opacity: 1;
}

</style>
