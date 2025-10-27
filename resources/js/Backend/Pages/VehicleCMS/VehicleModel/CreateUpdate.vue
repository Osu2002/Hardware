<template>
  <AppLayout>
    <div class="row">
      <div class="col-md-12">
        <div class="card mb-4">
          <div class="card-header pb-0">
            <h5>Vehicle Models</h5>
            <p>Manage Vehicle Models.</p>
          </div>
          <hr />
          <div class="card-body">
            <form id="formAccountSettings" @submit.prevent="submit">
              <div class="row">
                <div class="mb-3 col-md-6">
                  <SelectInputComponentVue
                    class="w-100"
                    id="tst"
                    label="Manufacturer Name"
                    v-model="form.manufacture_id"
                    :options="vehicle_manufacture"
                  />
                </div>
                <div class="mb-3 col-md-12">
                  <label for="title" class="form-label">TITLE</label>
                  <input
                    class="form-control"
                    type="text"
                    id="title"
                    name="title"
                    v-model="form.title"
                  />
                  <div class="text-danger">
                    {{ form.errors.name }}
                  </div>
                </div>
              </div>
              <div class="mt-2">
                <button
                  type="submit"
                  class="btn btn-main me-2"
                  :disabled="form.processing"
                >
                  {{ vehicle_models ? "Update" : "Save" }}
                </button>
                <Link
                  class="btn btn-outline-danger"
                  :href="route('vehicle.model')"
                  >Cancel</Link
                >
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>


<script>
import { Link, useForm } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import SelectInputComponentVue from "../../../Components/SelectInputComponent.vue";

export default {
  components: {
    Link,
    AppLayout,
    SelectInputComponentVue,
  },

  props: {
    errors: Object,
    backendUser: Object,
    vehicle_models: Object,
    manufacture: Object,
    vehicle_manufacture: Object
    
  },

  data() {
    return {
      form: useForm({
        id: "",
        title: "",
        manufacture_id: "",
      }),
    };
  },
  mounted() {
    let self = this;

    if (this.vehicle_models) {
      this.form.id = this.vehicle_models.id;
      this.form.manufacture_id = this.vehicle_models.manufacture_id;
      this.form.title = this.vehicle_models.title;
    }
    $(".card-body").on("change", "#role", function (evt) {
      self.form.role = $(evt.target).val();
    });
    $(".card-body").on("change", "#branch", function (evt) {
      self.form.branch = $(evt.target).val();
    });
  },
  computed: {
    // profileImage() {
    //   return this.backendUser ? this.backendUser.profile_photo_path ?? "" : "";
    // },
  },
  methods: {
    submit() {
      this.form.post(
        this.vehicle_models
          ? route("vehicle_model.update")
          : route("vehicle.model.storedata"),
        {
          onSuccess: () => {
            this.form.reset();
            this.$root.showMessage(
              "success",
              '<span class="text-success">Success</span><br/>',
              "A Record Was Created Successfully! "
            );
          },
          onError: () => {
            this.form.reset();
            this.$root.showMessage(
              "error",
              '<span class="text-danger">Error</span><br>',
              "Something went wrong! "
            );
          },
        }
      );
    },
  },
};
</script>
