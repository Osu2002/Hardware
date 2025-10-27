<template>
  <!-- Full-bleed “Our Values” with image on the left -->
  <section class="our-values-section py-5">
    <div class="container-fluid p-0">
      <!-- use align-items-stretch so both columns match height -->
      <div class="row g-0 align-items-stretch overflow-hidden rounded-4">
        <!-- Left: Image wrapped in flex so it fills column -->
        <div class="col-12 col-md-6 px-4 px-md-5 d-flex flex-column">
          <div class="flex-fill position-relative">

            <div v-if="!valuesImageLoaded" class="skeleton-placeholder"></div>
            <img
              src="/images/check.png"
              alt="Our Values"
              class="w-100 h-100 object-fit-cover rounded"
              :class="{ 'is-loaded': valuesImageLoaded }"
              @load="onValuesImageLoad"
             />
          </div>
        </div>

        <!-- Right: Content with matching padding -->
        <div class="col-12 col-md-6 px-4 px-md-5 d-flex flex-column justify-content-center">
          <h2 class="values-title mb-4">Our Values</h2>
          <p class="mb-4 text">
            At World Auto Dealers, we uphold the core values that guide our operations and
            relationships with our clients. Our commitment to <strong>Honesty</strong>,
            <strong>Integrity</strong>, and <strong>Transparency</strong> ensures that we
            maintain trust and deliver exceptional service in every transaction.
          </p>
          <ul class="list-unstyled text ">
            <li class="mb-2">
              <i class="fa-solid fa-check text-primary me-2"></i>
              Honesty – We are truthful in all our dealings.
            </li>
            <li class="mb-2">
              <i class="fa-solid fa-check text-primary me-2"></i>
              Integrity – We adhere to ethical principles in every decision.
            </li>
            <li class="mb-2">
              <i class="fa-solid fa-check text-primary me-2"></i>
              Transparency – We provide clear and open information to our clients.
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "OurValuesSection",
  data() {
    return {
      valuesImageLoaded: false,
    };
  },
   methods: {
    onValuesImageLoad() {
      this.valuesImageLoaded = true;
    },
  },
};
</script>

<style scoped>
/* break out to full viewport width */


.text{
  font-size: 1.2rem;

}
.our-values-section {
  position: relative;
  left: 50%;
  margin-left: -50vw;
  width: 100vw;
  overflow-x: hidden;
}

/* make the image fill its flex container */
.object-fit-cover {
  object-fit: cover;
  width: 100%;
  height: 100%;
  border-radius: 15px !important;
}

.values-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #171b4c;
}

@media (max-width: 768px) {
  .values-title {
    font-size: 2rem;
  }
  .object-fit-cover {
    max-height: 300px;
  }
}


@media (max-width: 768px) {
 .values-title {
    font-size: 2rem;
  }
  .text {
    font-size: 1rem;
    /* padding: 0 1rem; */
  }
}
@media (max-width: 768px) {
  .our-values-section .flex-fill {
    margin-bottom: 1.5rem;
  }
}

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
  border-radius: 15px; /* match your rounding */
}

@keyframes shimmer {
  0%   { background-position: -200% 0; }
  100% { background-position:  200% 0; }
}

</style>
