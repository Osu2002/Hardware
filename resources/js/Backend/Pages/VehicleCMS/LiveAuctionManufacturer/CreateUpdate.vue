<template>
  <AppLayout>
    <div class="row">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h5>Vehicle Manufacture (Live Auction)</h5>
          <p>Manage Vehicle Manufacture.</p>
        </div>
        <hr />
        <div class="card-body">
          <form id="formAccountSettings" @submit.prevent="submit">
            <div class="row">
              
              <SelectInputComponentVue
                  id="name"
                  label="Name"
                  :error="form.errors.name"
                  :options="manufactures"
                  v-model="form.name"
                />

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

              <div class="mb-3 col-md-6">
                <label for="vehicleType_image" class="form-label me-3"
                  >Vehicle Manufacture Image</label
                >
                <br />
                <FileInputComponent
                  :isRequired="false"
                  id="image"
                  :prvImage="Image"
                  v-model="form.image"
                />
                <div class="text-danger">
                  {{ form.errors.image }}
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
                :href="route('vehicle_manufacture.live.index')"
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
    vehicle_manufacture: Object,
    manufactures: Object,
  },

  data() {
    return {
      form: useForm({
        id: "",
        name: "",
        status: "",
        image: "",
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
      this.form.name = this.vehicle_manufacture.name;
      this.form.status = this.vehicle_manufacture.status;
    }
    // $(".card-body").on("change", "#role", function (evt) {
    //   self.form.role = $(evt.target).val();
    // });
    // $(".card-body").on("change", "#branch", function (evt) {
    //   self.form.branch = $(evt.target).val();
    // });
  },
  computed: {
    Image() {
      return this.vehicle_manufacture ? this.vehicle_manufacture.media.length > 0 ? this.vehicle_manufacture.media[0].original_url: "" : "";
    },
  },
  methods: {
    submit() {
      this.form.post(
        this.vehicle_manufacture
          ? route("vehicle_manufacture.live.update")
          : route("vehicle_manufacture.live.store"),
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
