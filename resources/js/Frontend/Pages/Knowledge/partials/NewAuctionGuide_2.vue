<template>
  <section class="auction-info-section py-5">
    <div class="container-fluid">
      <div class="row g-4">
        <div class="col-lg-7 order-1">
          <div class="auction-info pe-lg-4">
            <div class="auction-points mb-4">
              <div class="d-flex align-items-start mb-3">
                <span class="tick-item">You can know condition of a vehicle through an auction sheet i.e. an inspection report by a professional car inspector.</span>
              </div>
              <div class="d-flex align-items-start mb-3">
                <span class="tick-item">You can know every thing about the vehicle. The auction sheet shows you even all the hidden problems for that particular car.</span>
              </div>
              <div class="d-flex align-items-start mb-3">
                <span class="tick-item">This inspection report is a declaration of the condition of the vehicle to the customer to know the overall of the vehicle they're about to invest in.</span>
              </div>
            </div>
            <p class="mb-4">In order to full understand the kind of inspection they perform, an auction grade is given indicating the vehicle's condition and is included on the auction sheet. The auction grade of a vehicle is an index of a vehicle's overall condition. You can know the general, rough condition of the vehicle through this auction grade and the vehicle's diagram that is also included in the auction sheet, even if you are unable to read Japanese, which is one of the benefits of having an auction sheet.</p>
            <p>Each auction sheet however includes notes by the inspector, an inspector's report which includes important information on a vehicle's condition e.g. major mechanical trouble, such as engine knocking, a slipping transmission, or major body problems, such as major accident damage or major rust damage. This information would provide you with a clearer picture of the true condition of the vehicle, allowing you to make a more informed decision concerning your purchase, yet this inspector's report is written in Japanese.</p>
          </div>
        </div>
        <div class="col-lg-5 order-2">
        <div class="h-100">
          <div class="image-loader-container position-relative h-100 w-100">
            <!-- show shimmer until the image fires load -->
            <div v-if="knowledge2Loaded === false" class="skeleton-placeholder"></div>

            <!-- fade-in actual image -->
            <img
              src="/images/knowledge-2.png"
              alt="Happy customers with their car"
              class="img-fluid rounded-4 h-100 w-100 object-fit-cover"
              :class="{ 'is-loaded': knowledge2Loaded }"
              @load="knowledge2Loaded = true"
            />
          </div>
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
      // other data…
      knowledge2Loaded: false
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

.auction-points img {
  width: 20px;
  height: 20px;
  object-fit: contain;
  margin-top: 2px; /* Kept for consistency, though image is removed */
}

/* Media Queries */
@media (max-width: 992px) {
  .auction-info-section {
    padding: 60px 0;
  }

  .auction-info-section img {
    max-height: 400px;
    margin-top: 2rem;
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
}

/* ensure loader container fills its parent */
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
  border-radius: .8rem; /* adjust to match your rounded-4 */
}

/* simple fade-in for .img-fluid */
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