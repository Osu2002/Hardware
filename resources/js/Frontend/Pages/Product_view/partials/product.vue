<template>

  <div class="container px-3 px-md-5 main-container section">
    <div class="breadcrumbs mb-4">
      <a href="/">Home</a> /
      <a href="/available-cars">Listings</a> /
      <span class="current">
        {{ vehicle.manufacture.title }} {{ vehicle.vehicle_model.title }}
      </span>
    </div>
    <!-- HEADER ROW: Title + Pills on left, Action-panel on right -->
    <div class="d-flex justify-content-between align-items-baseline mb-4 sticky-header">

      <div>
        <!-- Vehicle Title -->
        <h1 class="vehicle-title mb-2">
          {{ vehicle.manufacture.title }} {{ vehicle.vehicle_model.title }}<span class="separator">-</span>

          <!-- year‑pill: inline right after the text -->
          <span class="year-pill ">
            <i class="fa-solid fa-calendar-days"></i>
            <span class="">{{ vehicle.year }}</span>
          </span>
        </h1>


        <!-- Detail Pills -->
        <!-- <div class="details-grid">
          <div class="detail-item"><i class="fa-solid fa-calendar-days"></i><span class="label"></span><span
              class="value">{{ vehicle.year }}</span></div>

        </div> -->
      </div>
      <!-- Action-panel floated right -->
       <div class="action-panel ms-auto">

        <!-- Price -->
        <div class="price-inline mb-3">
          <span class="upwards-label">Upwards</span>
          <span class="main-price">
            {{ vehicle.monthly_price_currency==='USD'?'$':'LKR ' }}
            {{ Number(vehicle.monthly_price||0).toLocaleString() }}
          </span>
        </div>


      </div>
    </div>



    <div class="row mb-4">
      <div class="col-lg-7 mb-3 mb-lg-0 px-2 pe-lg-1">
        <div class="main-image-container position-relative">


          <div v-if="!mainImageLoaded" class="skeleton-placeholder"></div>

          <img :src="allImages[currentThumbIndex].original_url" :alt="allImages[currentThumbIndex].alt"
            class="main-image" @click="openGrid" @load="onMainLoad" :class="{ 'is-loaded': mainImageLoaded }" />


          <div v-if="hasVideo" class="video-overlay" @click="playVideo">
            <i class="bi bi-play-circle"></i>
            <span>Video</span>
          </div>
        </div>
      </div>



      <!-- 2×2 Thumbnail grid -->
      <div class="col-lg-5 px-2 ps-lg-2">
        <div class="thumbnail-grid">
          <div v-for="(img, idx) in allImages.slice(1, 5)" :key="idx" class="thumbnail-wrapper"
            @click="openGrid">

            <div v-if="!thumbLoaded[idx + 1]" class="skeleton-placeholder"></div>
            <img :src="img.original_url" class="thumbnail-img" @load="onThumbLoad(idx + 1)"
              :class="{ 'is-loaded': thumbLoaded[idx + 1] }" />
            <div v-if="idx === 3" class="gallery-button" @click="openGrid">
              <i class="fa-solid fa-images"></i>
              <span>More Images</span>
            </div>
          </div>
        </div>
      </div>
    </div>


    <!-- right before your swiper-lightbox modal: -->
    <GalleryGrid :visible="isGridOpen" :images="allImages" :categories="imageCategories" @close="isGridOpen = false"
      @select="handleGridSelect" />


    <!-- Fullscreen gallery modal -->
    <transition name="fade">
      <div v-if="isGalleryOpen" class="gallery-modal">
        <button class="close-btn" @click="closeGallery">&times;</button>

        <swiper ref="gallerySwiper" :modules="modules" class="gallery-swiper" :initial-slide="galleryIndex" navigation
          loop :slides-per-view="1" :space-between="0" :centered-slides="false">
          <swiper-slide v-for="(img, idx) in galleryImages" :key="idx">
            <img :src="img.original_url" :alt="img.alt" class="gallery-slide-img" />
          </swiper-slide>
        </swiper>
      </div>
    </transition>



    <!-- Car Overview and Dealer Panel -->
    <div class="row">
      <div class="col-lg-8">
        <h2 class="summary-title mb-4 text-left">Grades</h2>

        <div class="mb-5 px-4 py-4">
          <div class=" grades">

            <!-- <div class="overview-label">Grades</div> -->
            <div class="overview-value">
              <div class="grades-grid">
                <div v-for="(g, i) in rawGrades" :key="i" class="grade-card"
                  :class="{ active: selectedGradeIndex === i }" @click="selectedGradeIndex = i">
                  <img src="/images/icons/file.gif" class="grades-icon" alt="Grades" />
                  {{ g.grade }}
                </div>
              </div>


            </div>
          </div>
        </div>

        <h2 class="summary-title mb-4 text-left">Features</h2>
        <div class="mb-5 px-4 py-4">
          <div class="overview-grid">
            <div class="overview-item engine">
              <img src="/images/icons/engine.gif" alt="Engine CC" class="overview-icon" />
              <div class="overview-label">Engine CC</div>
              <div class="overview-value">
                {{ rawList(selectedGrade.engine_capacities) || rawList(vehicle.engine_capacities) || '–' }}
              </div>
            </div>
            <div class="overview-item transmission">
              <img src="/images/icons/management.gif" alt="Transmission" class="overview-icon" />
              <div class="overview-label">Transmission</div>
              <div class="overview-value">{{ selectedGrade.transmission }}</div>
            </div>
            <div class="overview-item drive">
              <img src="/images/icons/chassis.gif" alt="Drive Type" class="overview-icon" />
              <div class="overview-label">Chassis No</div>
              <div class="overview-value"> {{ (selectedGrade.chassis_nos || []).join(', ') }}</div>
            </div>
            <div class="overview-item fuel">
              <img src="/images/icons/bio-oil.gif" alt="Fuel Types" class="overview-icon" />
              <div class="overview-label">Fuel Types</div>
              <div class="overview-value"> {{(selectedGrade.fuel_types || []).map(ft => ft.name).join(', ')}}
              </div>
            </div>
          </div>
        </div>

        <!-- <h2 class="summary-title mb-4 text-left">Description</h2> -->
        <!-- responsive description block -->
        <div class="row mb-5">
          <div class="col-12 px-3 px-md-4 py-3 py-md-4">
            <div class="description-content">
              {{ selectedGrade.description }}
            </div>
          </div>
        </div>


        <!-- Car Overview -->
        <!-- <div class="summary mb-5 px-4 py-4">
          <h2 class="summary-title mb-4">Car Overview</h2>


          <div class="row row-cols-1 row-cols-md-2 gx-5 gy-4">

            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/car.png" class="summary-icon me-2" alt="Body" />
                <span class="label me-auto">Body</span>
              </div>
            </div>

            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/user.png" class="summary-icon me-2" alt="Status" />
                <span class="label me-auto">Condition</span>
                <span class="value">
                  {{
                    vehicle.vehicle_status === 'new'
                      ? 'Brand New'
                      : vehicle.vehicle_status === 'used'
                        ? 'Used '
                        : '—'
                  }}
                </span>
              </div>
            </div>


            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/tachometer.png" class="summary-icon me-2" alt="Mileage" />
                <span class="label me-auto">Mileage</span>
              </div>
            </div>

            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/engine.png" class="summary-icon me-2" alt="Engine Size" />
                <span class="label me-auto">Engine Size</span>
              </div>
            </div>

            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/gasoline-pump.png" class="summary-icon me-2" alt="Fuel Type" />
                <span class="label me-auto">Fuel Type</span>
              </div>
            </div>

            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/car-door.png" class="summary-icon me-2" alt="Door" />
                <span class="label me-auto">Door</span>
              </div>
            </div>

            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/calendar.png" class="summary-icon me-2" alt="Year" />
                <span class="label me-auto">Year</span>
                <span class="value">{{ vehicle.year }}</span>
              </div>
            </div>

            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/gearbox.png" class="summary-icon me-2" alt="Transmission" />
                <span class="label me-auto">Transmission</span>
              </div>
            </div>

            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/steering-wheel.png" class="summary-icon me-2" alt="Drive Type" />
                <span class="label me-auto">Drive Type</span>
              </div>
            </div>

            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/fill.png" class="summary-icon me-2" alt="Color" />
                <span class="label me-auto">Color</span>
              </div>
            </div>

            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/passenger.png" class="summary-icon me-2" alt="Passengers" />
                <span class="label me-auto">Passengers</span>
              </div>
            </div>

            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/sim.png" class="summary-icon me-2" alt="VIN" />
                <span class="label me-auto">VIN</span>
              </div>
            </div>

          </div>
        </div> -->


        <!-- Description -->
        <div class="description" ref="descBox">
          <h1 class="fw-bold text-start" style="font-size: 30px; margin-bottom: 2rem;">Description</h1>
          <div v-html="$page.props.vehicle.editorContent"></div>
          <!-- <div id="interiorFeatures" class="accordion-collapse">
            <div class="accordion-body">
              <ul class="list-unstyled" style="font-size: 1.3rem;">
                <li v-html="$page.props.vehicle.editorContent"></li>
              </ul>
            </div>
          </div> -->
        </div>
      </div>

      <!-- Dealer Panel -->
      <div class="col-lg-4">
        <div class="dealer-panel-wrapper position-sticky" style="top:190px;">
          <div class="dealer-card shadow border  rounded-4 p-4">
            <div class="p-4 bg-white" style="border-radius: 12px;">
              <!-- Image & header -->

              <div class="dealer-profile mb-4 text-center">
                <img src="/images/test.jpg.png" alt="Dealer Profile" class="profile-picture mb-3"
                  style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;" />


                <h5 class="dealer-name mb-1">World Auto Dealers</h5>
                <!-- <p class="dealer-location text-muted mb-0">Galle, Sri Lanka</p> -->
              </div>

              <div class="row dealer-contact-links mb-2 gx-2 gy-2">


                <!-- Dealer Contact Links -->
                <div class="row row-cols-1 row-cols-md-auto gx-2 gy-2 mb-4">
                  <!-- Get Direction -->
                  <!-- <div class="col">
                    <a href="https://www.google.com/maps/dir/?api=1&destination=Galle%2C%20Sri%20Lanka" target="_blank"
                      rel="noopener noreferrer" class="dealer-link d-flex align-items-center w-100">
                      <img src="/images/getLocation.png" class="flat-icon me-2" alt="Get Direction" />
                      <span>Get Direction</span>
                    </a>
                  </div> -->

                  <!-- Phone -->
                  <div class="col">
                    <a href="tel:+94114001499"
                      class="dealer-link d-flex align-items-center justify-content-md-end w-100">
                      <img src="/images/call.png" class="flat-icon me-2" alt="Call Dealer" />
                      <span>+94 11 400 1 499</span>
                    </a>
                  </div>
                </div>

              </div>


              <!-- Actions -->
              <div class="dealer-actions d-grid gap-3">
                <!-- <button class="primary-btn w-100 d-flex justify-content-between align-items-center">
                  <span>Message Dealer</span>
                  <img src="/images/Vector.png" alt="→" class="arrow-icon" />
                </button> -->

                <a :href="whatsappLink" target="_blank" rel="noopener" class="secondary-btn w-100">
                  Chat Via WhatsApp
                  <img src="/images/Vector.png" class="arrow-icon_1" alt="" />
                </a>

                <a href="/available-cars" class="view-stock-link">
                  View all dealer’s stock
                  <i class="bi bi-chevron-right"></i>
                  <img src="/images/Vector.png" class="arrow-icon_1" />
                </a>

              </div>
              <ContactUs :vehicle="vehicle" />

            </div>
          </div>

        </div>
      </div>

    </div>

    <!-- Similar Cars -->
  </div>
</template>

<script>
import { Link, useForm } from "@inertiajs/inertia-vue3";
import { ref } from "vue";
import Swal from "sweetalert2";
import AppLayout from "@@/Layouts/AppLayout.vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/thumbs";
// ← ADDED Keyboard here
import { Mousewheel, FreeMode, Navigation, Thumbs, Keyboard } from "swiper/modules";
import Featured_Listings from "./Featured_Listings.vue";
import ContactUs from "./ContactUs.vue";
import GalleryGrid from './GalleryGrid.vue';

export default {
  components: {
    AppLayout,
    Swiper,
    SwiperSlide,
    Link,
    Featured_Listings,
    ContactUs,
    GalleryGrid,

  },
  props: {
    vehicle: Object,
    randomVehicles: Object,
  },
  data() {
    return {
      isGridOpen: false,
      isMobile: window.innerWidth < 576,
      mainImageLoaded: false,
      selectedGradeIndex: 0,
      thumbLoaded: {},
      form: useForm({
        vehicle_name:
          this.$page.props.vehicle.manufacture.title +
          " " +
          this.$page.props.vehicle.vehicle_model.title,
        vehicle_url: route("product", {
          model: this.$root.stringToSlug(
            this.$page.props.vehicle.manufacture.title
          ),
          slug:
            this.$root.stringToSlug(
              this.$page.props.vehicle.manufacture.title
            ) +
            "-" +
            this.$root.stringToSlug(
              this.$page.props.vehicle.vehicle_model.title
            ),
          id: this.$page.props.vehicle.id,
        }),
        vehicle_id: this.$page.props.vehicle.id,
        stock_type: "local",
        name: "",
        email: "",
        mobile: "",
        purchase_time: "",
        payment_method: "",
        address: "",
        message: "",

      }),

      // your existing image arrays
      mainImages: this.vehicle.media.filter(
        (image) => image.custom_properties?.type === "vehicle_main"
      ),
      thumbnailImages: this.vehicle.media.filter(
        (image) => image.custom_properties?.type === "vehicle_gallery"
      ),
      currentThumbIndex: 0,

      // toggle full-width gallery (existing)
      isFullGallery: false,

      // ← NEW for slideshow
      isGalleryOpen: false,
      galleryIndex: 0,
    };
  },
  // ← REGISTER Keyboard for your main Swiper if you ever used it there
  setup() {
    return {
      modules: [Mousewheel, FreeMode, Thumbs, Navigation, Keyboard],
    };
  },

  // ← NEW computed properties
  computed: {
    selectedFuelNames() {
      const list = this.selectedGrade.fuel_types || [];
      return list.map(ft => ft.name).join(', ');
    },
    rawGrades() {
      try {
        const arr = JSON.parse(this.vehicle.grades)
        return Array.isArray(arr) ? arr : [arr]
      } catch {
        return []
      }
    },
    // whichever one is selected (or empty object)
    selectedGrade() {
      return this.rawGrades[this.selectedGradeIndex] || {}
    },
    // keep this around if you still want the grade-only list elsewhere
    gradeLabels() {
      return this.rawGrades.map(g => g.grade || '')
    },
    pageurl() {
      return window.location.href;
    },
    whatsappLink() {
      const phone = '94114001499'
      const lines = [
        `Hello! I’m interested in this car: *${this.vehicle.manufacture.title} ${this.vehicle.vehicle_model.title}*`,
        `*Year:* ${this.vehicle.year}`,
        ``,
        `*View details:* ${window.location.href}`,
        ``,
        `Looking forward to your response!`
      ]
      const text = encodeURIComponent(lines.join('\n'))
      return `https://wa.me/${phone}?text=${text}`
    },
    formattedEngine() {
      try {
        const arr = JSON.parse(this.vehicle.engine_capacities);
        return Array.isArray(arr) ? arr.join(', ') : this.vehicle.engine_capacities;
      } catch {
        return this.vehicle.engine_capacities;
      }
    },
    formattedFuelTypes() {
      try {
        const arr = JSON.parse(this.vehicle.fuel_types);
        return Array.isArray(arr) ? arr.join(', ') : this.vehicle.fuel_types;
      } catch {
        return this.vehicle.fuel_types;
      }
    },
    gradesArray() {
      // 1) try to parse whatever’s in vehicle.grades
      let raw;
      try {
        raw = JSON.parse(this.vehicle.grades);
      } catch {
        // if it’s not valid JSON, just bail out
        return [];
      }

      // 2) if it’s an array, pull out item.grade (or '' if missing)
      if (Array.isArray(raw)) {
        return raw.map(item => item?.grade ?? '');
      }

      // 3) if it’s a single object with a .grade
      if (raw && raw.grade) {
        return [raw.grade];
      }

      // 4) otherwise nothing to show
      return [];
    },


    allImages() {
      // merge main + gallery into one array
      return [...this.mainImages, ...this.thumbnailImages];
    },

    galleryImages() {
      return this.allImages;
    },

    hasVideo() {
      // detect if any media item is a video
      return this.vehicle.media.some(
        (m) => m.custom_properties?.type === "vehicle_video"
      );
    },
    imageCategories() {
      const cats = this.allImages
        .map(img => img.custom_properties?.category)
        .filter(Boolean)
      return ['All', ...new Set(cats)]
    }
  },

  mounted() {
    // console.log(this.whatsappLink())
    console.log(this.$refs.thumbsSwiper);
    // window.addEventListener('scroll', this.handleDealerSticky)
    // window.addEventListener('resize', this.handleDealerSticky)
    // this.$nextTick(this.handleDealerSticky)
    window.addEventListener('resize', this.onResize)

    this.allImages.slice(1, 5).forEach((_, idx) => {
      this.thumbLoaded[idx + 1] = false;   // ← plain assignment
    });
  },

  beforeUnmount() {
    // window.removeEventListener('scroll', this.handleDealerSticky)
    // window.removeEventListener('resize', this.handleDealerSticky)
    window.removeEventListener('resize', this.onResize)

  },

  methods: {

rawList(val) {
  // Normalize and show exactly what backend sends.
  // Handles arrays, JSON strings, arrays of objects (uses .name when present).
  const toNames = (arr) =>
    arr.map(x => (x && typeof x === 'object' && 'name' in x) ? x.name : x).join(', ');

  if (val == null) return '';

  if (Array.isArray(val)) {
    return toNames(val);
  }

  const s = String(val).trim();
  if (!s) return '';

  // Try JSON parse (for JSON-encoded arrays/objects)
  try {
    const parsed = JSON.parse(s);
    if (Array.isArray(parsed)) return toNames(parsed);
    // single object → prefer its name if present
    if (parsed && typeof parsed === 'object') {
      return parsed.name ?? s;
    }
  } catch {
    // not JSON → fall through
  }

  return s;
},


    handleGridSelect(realIndex) {
      // realIndex is now zero-based in allImages
      this.openGallery(realIndex + 1)
    },


    openGrid() {
      this.isGridOpen = true
    },
    onResize() {
      this.isMobile = window.innerWidth < 576
    },

    // handleDealerSticky() {
    //   window.requestAnimationFrame(() => {
    //     const NAV_H = 80  // height of your fixed header
    //     const col = this.$refs.dealerColumn
    //     const box = this.$refs.dealerBox
    //     const desc = this.$refs.descBox
    //     if (!col || !box || !desc) return

    //     // disable on small screens
    //     if (window.innerWidth < 992) {
    //       box.style.position = ''
    //       box.style.top = ''
    //       return
    //     }

    //     const scrollY = window.pageYOffset
    //     const colRect = col.getBoundingClientRect()
    //     const colTop = colRect.top + scrollY
    //     const boxH = box.offsetHeight

    //     // bottom of description, minus panel height
    //     const descRect = desc.getBoundingClientRect()
    //     const descBottom = descRect.top + scrollY + desc.offsetHeight
    //     const startY = colTop - NAV_H
    //     const endY = descBottom - boxH - NAV_H

    //     if (scrollY < startY) {
    //       // before we hit the panel
    //       box.style.position = ''
    //       box.style.top = ''
    //     }
    //     else if (scrollY > endY) {
    //       // after description ends
    //       box.style.position = 'absolute'
    //       // pin it to the bottom of the column
    //       box.style.top = (descBottom - colTop - boxH) + 'px'
    //     }
    //     else {
    //       // in-between: fixed
    //       box.style.position = 'fixed'
    //       box.style.top = NAV_H + 'px'
    //     }
    //   })
    // },

    setMainImage(index) {
      this.currentThumbIndex = index;
      this.$nextTick(() => {
        const swiper = this.$refs.mainSwiper.$el.swiper;
        if (swiper) swiper.slideTo(index);
      });
    },
    submitEnquiry() {
      this.form.post(route("submit-inquiry"), {
        preserveScroll: true,
        onSuccess: () => {
          this.form.reset(
            "name",
            "email",
            "mobile",
            "purchase_time",
            "payment_method",
            "address",
            "message"
          );
          Swal.fire({
            icon: "success",
            title: "Inquiry Submitted Successfully",
            showConfirmButton: false,
            timer: 1500,
          });
        },
        onError: () => {
          Swal.fire({
            icon: "error",
            title: "Something went wrong! Please try again later",
            showConfirmButton: false,
            timer: 1500,
          });
        },
      });
    },
    startApplication() {
      window.location.href = route("application", { id: this.vehicle.id });
    },
    viewGallery() {
      console.log("View gallery clicked");
    },
    getManufacturerModel(car) {
      const baseName = `${car.manufacture?.title || ""} ${car.vehicle_model?.title ||
        ""}`.trim();
      return baseName;
    },
    availabilityColor(status) {
      switch (status) {
        case "Available":
          return "availability-available";
        case "Arriving":
          return "availability-arriving";
        case "Sold":
          return "availability-sold";
        default:
          return "";
      }
    },

    // NEW slideshow handlers
    openGallery(startIndex) {
      const slideIdx = startIndex > 0 ? startIndex - 1 : 0;
      this.galleryIndex = slideIdx;
      this.isGalleryOpen = true;
      this.$nextTick(() => {
        if (this.$refs.gallerySwiper) {
          this.$refs.gallerySwiper.swiper.slideTo(slideIdx);
        }
      });
    },
    closeGallery() {
      this.isGalleryOpen = false;
    },
    nextImage() {
      this.galleryIndex = (this.galleryIndex + 1) % this.allImages.length;
    },
    prevImage() {
      this.galleryIndex =
        (this.galleryIndex + this.allImages.length - 1) %
        this.allImages.length;
    },

    // existing toggles
    showFullGallery() {
      this.isFullGallery = true;
    },
    showTwoCol() {
      this.isFullGallery = false;
    },

    onMainLoad() {
      this.mainImageLoaded = true
    },
    onThumbLoad(idx) {
      this.thumbLoaded[idx] = true
    },
  },
};
</script>




<style scoped>
/* ————————————————————————————————————————————————
   Grades “pill” cards
   ———————————————————————————————————————————————— */
.grades-wrapper {
  background: #fff;
  border-radius: 1rem;
  padding: 1.5rem 1rem;
  box-shadow:
    0 6px 20px rgba(0, 0, 0, 0.10),
    0 12px 32px rgba(0, 0, 0, 0.06);
}

.overview-item.grades {
  display: flex;
  align-items: flex-start;
}

.overview-item.grades .overview-icon {
  width: 48px;
  height: 48px;
  margin-right: 1rem;
}

.overview-item.grades .overview-label {
  font-size: 0.9rem;
  color: #555;
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.overview-item.grades .overview-value {
  flex: 1;
}

/* grid of grade cards */
.grades-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
  gap: 1rem;
   /* cursor: pointer !important; */
}

/* each little pill/card */
.grade-card {
  border: 1px solid #ccc;
  border-radius: 0.5rem;
  padding: 0.5rem 1rem;
  text-align: center;
  font-size: 0.95rem;
  font-weight: 600;
  color: #333;
  background: #fff;
  cursor: pointer;
  transition:
    background 0.2s ease,
    color 0.2s ease,
    border-color 0.2s ease,
    box-shadow 0.2s ease;
}

.grade-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* if you ever need an “active” state */
.grade-card.active {
  background: #000;
  border-color: #000;
  color: #fff;
}

/* responsive tweaks */
@media (max-width: 992px) {
  .grades-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 576px) {
  .grades-grid {
    grid-template-columns: 1fr;
  }
}

.dealer-box {
  position: relative;
  /* any padding/background as you like */
}


.accordion-body {
  overflow: hidden;
  max-height: 100%;
}




/* ===== Header & Breadcrumbs ===== */
.product-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding-top: 80px;
  margin-bottom: 1.5rem;
}

.breadcrumbs {
  padding-top: 80px;
  /* match nav height */
  font-size: 1.1rem;
  color: #666666a2;
}

.vehicle-title[data-v-5c5fdec4] {

  font-size: 3rem;
  font-family: 'Poppins', sans-serif !important;
  color: #050B20 !important;
  font-weight: 700 !important;
  padding-top: 4%;


}

.vehicle-title {
  display: inline-flex;
  align-items: center;
  gap: 1rem;
  /* space between text & pill */
  flex-wrap: wrap;
}

.year-pill {
  display: inline-flex;
  align-items: center;
  background: linear-gradient(90deg,
      #0d1b2a 0%,
      #273b51 50%,
      #222b35 100%);
  color: #fff !important;
  border-radius: 999px;
  padding: 0.4rem 0.8rem;
  font-size: 1rem;
  color: #fff;
  gap: 0.5rem;
  font-weight: 500;
}

/* icon sizing + spacing */
.year-pill i {
  margin-right: 0.4rem;
  font-size: 1.1rem;
}

.breadcrumbs a {
  color: #666;
  text-decoration: none;
}

.breadcrumbs a:hover {
  color: #007bff;
}

.breadcrumbs .current {
  font-weight: 500;
  color: #333;
}

/* ===== Title & Subtitle ===== */
.vehicle-title {
  font-size: 2.5rem !important;
  font-weight: 700;
  margin: 0;
  line-height: 1.2;
  margin-bottom: 3rem !important;
}

.trim-variant {
  display: block;
  font-size: 1.3rem;
  color: #000000;
  margin-top: 0.8rem;
}

/* ===== Share / Save Actions ===== */
.header-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.header-right .actions {
  display: flex;
  gap: 1rem;
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
}

.btn-action {
  background: transparent;
  border: none;
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  color: #333;
  padding: 0;
  cursor: pointer;
}

.btn-action i {
  font-size: 1rem;
}

.btn-action:hover {
  color: #007bff;
}

.action-panel {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

/* ===== Share and Save Buttons in Action Panel ===== */
.share-save {
  display: flex;
  justify-content: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.btn-icon {
  background: rgba(255, 255, 255, 0.9);
  border: 1px solid rgba(0, 123, 255, 0.2);
  border-radius: 25px;
  padding: 0.5rem 1rem;
  font-size: 0.85rem;
  color: #495057;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.4rem;
  white-space: nowrap;
  backdrop-filter: blur(10px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.btn-icon:hover {
  background: rgba(255, 255, 255, 1);
  border-color: #007bff;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
}

.btn-icon i {
  font-size: 0.9rem;
  color: #6c757d;
}

.btn-icon span {
  font-size: 0.85rem;
  font-weight: 500;
}

/* ===== Price & Offer Button ===== */
.price-block {
  display: flex;
  align-items: center;
  gap: 1rem;
}

@media (min-width: 1200px) {

  .h1,
  h1 {
    font-size: 3rem;
  }
}

.grades-icon {
  width: 20px;
  height: 20px;
  margin-right: 0.5rem;
}

.main-price {
  font-size: 2.5rem;
  font-weight: 700;
  color: #000;
  margin: 0;
  text-align: center;
}

.offer-text {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  color: #007bff;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 600;
  padding: 0.5rem;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.offer-text:hover {
  background-color: rgba(0, 123, 255, 0.1);
  transform: translateY(-1px);
}

.offer-text i {
  font-size: 1.1rem;
}

.offer-text .bold {
  font-weight: 700;
}

.btn-offer {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: #0f0000;
  border: none;
  border-radius: 25px;
  padding: 0.75rem 2rem;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s, transform 0.1s;
  background: #007bff;
  color: #fff;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.btn-offer:hover {
  background: #0056b3;
  transform: scale(1.05);
}

.btn-offer i {
  font-size: 1.1rem;
}

/* ===== Detail Pills ===== */
.details-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.details-grid .detail-item {
  background: linear-gradient(90deg, #0d1b2a 0%, #273b51 50%, #222b35 100%);
  color: #fff;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.95rem;
}

.details-grid .detail-item .label,
.details-grid .detail-item .value {
  color: #fff;
  font-size: 1.1rem;
}

.details-grid .detail-item .label {
  font-weight: 500;
}

.details-grid .detail-item .value {
  font-weight: 700;
}

/* ===== Image Section ===== */
.main-container {
  /* background: #ffffff; */
  padding: 0 1.5rem;
  /* max-width: 100% !important; */
  margin: 0 auto;
}

/* .main-image-container {
  position: relative;
  width: 100%;

    height: 100%;
} */



.main-image-container {

  width: 100%;
  height: 100%;
  overflow: hidden;
  /* Prevent overflow */
}

/* .main-image {
  width: 100%;
  height: 100%;
  object-fit: cover;


} */

.main-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  /* Ensure image fills container */
  border-top-left-radius: 16px;
  border-bottom-left-radius: 16px;

}

.col-lg-5 .row.g-2.h-100 {
  /* height: 60vh !important;  */
  display: flex !important;
  flex-wrap: wrap !important;
}

/* Main image: round top-right & bottom-right */
.round-main-right {
  border-top-left-radius: 16px !important;
  border-bottom-left-radius: 16px !important;
}

/* 2nd thumbnail → round its top-left corner */
.round-tl {
  border-top-right-radius: 16px !important;
}

/* 4th thumbnail → round its top-right & bottom-right corners */
.round-tr-br {

  border-bottom-right-radius: 16px !important;
}


.main-image,
.thumbnail-grid {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.thumbnail-grid {
  display: grid !important;
  grid-template-columns: repeat(2, 1fr) !important;
  grid-template-rows: repeat(2, 1fr) !important;
  gap: 0.5rem !important;
  /* same as your g-2 gutter */
}



/* .thumbnail-grid {
 width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;


} */

.h-100 {
  height: 100%;
}


/* .col-6 {
  height: 50% !important;
} */

.thumbnail-wrapper {
  /* aspect-ratio: 1 / 1;
  position: relative;
  width: 100%;

  overflow: hidden;  */

  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden;

}

.thumbnail-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  cursor: pointer;
}



.gallery-button {
  position: absolute;
  bottom: 8px;
  right: 8px;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 20px;
  padding: 6px 12px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.9rem;
  color: #050B20;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
  transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
}

.gallery-button i {
  font-size: 1.1rem;
}

.gallery-button:hover {
  /* lift and grow just a bit */
  transform: translateY(-3px) scale(1.05);
  /* deepen the shadow */
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
  /* solidify the background */
  background-color: rgba(255, 255, 255, 1);
}

.thumbnail-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);

  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 1.5rem;
  cursor: pointer;
}


.gallery-modal {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.gallery-swiper {
  width: 90vw;
  height: 90vh;
  overflow: hidden;
}

.gallery-slide-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

::v-deep .gallery-modal .gallery-swiper .swiper-button-prev,
::v-deep .gallery-modal .gallery-swiper .swiper-button-next {
  position: absolute;
  /* ensure absolute inside swiper */
  top: 50% !important;
  /* vertically centered */
  transform: translateY(-50%) !important;
  width: 3rem !important;
  /* base button size */
  height: 3rem !important;
  background: rgba(255, 255, 255, 0.8) !important;
  border-radius: 50% !important;
  display: flex !important;
  align-items: center;
  justify-content: center;
  z-index: 10 !important;


}

::v-deep .gallery-modal .swiper-slide {
  width: 100% !important;
}

/* 3) Position each side 10px from its container edge */
::v-deep .gallery-modal .gallery-swiper .swiper-button-prev {
  left: 10px !important;
  right: auto !important;
}

::v-deep .gallery-modal .gallery-swiper .swiper-button-next {
  right: 10px !important;
  left: auto !important;
}

/* 4) Enlarge and color the arrow glyph */
::v-deep .gallery-modal .gallery-swiper .swiper-button-prev::after,
::v-deep .gallery-modal .gallery-swiper .swiper-button-next::after {
  font-size: 1.5rem !important;
  color: #050B20 !important;
}


.close-btn {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: rgba(255, 255, 255, 0.9);
  border: none;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 50%;
  cursor: pointer;
  z-index: 3000;

  /* add these: */
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1;
}


.close-btn i {
  /* optional: tweak icon size */
  font-size: 3.8rem !important;
  line-height: 1;
}


/* ===== Summary Section ===== */

.summary {
  margin-bottom: 4rem;
}

.summary-title {
  font-size: 2rem;
  font-weight: 500;
  margin-bottom: 2rem;
}


.summary-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  column-gap: 3rem;
  row-gap: 1.5rem;
}


.summary-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
}


.item-left {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

/* icon size */
.summary-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

/* label bold */
.label {
  font-weight: 700;
  color: #222;
}

/* value normal */
.value {
  font-weight: 400;
  color: #000;
}





/* ===== Similar Cars Section ===== */
.section {
  padding: 100px 0;
}

.description h1 {
  font-size: 3.5rem;
  font-weight: 500 !important;
  margin-bottom: 2rem !important;
}

.accordion-collapse {
  margin-top: 1rem;
}

.accordion-body {
  padding: 0;
}

.accordion-body ul {
  margin: 0;
  padding: 0;
}

.accordion-body li {
  font-size: 1.3rem;
  line-height: 1.6;
  color: #333;
}

/* ===== Updated Dealer Card with Increased Width ===== */
.dealer-card-wrapper {
  background: linear-gradient(135deg, #f8f9ff 0%, #e8ecff 50%, #f0f4ff 100%);
  border-radius: 20px;
  padding: 2px;
  width: 100%;
  /* Changed from max-width to width */
  margin: 0;
  height: 100%;
  display: flex;
  flex-direction: column;
  background: linear-gradient(90deg,
      #415A77 0%,
      #0A3F79 0%,
      #163353 34%,
      #142334 67%,
      #0E1E30 80%,
      #0D1B2A 100%);
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.ficon {
  width: 16px;
  height: 16px;
  filter: invert(1);
}



@media (max-width: 992px) {


  ::v-deep .gallery-modal .swiper-button-prev,
  ::v-deep .gallery-modal .swiper-button-next {
    width: 3rem !important;
    height: 3rem !important;
  }

  ::v-deep .gallery-modal .swiper-button-prev::after,
  ::v-deep .gallery-modal .swiper-button-next::after {
    font-size: 1.5rem !important;
  }

  .row.mb-4.gx-2>.col-lg-7,
  .row.mb-4.gx-2>.col-lg-5 {
    height: 100%;
    /* match whatever you had on .main-image-container */
  }





}

@media (max-width: 768px) {
  .product-header {
    flex-direction: column;
    gap: 1rem;
  }


  .dealer-actions .primary-btn,
  .dealer-actions .secondary-btn {
    padding: 14px 16px;
    font-size: 0.95rem;
  }

  /* shrink arrow icon a bit */
  .dealer-actions .arrow-icon,
  .dealer-actions .arrow-icon_1 {
    width: 12px;
    height: 12px;
    margin-left: 0.75rem;
  }

  .header-right {
    align-items: flex-start;
  }

  .main-image {
    height: 40vh;
    min-height: 300px;
    border-top-right-radius: 16px;
    border-bottom-right-radius: 16px;
  }

  .col-lg-5 .row>.col-6:nth-child(2) .thumbnail-grid {
    border-top-left-radius: 16px;
  }

  .col-lg-5 .row>.col-6:nth-child(4) .thumbnail-grid {
    border-top-right-radius: 16px;
    border-bottom-right-radius: 16px;

    .col-lg-5 .row>.col-6:nth-child(2) .thumbnail-grid {
      border-top-left-radius: 16px !important;
    }

    .col-lg-5 .row>.col-6:nth-child(4) .thumbnail-grid {
      border-top-right-radius: 16px !important;
      border-bottom-right-radius: 16px !important;
    }

    .gallery-button {
      padding: 4px 8px;
      font-size: 0.8rem;
    }
  }


  .thumbnail {
    width: 100px;
    height: 60px;
  }


  .stock-price-section .price-section {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }

  .stock-price-section .price-section .btn-offer {
    width: 100%;
    text-align: center;
  }
}


@media (max-width: 576px) {
  .main-image {
    height: 30vh;
    min-height: 250px;
  }

  .gallery-button {
    padding: 2px 6px;
    font-size: 0.7rem;
  }

  ::v-deep .thumbnail-grid {
    border-radius: 0 !important;
  }

  ::v-deep .gallery-modal .gallery-swiper .swiper-button-prev,
  ::v-deep .gallery-modal .gallery-swiper .swiper-button-next {
    width: 2.2rem !important;
    height: 2.2rem !important;
  }

  ::v-deep .gallery-modal .gallery-swiper .swiper-button-prev::after,
  ::v-deep .gallery-modal .gallery-swiper .swiper-button-next::after {
    font-size: 1.2rem !important;
  }

  .dealer-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }

  .dealer-actions .primary-btn,
  .dealer-actions .secondary-btn {
    padding: 12px 14px;
    font-size: 0.9rem;
  }

  /* make the “View All stock…” link full width and center */
  .dealer-actions .view-stock-link {
    width: 100%;
    text-align: center;
  }

  .bold {
    font-weight: 700;
    font-size: 5rem;
  }

  .thumbnail {
    width: 80px;
    height: 50px;
  }

  .car-details h3 {
    font-size: 1.5rem;
  }

  .summary-item .label,
  .summary-item .value {
    font-size: 14px;
  }

  .main-price {
    font-size: 2rem;
  }

  .stock-price-section .share-save .btn-icon {
    font-size: 0.9rem;
  }

  .stock-price-section .share-save {
    gap: 1rem;
  }

  .gallery-modal .gallery-slide-img {
    border: none !important;
  }
}

.card {
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.card img {
  border: 2px solid #ddd;
}

.card h5 {
  font-size: 1.25rem;
  font-weight: 600;
}

.card p {
  font-size: 0.9rem;
  color: #6c757d;
}

.btn-outline-secondary {
  border: 1px solid #ced4da;
  color: #6c757d;
}

.btn-outline-secondary:hover {
  background-color: #f8f9fa;
}

.btn-primary {
  background: linear-gradient(90deg, #007bff, #0056b3);
  border: none;
  color: #fff;
  font-weight: 600;
}

.btn-outline-success {
  border: 1px solid #28a745;
  color: #28a745;
}

.btn-outline-success:hover {
  background-color: #e8f5e9;
}

.btn-link {
  color: #007bff;
  font-size: 0.9rem;
}

.btn-link:hover {
  text-decoration: underline;
}



.dealer-panel-wrapper {
  background-color: #ffffff1c !important;
  border-radius: 10px;
  /* padding: 1.5rem; */
  position: sticky;
  padding-top: 1.5rem;
  top: 120px;
  padding-bottom: 2rem;
  align-self: flex-start;
}


.dealer-panel-wrapper .dealer-card {
  background-color: #fff;
  border-radius: 12px;
  position: sticky !important;
  /* top: 100px !important; */
  margin: 0;
  /* padding-bottom: 8rem !important; */

}



.dealer-panel-wrapper .dealer-profile {
  display: block !important;
  text-align: left !important;
}

/* make the direction link and phone text smaller */
.dealer-contact-links .dealer-link span,
.dealer-contact-links .dealer-text span {
  font-size: 0.85rem;
  /* was 1rem */
}

/* shrink the little icons next to them */
.dealer-contact-links .flat-icon {
  width: 44px;
  /* was 44px */
  height: 40px;
  /* was 40px */
}

.dealer-text {
  color: #000 !important;
  text-decoration: none !important;
}

.dealer-text:hover {
  color: #000 !important;
}



.dealer-panel-wrapper .dealer-profile .profile-picture {
  margin: 0 !important;
}


.dealer-panel-wrapper .dealer-profile .dealer-name,
.dealer-panel-wrapper .dealer-profile .dealer-location {
  margin-left: 0 !important;
}


.dealer-card {
  /* position: sticky; */
  /* top: 100px; */
  background-color: #14233412;
  border-radius: 12px;
  z-index: 10;

}

.dealer-profile {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  text-align: left;
}

.profile-picture {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  object-fit: cover;
  display: block;
  border: 3px solid #fff;
  box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
  margin-bottom: 1rem;
}

.dealer-name {
  font-size: 1.25rem;
  font-weight: 600;

  margin-top: 0.75rem;
}

.dealer-location {
  font-size: 0.95rem;
  color: #6c757d;
  margin: 0 0 1.5rem 0;
}

/* Compact action buttons */
.compact-actions {
  display: flex;
  justify-content: flex-start;
  gap: 0.5rem;
}

.compact-btn {
  background: rgba(248, 249, 255, 0.9);
  border: 1px solid rgba(0, 123, 255, 0.2);
  border-radius: 25px;
  padding: 0.5rem 1rem;
  font-size: 0.85rem;
  color: #495057;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.4rem;
  white-space: nowrap;
  backdrop-filter: blur(10px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.compact-btn i {
  font-size: 0.9rem;
  color: #6c757d;
  margin-top: 0.25rem;
}

.compact-btn .btn-text {
  font-size: 0.85rem;
  font-weight: 500;
}

.compact-btn:hover {
  background: rgba(255, 255, 255, 1);
  border-color: #007bff;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
}

.dealer-actions {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.primary-btn,
.secondary-btn {
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.primary-btn {
  background: linear-gradient(90deg,
      #0D1B2A 0%,
      #232E3B 20%,
      #1E1E1E 40%,
      #273B51 60%,
      #222B35 80%,
      #26384D 100%);
  color: #fff;
  border: none;
  border-radius: 20px;
  padding: 18px 20px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition:
    transform 0.25s ease,
    box-shadow 0.25s ease,
    background-position 0.5s ease;
  background-size: 200% 100%;
  background-position: left center;
}

.primary-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  background-position: right center;
}

.secondary-btn {
  border: 1px solid #28a745;
  display: flex !important;
  justify-content: center;
  align-items: center;
  color: #28a745;
  gap: 0.5rem;
  text-decoration: none;
  border-radius: 20px;
  padding: 18px 20px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition:
    transform 0.25s ease,
    box-shadow 0.25s ease,
    background-position 0.5s ease;
}

.secondary-btn:hover {
  background-color: #e8f5e9;
  transform: translateY(-3px);

}

.view-stock-link {
  font-size: 0.85rem;
  color: #007bff;
  text-decoration: none;
  font-weight: 500;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.25rem;
  padding: 0.5rem;
}

.view-stock-link:hover {
  text-decoration: underline;
  color: #0056b3;
}


@media (max-width: 576px) {

  .dealer-card-wrapper {
    padding: 2px;
  }

  .dealer-card-inner {
    padding: 1rem;
  }

  .profile-picture {
    width: 70px;
    height: 70px;
  }

  .dealer-name {
    font-size: 1.2rem;
  }

  .dealer-location {
    font-size: 0.85rem;
  }

  .compact-btn {
    padding: 0.35rem 0.6rem;
    font-size: 0.75rem;
  }

  .compact-btn .btn-text {
    font-size: 0.75rem;
  }
}



/* .dealer-contact-links {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
} */


.dealer-contact-links .dealer-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #273b51;
  font-size: 1rem;
  text-decoration: none;

}

.dealer-contact-links .dealer-link:hover {
  color: rgb(3, 3, 40)
}


.dealer-contact-links .dealer-link,
.dealer-contact-links .dealer-text {
  white-space: normal !important;
}

/* .dealer-contact-links .flat-icon {
  width: 44px;
  height: 40px;
} */

.arrow-icon {
  width: 15px;
  height: 15px;
  margin-left: 1rem;
  vertical-align: middle;
  display: inline-block;
  filter: invert(1);
}

.arrow-icon_1 {
  width: 15px;
  height: 15px;
  margin-left: 1rem;
  vertical-align: middle;
  display: inline-block;
}

.action-btn {
  font-size: 1rem;
  padding: 12px 20px;
  border: 1px solid #ccc;
  border-radius: 50px;
  background-color: #fff;
  color: #333;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.action-btn i {
  font-size: 1.25rem;
  color: #666;
}

.action-btn:hover {
  border-color: #999;
  background-color: #f9f9f9;
}

.primary-btn {
  background: linear-gradient(90deg,
      #0D1B2A 0%,
      #232E3B 20%,
      #1E1E1E 40%,
      #273B51 60%,
      #222B35 80%,
      #26384D 100%);
  color: #fff;
  border: none;
  border-radius: 20px;
  padding: 18px 20px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition:
    transform 0.25s ease,
    box-shadow 0.25s ease,
    background-position 0.5s ease;
  background-size: 200% 100%;
  background-position: left center;
}

.primary-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  background-position: right center;
}

.secondary-btn {
  border: 1px solid #28a745;
  display: flex !important;
  justify-content: center;
  align-items: center;
  color: #28a745;
  gap: 0.5rem;
  text-decoration: none;
  border-radius: 20px;
  padding: 18px 20px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition:
    transform 0.25s ease,
    box-shadow 0.25s ease,
    background-position 0.5s ease;
}

.secondary-btn:hover {
  background-color: #e8f5e9;
  transform: translateY(-3px);

}


.dealer-actions .view-stock-link {
  align-self: center;
  text-align: center;
  display: block;
}

.view-stock-link {
  font-size: 1rem;
  color: #050B20;
  text-decoration: none;


}


.main-price {
  font-size: 2rem;
}

.vehicle-title {
  font-size: 2.5rem;
}

.trim-variant {
  font-size: 1.1rem;
}

.btn-icon {
  padding: 0.4rem 0.8rem;
  font-size: 0.8rem;
}

.btn-icon span {
  font-size: 0.8rem;
}




@media (max-width: 980px) {

  ::v-deep .thumbnail-grid {
    border-radius: 0 !important;
  }
}

@media (max-width: 991px) {

  .round-main-right,
  .round-tl,
  .round-tr-br,
  .thumbnail-img {
    border-radius: 0 !important;
  }

  .main-image,
  .thumbnail-img {
    border-radius: 0 !important;
  }
}

/* Desktop corner rounding */
@media (min-width: 992px) {

  /* round the left side of the main image */
  .main-image {
    border-radius: 1rem 0 0 1rem;
  }

  /* top-right thumbnail (2nd cell) */
  .thumbnail-wrapper:nth-child(2) .thumbnail-img {
    border-top-right-radius: 1rem;
  }

  /* bottom-right thumbnail (4th cell) */
  .thumbnail-wrapper:nth-child(4) .thumbnail-img {
    border-bottom-right-radius: 1rem;
  }
}


@media (max-width: 266px) {
  .gallery-button {
    padding: 1px 3px;
    font-size: 0.5rem;
  }
}

/* reuse your existing shimmer keyframes... */
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
  border-radius: 12px;
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

/* fade the real images in on top */
.main-image,
.thumbnail-img {
  opacity: 0;
  transition: opacity 0.4s ease;
}

.main-image.is-loaded,
.thumbnail-img.is-loaded {
  opacity: 1;
  z-index: 2;
}

/* icons */
.overview-icon {
  width: 48px;
  height: 48px;
}

/* make the Grades icon extra-large */
.overview-item.grades .overview-icon {
  width: 80px;
  height: 80px;
  /* margin-bottom: 1rem; */

}

/* the little label underneath */
.overview-label {
  font-size: 0.95rem;
  color: #555;
  margin-bottom: 0.25rem;
  font-weight: 500;
}

/* the bold value under the label */
.overview-value {
  font-size: 1.25rem;
  font-weight: 700;
  color: #222;
}

/* section title centered */
.summary .summary-title {
  text-align: center;
}

/* optional: a little padding around the grid */
.summary {
  padding: 2rem 1rem;
}

.overview-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  grid-template-areas: "engine transmission drive fuel";
  gap: 1rem;
}

.engine {
  grid-area: engine;
}

.transmission {
  grid-area: transmission;
}

.drive {
  grid-area: drive;
}

.fuel {
  grid-area: fuel;
}

.upwards-label {
  font-weight: 700;
  font-size: 1rem;
  color: inherit;
  text-align: right;
  padding-top: 2%;

}

/* two-column on tablets */
@media (max-width: 992px) {
  .overview-grid {
    grid-template-columns: repeat(2, 1fr);
    grid-template-areas:
      "engine transmission"
      "drive  fuel"
      "grades grades";
  }
}

/* single-column on phones */
@media (max-width: 576px) {
  .overview-grid {
    grid-template-columns: 1fr;
    grid-template-areas:
      "engine"
      "transmission"
      "grades"
      "drive"
      "fuel";
  }
}

.overview-item {
  background: #fff;
  border-radius: 0.8rem !important;
  ;
  padding: 1rem;
  text-align: center;
  border: 1.5px solid #cfcece;
  transition: transform .2s ease, box-shadow .2s ease;
}

.overview-item:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.engine {
  grid-area: engine;
}

.transmission {
  grid-area: transmission;
}

.grades {
  grid-area: grades;
}

.drive {
  grid-area: drive;
}

.fuel {
  grid-area: fuel;
}

.overview-icon {
  width: 36px;
  height: 36px;
  margin-bottom: .5rem;
}

.overview-label {
  font-size: .9rem;
  color: #555;
  margin-bottom: .25rem;
  font-weight: 500;
}

.overview-value {
  font-size: 1.2rem;
  font-weight: 600;
  color: #222;
}

.overview-item.engine .overview-icon,
.overview-item.transmission .overview-icon,
.overview-item.drive .overview-icon,
.overview-item.fuel .overview-icon {
  width: 60px;
  height: 60px;
}

.grade-list {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  column-gap: 1rem;
  row-gap: 0.75rem;
  margin: 1rem 0 0;
  padding: 0;
  list-style: none;
  text-align: left;
}

.grade-list li {
  display: flex;
  align-items: left;
  margin-bottom: .5rem;
  margin-top: 1rem;
  font-size: 3rem;
  font-weight: 800;
  color: #222;
}

.description-content {
  font-size: 1rem;
  line-height: 1.6;
}

@media (min-width: 768px) {
  .description-content {
    font-size: 1.125rem;
    line-height: 1.75;
  }
}

.tick-icon {
  color: #28a745;
  margin-right: .5rem;
  font-size: 1rem;
}

.grade-text {
  font-size: .85rem;
}

@media (max-width: 576px) {
  .overview-label {
    font-size: .8rem;
  }

  .overview-value {
    font-size: 1rem;
  }
}

@media (max-width: 576px) {

  /* keep title + action-panel in one row */
  .d-flex.justify-content-between {
    flex-direction: row !important;
    align-items: center;
    flex-wrap: wrap;
  }

  /* let the title take most of the space */
  .vehicle-title {
    flex: 1 1 auto;
    font-size: 1.8rem !important;
    margin-bottom: 0.25rem !important;
    line-height: 1.1;
  }

  /* pin the price panel to the right */
  .action-panel {
    flex: 0 0 auto;
    margin-left: auto !important;
  }

  /* tighten up the pill and price */
  .details-grid .detail-item {
    padding: 0.25rem 0.5rem !important;
    font-size: 0.85rem !important;
  }

  .upwards-label {
    font-size: 0.8rem !important;
  }

  .main-price {
    font-size: 1.75rem !important;
  }
}

.px-4 {
  padding-right: 1.5rem !important;
  padding-left: 0rem !important;
}



.sticky-header {
  position: sticky;
  top: 130px;
  z-index: 10;
  background-color: rgba(255, 255, 255, 0.6);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);

  /* your existing spacing */
  margin: 0 -3rem;
  padding: 0rem 3rem;

  font-size: large;
}

.sticky-header .vehicle-title {

  margin-bottom: 0.5rem !important;
}

@media (max-width: 767.98px) {
  .sticky-header {
    position: static !important;
    top: auto !important;
    margin: 1rem 0;
    /* whatever spacing you want instead */
    padding: 0;
    /* reset or tweak as needed */
  }
}

@media (max-width: 575.98px) {
  .separator {
    display: none;
  }
}


.price-inline {
  display: inline-flex !important;
  align-items: baseline !important;
  gap: 0.5rem !important;
}
.upwards-label {
  margin: 0 !important;
  font-weight: 700 !important;
  /* adjust size/color to match your design */
}




@media (min-width: 992px) and (max-width: 1200px) {

  .dealer-panel-wrapper .dealer-name {
    font-size: 1rem;
  }

  .dealer-contact-links .dealer-link span {
    font-size: 0.6rem;
  }
  .dealer-contact-links .dealer-link img.flat-icon {
    width: 25px;
    height: 25px;
  }


}

@media (min-width: 992px) and (max-width: 1198px) {

  .dealer-actions .secondary-btn {
    font-size: 0.6rem;       /* smaller text */
    padding: 0.4rem 0.6rem;  /* tighter padding */
  }

  /* shrink “View all dealer’s stock” link */
  .dealer-actions .view-stock-link {
    font-size: 0.6rem;
    padding: 0.4rem 0.6rem;
  }
  /* floating Inquiry button */
   .contact-btn {
    font-size: 0.7rem !;        /* match your chat/view-all size */
    padding: 0.4rem 0.6rem;   /* tighter padding for consistency */
  }

   .dealer-actions .secondary-btn .arrow-icon_1 {
    display: none !important;
  }
  /* hide both arrow elements in “View all dealer’s stock” */
  .dealer-actions .view-stock-link i,
  .dealer-actions .view-stock-link .arrow-icon_1 {
    display: none !important;
  }

}


@media (min-width: 1199px) and (max-width: 1400px) {
  .dealer-actions .secondary-btn {
    font-size: 0.8rem;       /* smaller text */
    padding: 0.4rem 0.6rem;  /* tighter padding */
  }

  /* shrink “View all dealer’s stock” link */
  .dealer-actions .view-stock-link {
    font-size: 0.8rem;
    padding: 0.4rem 0.6rem;
  }
  /* floating Inquiry button */
   .contact-btn {
    font-size: 0.8rem !;        /* match your chat/view-all size */
    padding: 0.4rem 0.6rem;   /* tighter padding for consistency */
  }

   .dealer-actions .secondary-btn .arrow-icon_1 {
    display: none !important;
  }
  /* hide both arrow elements in “View all dealer’s stock” */
  .dealer-actions .view-stock-link i,
  .dealer-actions .view-stock-link .arrow-icon_1 {
    display: none !important;
  }

}


/* even smaller on phones */
@media (max-width: 576px) {
  .dealer-panel-wrapper .dealer-name {
    font-size: 0.9rem;
  }
  .dealer-contact-links .dealer-link span {
    font-size: 0.75rem;
  }
  .dealer-contact-links .dealer-link img.flat-icon {
    width: 28px;
    height: 28px;
  }
  .dealer-actions .secondary-btn,
  .dealer-actions .view-stock-link {
    font-size: 0.75rem;
    padding: 0.4rem 0.6rem;
  }
}

</style>
