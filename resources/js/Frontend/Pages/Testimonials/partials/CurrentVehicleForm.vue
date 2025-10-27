<template>
  <section class="container my-5">
    <div class="row g-4 align-items-center">
      <!-- LEFT PANEL: your form -->
      <div class="col-lg-6">
        <div class="p-5 rounded-4 bg-light leasing-panel">
          <h3 class="mb-4 fw-bold">Current Car Details</h3>
          <form @submit.prevent="calculate">
            <div class="row gy-3 gx-3">
              <!-- Brand -->
              <div class="col-md-6">
                <div class="bg-white rounded-4 p-2">
                  <label class="form-label small text-secondary">
                    Make of your current car
                  </label>
                  <select v-model="brand" class="form-select border-0 p-0">
                    <option disabled value="">Select Vehicle Brand</option>
                    <option v-for="b in brands" :key="b">{{ b }}</option>
                  </select>
                </div>
              </div>

              <!-- Model -->
              <div class="col-md-6">
                <div class="bg-white rounded-4 p-2">
                  <label class="form-label small text-secondary">
                    Model of your current car
                  </label>
                  <input
                    type="text"
                    v-model="model"
                    class="form-control border-0 p-0"
                    placeholder="e.g. Corolla 2018"
                  />
                </div>
              </div>

              <!-- Daily distance -->
              <div class="col-md-6">
                <div class="bg-white rounded-4 p-2">
                  <label class="form-label small text-secondary">
                    Daily travel distance (km)
                  </label>
                  <input
                    type="number"
                    v-model.number="dailyKm"
                    class="form-control border-0 p-0"
                    placeholder="e.g. 40"
                  />
                </div>
              </div>

              <!-- Fuel Type -->
              <div class="col-md-6">
                <div class="bg-white rounded-4 p-2">
                  <label class="form-label small text-secondary">
                    Fuel type used
                  </label>
                  <select
                    v-model="fuelType"
                    @change="onFuelTypeChange"
                    class="form-select border-0 p-0"
                  >
                    <option disabled value="">Select Fuel Type</option>
                    <option
                      v-for="f in fuelOptions"
                      :key="f.type"
                      :value="f.type"
                    >
                      {{ f.type }}
                    </option>
                  </select>
                </div>
              </div>

              <!-- Fuel Price -->
              <div class="col-md-6">
                <div class="bg-white rounded-4 p-2">
                  <label class="form-label small text-secondary">
                    Fuel price (Rs per unit)
                  </label>
                  <input
                    type="text"
                    :value="price !== null ? `Rs ${price}` : ''"
                    readonly
                    class="form-control border-0 p-0"
                  />
                </div>
              </div>

              <!-- Efficiency -->
              <div class="col-md-6">
                <div class="bg-white rounded-4 p-2">
                  <label class="form-label small text-secondary">
                    Fuel efficiency (km/l or Rs/km)
                  </label>
                  <input
                    type="number"
                    v-model.number="efficiency"
                    class="form-control border-0 p-0"
                    placeholder="e.g. 10"
                  />
                </div>
              </div>

              <!-- Submit button -->
              <div class="col-12 d-grid">
                <button type="submit" class="btn btn-gradient rounded-4 p-2 btn-lg">
                  Next
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- RIGHT IMAGE: your illustration -->
      <!-- <div class="col-lg-6">
        <div class="position-relative">
          
          <div v-if="!leasingImageLoaded" class="skeleton-placeholder"></div>
          
          <img
            src="/images/h93.jpg"
            alt="Leasing Illustration"
            class="w-100 rounded-4 object-fit-cover"
            :class="{ 'is-loaded': leasingImageLoaded }"
            @load="leasingImageLoaded = true"
          />
        </div>
      </div> -->
    </div>
  </section>
</template>

<script>
import axios from 'axios'

export default {
  name: 'LeasingFacilities',
  data() {
    return {
      brands: ['Toyota', 'Honda', 'Nissan', 'Mazda'],
      fuelOptions: [],
      brand: '',
      model: '',
      dailyKm: null,
      fuelType: '',
      price: null,
      efficiency: null,
      leasingImageLoaded: false,
    }
  },
  methods: {
    async fetchFuelOptions() {
      const res = await axios.get(route('fuels.getdata'))
      this.fuelOptions = res.data.data.map(f => ({
        type: f.fuel_type,
        price: f.price
      }))
    },
    onFuelTypeChange() {
      const entry = this.fuelOptions.find(f => f.type === this.fuelType)
      this.price = entry ? entry.price : null
    },
    calculate() {
      this.$emit("next", {
      brand:      this.brand,
      model:      this.model,
      dailyKm:    this.dailyKm,
      fuelType:   this.fuelType,
      price:      this.price,
      efficiency: this.efficiency
    })
    }
  },
  mounted() {
    this.fetchFuelOptions()
  }
}
</script>

<style scoped>
.leasing-panel { padding-bottom: 3rem; }
.bg-light { background-color: #f2f2f2 !important; }
.bg-white { background-color: #fff !important; }
.form-select, .form-control { border: none; height: auto; }
.btn-gradient {
  background: linear-gradient(135deg, #3a3f5c, #1e1f2d);
  color: #fff; border: none; border-radius: .75rem;
}
.btn-gradient:hover {
  background: linear-gradient(135deg, #1e1f2d, #3a3f5c);
}
.skeleton-placeholder {
  position: absolute; top:0; left:0;
  width:100%; height:100%;
  background:linear-gradient(to right,#e0e0e0 0%,#f8f8f8 50%,#e0e0e0 100%);
  background-size:200% 100%; animation:shimmer 1.5s infinite;
  border-radius:16px;
}
@keyframes shimmer {
  0% { background-position:-200% 0 } 
  100% { background-position:200% 0 }
}
.object-fit-cover {
  object-fit:cover; height:100%; opacity:0; transition:opacity .5s;
}
.object-fit-cover.is-loaded { opacity:1; }
</style>
