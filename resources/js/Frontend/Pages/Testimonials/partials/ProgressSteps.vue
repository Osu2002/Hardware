<template>
  <div class="stepper">
    <div
      v-for="(label, index) in steps"
      :key="index"
      class="step"
    >
      <!-- Circle with number or check -->
      <div
       class="circle"
       :class="{ 
         done: index  < currentStep, 
         active: index  === currentStep 
       }"
     >
       <!-- only show tick for true “completed” steps -->
       <span v-if="index + 1 < currentStep">✓</span>
       <span v-else>{{ index + 1 }}</span>
     </div>

      <!-- Connector line -->
      <div
        v-if="index < steps.length - 1"
        class="connector"
       :class="{ done: index < currentStep - 1 }"
      ></div>

      <!-- Label -->
      <div
        class="label"
         :class="{ activeLabel: index + 1 === currentStep }"
      >{{ label }}</div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  // zero-based: 0 = first step, 1 = second, etc.
  currentStep: { type: Number, required: true }
});

const steps = [
  "Vehicle Details",
  "Dream Car",
  "Fuel Savings"
];
</script>

<style scoped>
.stepper {
  display: flex;
  align-items: center;
  justify-content: space-between;
  /* gap between each step container */
  gap: 10px;
  /* expose that gap for connector sizing */
  --step-gap: 10px;
}

.step {
  position: relative;
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
}

/* the circles */
.circle {
  width: 40px;
  height: 40px;
  border: 2px solid #ccc;
  background: #fff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  color: #ccc;
  z-index: 1;             /* sit above the connector */
  transition: all 0.2s;
}
/* completed */
.circle.done {
  background: #28a745;
  border-color: #28a745;
  color: #fff;
}
/* current */
.circle.active {
  background: #fff;
  border-color: #9d9d9a;
  color: #000;
}

/* labels */
.label {
  margin-top: 6px;
  font-size: 14px;
  color: #ccc;
  text-align: center;
  transition: color 0.2s;
}
.label.activeLabel {
  color: #000;
}

.connector {
  position: absolute;
  top: 20px;           /* same as before */
  left: 50%;
  width: calc(100% + var(--step-gap));
  height: 4px;
  background-color: #ccc;
  overflow: hidden;    /* clip the ::before */
}

.connector::before {
  content: "";
  position: absolute;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  background-color: #28a745;
  transform-origin: left center;
  transform: scaleX(0);
  transition: transform 0.5s ease-in-out;  /* → make it slower */
}

.connector.done::before {
  transform: scaleX(1);
}



</style>
