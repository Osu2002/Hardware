<template>
  <div>


    <br>
    <ProgressSteps
      :steps="['Current Car','Dream Car','Results']"
      :current-step="step"
      class="mb-4"
    />

    
    <CurrentVehicleForm
      v-if="step === 1"
      @next="onCurrentNext"
    />
    <DreamCarForm
      v-else-if="step === 2"
      :initial-fuel-type="current.fuelType"
      :makes="makes"
      :models="models"
      :fuel-options="normalizedFuelOptions"
      :vehicles="vehicles"
      @back="step = 1"
      @next="onDreamNext"
    />
    <SavingsResult
      v-else-if="step === 3"
      :current="current"
      :dream="dream"
      :fuel-options="normalizedFuelOptions"
      @restart="step = 1"
    />
  </div>
</template>

<script>
import CurrentVehicleForm from './CurrentVehicleForm.vue'
import DreamCarForm       from './DreamCarForm.vue'
import SavingsResult      from './SavingsResult.vue'
import ProgressSteps      from './ProgressSteps.vue'

export default {
  components: { CurrentVehicleForm, DreamCarForm, SavingsResult, ProgressSteps, },
  props: {
    makes:       Array,
    models:      Array,
    fuelOptions: Array,   // ← receives via `fuel-options`
    vehicles:    Array,
  },
  data() {
    return {
      step:    1,
      current: null,
      dream:   null,
    }
  },
  computed: {
    normalizedFuelOptions() {
      return this.fuelOptions.map(f => ({ type: f.fuel_type, price: f.price }))
    }
  },
  methods: {
    onCurrentNext(payload) {
      this.current = payload
      this.step    = 2
    },
      async onDreamNext(payload) {
    // look up the human-readable titles
    const make  = this.makes.find(m => m.id === payload.makeId)?.title || ''
    const model = this.models.find(m => m.id === payload.modelId)?.title || ''

    this.dream = {
      ...payload,
      brand:  make,
      model:  model
    }

    this.step = 3
  },
  }
}
</script>
