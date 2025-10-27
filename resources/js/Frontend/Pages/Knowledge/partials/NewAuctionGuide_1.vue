<template>
  <section class="auction-info-section py-5">
    <div class="container-fluid">
      <div class="row g-4">
        <div class="col-lg-5 order-1">
          <div class="h-100">
            <div class="image-loader-container position-relative h-100 w-100">
              <!-- skeleton placeholder while loading -->
              <div v-if="auctionImageLoaded === false" class="skeleton-placeholder"></div>

              <!-- actual image, fades in on load -->
              <img
                src="/images/acution1_image.png"
                alt="Happy customers with their car"
                class="img-fluid rounded-4 h-100 w-100 object-fit-cover"
                :class="{ 'is-loaded': auctionImageLoaded }"
                @load="auctionImageLoaded = true"
              />
            </div>
          </div>
        </div>

        <div class="col-lg-7 order-2">
          <div class="auction-info pe-lg-4">
            <h2 class="section-title mb-4">The Points Of Auction Guide</h2>
            <div class="auction-points mb-4">
              <div class="d-flex align-items-start mb-3">
                <span class="tick-item">Exterior Auction Grade</span>
              </div>
              <div class="d-flex align-items-start mb-3">
                <span class="tick-item">Interior Auction Grade</span>
              </div>
              <div class="d-flex align-items-start mb-3">
                <span class="tick-item">Abbreviations in Auction Sheet</span>
              </div>
              <div class="d-flex align-items-start mb-3">
                <span class="tick-item">Sample Auction Sheet with English Translation</span>
              </div>
            </div>
            <h3 class="section-sub-title mb-4">Did you know that purchasing vehicles at this kind of auction directly is the most reliable way to find and obtain what you need?</h3>
            <p class="mb-4">Every vehicle goes through a comprehensive inspection before an auction opens by the auction houses' professional car inspectors. This is done by performing a comprehensive inspection of the mechanical condition, body condition, and interior condition of a vehicle and their findings are filled out on an auction sheet. This professional inspection not only uncovers obvious damages, but also any kind of hidden problems, such as flood damage, odometer rollback or the even the vehicle having been salvaged, all this major details will also be noted on the auction sheet.</p>
            <p>Many import dealers are not so willing to disclose the "Auction Sheet" for a particular vehicle they mean to sell to a customer even if the vehicle has been bought through the Japanese Car Auction including with it the "Auction Sheet". Since the "Auction Sheet" gives an awareness to the customer of the hidden problems with the vehicle. There are too many lemons in this market, which have been imported by the countries all over the world and which are sold at massive profits.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {

  data() {
    return {
      auctionImageLoaded: false
    }
  }
}
</script>

<style scoped>
.auction-info-section {
  margin: 2rem;
}

.auction-points {
  font-size: 1.1rem;
}

.tick-item {
  position: relative;
  padding-left: 1.5rem;
}

.tick-item::before {
  content: '✔';
  position: absolute;
  left: 0;
  color: #405ef2; /* Matching the FAQ page's tick color */
}

.section-title {
  font-size: 2rem;
  font-weight: bold;
}

.section-sub-title {
  font-size: 1.5rem;
  font-weight: 500;
}

/* Media Queries */
@media (max-width: 992px) {
  .auction-info-section {
    padding: 60px 0;
  }

  .auction-info-section img {
    max-height: 550px;
    margin-bottom: 2rem;
  }
}

@media (max-width: 768px) {
  .auction-info-section {
    padding: 40px 0;
  }

  .auction-points {
    font-size: 1rem;
  }
}

@media (max-width: 576px) {
  .auction-info-section {
    margin: 0 0.5rem 0 0.5rem;
  }

  .auction-info-section img {
    max-height: 400px;
  }
}


/* loader container must fill the parent */
.image-loader-container {
  width: 100%;
  height: 100%;
  overflow: hidden;
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
  border-radius: 1rem; /* match .rounded-4 */
}

/* fade-in transition */
.img-fluid {
  transition: opacity 0.5s ease;
  opacity: 0;
}
.img-fluid.is-loaded {
  opacity: 1;
}

@keyframes shimmer {
  0%   { background-position: -200% 0; }
  100% { background-position:  200% 0; }
}

</style>