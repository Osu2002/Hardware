<template>
  <section class="container my-5">
    <!-- Monthly Savings Header -->
    <div class="row justify-content-center mb-4">
      <div class="col-md-8 text-center">
        <h4 class="mb-0">Monthly Fuel Savings Result</h4>
      </div>
    </div>

    <div class="row g-4">
      <!-- Current Vehicle Column -->
      <div class="col-md-6">
        <div class="p-4 rounded-4 bg-light leasing-panel">
          <h5 class="mb-3">Current Vehicle</h5>
          <p><strong>{{ current.brand }} {{ current.model }}</strong></p>

          <div class="mb-3">
            <label class="form-label small">Fuel Type:</label>
            <select class="form-select" v-model="currentFuelType">
              <option v-for="f in fuelOptions" :key="f.type" :value="f.type">
                {{ f.type }}
              </option>
            </select>
          </div>

          <p>Distance (30d): {{ currentDistance }} km</p>
          <p v-if="!isPerKm(currentFuelType)">
            Litres used: {{ currentLitres.toFixed(1) }} L
          </p>
          <p>Cost: {{ formatLKR(currentCost) }}</p>
        </div>
      </div>

      <!-- Dream Vehicle Column -->
      <div class="col-md-6">
        <div class="p-4 rounded-4 bg-light leasing-panel">
          <h5 class="mb-3">Dream Vehicle</h5>
          <p><strong>{{ dream.brand }} {{ dream.model }}</strong></p>

          <div class="mb-3">
            <label class="form-label small">Fuel Type:</label>
             <select class="form-select" v-model="dreamFuelType">
              <option disabled value="">Select Fuel Type</option>
                <option
                  v-for="f in dreamFuelOptions"
                  :key="f.type"
                  :value="f.type"
                >
                  {{ f.type }}
                </option>
           </select>
          </div>

          <p>Distance (30d): {{ dreamDistance }} km</p>
          <p v-if="!isPerKm(dreamFuelType)">
            Litres used: {{ dreamLitres.toFixed(1) }} L
          </p>
          <p>Cost: {{ formatLKR(dreamCost) }}</p>
        </div>
      </div>
    </div>

    <!-- Savings Row -->
    <div class="row justify-content-center mt-4">
      <div class="col-md-6 text-center">
        <h5>You Save:</h5>
        <p class="fs-4 text-success">
          {{ formatLKR(currentCost - dreamCost) }}
        </p>
        <button class="btn btn-secondary mt-2" @click="$emit('restart')">
          ← Start Over
        </button>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'SavingsResult',
  props: {
    current:     { type: Object, required: true },
    dream:       { type: Object, required: true },
    fuelOptions: { type: Array,  required: true },
  },
  data() {
    return {
      imageLoaded:    false,
      currentFuelType: '',
      dreamFuelType:   '',
    }
  },
  watch: {
    current: {
      immediate: true,
      handler(c) {
        this.currentFuelType = c.fuelType
      }
    },
    dream: {
      immediate: true,
      handler(d) {
        this.dreamFuelType = d.fuelType
      }
    }
  },
  methods: {
    isPerKm(type) {
      return type === 'Electric'
    },
    formatLKR(amount) {
      return 'LKR ' + Number(amount || 0)
        .toLocaleString('en-LK', { minimumFractionDigits: 0 })
    }
  },
  computed: {
    // currentDistance() { return this.current.dailyKm * 30 },
    // dreamDistance()   { return this.current.dailyKm * 30 },
    currentDistance() { return this.current.dailyKm * 30 },
    dreamDistance()   { return this.currentDistance },
 

    currentPrice() {
      const f = this.fuelOptions.find(x => x.type === this.currentFuelType)
      return f ? f.price : 0
    },
    dreamPrice() {
      const f = this.fuelOptions.find(x => x.type === this.dreamFuelType)
      return f ? f.price : 0
    },

     currentCost() {
    // If electric: cost = km × Rs/km (efficiency)
     if (this.isPerKm(this.currentFuelType)) {
       return this.currentDistance * this.current.efficiency
     }
     // Otherwise: cost = (km ÷ km/l) × Rs/liter
     return (this.currentDistance / this.current.efficiency) * this.currentPrice
   },
    dreamCost() {
    if (this.isPerKm(this.dreamFuelType)) {
       return this.dreamDistance * this.dream.efficiency
     }
     return (this.dreamDistance / this.dream.efficiency) * this.dreamPrice
   },
    currentLitres() {
      return this.currentDistance / this.current.efficiency
    },
    dreamLitres() {
      return this.dreamDistance / this.dream.efficiency
    },
    dreamFuelOptions() {
    // if dream is electric, only one choice:
    if (this.isPerKm(this.dreamFuelType)) {
      return [{ type: 'Electric', price: 0 }];
    }
    // otherwise: all except Electric
    return this.fuelOptions.filter(
      f => f.type.toLowerCase() !== 'electric'
    );
  },
  }
}
</script>

<style scoped>
.fs-4 { font-size: 1.5rem; }
.leasing-panel { padding: 1.5rem; }
.bg-light { background-color: #f2f2f2 !important; }

.form-select {
  width: 100%;
  margin-bottom: 0.75rem;
}
</style>
