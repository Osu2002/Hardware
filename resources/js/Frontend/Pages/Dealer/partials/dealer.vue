<template>
  <div class="dealer-page">
    <!-- Screenshot Slider Section -->
    <section class="py-5">

      <h2 class="text-center fw-bold mb-5">Explore the App</h2>
      <div class="screenshot-slider-container">
        <swiper :slides-per-view="3" :space-between="30" :loop="true" :autoplay="{ delay: 3000 }"
          :pagination="{ clickable: true }" :breakpoints="{
            576: { slidesPerView: 1.5 },
            768: { slidesPerView: 2 },
            992: { slidesPerView: 3 }
          }" :modules="modules" class="full-visibility-slider">
          <swiper-slide v-for="(screenshot, index) in screenshots" :key="index">
            <div class="slide-content">
              <img :src="screenshot" class="full-screenshot" :alt="'App Screenshot ' + (index + 1)">
              <p class="screenshot-label">Screenshot {{ index + 1 }}</p>
            </div>
          </swiper-slide>
        </swiper>
      </div>
    </section>

    <!-- Features Section -->
    <section class=" py-5 ">
      <div class="container">
        <h2 class="text-center fw-bold mb-5">Why Choose Nikoba Dealer?</h2>
        <div class="row g-4">
          <div class="col-md-4">
            <div class="feature-card text-center p-4 h-100">
              <i class="fas fa-gavel fa-3x text-primary mb-3"></i>
              <h3 class="fw-bold mb-3">Live Auctions</h3>
              <p>Bid in real-time on Japanese vehicle auctions with transparent pricing.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="feature-card text-center p-4 h-100">
              <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
              <h3 class="fw-bold mb-3">Full Service</h3>
              <p>NIKOBA imports Japanese vehicles with full auction transparency, offering direct Japan auction access
                since 2010.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="feature-card text-center p-4 h-100">
              <i class="fas fa-mobile-alt fa-3x text-primary mb-3"></i>
              <h3 class="fw-bold mb-3">Easy Interface</h3>
              <p>Intuitive app design makes the process simple for all users.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Download CTA Section -->
    <section class=" py-5">
      <div class="container text-center">
        <h2 class="fw-bold mb-4">Ready to Get Started?</h2>
        <p class="lead mb-4">Download the Nikoba Dealer app today</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
          <a href="https://apps.apple.com/lk/app/nikoba-dealer/id6743689787" target="_blank" class="market-btn apple-btn" role="button">
        <span class="market-button-subtitle">Download on the</span>
        <span class="market-button-title">App Store</span>
    </a>
    <a href="https://play.google.com/store/apps/details?id=lk.nikoba.admin.nikoba&pcampaignid=web_share" target="_blank" class="market-btn google-btn" role="button">
        <span class="market-button-subtitle">Download on the</span>
        <span class="market-button-title">Google Play</span>
    </a>
        </div>
      </div>
      

    </section>
  </div>
</template>

<script>
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';

export default {
  components: {
    Swiper,
    SwiperSlide
  },
  data() {
    return {
      modules: [Autoplay, Pagination],
      screenshots: [
        '/images/dealer/dealer1.png',
        '/images/dealer/dealer2.png',
        '/images/dealer/dealer3.png',
        '/images/dealer/dealer4.png',
        '/images/dealer/dealer5.png',
        '/images/dealer/dealer6.png'
        
      ]
    };
  }
};
</script>

<style scoped>
.market-btn {
    display: inline-block;
    padding: 0.3125rem 0.875rem;
    padding-left: 2.8125rem;
    transition: border-color 0.25s ease-in-out, background-color 0.25s ease-in-out;
    border: 1px solid #e7e7e7;
    background-position: center left 0.75rem;
    background-color: #fff;
    background-size: 1.5rem 1.5rem;
    background-repeat: no-repeat;
    text-decoration: none;
    margin: 10px;
    border-radius: 8px;
}
.market-btn .market-button-title {
    display: block;
    color: #222;
    font-size: 1.125rem;
}
.market-btn .market-button-subtitle {
    display: block;
    margin-bottom: -0.25rem;
    color: #888;
    font-size: 0.75rem;
}
.market-btn:hover {
    background-color: #f7f7f7;
    text-decoration: none;
}
.apple-btn {
    background-image: url('https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg');
}
.google-btn {
    background-image: url('https://upload.wikimedia.org/wikipedia/commons/d/d0/Google_Play_Arrow_logo.svg');
}
.windows-btn {
    background-image: url('https://upload.wikimedia.org/wikipedia/commons/4/48/Windows_logo_-_2021.svg');
}
.amazon-btn {
    background-image: url('https://upload.wikimedia.org/wikipedia/commons/a/a9/Amazon_logo.svg');
}
/* Base Styles */
.dealer-page {
  overflow-x: hidden;
}

/* Screenshot Slider */
.screenshot-slider-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 15px;
  width: 100%;
  height: auto;
}

.full-visibility-slider {
  padding: 10px 7px;
  width: 100%;
  height: 100%;
}

.slide-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  height: 100%;
}

.full-screenshot {
  width: auto;
  height: auto;
  max-width: 100%;
  max-height: 800px;
  object-fit: contain;
  border-radius: 12px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.screenshot-label {
  margin-top: 15px;
  color: #6c757d;
  font-size: 0.9rem;
}

/* Features Section */


.feature-card {
  background: white;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-5px);
}



.download-btn {
  min-width: 180px;
  transition: all 0.3s ease;
  padding: 10px 25px;
}

.download-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Responsive Adjustments */
@media (max-width: 992px) {
  .full-screenshot {
    max-height: 400px;
  }
}

@media (max-width: 768px) {
  .full-screenshot {
    max-height: 350px;
  }

  .feature-card {
    margin-bottom: 20px;
  }
}

@media (max-width: 576px) {
  .full-screenshot {
    max-height: 300px;
  }

  .download-btn {
    width: 100%;
    max-width: 250px;
  }
}
</style>