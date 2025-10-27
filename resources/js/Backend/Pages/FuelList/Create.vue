<template>
  <AppLayout>
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card mb-4">

          <div class="card-header">
            <h5>Create Fuel Type</h5>
          </div>

          <div class="card-body">
            <form @submit.prevent="submit">
              <div class="mb-3">
                <label class="form-label">Oil Name</label>
                <select v-model="form.fuel_type" class="form-select" required>
                  <option disabled value="">— Select oil —</option>
                  <option value="petrol_92">Petrol 92</option>
                  <option value="petrol_95">Petrol 95</option>
                  <option value="diesel">Diesel</option>
                  <!-- <option value="electric">Electric</option> -->
                </select>
                <div v-if="form.errors.fuel_type" class="text-danger">{{ form.errors.fuel_type }}</div>
              </div>

              <div class="mb-3">
                <label class="form-label">Price (Rs)</label>
                <input v-model.number="form.price" type="number" class="form-control" required />
                <div v-if="form.errors.price" class="text-danger">{{ form.errors.price }}</div>
              </div>

              <button class="btn btn-main btn-sm" type="submit">Save</button>
              <Link href="{ route('index') }" class="btn btn-secondary btn-sm ms-2">Cancel</Link>
            </form>
          </div>

        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { useForm } from '@inertiajs/inertia-vue3'
import { Link }    from '@inertiajs/inertia-vue3'
import AppLayout   from '@/Layouts/AppLayout.vue'

export default {
  components: { Link, AppLayout },
  setup() {
    const form = useForm({
      fuel_type: '',
      price:     null,
    })
    function submit() {
      form.post(route('fuels.store'))
    }
    return { form, submit }
  }
}
</script>
