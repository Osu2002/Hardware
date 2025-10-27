<template>
  <section class="container my-5">
    <div class="row g-4 align-items-center">
      <!-- LEFT PANEL: Dream Car Form -->
      <div class="col-lg-6">
        <div class="p-5 rounded-4 bg-light leasing-panel">
          <h3 class="mb-4 fw-bold">Select Your Dream Car</h3>
          <form @submit.prevent="submit">
            <div class="row gy-3 gx-3">
              <!-- Make -->
              <div class="col-md-6">
                <div class="bg-white rounded-4 p-2">
                  <label class="form-label small text-secondary">Make</label>
                  <select v-model="makeId" class="form-select border-0 p-0">
                    <option disabled value="">Select Vehicle Make</option>
                    <option v-for="m in makes" :key="m.id" :value="m.id">
                      {{ m.title }}
                    </option>
                  </select>
                </div>
              </div>

              <!-- Model -->
              <div class="col-md-6">
                <div class="bg-white rounded-4 p-2">
                  <label class="form-label small text-secondary">Model</label>
                  <select
                    v-model="modelId"
                    class="form-select border-0 p-0"
                    @change="onModelChange"
                  >
                    <option disabled value="">Select Vehicle Model</option>
                    <option
                      v-for="m in filteredModels"
                      :key="m.id"
                      :value="m.id"
                    >{{ m.title }}</option>
                  </select>
                </div>
              </div>

              <!-- Efficiency -->
              <div class="col-md-6">
                <div class="bg-white rounded-4 p-2">
                  <label class="form-label small text-secondary">
                    Fuel Efficiency
                  </label>
                  <input
                    type="text"
                    :value="efficiency
                      ? efficiency + (isElectric ? ' Rs/km' : ' km/L')
                      : '—'"
                    readonly
                    class="form-control border-0 p-0"
                  />
                </div>
              </div>

              <!-- Fuel Type (always visible but sometimes locked) -->
              <div class="col-md-6">
                <div class="bg-white rounded-4 p-2">
                  <label class="form-label small text-secondary">
                    Fuel Type
                  </label>
                  <select
                    v-model="fuelType"
                    @change="onFuelTypeChange"
                   
                    class="form-select border-0 p-0"
                  >
                  <option disabled value="" v-if="!fuelType">
    Select Fuel Type
  </option>
  <!-- electric remains the only choice if it’s electric -->
  <option v-if="isElectric" value="Electric">
    Electric
  </option>
                    
                    <option
                      v-for="f in filteredFuelOptions"
                      :key="f.type"
                      :value="f.type"
                    >
                  
                      {{ f.type }}
                    </option>

                    
                    
                  </select>
                </div>
              </div>

              <!-- Price (only for non-electric) -->
              <div class="col-md-6" v-if="!isElectric">
                <div class="bg-white rounded-4 p-2">
                  <label class="form-label small text-secondary">
                    Fuel Price (Rs)
                  </label>
                  <input
                    type="text"
                    :value="price ? `Rs ${price}` : ''"
                    readonly
                    class="form-control border-0 p-0"
                  />
                </div>
              </div>

              <!-- Buttons -->
              <div class="col-12 d-grid">
                <button
                  type="button"
                  class="btn btn-secondary mb-2"
                  @click="$emit('back')"
                >
                  ← Back
                </button>
                <button type="submit" class="btn btn-gradient">
                  Calculate Savings
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- RIGHT IMAGE -->
      <!-- <div class="col-lg-6">
        <div class="position-relative">
          <div v-if="!imageLoaded" class="skeleton-placeholder"></div>
          <img
            src="/images/h93.jpg"
            alt="Dream Car Illustration"
            class="w-100 rounded-4 object-fit-cover"
            :class="{ 'is-loaded': imageLoaded }"
            @load="imageLoaded = true"
          />
        </div>
      </div> -->
    </div>
  </section>
</template>

<script>
export default {
  props: {
    makes: Array,
    models: Array,
    fuelOptions: Array,
    vehicles: Array,
    initialFuelType: String,
  },
  data() {
    return {
      makeId: '',
      modelId: '',
      efficiency: null,
      // fuelType: '',
      fuelType:     this.initialFuelType || '', 
      price: null,
      imageLoaded: false,
    };
  },
  computed: {
    // Monthly wizard will pass in fuelOptions like
    // [{ type: 'Petrol 92', price: 420 }, ...]
    filteredModels() {
      return this.models.filter(m => m.manufacture_id === this.makeId);
    },

    // If the current fuelType is Electric, only show that;
    // otherwise show everything except Electric
    filteredFuelOptions() {
     if (this.isElectric) {
      return this.fuelOptions.filter(f =>
        String(f.type).toLowerCase() === 'electric'
      );
    }
    return this.fuelOptions.filter(f =>
      String(f.type).toLowerCase() !== 'electric'
    );
    },

    // Simple flag: true as soon as fuelType === 'Electric'
    isElectric() {
    return String(this.fuelType).toLowerCase() === 'electric';
  },
  },
  methods: {
    onModelChange() {
      const veh = this.vehicles.find(
        v =>
          v.manufacture_id === this.makeId &&
          v.vehicle_model_id === this.modelId
      );
      if (!veh) return;

      // 1) Grab its efficiency & fuel_type
      this.efficiency = veh.fuel_efficiency;
      this.fuelType = veh.fuel_type || this.initialFuelType;

      // 2) force fuelType to whatever this vehicle is
     if (veh.fuel_type) {
  this.fuelType = veh.fuel_type;
}

      // 2) Update price
      this.updatePrice();
    },
    onFuelTypeChange() {
      this.updatePrice();
    },
    updatePrice() {
      const f = this.fuelOptions.find(x => x.type === this.fuelType);
      this.price = f ? f.price : null;
    },
    submit() {
      this.$emit('next', {
        makeId: this.makeId,
        modelId: this.modelId,
        efficiency: this.efficiency,
        fuelType: this.fuelType,
        price: this.price,
      });
    },
  },
};
</script>

<style scoped>
.leasing-panel { padding-bottom: 3rem; }
.bg-light { background-color: #f2f2f2 !important; }
.bg-white { background-color: #fff !important; }
.form-select,
.form-control { border: 0; height: auto; }

.btn-gradient {
  background: linear-gradient(135deg, #3a3f5c, #1e1f2d);
  color: #fff; border: none; border-radius: .75rem;
}
.btn-gradient:hover {
  background: linear-gradient(135deg, #1e1f2d, #3a3f5c);
}

</style>
