<template>
  <section class="featured-listings my-5 mb-0  container-fluid px-4">
    <!-- header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Testimonials</h2>
    </div>

    <!-- wrap the grid in a single transition -->
    <transition :name="transitionName" mode="out-in">
      <div :key="page" 
        class="grid"
        @touchstart="onTouchStart"
        @touchend="onTouchEnd">
        <div
          v-for="(t, i) in currentPageReviews"
          :key="i"
          class="testimonial-card"
        >
          <!-- title + quote -->
          <div class="card-header d-flex justify-content-between align-items-start">
            <h5 class="card-title">{{ t.title }}</h5>
            <i class="fas fa-quote-right quote-icon"></i>
          </div>
          <!-- comment -->
          <div class="card-body">
            <p class="testimonial-text">“{{ t.comment }}”</p>
          </div>
          <!-- avatar + name/role -->
          <div class="card-footer d-flex align-items-center">
            <img :src="t.image" alt="" class="avatar" />
            <div class="ms-3">
              <p class="mb-0 fw-semibold">{{ t.name }}</p>
              <p class="mb-0 text-muted">{{ t.role }}</p>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- pager -->
    <div class="pager">
      <button @click="prevPage" :disabled="page === 0">
        <i class="fas fa-chevron-left"></i>
      </button>
      <button @click="nextPage" :disabled="(page + 1) * perPage >= reviews.length">
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>
  </section>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'

export default {
   name: 'Testimonials',
  components: { Link },
  props: {
    reviews: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      page: 0,
      perPage: window.innerWidth < 576 ? 1 : 4,

      touchStartX: 0,
      touchEndX: 0,
      transitionName: 'slide-left'
    }
  },
  computed: {
    // break the reviews into pages of 4
    currentPageReviews() {
      const start = this.page * this.perPage
      return this.reviews.slice(start, start + this.perPage)
    }
  },
  methods: {
    prevPage() {
      if (this.page > 0) 
      this.transitionName = 'slide-right';
      this.page--
    },
    nextPage() {
      if ((this.page + 1) * this.perPage < this.reviews.length)
      this.transitionName = 'slide-left';
      this.page++
    },
    onTouchStart(e) {
    this.touchStartX = e.changedTouches[0].clientX;
  },
  onTouchEnd(e) {
    this.touchEndX = e.changedTouches[0].clientX;
    const delta = this.touchEndX - this.touchStartX;
    if (delta < -50) {
      // swipe left → next
      this.nextPage();
    } else if (delta > 50) {
      // swipe right → prev
      this.prevPage();
    }
  }

  }
}
</script>

<style scoped>
.px-4 {
    padding-right: 2.3rem !important;
    padding-left: 2.3rem !important;
}
.featured-listings { position: relative; }

.featured-listings .fw-bold {
  font-family: 'Poppins', sans-serif;
  color: #050B20;
}

/* grid: 5 columns */
.grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2rem;
}

/* testimonial card */
.testimonial-card {
  background: #fff;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 8px 20px rgba(0,0,0,0.08);
  display: flex;
  flex-direction: column;
  height: 100%;
}

/* header */
.card-header { margin-bottom: 1rem; }
.card-title { margin: 0; font-size: 1.125rem; font-weight: 4500; color: #050B20;}
.quote-icon { font-size: 1.5rem; color: #0A1F44; opacity: 0.8; }

/* body */
.card-body { flex: 1; }
.testimonial-text {
  margin: 0;
  color: #333;
  font-size: 0.9rem;
  line-height: 1.5;
}

/* footer */
.card-footer { margin-top: 1.25rem; }
.avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #fff;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
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
/* responsive */
@media (max-width: 992px) {
  .grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 576px) {
  .grid { grid-template-columns: 1fr; }
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

/* Common spacing styles to add to each component */
.section-spacing {
  --title-spacing: 2.5rem;
  --bottom-spacing: 2rem;
  margin-top: 2rem; /* Match Featured Listings spacing */
  margin-bottom: 0;
}

/* forward swipe: slide new page in from right */
.slide-left-enter-active,
.slide-left-leave-active {
  transition: all 0.4s ease;
}
.slide-left-enter-from { opacity: 0; transform: translateX(20px); }
.slide-left-enter-to   { opacity: 1; transform: translateX(0); }
.slide-left-leave-from { opacity: 1; transform: translateX(0); }
.slide-left-leave-to   { opacity: 0; transform: translateX(-20px); }

/* backward swipe: slide new page in from left */
.slide-right-enter-active,
.slide-right-leave-active {
  transition: all 0.4s ease;
}
.slide-right-enter-from { opacity: 0; transform: translateX(-20px); }
.slide-right-enter-to   { opacity: 1; transform: translateX(0); }
.slide-right-leave-from { opacity: 1; transform: translateX(0); }
.slide-right-leave-to   { opacity: 0; transform: translateX(20px); }



</style>
