<template>
  <AppLayout>
    <div class="row">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h5>Vehicle Manufacture</h5>
          <p>Manage Vehicle Manufacture.</p>
        </div>
        <hr />
        <div class="card-body">
          <form id="formAccountSettings" @submit.prevent="submit">
            <div class="row">
              <div class="mb-3 col-md-6">
                <label for="name" class="form-label">Title</label>
                <input
                  class="form-control"
                  type="text"
                  id="title"
                  name="title"
                  v-model="form.title"
                />
                <div class="text-danger">
                  {{ form.errors.title }}
                </div>
              </div>

              <SelectInputComponentVue
                  id="status"
                  label="status"
                  :error="form.errors.status"
                  :options="[
                        {
                          id: '1',
                          name: 'Active',
                        },
                        {
                          id: '0',
                          name: 'Inactive',
                        },
                      ]"
                  v-model="form.status"
                />
                <SelectInputComponentVue
                  id="featured"
                  label="Is Featured"
                  :error="form.errors.featured"
                  :options="[
                        {
                          id: '1',
                          name: 'Enable',
                        },
                        {
                          id: '0',
                          name: 'Disable',
                        },
                      ]"
                  v-model="form.featured"
                />


              <div class="mb-3 col-md-6">
                <label for="vehicleType_image" class="form-label me-3"
                  >Vehicle Manufacture Image</label
                >
                <br />
                <FileInputComponent
                  :isRequired="false"
                  id="vehicle_Manufacture_image"
                  :prvImage="vehicle_Manufacture_image"
                  v-model="form.vehicle_Manufacture_image"
                />
                <div class="text-danger">
                  {{ form.errors.vehicle_Manufacture_image }}
                </div>
              </div>
            </div>
            <div class="mt-2">
              <button
                type="submit"
                class="btn btn-main me-2"
                :disabled="form.processing"
              >
                {{ vehicle_manufacture ? "Update" : "Save" }}
              </button>
              <Link
                class="btn btn-outline-danger"
                :href="route('vehicle_manufacture.index')"
                >Cancel</Link
              >
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>


<script>
import { Link, useForm } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import FileInputComponent from "@/Components/FileInputComponent.vue";
import SelectInputComponentVue from '../../../Components/SelectInputComponent.vue';
// import SuspendedUserAlert from "./Partials/SuspendedUserAlert.vue";

export default {
  components: {
    Link,
    AppLayout,
    FileInputComponent,
    // SuspendedUserAlert,
    SelectInputComponentVue
  },

  props: {
    errors: Object,
    allRoles: Object,
    backendUser: Object,
    branches: Array,
    service_types: Object,
    vehicle_manufacture: Object,
  },

  data() {
    return {
      form: useForm({
        id: "",
        title: "",
        status: "",
        featured: "",
        vehicle_Manufacture_image: "",
      }),
      // password: useForm({
      //   id: "",
      //   confirm_password: "",
      //   password: "",
      //   password_confirmation: "",
      // }),
    };
  },
  mounted() {
    let self = this;

    if (this.vehicle_manufacture) {
      this.form.id = this.vehicle_manufacture.id;
      this.form.title = this.vehicle_manufacture.title;
      this.form.status = this.vehicle_manufacture.status;
      this.form.featured = this.vehicle_manufacture.featured;
    }
    // $(".card-body").on("change", "#role", function (evt) {
    //   self.form.role = $(evt.target).val();
    // });
    // $(".card-body").on("change", "#branch", function (evt) {
    //   self.form.branch = $(evt.target).val();
    // });
  },
  computed: {
    vehicle_Manufacture_image() {
      return this.vehicle_manufacture ? this.vehicle_manufacture.media.length > 0 ? this.vehicle_manufacture.media[0].original_url: "" : "";
    },
  },
  methods: {
    submit() {
      this.form.post(
        this.vehicle_manufacture
          ? route("vehicle_manufacture.update")
          : route("vehicle_manufacture.store"),
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
            this.form.reset("password", "password_confirmation");
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
