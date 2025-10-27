<template>
  <AppLayout>
    <div class="row">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h5>News Letter</h5>
          <p>Manage news letter.</p>
        </div>
        <hr />
        <div class="card-body">
          <form id="formAccountSettings" @submit.prevent="submit">
            <div class="row">
              <div class="mb-3 col-md-6">
                <label for="name" class="form-label">User Name</label>
                <input
                  class="form-control"
                  type="text"
                  id="name"
                  name="name"
                  v-model="form.name"
                />
                <div class="text-danger">
                  {{ form.errors.name }}
                </div>
              </div>

              <div class="mb-3 col-md-6">
                <label for="email" class="form-label">Email</label>
                <input
                  class="form-control"
                  type="email"
                  id="email"
                  name="email"
                  v-model="form.email"
                />
                <div class="text-danger">
                  {{ form.errors.email }}
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
               


  
          
            </div>
            <div class="mt-2">
              <button
                type="submit"
                class="btn btn-main me-2"
                :disabled="form.processing"
              >
                {{ newsletter ? "Update" : "Save" }}
              </button>
              <Link
                class="btn btn-outline-danger"
                :href="route('newsletter.index')"
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
    newsletter: Object,
  },

  data() {
    return {
      form: useForm({
        id: "",
        name: "",
        status: "",
        email: "",
        
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

    if (this.newsletter) {
      this.form.id = this.newsletter.id;
      this.form.name = this.newsletter.name;
      this.form.status = this.newsletter.status;
      this.form.email = this.newsletter.email;
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
        this.newsletter
          ? route("newsletter.update")
          : route("newsletter.store"),
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
