<template>
  <AppLayout>
    <div class="row">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h5>Vehicle Types</h5>
          <p>Manage Vehicle Types.</p>
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
                  label="Status"
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
                  label="Featured"
                  :error="form.errors.featured"
                  :options="[
                        {
                          id: '1',
                          name: 'Yes',
                        },
                        {
                          id: '0',
                          name: 'No',
                        },
                      ]"
                  v-model="form.featured"
                />


              <div class="mb-3 col-md-6">
                <label for="vehicleType_image" class="form-label me-3"
                  >VehicleType Image</label
                >
                <br />
                <FileInputComponent
                  :isRequired="false"
                  id="vehicle_type_image"
                  :prvImage="vehicle_type_image"
                  v-model="form.vehicle_type_image"
                />
                <div class="text-danger">
                  {{ form.errors.vehicle_type_image }}
                </div>
              </div>
            </div>
            <div class="mt-2">
              <button
                type="submit"
                class="btn btn-main me-2"
                :disabled="form.processing"
              >
                {{ vehicle_types ? "Update" : "Save" }}
              </button>
              <Link
                class="btn btn-outline-danger"
                :href="route('vehicle.type.index')"
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
    vehicle_types: Object,
  },

  data() {
    return {
      form: useForm({
        id: "",
        title: "",
        status: "",
        featured:"",
        vehicle_type_image: "",
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

    if (this.vehicle_types) {
      this.form.id = this.vehicle_types.id;
      this.form.title = this.vehicle_types.title;
      this.form.featured = this.vehicle_types.featured;
      this.form.status = this.vehicle_types.status;
    }
    // $(".card-body").on("change", "#role", function (evt) {
    //   self.form.role = $(evt.target).val();
    // });
    // $(".card-body").on("change", "#branch", function (evt) {
    //   self.form.branch = $(evt.target).val();
    // });
  },
  computed: {
    vehicle_type_image() {
      return this.vehicle_types ? this.vehicle_types.media.length > 0 ? this.vehicle_types.media[0].original_url: "" : "";
    },
  },
  methods: {
    submit() {
      this.form.post(
        this.vehicle_types
          ? route("vehicle_type.update")
          : route("vehicle_type.store"),
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
