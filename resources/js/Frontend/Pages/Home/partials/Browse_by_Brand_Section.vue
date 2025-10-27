<template>
  <section class="categorySection my-5 mb-0 pt-2 container-fluid">
    <div class="container-fluid px-3 px-md-4 main-container">

      <!-- header / title -->
      <div class="row align-items-center mb-4">
        <div class="col-8">
          <h2 class="headText secondFontStyle fw-semibold">
            Browse by Brand
          </h2>
        </div>
        <!-- <div class="col-4 text-end">
          <Link as="button" :href="route('live.auction')" class="view-all-btn">
            View All
            <img src="/images/Vector.png" alt="→" class="arrow-icon"/>
          </Link>
        </div> -->
      </div>

      <!-- DESKTOP GRID: only show on ≥sm -->
      <div class="brand-grid d-none d-sm-grid mt-4">
        <Link
          v-for="m in category_manufactures.slice(0, 14)"
          :key="m.id"
          method="post"
          as="button"
          :href="route('live.auction')"
          :data="{ _method:'GET', manufacturer: m.name, search: true }"
          class="category-card text-decoration-none text-reset"
        >

          <div class="img position-relative">
                <div
                  v-if="!imageLoaded[m.id]"
                  class="skeleton-placeholder"
                ></div>
            <img
              :src="m.media?.[0]?.original_url||''"
              alt=""
              class="imgStyleClass"
              :class="{ 'is-loaded': imageLoaded[m.id] }"
              @load="markBrandLoaded(m.id)"
            />
          </div>
          
          <div class="text">
            <h5 class="topicText">{{ m.name }}</h5>
          </div>
        </Link>
      </div>

      <!-- MOBILE CAROUSEL: only show <sm -->
    <div class="mobile-carousel d-block d-sm-none mt-4">

      <!-- animated grid -->
      <transition :name="transitionName" mode="out-in">
        <div 
          class="mobile-grid" 
          :key="currentPage"
          @touchstart="onTouchStart"
          @touchend="onTouchEnd"
          >


          <Link
            v-for="m in paginated"
            :key="m.id"
            method="post"
            as="button"
            :href="route('live.auction')"
            :data="{ _method:'GET', manufacturer: m.name, search: true }"
            class="category-card text-decoration-none text-reset"
          >
            <div class="img position-relative">

               <div
                v-if="!imageLoaded[m.id]"
                class="skeleton-placeholder"
              ></div>
             <img
              :src="m.media?.[0]?.original_url||''"
              alt=""
              class="imgStyleClass"
              :class="{ 'is-loaded': imageLoaded[m.id] }"
              @load="markBrandLoaded(m.id)"
            />
            </div>
            <div class="text">
              <h5 class="topicText">{{ m.name }}</h5>
            </div>
          </Link>
        </div>
      </transition>

  <!-- pager under the grid -->
        <div class="pager mt-3 d-flex d-sm-none">
          <button @click="prevPage" :disabled="currentPage === 0">
            <i class="fas fa-chevron-left"></i>
          </button>
          <button
            @click="nextPage"
            :disabled="(currentPage + 1) * itemsPerPage >= category_manufactures.length"
          >
            <i class="fas fa-chevron-right"></i>
          </button>
              </div>

            </div>
          </div>
  </section>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'

export default {
  name: 'BrowseByBrand',
  components: { Link },
  props: {
    category_manufactures: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      currentPage: 0,
      itemsPerPage: 4,
      touchStartX: 0,
      touchEndX: 0,
      transitionName: 'slide-left',
       imageLoaded: {},
    }
  },
  computed: {
    paginated() {
      const start = this.currentPage * this.itemsPerPage
      return this.category_manufactures.slice(start, start + this.itemsPerPage)
    }
  },

  mounted() {
  // initialize to false
  this.category_manufactures.forEach(m => {
    this.imageLoaded[m.id] = false;
  });

  
  },
  methods: {
    prevPage() {
      if (this.currentPage > 0) {
        this.transitionName = 'slide-right';
        this.currentPage--;
      }
    },
    nextPage() {
      if ((this.currentPage + 1) * this.itemsPerPage < this.category_manufactures.length) {
        this.transitionName = 'slide-left';
        this.currentPage++;
      }
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
    },
     markBrandLoaded(id) {
    
      this.imageLoaded[id] = true;
      }
  }

}
</script>

<style scoped>
.categorySection {
  background-color: #ffffff;
}

.categorySectionColumn {
  margin-top: 30px;
}

.headText {
  font-size: clamp(1.5rem, 2vw, 2.25rem);
  color: #050b20 !important;
  line-height: 1.2;
  margin-bottom: var(--title-spacing) !important;
  font-family: 'Poppins', sans-serif !important;
  font-weight: 700 !important;
}

.view-all-btn {
  background: none;
  color: #000;
  font-weight: 600;
  border: none;
  display: inline-flex;
  align-items: center;
  cursor: pointer;
  gap: 0.4rem;
  white-space: nowrap;
  font-size: clamp(0.875rem, 1vw, 1.125rem) !important;
}
.view-all-btn:hover {
  text-decoration: underline;
}

.arrow-icon {
  width: clamp(0.75rem, 0.5vw, 0.9rem);
  height: clamp(0.75rem, 0.5vw, 0.9rem);
  vertical-align: middle;
}

/* 7 columns on large, then down to 2 on xs */
.brand-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 2rem;
}
@media (max-width: 1200px) {
  .brand-grid { grid-template-columns: repeat(5, 1fr); }
}
@media (max-width: 992px) {
  .brand-grid { grid-template-columns: repeat(4, 1fr); }
}
@media (max-width: 768px) {
  .brand-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 576px) {
  .brand-grid { grid-template-columns: repeat(2, 1fr); }
}

/* smaller cards */
.category-card {
  background-color: #4098FF08 !important;
  border: 1px solid #0d1b2a66 !important;
  border-radius: 12px;
  padding: 0.25rem;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
  aspect-ratio: 1 / 1;
}
@media (hover: hover) and (pointer: fine) {
  .category-card:hover {
    transform: scale(1.03);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
  }
}
.category-card .img {
  flex: 3;
  display: flex;
  align-items: center;
  justify-content: center;
  padding-top: 0.25rem;
}

.category-card .text {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding-bottom: 0.25rem;
}

.imgStyleClass {
  /* max-width: 50%; */
  max-height: 50%;
  object-fit: contain;
}

.topicText {
  font-family: 'Poppins', sans-serif !important;
  font-weight: 600;
  font-size: 0.9rem;
  color: #050b20;
  text-align: center;
}




/*—— MOBILE CAROUSEL tweaks ——*/

.mobile-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

/* nav container under grid, center the two circles */
.mobile-nav {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
}

/* perfect circles with chevrons */
.nav-btn {
  width: 2.5rem;
  height: 2.5rem;
  border: 1px solid #3f3f3fd0;
  border-radius: 50%;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  transition: all 0.2s ease;
}
.nav-btn:hover {
  background: #f8f9fa;
  box-shadow: 0 3px 6px rgba(0,0,0,0.08);
}
.nav-btn:disabled {
  opacity: 0.4;
  cursor: default;
  box-shadow: none;
}
.nav-btn img {
  width: 1rem;
  height: 1rem;
  display: block;
}




/* .pager {
  display: flex;
  justify-content: center;    
  gap: 1rem;                   
  padding: 2rem 0;             
} */

.pager {
  /* margin-left: 1rem;             */
  display: flex;
  justify-content: flex-start;
  gap: 1rem;
  padding: 2rem 0;
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






/* fade-slide transition */
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
  border-radius: inherit; /* match whatever .img had */
}

@keyframes shimmer {
  0%   { background-position: -200% 0; }
  100% { background-position:  200% 0; }
}

/* fade the image in without altering its original size */
.imgStyleClass {
  opacity: 0;
  transition: opacity 0.5s ease;
}
.imgStyleClass.is-loaded {
  opacity: 1;
}
</style>
