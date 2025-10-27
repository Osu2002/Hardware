<template>
  <AppLayout>
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card mb-4">

          <div class="card-header">
            <h5>Edit Fuel Type</h5>
          </div>

          <div class="card-body">
            <form @submit.prevent="submit">
              <div class="mb-3">
                <label class="form-label">Oil Name</label>
                <!-- use readonly instead of disabled so value is submitted -->
                <input v-model="form.fuel_type" class="form-control" readonly />
              </div>

              <div class="mb-3">
                <label class="form-label">Price (Rs)</label>
                <input v-model.number="form.price" type="number" class="form-control" required />
                <div v-if="form.errors.price" class="text-danger">{{ form.errors.price }}</div>
              </div>

              <button class="btn btn-main btn-sm" type="submit">Update</button>
              <Link :href="route('fuels.index')" class="btn btn-secondary btn-sm ms-2">Cancel</Link>
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
  props: {
    fuelPrice: Object
  },
  setup(props) {
    const form = useForm({
      fuel_type: props.fuelPrice.fuel_type,
      price:     props.fuelPrice.price,
    })
    
    function submit() {
      // submit fuel_type and price, use correct id
      form.put(route('fuels.update', props.fuelPrice.id), {
        preserveScroll: true,
        onSuccess: () => {
          // optional: show a notification or redirect
        }
      })
    }

    return { form, submit }
  }
}
</script>
