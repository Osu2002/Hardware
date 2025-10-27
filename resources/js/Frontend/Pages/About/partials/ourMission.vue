<template>
  <!-- Full-bleed Our Mission with background color and centered text -->
  <section class="our-mission-section py-5">
    <div class="container text-center">
      <h2 class="mission-title mb-4">Our Mission</h2>
      <p class="mission-text mx-auto">
        To be the most trusted online car dealer making it easier than ever for you to import your dream car from Australia, Japan, the UK, or Singapore. 
      </p>
    </div>
  </section>
</template>

<script>
export default {
  name: "OurMissionSection",
};
</script>

<style scoped>
/* break out to full viewport width */
.our-mission-section {
  position: relative;
  left: 50%;
  margin-left: -50vw;
  width: 100vw;
  background-color: #f8f9fa;
}

.mission-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #171b4c;
}

.mission-text {
  font-size: 1.2rem;
  color: #333;
  max-width: 800px;
}

@media (max-width: 768px) {
  .mission-title {
    font-size: 2rem;
  }
  .mission-text {
    font-size: 1rem;
    padding: 0 1rem;
  }
}
</style>
