<template>
  <section class="container-fluid mb-5">
    <div class="row justify-content-center">
      <div class="col-md-4 mb-3 text-center">
        <button class="rounded-0 border-0 bg-white" type="button" data-bs-toggle="offcanvas"
          data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions" style="font-size:14px;"><i
            class="fa-regular fa-lightbulb pe-2 text-primary"></i> How to read
          auction sheet</button>
      </div>
      <div class="col-md-10">
        <ul class="nav nav-pills justify-content-center align-items-center" role="tablist">
          <!-- <li class="nav-item bg-light" role="presentation">
            <button
              type="button"
              role="tab"
              @click="activeTab = 'vehicle'"
              :class="['nav-link', 'rounded-0', 'fs-6', { active: activeTab === 'vehicle' }]"
            >
              VEHICLE DETAILS
            </button>
          </li> -->
          <li class="nav-item bg-light" role="presentation">
            <button type="button" role="tab" @click="activeTab = 'stats'"
              :class="['nav-link', 'rounded-2', 'fs-6', { active: activeTab === 'stats' }]">
              STATISTIC DETAILS
            </button>
          </li>
          <li class="nav-item bg-light" role="presentation">
            <button type="button" role="tab" @click="activeTab = 'inquiry'"
              :class="['nav-link', 'rounded-2', 'fs-6', { active: activeTab === 'inquiry' }]">
              SEND INQUIRY
            </button>
          </li>
        </ul>

        <div class="mt-4">
          <VehicleDetails v-if="activeTab === 'vehicle'" />
          <StatDetails v-if="activeTab === 'stats'" />
          <ContactUs v-if="activeTab === 'inquiry'" />
        </div>
      </div>
    </div>
    <Offcanvas />
  </section>
</template>

<script>
import Offcanvas from './Models/Offcanvas.vue';
import VehicleDetails from './Models/VehicleDetails.vue';
import StatDetails from './Models/StatDetails.vue';
import ContactUs from './Models/ContactUs.vue';

export default {
  components: {
    Offcanvas,
    VehicleDetails,
    StatDetails,
    ContactUs,
  },
  data() {
    return {
      activeTab: 'stats',
    };
  },
};
</script>

<style scoped>
.nav-link {
  color: black;
  font-weight: 700;
}

.nav-link.active {
  background: linear-gradient(90deg,
      rgba(3, 70, 255, 0.705) 0%,
      rgba(3, 72, 136, 0.945) 100%);
}
</style>
