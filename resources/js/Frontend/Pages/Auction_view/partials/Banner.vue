<template>
  <section class="container-fluid px-3 px-md-5 main-container section">

    <!--BreadCrumbs-->
     <div class="breadcrumbs mb-4">
    <a href="/home" class="breadcrumb-link">Home</a> /
    <a href="/live-auction" class="breadcrumb-link">Live Auction In Japan</a> /
    <span class="current">
      <span class="fw-bold" style="font-family:'Poppins',sans-serif;">
        {{ details.MARKA_NAME
            .toLowerCase()
            .replace(/^./, c => c.toUpperCase()) }}
        {{ details.MODEL_NAME }}
      </span>
    </span>
  </div>

    <!-- HEADER ROW -->
    <div class="mb-4">
      <!-- Row 1: MARKA MODEL ↔ YEAR -->
      <div class="row mb-1 align-items-baseline">
        <div class="col text-capitalize">
          <h1 class="vehicle-title mb-0"
            style="font-size:3rem; font-weight:700; line-height:1.2; margin-bottom:0!important;">
            <!-- <h1 class="display-xl-3 display-md-4 display-sm-5 display-6 fw-bold mb-2"> -->
            {{details.MARKA_NAME.toLowerCase().replace(/^./, c => c.toUpperCase())}}
            {{ details.MODEL_NAME }}
          </h1>
        </div>
        <div class="col-auto">
          <span class="vehicle-year">
            {{ details.YEAR }}
          </span>
        </div>
      </div>

      <!-- Row 2: ENG_V cc ↔ MILEAGE KM -->
      <div class="row justify-content-between align-items-baseline mb-4">
        <div class="col-auto">
          <span class="fs-3 text-secondary fw-medium">

            {{ details.ENG_V }} cc
          </span>
        </div>
        <div class="col-auto">
          <span class="fs-3 text-secondary fw-medium">
            {{ details.MILEAGE }} KM
          </span>
        </div>
      </div>
    </div>

    <!-- pills  GRADE & COLOR -->

    <div class="details-grid">
      <div class="detail-item"><i class="fa-solid fa-cogs"></i><span class="label"></span>Body No -<span
          class="value">{{
            details.KUZOV }}
        </span></div>
      <div class="detail-item"><i class="fa-solid fa-palette"></i><span class="label"></span><span class="value">{{
        details.COLOR }}</span></div>
    </div>

    <!-- pills  GRADE & COLOR -->
    <!-- <div class="details-grid">
          <div class="detail-item fs-6 d-none d-sm-flex">
         <i class="fa-solid fa-cogs"></i>
        <span class="value">{{ details.GRADE }}</span>
    </div>

    <div div class="detail-item fs-6 d-none d-sm-flex">
        <i class="fa-solid fa-gas-pump"></i>
        <span class="value">{{ details.COLOR }}</span>
        </div>
    </div> -->




    <!-- Image & Thumbnails Layout -->
    <div class="row mb-4 align-items-stretch image-row gx-4">
      <!-- Large left image -->
      <div class="col-lg-7 px-2 pe-lg-1 d-flex flex-column">
        <div class="main-image-container  position-relative flex-fill">
          <div v-if="!mainImageLoaded" class="skeleton-placeholder"></div>
          <img :src="allImages[currentThumbIndex].original_url" :alt="allImages[currentThumbIndex].alt"
            class="main-image" @load="onMainLoad" :class="{ 'is-loaded': mainImageLoaded }"
            @click="openGallery(currentThumbIndex)" />
          <div v-if="hasVideo" class="video-overlay" @click="playVideo">
            <i class="bi bi-play-circle"></i>
            <span>Video</span>
          </div>
        </div>
      </div>

      <!-- 2×2 Thumbnail grid -->
      <div class="col-lg-5 px-2 ps-lg-2 d-flex flex-column">
        <div class="thumbnail-grid h-100 flex-fill">
          <div v-for="(img, idx) in allImages.slice(1, 5)" :key="idx" class="thumbnail-wrapper"
            @click="openGallery(idx + 1)">
            <div v-if="!thumbLoaded[idx + 1]" class="skeleton-placeholder"></div>
            <img :src="img.original_url" :alt="img.alt" class="thumbnail-img" @load="onThumbLoad(idx + 1)"
              :class="{ 'is-loaded': thumbLoaded[idx + 1] }" />
            <div v-if="idx === 3" class="gallery-button" @click="openGallery()">
              <i class="fa-solid fa-images"></i>
              <span>More Images</span>
            </div>
          </div>
        </div>
      </div>
    </div>


    <!-- Fullscreen swiper -->
    <transition name="fade">
      <div v-if="isGalleryOpen" class="gallery-modal">
        <button class="close-btn" @click="closeGallery">&times;</button>
        <Swiper ref="gallerySwiper" :modules="modules" class="gallery-swiper" :initial-slide="galleryIndex" navigation
          loop :slides-per-view="1">
          <SwiperSlide v-for="(img, idx) in allImages" :key="idx">
            <img :src="img.original_url" class="gallery-slide-img" />
          </SwiperSlide>
        </Swiper>
      </div>
    </transition>

    <!-- Overview & Dealer -->
    <div class="row row-cols-1 gx-5 gy-4" ref="detailsGrid">
      <div class="col-lg-8">
        <div class="summary mb-3 px-3 py-3">
          <h2 class="summary-title mb-4 fs-2 fs-md-1">Car Overview</h2>
          <div class="row row-cols-1 gx-5 gy-2">
            <!-- Lot No -->
            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/car.png" class="summary-icon me-2" />
                <span class="label me-auto">Lot No</span>
                <span class="value">{{ details.LOT }}</span>
              </div>
            </div>
            <!-- Chassis -->
            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/sim.png" class="summary-icon me-2" />
                <span class="label me-auto">Chassis ID</span>
                <span class="value ms-auto text-end">{{ details.KUZOV }}{{ details.GRADE }}</span>
              </div>
            </div>
            <!-- Mileage -->
            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/tachometer.png" class="summary-icon me-2" />
                <span class="label me-auto">Mileage</span>
                <span class="value">{{ details.MILEAGE }} KM</span>
              </div>
            </div>
            <!-- Engine -->
            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/engine.png" class="summary-icon me-2" />
                <span class="label me-auto">Engine Size</span>
                <span class="value">{{ details.ENG_V }} CC</span>
              </div>
            </div>
            <!-- Auction Date -->

            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/schedule.png" class="summary-icon me-2" />
                <span class="label me-auto">Auction Date</span>
                <span class="value ms-auto text-end">{{ details.AUCTION_DATE }} {{ details.AUCTION }}</span>
              </div>
            </div>
            <!-- Year -->
            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/calendar.png" class="summary-icon me-2" />
                <span class="label me-auto">Year</span>
                <span class="value">{{ details.YEAR }}</span>
              </div>
            </div>

            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/art.png" class="summary-icon me-2" />
                <span class="label me-auto">Color</span>
                <span class="value">{{ details.COLOR }}</span>
              </div>
            </div>

            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/tools.png" class="summary-icon me-2" />
                <span class="label me-auto">Equip Box</span>
                <span class="value">{{ $page.props.details.KPP }} {{ $page.props.details.EQUIP }}</span>
              </div>
            </div>

            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/shuttle.png" class="summary-icon me-2" />
                <span class="label me-auto">Start</span>
                <span class="value">{{ details.START }}</span>
              </div>
            </div>

            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/car.png" class="summary-icon me-2" />
                <span class="label me-auto">Vehicle ID</span>
                <span class="value">{{ $page.props.details.ID }}</span>
              </div>
            </div>

            <div class="col">
              <div class="d-flex align-items-center">
                <img src="/images/icons/sold.png" class="summary-icon me-2" />
                <span class="label me-auto">Sold For</span>
                <span class="value">{{ $page.props.details.FINISH }} ¥</span>
              </div>
            </div>
          </div>
        </div>
      </div>




      <!-- Dealer Card (exact) -->
      <div class="col-lg-4 d-flex flex-column pt-4" ref="dealerColumn">
        <div class="dealer-panel-wrapper flex-fill px-1 pb-4 mt-4" ref="dealerBox">

          <div class="dealer-card shadow-sm bg-white rounded-4 p-5 mt-4">
            <div class="dealer-profile mb-4">
              <img
                src="https://media.istockphoto.com/id/1399565382/photo/young-happy-mixed-race-businessman-standing-with-his-arms-crossed-working-alone-in-an-office.jpg?s=612x612&w=0&k=20&c=buXwOYjA_tjt2O3-kcSKqkTp2lxKWJJ_Ttx2PhYe3VM="
                class="profile-picture" />
              <h5 class="dealer-name mt-3">World Auto Dealers
              </h5>
              <!-- <p class="dealer-location text-muted">Galle, Sri Lanka</p> -->
            </div>




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
                <a href="tel:+94114001499" class="dealer-link d-flex align-items-center justify-content-md-end w-100">
                  <img src="/images/call.png" class="flat-icon me-2" alt="Call Dealer" />
                  <span>+94 11 400 1 499</span>
                </a>
              </div>
            </div>





            <div class="dealer-actions d-flex flex-column gap-3">
              <!-- <button class="primary-btn w-100">
                                  Message Dealer <i class="bi bi-chevron-right"></i>
                                  <img src="/images/Vector.png" class="arrow-icon"/>
                                </button> -->
              <a :href="whatsappLink()" class="secondary-btn w-100" target="_blank">
                Chat Via WhatsApp
                <img src="/images/Vector.png" class="arrow-icon_1" />
              </a>
              <a href="/available-cars" class="view-stock-link">
                View all dealer’s stock
                <i class="bi bi-chevron-right"></i>
                <img src="/images/Vector.png" class="arrow-icon_1" />
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>


    <!-- /.row -->
  </section>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import { ref } from "vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Mousewheel, FreeMode, Thumbs, Navigation, Keyboard } from "swiper/modules";
import "swiper/css";
import "swiper/css/navigation";

export default {
  components: { Link, Swiper, SwiperSlide },
  props: {
    details: { type: Object, required: true },
    vImages: { type: Array, required: true }
  },
  data() {
    return {
      currentThumbIndex: 0,
      isGalleryOpen: false,
      galleryIndex: 0,
      mainImageLoaded: false,
      thumbLoaded: {
        1: false,
        2: false,
        3: false,
        4: false,
      },
    };
  },
  computed: {
    allImages() {
      return this.vImages.map(u => ({ original_url: u }));
    }
  },





  methods: {
    whatsappLink() {
      const phone = '94114001499';
      const pageUrl = window.location.href;
      const lines = [
        'Hello!',
        '',
        `I’m interested in this auction car: *${this.details.MARKA_NAME} ${this.details.MODEL_NAME}*`,
        `*Year:* ${this.details.YEAR}`,
        `*Lot No.:* ${this.details.LOT}`,
        '',
        `*View details:* ${window.location.href}`,
        '',
        'Looking forward to your response!'
      ]
      const text = lines.join('\n')
      return `https://wa.me/${phone}?text=${encodeURIComponent(text)}`
    },

    setMainImage(i) { this.currentThumbIndex = i; },
    openGallery(i) { this.galleryIndex = i; this.isGalleryOpen = true; },
    closeGallery() { this.isGalleryOpen = false; },
    playVideo() { /* no-op */ },

    onMainLoad() {
      this.mainImageLoaded = true;
    },
    onThumbLoad(idx) {
      this.thumbLoaded[idx] = true;
    }
  },
  setup() {
    return {
      modules: [Mousewheel, FreeMode, Thumbs, Navigation, Keyboard]
    }
  },

  capitalizeFirst(str = '') {
    const lower = str.toLowerCase();
    return lower.charAt(0).toUpperCase() + lower.slice(1);
  },
};
</script>

<style scoped>
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
  /* leave room for navbar */
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
  font-size: 3rem;
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

/* container holds everything in a vertical stack, left-aligned */
.action-panel {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
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

.main-price {
  font-size: 2.5rem;
  font-weight: 700;
  color: #000;
  margin: 0;
}

.stock-price-section .price-section .btn-offer {
  background: #007bff;
  color: #fff;
  border: none;
  border-radius: 25px;
  padding: 0.75rem 2rem;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s, transform 0.1s;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.stock-price-section .price-section .btn-offer:hover {
  background: #0056b3;
  transform: scale(1.05);
}

.btn-offer i {
  font-size: 1.1rem;
}

.btn-offer:hover {
  background: #0056b3;
  transform: scale(1.05);
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
}

/* ===== Detail Pills (Gradient Fill) ===== */
.details-pills {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
}

.details-pills .pill-item {
  background: linear-gradient(90deg, #0d1b2a 0%, #273b51 50%, #222b35 100%);
  color: #fff;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.9rem;
}

.details-pills .pill-item i {
  color: #fff;
  font-size: 1.1rem;
}

/* ===== Details Grid ===== */
.details-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 1.75rem;
  margin-bottom: 2rem;
}

/* pill styling with your gradient */
.details-grid .detail-item {
  background: linear-gradient(90deg,
      #0d1b2a 0%,
      #273b51 50%,
      #222b35 100%);
  color: #fff;
  /* white text */
  padding: 0.5rem 1rem;
  /* space inside each pill */
  border-radius: 20px;
  /* pill shape */
  display: flex;
  align-items: center;
  gap: 0.5rem;
  /* little gap between label/value (or icon/text) */
  font-size: 0.95rem;
}


/* keep your label/value styling but force white */
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

/* ===== Image Section (Full Width) ===== */
.main-container {
  background: #fff;
  padding: 4rem 1.5rem;
  max-width: 100% !important;
  margin: 0 auto;
}

.image-section {
  width: 100%;
}


.main-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  /* border-top-left-radius: 16px;
  border-bottom-left-radius: 16px; */
}

/* Main image: round top-right & bottom-right */
.round-main-right {
  border-top-right-radius: 1rem;
  border-bottom-right-radius: 1rem;
}

/* 2nd thumbnail → round its top-left corner */
.round-tl {
  border-top-right-radius: 16px !important;
}

/* 4th thumbnail → round its top-right & bottom-right corners */
.round-tr-br {

  border-bottom-right-radius: 16px !important;
}


.thumbnail-grid {
  display: grid !important;
  grid-template-columns: repeat(2, 1fr) !important;
  grid-template-rows: repeat(2, 1fr) !important;
  gap: 0.5rem !important;

}

.h-100 {
  height: 100%;
}

.col-6 {
  height: 50%;
  /* Each thumbnail takes half the height of the column (for 2x2 grid) */
}

.thumbnails-container {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-top: 10px;
}

.thumbnail {
  width: 120px;
  height: 80px;
  object-fit: cover;
  cursor: pointer;
  border-radius: 4px;
  opacity: 0.8;
  transition: opacity 0.2s, transform 0.2s, border 0.2s;
}

.thumbnail.active,
.thumbnail:hover {
  opacity: 1;
  transform: scale(1.05);
  border: 2px solid #007bff;
}

/* ===== Stock & Video Sections ===== */
.stock-price-section {
  background: #fff;
  padding: 1.5rem;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  margin-bottom: 1.5rem;
}

.stock-status {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.badge.in-stock {
  background: #28a745;
  color: #fff;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.9rem;
}

.share-save {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 0.75rem;
}

.stock-price-section .share-save .btn-icon {
  background: none;
  border: none;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #333;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  padding: 0;
}

.share-save .btn-icon {
  background: none;
  border: none;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1rem;
  color: #333;
  cursor: pointer;
  padding: 0;
}

.share-save .btn-icon svg {
  width: 16px;
  height: 16px;
}

.stock-price-section .share-save .btn-icon:hover {
  color: #007bff;
}

.price-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
}

.video-section {
  border: 2px dashed #dee2e6;
  border-radius: 8px;
  padding: 2rem;
  text-align: center;
  margin-bottom: 1.5rem;
}

.video-placeholder {
  color: #666;
  cursor: pointer;
  transition: color 0.2s;
}

.video-placeholder i {
  font-size: 2.5rem;
  display: block;
  margin-bottom: 0.5rem;
}

.video-placeholder:hover {
  color: #007bff;
}

/* ===== Car Details Section ===== */
.car-details {
  padding: 0;
}

.car-details h3 {
  font-size: 3rem;
  font-weight: bold;
  margin-bottom: 1rem;
}

.car-details .text-muted {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
}

.car-details .description {
  font-size: 3.5rem !important;
  font-weight: 700;
  margin: 0;
  line-height: 1.2;
  margin-bottom: 3rem !important;
}

.thumbnail-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  cursor: pointer;
}

.thumbnail-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden;
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

.gallery-button i {
  font-size: clamp(0.8rem, 2.5vw, 1.1rem);
}

.gallery-button:hover {
  transform: translateY(-3px) scale(1.05);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
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
  top: 50% !important;
  transform: translateY(-50%) !important;
  width: 3rem !important;
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
  font-weight: 700;
  margin-bottom: 2rem;
}


/* .summary-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  column-gap: 3rem;
  row-gap:    1.5rem;
} */


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


.car-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}

.car-card {
  display: flex;
  flex-direction: column;
}

.card {
  border-radius: 10px;
  overflow: hidden;
  transition: transform 0.2s;
  color: #000;
  text-decoration: none;
}

.card:hover {
  transform: scale(1.02);
}

.image-container {
  position: relative;
  width: 100%;
  padding-top: 56.25%;
}

.car-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.image-counter {
  position: absolute;
  bottom: 10px;
  right: 10px;
  background: rgba(0, 0, 0, 0.6);
  color: #fff;
  padding: 5px 10px;
  border-radius: 20px;
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 0.9rem;
}

.card-content {
  padding: 15px;
  flex: 1;
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
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
  margin-top: 10px;
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

.dealer-text {
  color: #000 !important;
  text-decoration: none !important;
}

.dealer-text a:hover {
  color: #003366 !important;
  /* dark‐blue on hover */
}


@media (max-width: 1200px) {
  /* Car Overview: switch to 2-column (label → value) */
  /* .summary-grid {
    grid-template-columns: auto 1fr !important;
    column-gap: 2rem;
  } */

  /* Dealer contact links: allow wrapping in two per row */
  .dealer-contact-links {
    flex-wrap: wrap;
    gap: 1rem;
  }

  .dealer-contact-links .dealer-link,
  .dealer-contact-links .dealer-text {
    flex: 1 1 calc(50% - 1rem);
  }

  .dealer-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }

  .dealer-actions .primary-btn,
  .dealer-actions .secondary-btn {
    padding: 10px 10px;
    font-size: 0.9rem;
  }

  /* make the “View All stock…” link full width and center */
  .dealer-actions .view-stock-link {
    width: 100%;
    text-align: center;
  }
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
    border-top-right-radius: 16px !important;
    border-bottom-right-radius: 16px !important;
  }

  .col-lg-5 .row>.col-6:nth-child(2) .thumbnail-grid {
    border-top-left-radius: 16px !important;
  }

  .col-lg-5 .row>.col-6:nth-child(4) .thumbnail-grid {
    border-top-right-radius: 16px !important;
    border-bottom-right-radius: 16px !important;
  }

  .thumbnail {
    width: 100px;
    height: 60px;
  }

  .features-grid {
    grid-template-columns: repeat(2, 1fr);
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

  .gallery-button {
    bottom: 0.5rem;
    right: 0.5rem;
    padding: 0.25rem 0.5rem;
    font-size: 0.7rem;
    gap: 0.25rem;
  }
}

@media (min-width: 577px) and (max-width: 992px) {
  .gallery-button {
    padding: 0.5rem 0.75rem;
    font-size: 0.8rem;
    gap: 0.4rem;
  }
}


@media (min-width: 993px) {
  .gallery-button {
    padding: 0.6rem 1rem;
    font-size: 0.9rem;
    gap: 0.5rem;
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

  background-color: #ffffff;
  border-radius: 20px;
  /* padding: 1.5rem; */
  /* box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); */
}


.dealer-panel-wrapper .dealer-card {
  background-color: #fff;
  border-radius: 15px;
  margin: 0;
  box-shadow: 0 10px 50px rgba(0, 0, 0, 0.1) !important;
  border: #00000083 2px solid;
  padding-bottom: 2rem !important;
}



.dealer-panel-wrapper .dealer-profile {
  display: block !important;
  text-align: left !important;
}


.dealer-panel-wrapper .dealer-profile .profile-picture {
  margin: 0 !important;
}


.dealer-panel-wrapper .dealer-profile .dealer-name,
.dealer-panel-wrapper .dealer-profile .dealer-location {
  margin-left: 0 !important;
}


.dealer-card {
  position: sticky;
  top: 150px;
  width: 100%;
  box-sizing: border-box;
  z-index: 10;

}

.dealer-profile {
  display: flex;
  align-items: center;
}

.profile-picture {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  object-fit: cover;
}

.dealer-info {
  margin-left: 1rem;
}

.dealer-name {
  font-size: 1.25rem;
  font-weight: 600;

  margin-top: 0.75rem;
}

.dealer-location {
  font-size: 0.9rem;
  color: #6c757d;
  margin-top: 0.25rem;
}

.dealer-actions {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}



.dealer-contact-links {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}


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
  white-space: nowrap;
}

.dealer-contact-links .flat-icon {
  width: 44px;
  height: 40px;
}

.flat-icon {
  width: 24px !important;
  height: 24px !important;
}


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

.view-stock-link:hover {
  text-decoration: underline;
}


.main-image-container {
  width: 100%;
  height: 100%;
  overflow: hidden;
}


/* Force only the dealer‐link and view-stock-link back to black */
.dealer-panel-wrapper a.dealer-link,
.dealer-panel-wrapper .view-stock-link {
  color: #000 !important;
}

.dealer-panel-wrapper a.dealer-link:hover,
.dealer-panel-wrapper .view-stock-link:hover {
  color: #000 !important;
  text-decoration: none;
}

.main-image-container,
.thumbnail-grid {
  max-height: 600px;
  overflow: hidden;
}


/* shimmering placeholder */
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

/* fade real images in on top */
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





/* two-tone backgrounds for the Car Overview grid */
::v-deep .summary .row.row-cols-1 {
  /* undo the negative margins so our paddings show up nicely */
  margin-left: 0 !important;
  margin-right: 0 !important;
}

/* give each cell some breathing room */
::v-deep .summary .row.row-cols-1>.col {
  padding: 0.55rem 1rem;
}

/* odd = darker, even = lighter */
::v-deep .summary .row.row-cols-1>.col:nth-child(odd) {
  background-color: #d3cece7e;
}

::v-deep .summary .row.row-cols-1>.col:nth-child(even) {
  background-color: #c3c7da71;
}

.summary .row.row-cols-1>.col {
  padding-top: 7px;
  padding-bottom: 7px;
}

.vehicle-year {
  font-size: 3rem;
  font-weight: 700;
  line-height: 1.2;
}

@media (max-width: 768px) {

  .vehicle-title,
  .vehicle-year {
    font-size: 2.2rem !important;
  }
}

/* Shrink further on phones */
@media (max-width: 576px) {

  .vehicle-title,
  .vehicle-year {
    font-size: 1.6rem !important;
  }
}


.breadcrumb-link {
  color: #171b4c;
  text-decoration: none;
  margin-right: 0.25rem;
}
.breadcrumb-link:hover {
  text-decoration: underline;
  cursor: pointer;
}

</style>
