<template>
  <Banner :selected-country="selectedCountry" />

  <div class="container-fluid px-4 px-lg-5 mt-5 mb-5">
    <div class="row gx-4 align-items-stretch">
      <!-- Sidebar -->
      <div class="col-12 col-lg-3 filter-column mb-5 mb-lg-0 pe-lg-4" ref="filterColumn">
        <div class="filter-background" ref="filterBox">
          <div class="p-3 filter-sidebar">
            <div class="card-body">
              <h2 class="mb-4">
                <bolt>Filter By</bolt>
              </h2>
              <div class="accordion" id="accordionExample">

            

                <!-- Make -->
                <div class="filter-label">Make</div>
                <div class="accordion-item border-0 mb-3">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse"
                      data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                      {{ getSelectedMakes.length ? getSelectedMakes.join(', ') : 'Select..' }}
                    </button>
                  </h2>
                  <div id="collapseTwo" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                      <Manufactures />
                    </div>
                  </div>
                </div>

                <!-- Model -->
                <div class="filter-label">Model</div>
                <div class="accordion-item border-0 mb-3">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse"
                      data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                      {{ getSelectedModels.length ? getSelectedModels.join(', ') : 'Select..' }}
                    </button>
                  </h2>
                  <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                      <Models />
                    </div>
                  </div>
                </div>
    <!-- Type -->
                <div class="filter-label">Type</div>
                <div class="accordion-item border-0 mb-3">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed  rounded" type="button" data-bs-toggle="collapse"
                      data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                      {{ getSelectedTypes.length ? getSelectedTypes.join(', ') : 'Select..' }}
                    </button>
                  </h2>
                  <div id="collapseOne" class="accordion-collapse collapse " data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                      <Types />
                    </div>
                  </div>
                </div>
                <!-- Country -->
                <div class="filter-label">Country</div>
                <div class="accordion-item border-0 mb-3">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse"
                      data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                      {{ getSelectedCountries.length ? getSelectedCountries.join(', ') : 'Select..' }}
                    </button>
                  </h2>
                  <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                      <Countries :countries="$page.props.countries" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main grid -->
      <div class="col-12 col-lg-9 ps-lg-4">

        <div v-if="isLoading" class="text-center">
          <p>Loading vehicles…</p>
        </div>

        <!-- cardsSection wrapper -->
        <div ref="cardsSection">
          <div v-if="$page.props.vehicles && $page.props.vehicles.data.length"
            class="row row-cols-1 row-cols-md-2 row-cols-lg-2 row-cols-xl-3 g-4">
            <div class="col d-flex justify-content-center" v-for="vehicle in $page.props.vehicles.data"
              :key="vehicle.id">
              <a :href="route('product', {
                model: $root.stringToSlug(vehicle.manufacture.title),
                slug:
                  $root.stringToSlug(vehicle.manufacture.title) +
                  '-' +
                  $root.stringToSlug(vehicle.vehicle_model.title),
                id: vehicle.id,
              })" class="w-100 text-decoration-none">
              <div class="car-card h-100">


                <!-- top image -->
                <div class="card-image position-relative">

                  <div v-if="!imageLoaded[vehicle.id]" class="skeleton-placeholder"></div>

                  <img :src="vehicle.media[0]?.original_url" alt="car" class="auction-image"
                    :class="{ 'is-loaded': imageLoaded[vehicle.id] }" @load="markLoaded(vehicle.id)" />

                  <span class="year-pill">{{ vehicle.year }}</span>
                  <span class="bookmark-icon" @click.stop>
                    <img src="/images/bookmark.png" alt="Bookmark" class="bookmark-img" />
                  </span>

                  <span class="image-counter">
                    <img src="/images/image.png" alt="Photos" class="counter-icon" />
                    {{ vehicle.media?.length || 0 }}
                  </span>
                </div>



                <!-- details -->
                <div class="card-details">
                  <h5 class="car-title">
                    {{ vehicle.manufacture.title }} {{ vehicle.vehicle_model.title }}
                  </h5>
                  <!-- <p class="car-subtitle">{{ vehicle.vehicle_type.title }}</p> -->

                  <div class="specs">
                    <!-- Engine CC -->
                    <div v-if="parseGrades(vehicle.grades).length || vehicle.engine_capacity" class="spec-item">
                      <img src="/images/car-engine.png" alt="Engine" class="spec-icon" />
                      <div class="spec-text">
                        {{ rawList(getFirstGrade(vehicle).engine_capacities) || vehicle.engine_capacity || '–' }}
                      </div>
                    </div>


                    <!-- Fuel Types -->
                  <div class="spec-item" v-if="parseGrades(vehicle.grades).length">
                      <img src="/images/gasoline-pump.png" alt="Fuel" class="spec-icon" />
                      <div class="spec-text">
                        {{ getFuelSummary(getFirstGrade(vehicle).fuel_types) }}
                      </div>
                    </div>

                    <!-- Transmission -->
                    <div
                      v-if="parseGrades(vehicle.grades).length"
                      class="spec-item"
                    >
                      <img src="/images/gearbox.png" alt="Transmission" class="spec-icon" />
                      <div class="spec-text">
                        {{ getFirstGrade(vehicle).transmission || '–' }}
                      </div>
                    </div>
                  </div>

                  <div class="card-footer">
                    <div class="price-wrapper text-center">

                      <div class="d-flex flex-column align-items-start">
                        <small class="text-muted mb-1">Upwards</small>
                        <div class="price fs-4 fw-bold">
                          {{ vehicle.monthly_price_currency === 'USD' ? '$' : 'LKR ' }}
                          {{ formatPrice(vehicle.monthly_price) }}
                        </div>
                      </div>
                    </div>

                    <!-- <span class="view-details">
                      View Details
                      <img src="/images/Vector.png" alt="→" class="arrow-icon_1 ps-2" />
                    </span> -->
                  </div>
                </div>
              </div>
            </a>
            </div>
          </div>

          <div v-else class="text-center">
            <p>No vehicles found matching your filters.</p>
          </div>
        </div>

        <!-- pagination -->
        <div v-if="$page.props.vehicles.last_page > 1"
          class="d-flex justify-content-center justify-content-lg-end mt-4 pagination-wrapper">
          <nav aria-label="Page navigation">
            <ul class="pagination mb-0">
              <li class="page-item" :class="{ disabled: !$page.props.vehicles.prev_page_url }">
                <a class="page-link" href="#" @click.prevent="changePage($page.props.vehicles.current_page - 1)">
                  Previous
                </a>
              </li>

              <li v-if="visiblePages[0] > 1" class="page-item disabled">
                <span class="page-link">…</span>
              </li>

              <li class="page-item" v-for="page in visiblePages"
                :class="{ active: $page.props.vehicles.current_page === page }" :key="page">
                <a class="page-link" href="#" @click.prevent="changePage(page)">
                  {{ page }}
                </a>
              </li>

              <!-- optionally show “…” if you’ve clipped the end -->
              <li v-if="visiblePages[visiblePages.length - 1] < $page.props.vehicles.last_page"
                class="page-item disabled">
                <span class="page-link">…</span>
              </li>

              <li class="page-item" :class="{ disabled: !$page.props.vehicles.next_page_url }">
                <a class="page-link" href="#" @click.prevent="changePage($page.props.vehicles.current_page + 1)">
                  Next
                </a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import Types from "./models/Types.vue";
import Manufactures from "./models/Manufactures.vue";
import Models from "./models/Models.vue";
import Countries from "./models/Countries.vue";
import Banner from "./Banner.vue";
// import { set } from "vue";
export default {

  updated() {
    this.$nextTick(() => {
      const box = this.$refs.filterBox
      const current = box?.style.position
      // only recalc if we're in fixed/absolute (i.e. already stuck)
      if (current === 'fixed' || current === 'absolute') {
        this.handleSticky()
      }
    })
  },
  components: {
    Banner,
    Types,
    Manufactures,
    Models,
    Countries,
    Link,
  },
  data() {
    return {
      // isSticky: false,
      sortBy: this.$page.props.requestQuery.sortBy || "",
      keyword: this.$page.props.requestQuery.keyword || "",
      isLoading: false,
      imageLoaded: {},
    };
  },
  computed: {
    selectedCountry() {
      return this.$page.props.selectedCountry || null;
    },
    getSelectedTypes() {
      const query = this.$page.props.requestQuery?.type || [];
      const types = Array.isArray(query) ? query : [query];
      return types.map(id => {
        const type = this.$page.props.vehicleTypes.find(t => t.id === parseInt(id));
        return type ? type.title : '';
      }).filter(Boolean);
    },

    getSelectedMakes() {
      const query = this.$page.props.requestQuery?.brand || [];
      const makes = Array.isArray(query) ? query : [query];
      return makes.map(id => {
        const make = this.$page.props.manufacturers.find(m => m.id === parseInt(id));
        return make ? make.title : '';
      }).filter(Boolean);
    },

    getSelectedModels() {
      const query = this.$page.props.requestQuery?.model || [];
      const models = Array.isArray(query) ? query : [query];
      return models.map(id => {
        const model = this.$page.props.models.find(m => m.id === parseInt(id));
        return model ? model.title : '';
      }).filter(Boolean);
    },

    getSelectedCountries() {
      const query = this.$page.props.requestQuery?.country || [];
      const countries = Array.isArray(query) ? query : [query];
      return countries.map(id => {
        const country = this.$page.props.countries.find(c => c.id === parseInt(id));
        return country ? country.name : '';
      }).filter(Boolean);
    },
    visiblePages() {


      const vp = this.$page.props.vehicles
      const last = vp.last_page
      const cur = vp.current_page

      // we want a window of 3 pages centered on `cur`
      let start = cur - 1
      let end = cur + 1

      // clamp at the low end
      if (start < 1) {
        start = 1
        end = Math.min(3, last)
      }
      // clamp at the high end
      if (end > last) {
        end = last
        start = Math.max(last - 2, 1)
      }

      const pages = []
      for (let p = start; p <= end; p++) pages.push(p)
      return pages
    }
  },
  mounted() {
    window.addEventListener('scroll', this.handleSticky)
    window.addEventListener('resize', this.handleSticky)
    // this.$nextTick(this.handleSticky)
    //  const panels = this.$refs.filterColumn.querySelectorAll('.accordion-collapse')
    //  panels.forEach(panel => {
    // panel.addEventListener('shown.bs.collapse',  this.handleSticky)
    // panel.addEventListener('hidden.bs.collapse', this.handleSticky)
    // })


  },

  beforeUnmount() {
    window.removeEventListener('scroll', this.handleSticky)
    window.removeEventListener('resize', this.handleSticky)
    //   const panels = this.$refs.filterColumn.querySelectorAll('.accordion-collapse')
    // panels.forEach(panel => {
    //   panel.removeEventListener('shown.bs.collapse',  this.handleSticky)
    //   panel.removeEventListener('hidden.bs.collapse', this.handleSticky)
    // })
  },
  methods: {
    parseGrades(grades) {
    try {
      return Array.isArray(grades)
        ? grades
        : JSON.parse(grades || '[]')
    } catch {
      return []
    }
  },

  rawList(val) {
    // Show exactly what backend sends, but render arrays / JSON arrays nicely
    if (val == null) return '';
    if (Array.isArray(val)) return val.join(', ');
    const s = String(val).trim();
    if (!s) return '';
    try {
      const arr = JSON.parse(s);
      return Array.isArray(arr) ? arr.join(', ') : s;
    } catch {
      return s; // not JSON → show as-is
    }
  },


  // pull out the very first grade object (or {} if none)
 getFirstGrade(vehicle) {
    const arr = this.parseGrades(vehicle.grades)
    return arr.length ? arr[0] : {}
  },
  
  parseList(input) {
    // 1) if it's already an array, return it
    if (Array.isArray(input)) {
      return input
    }
    // 2) if it's already an object, wrap it
    if (input && typeof input === 'object') {
      return [input]
    }
    // 3) otherwise try JSON.parse
    try {
      const arr = JSON.parse(input)
      return Array.isArray(arr) ? arr : [arr]
    } catch {
      return []
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
      getFuelSummary(input) {
    // parse into an array of { id, name } objects or strings
    const raw = this.parseList(input)

    // map everything to its .name if it's an object, otherwise leave it
    const names = raw.map(x => (x && x.name) ? x.name : x)

    if (names.length === 0)       return ''
    if (names.length === 1)       return names[0]
    // two or more: show first + “+N more”
    return `${names[0]} +${names.length - 1} `
  },
    
    markLoaded(id) {
      this.imageLoaded[id] = true;
    },

    handleSticky() {
      // queue up our sticky logic for the next paint
      window.requestAnimationFrame(() => {
        const NAV_H = 80
        const col = this.$refs.filterColumn
        const box = this.$refs.filterBox
        if (!col || !box) return

        if (window.innerWidth < 992) {
          box.style.position = ''
          box.style.top = ''
          box.style.left = ''
          box.style.width = ''
          return
        }

        const colRect = col.getBoundingClientRect()
        const style = getComputedStyle(col)
        const padL = parseFloat(style.paddingLeft)
        const padR = parseFloat(style.paddingRight)
        const colTop = colRect.top + window.pageYOffset
        const colH = col.offsetHeight
        const innerW = colRect.width - padL - padR
        const innerL = colRect.left + padL
        const boxH = box.offsetHeight
        const scrollY = window.pageYOffset || window.scrollY
        const startY = colTop - NAV_H
        const endY = colTop + colH - boxH - NAV_H

        if (scrollY < startY) {
          box.style.position = ''
          box.style.top = ''
          box.style.left = ''
          box.style.width = ''
        }
        else if (scrollY > endY) {
          box.style.position = 'absolute'
          box.style.top = (colH - boxH) + 'px'
          // box.style.left = padL + 'px'
          // box.style.width = innerW + 'px'
          const rawW = innerW;
          box.style.width = rawW + 'px';

          const maxW = parseFloat(getComputedStyle(box).maxWidth);
          const actualW = Math.min(rawW, maxW);
          const extra = (rawW - actualW) / 2;
          box.style.left = (padL + extra) + 'px';
        }
        else {
          box.style.position = 'fixed'
          box.style.top = NAV_H + 'px'
          // box.style.left = innerL + 'px'
          // box.style.width = innerW + 'px'
          const rawW = innerW;
          box.style.width = rawW + 'px';

          const maxW = parseFloat(getComputedStyle(box).maxWidth);
          const actualW = Math.min(rawW, maxW);
          const extra = (rawW - actualW) / 2;

          box.style.left = (innerL + extra) + 'px';


        }
      })
    }

    ,

    handleScroll() {
      const scrollY = window.scrollY;
      const offsetTop = this.$refs.filterSidebar.offsetTop;
      this.isSticky = scrollY > offsetTop;
    },
    search() {
      this.isLoading = true;
      this.$inertia.reload({
        method: "POST",
        data: { _method: "GET", keyword: this.keyword },
        onFinish: () => (this.isLoading = false),
      });
    },
    clearSearch() {
      this.keyword = "";
      this.isLoading = true;
      this.$inertia.visit("/available-cars", {
        method: "get",
        onFinish: () => (this.isLoading = false),
      });
    },
    applySort() {
      this.isLoading = true;
      this.$inertia.reload({
        method: "POST",
        data: { _method: "GET", sortBy: this.sortBy },
        onFinish: () => (this.isLoading = false),
      });
    },
    formatPrice(val) {
      return Number(val).toLocaleString();
    },
    getExColor(colorId) {
      const entry = this.$page.props.vehicleTypes.find(
        (x) => x.id === colorId
      );
      return entry ? entry.title : "";
    },

    changePage(page) {
      if (page < 1 || page > this.$page.props.vehicles.last_page) return

      // 1) snap instantly to top of the cards
      const el = this.$refs.cardsSection
      if (el) {
        const NAV_H = 80    // if you have a fixed header; otherwise make this 0
        const topY = el.getBoundingClientRect().top + window.pageYOffset - NAV_H
        
      }

      // 2) fire off Inertia but preserve the scroll position we just set
      this.isLoading = true
      this.$inertia.reload({
        data: {
          ...this.$page.props.requestQuery,
          page
        },
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
          this.isLoading = false
        }
      })
    }

    //   changePage(page) {
    //   if (page < 1 || page > this.$page.props.vehicles.last_page) return

    //   this.isLoading = true

    //   this.$inertia.reload({
    //     data: { ...this.$page.props.requestQuery, page },
    //     preserveState: true,
    //     onSuccess: () => {
    //       this.$nextTick(() => {
    //         const el = this.$refs.cardsSection
    //         if (el) {
    //           // instant jump—no smooth scroll
    //           el.scrollIntoView({ behavior: 'auto', block: 'start' })
    //         }
    //       })
    //       this.isLoading = false
    //     }
    //   })
    // }
  },
};
</script>

<style scoped>
.filter-column {
  /* background-color: #14233420; */

  /* box-shadow: 0 10px 40px rgba(0, 0, 0, 0.35); */

  z-index: 1;
  /* border-right: 1px solid rgba(0, 0, 0, 0.1); */

  /* padding: 18px 0; */

  overflow: visible;
  position: relative;
  /* padding: 18px; */


}



.filter-background {
  background-color: #ffffff;
  border: 1px solid #00000054;
  border-radius: 15px;
  padding: 35px 25px;
box-shadow: #00000073 0px 0px 10px 0px;
/* top: 150%; */
  width: auto;
  max-width: 420px;
  margin: 0 auto;

  position: static;
  box-sizing: border-box;
}


.filter-column,
.filter-background {
  overflow: visible;
  /* allow dropdowns to spill out */
}

.accordion-body {
  max-height: 300px;
  /* adjust to taste */
  overflow-y: auto;
  /* scroll if too tall */
  overflow-x: visible;
  /* let it expand sideways */
  box-sizing: border-box;
  white-space: normal;
  /* long labels wrap instead of stretch */
}

.dropdown-menu {
  position: absolute;
  z-index: 1000;
  /* higher than your card */
}

.filter-sidebar {


  border: none;
  margin-right: 0;
  /* background-color: #FCFCFC; */
  /* Remove any position properties */
  position: static;
}

.gradient-body {
  background: linear-gradient(90deg,
      #415a77 0%,
      #0a3f79 0%,
      #163353 34%,
      #142334 67%,
      #0e1e30 80%,
      #0d1b2a 100%);
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.gradient-body .text-white {
  color: #fff !important;
  font-size: 1rem;
}


.gradient-body .text-white-50 {
  color: rgba(255, 255, 255, 0.5) !important;
}

.gradient-body i.fa-2x {
  font-size: 1.2rem;
  color: #fff;
}

/* .sticky-filter {
    position: relative;
    
    z-index: 10;
} */

.accordion-body {
  height: auto !important;
  overflow-y: auto !important;
  font-size: 12px;
  padding-top: 0px;
}

.accordion-button {
  border: 1px solid #eaeaea;
  background-color: #FCFCFC;
  color: #666;
  font-weight: 500;
  padding: 12px 16px;
  font-size: 14px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
}

.accordion-button::after {
  opacity: 0.5;
}

.accordion-button:not(.collapsed) {
  background-color: #f8f9fa;
  color: #04245d;
  font-weight: 600;
}

.accordion-button.collapsed::after {
  margin-left: auto;
}

.accordion-collapse {
  background-color: #fff;
}

.btn-outline-secondary {
  font-weight: 600;
}


/* Add styles for filter labels */
.filter-label {
  color: #7f6c6c;
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 8px;
  padding-left: 4px;
}

.accordion-item {
  margin-bottom: 20px;
}

.filter-sidebar h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #142334;
  margin-bottom: 1.5rem;
}

.filter-sidebar h3 {
  font-size: 1.25rem;
  margin-bottom: 1rem;
}

/* Pagination column padding */
.col-lg-7 {
  padding: 0 1.5rem 0 0;
}

/* Form controls unchanged */
.form-control {
  background-color: #f7f7f7;
  border: 1px solid #9ea3b9;
  border-radius: 0;
  padding: 0.75rem;
  margin-bottom: 1.5rem;
}

/* Update/add these specific styles */
/* .container-fluid {
    max-width: 100% !important;
  margin: 0 auto;
   padding-left: 1.5rem !important;   
  padding-right: 1.5rem !important;
} */


/* .col-lg-9 {
  padding-left: 12px;
  
} */




.row.gx-4 {

  --bs-gutter-x: 2rem !important;
  /* Adjust space between cards */
}

.g-4 {
  --bs-gutter-x: 2rem;
}


/* 1) Active page pill stays the gradient + white text */
.pagination .page-item.active .page-link {
  background: linear-gradient(90deg,
      #415a77 0%,
      #0a3f79 0%,
      #163353 34%,
      #142334 67%,
      #0e1e30 80%,
      #0d1b2a 100%);
  border-color: transparent;
  color: #fff;
}

/* 2) Prev & Next: light-gray background + dark text */
.pagination .page-item:first-child .page-link,
.pagination .page-item:last-child .page-link {
  /* background-color: #e9ecef !important; */
  color: #6c757d !important;
  background-image: none !important;
  -webkit-text-fill-color: initial !important;
}

/* 3) Numbered pages (only): gradient-filled text */
.pagination .page-item:not(.active):not(:first-child):not(:last-child) .page-link {
  background: none;
  background-image: linear-gradient(90deg,
      #415a77 0%,
      #0a3f79 0%,
      #163353 34%,
      #142334 67%,
      #0e1e30 80%,
      #0d1b2a 100%);
  background-clip: text;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}


/* 1) Outer card container: grey background, full radius */


@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

.car-card {
  font-family: 'Poppins', sans-serif !important;
  background: #f0f3f7;
  border-radius: 16px;
  overflow: hidden;
  /* add this: */
  border: 0.5px solid #a1bad6;
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

.col-lg-7 .col.d-flex .car-card {
  width: 90%;
  max-width: 360px;
  /* tweak this number up/down to taste */
  margin: 0 auto;
}

/* Update the main content column */
.col-lg-7 {
  padding-left: 1.5rem;
  /* Increase left padding for gap between filter and cards */
  padding-right: 0;
  /* Remove right padding */
}


.car-card,
.car-title,
.car-subtitle,
.price,
.view-details {
  color: #21252B;
}

/* 2) Top “white cap” */
.card-image {
  position: relative;
  background: #fff;
  /* pure white behind photo */
  border-radius: 16px;
  /* all four corners */
  overflow: hidden;
  box-shadow: 0px 4px 12px #79787840;
  /* clip the <img> to that shape */
}

.card-image img {
  display: block;
  width: 100%;
  height: 240px;
  /* bump this if you want it taller */
  object-fit: cover;

}



/* year pill + bookmark */
.card-image .year-pill,
.card-image .bookmark-icon {
  position: absolute;
  z-index: 1;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.card-image .year-pill {
  position: absolute;
  top: 12px;
  left: 12px;
  background-image: linear-gradient(90deg,
      #415a77 0%,
      #0a3f79 0%,
      #163353 34%,
      #142334 67%,
      #0e1e30 80%,
      #0d1b2a 100%);
  color: #fff;
  font-family: 'Poppins', sans-serif;
  font-size: 0.875rem;
  /* bump from .75rem */
  padding: 6px 24px;
  /* more padding for a larger pill */
  border-radius: 22px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
  z-index: 1;
  display: inline-block;
}

.card-image .bookmark-icon {
  top: 12px;
  right: 12px;
  background: #d2d4d6;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  color: #142334;
}

/* 3) Grey details section */
.card-details {
  padding: 20px 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  flex: 1;
}

/* Titles */
.car-title {
  font-family: 'Poppins', sans-serif;
  font-size: 1.125rem;
  /* 18px */
  font-weight: 500;
  color: #21252B;
  margin: 0;
}

.car-subtitle {
  font-family: 'Poppins', sans-serif;
  font-size: 0.875rem;
  /* 14px */
  font-weight: 400;
  color: #21252B;
  margin: 0;
}

/* Specs grid */
.specs {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 8px 0 16px;
}

.spec-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.spec-icon {
  width: 24px;
  /* adjust to taste */
  height: 24px;
  margin-bottom: 6px;
  object-fit: contain;
}

.spec {
  display: flex;
  flex-direction: column;
  align-items: center;
  font-family: 'Poppins', sans-serif;
  font-size: .875rem;
  /* 14px */
  font-weight: 400;
  color: #050B20;
}

.spec i {
  font-size: 1.25rem;
  /* 20px */
  margin-bottom: 6px;
}

.spec-item span {
  font-size: 15px;
  /* e.g. 14px – bump up or down */
  font-weight: 500;
  /* medium weight for emphasis */
  color: #050B20;
  /* ensure it stays your brand color */
}

/* Footer */
.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
}

.price {
  font-family: 'Poppins', sans-serif;
  font-size: 1.25rem;
  /* 20px */
  font-weight: 600;
  color: #050B20;
}

.view-details {
  font-family: 'Poppins', sans-serif;
  font-size: 0.875rem;
  /* 14px */
  font-weight: 500;
  color: #050B20 !important;
  display: inline-flex;
  align-items: center;
}

.view-details i {
  margin-left: 6px;
  font-size: 0.75rem;
}

.bookmark-icon {
  position: absolute;
  top: 12px;
  right: 12px;
  background-color: #464545;
  /* light gray circle */
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
  cursor: pointer;
  z-index: 1;
}

.bookmark-icon .bookmark-img {
  width: 15px;
  height: 15px;
  object-fit: contain;
}



/* 2) Desktop only: keep that big left gutter, remove right */
@media (min-width: 992px) {
  .col-lg-7 {
    padding-left: 1.5rem;
    /* your desired large left gap */
    padding-right: 0;
  }
}


/* shimmering placeholder */
.skeleton-placeholder {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to right, #e0e0e0 0%, #f8f8f8 50%, #e0e0e0 100%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: inherit;
  z-index: 1;
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
  z-index: 2;
}

@media (min-width: 992px) {
  .filter-column {
    padding-left: 1rem;
    /* or 2rem, whatever gap you like */
  }
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
</style>
