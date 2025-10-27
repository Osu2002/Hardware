<template>
  <AppLayout>
    <div class="row">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h5>Countries</h5>
          <p>Manage Countries.</p>
        </div>
        <hr />
        <div class="card-body">
          <form id="formAccountSettings" @submit.prevent="submit">
            <div class="row">
              <!-- Country Details Section -->
              <div class="col-12 mb-4">
                <h6>Country Details</h6>
                <div class="row">
                  <!-- Country Name -->
                  <div class="mb-3 col-md-6">
                    <label for="name" class="form-label">Country Name</label>
                    <input
                      class="form-control"
                      type="text"
                      id="name"
                      name="name"
                      v-model="form.name"
                    />
                    <div class="text-danger">{{ form.errors.name }}</div>
                  </div>

                  <!-- Country slug -->
                  <div class="mb-3 col-md-6">
                    <label for="slug" class="form-label">Slug</label>
                    <input
                      class="form-control"
                      type="text"
                      id="slug"
                      name="slug"
                      v-model="form.slug"
                    />
                    <div class="text-danger">{{ form.errors.slug }}</div>
                  </div>

                  <!-- Status Dropdown -->
                  <SelectInputComponentVue
                    id="status"
                    label="Status"
                    :error="form.errors.status"
                    :options="[
                      { id: '1', name: 'Active' },
                      { id: '0', name: 'Inactive' }
                    ]"
                    v-model="form.status"
                  />

                  <!-- Flag Image Input -->
                  <div class="mb-3 col-md-6">
                    <label for="flag_Image" class="form-label me-3">Flag</label>
                    <br />
                    <FileInputComponent
                      :isRequired="false"
                      id="flag_Image"
                      :prvImage="Flag_Image"
                      v-model="form.flag_Image"
                    />
                    <div class="text-danger">{{ form.errors.flag_Image }}</div>
                  </div>
                </div>
              </div>

              <!-- Contact Information Section -->
              <div class="col-12 mb-4">
                <h6>Contact Information</h6>
                <div class="row">
                  <div class="mb-3 col-md-6">
                    <label for="phone1" class="form-label">Primary Phone Number</label>
                    <input
                      class="form-control"
                      type="tel"
                      id="phone1"
                      name="phone1"
                      v-model="form.phone1"
                      required
                    />
                    <div class="text-danger">{{ form.errors.phone1 }}</div>
                  </div>

                  <div class="mb-3 col-md-6">
                    <label for="phone2" class="form-label">Secondary Phone Number (Optional)</label>
                    <input
                      class="form-control"
                      type="tel"
                      id="phone2"
                      name="phone2"
                      v-model="form.phone2"
                    />
                    <div class="text-danger">{{ form.errors.phone2 }}</div>
                  </div>

                  <!-- Location -->
                  <div class="mb-3 col-md-6">
                    <label for="location" class="form-label">Location</label>
                    <input
                      class="form-control"
                      type="text"
                      id="location"
                      name="location"
                      v-model="form.location"
                      required
                    />
                    <div class="text-danger">{{ form.errors.location }}</div>
                  </div>

                  <!-- Email  -->
                  <div class="mb-3 col-md-6">
                    <label for="email" class="form-label">Email (Optional)</label>
                    <input
                      class="form-control"
                      type="email"
                      id="email"
                      name="email"
                      v-model="form.email"
                    />
                    <div class="text-danger">{{ form.errors.email }}</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-2">
              <button
                type="submit"
                class="btn btn-main me-2"
                :disabled="form.processing"
              >
                {{ country ? "Update" : "Save" }}
              </button>
              <Link class="btn btn-outline-danger" :href="route('country.index')">
                Cancel
              </Link>
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
import SelectInputComponentVue from "../../Components/SelectInputComponent.vue";
import axios from "axios";

export default {
components: {
  Link,
  AppLayout,
  FileInputComponent,
  SelectInputComponentVue,
},
props: {
  errors: Object,
  allRoles: Object,
  backendUser: Object,
  branches: Array,
  service_types: Object,
  vehicle_types: Object,
  country: Object,
},
data() {
  return {
    form: useForm({
      id: "",
      name: "",
      code: "",
      slug: "",
      status: "",
      flag_Image: "",
      phone1: "",
      phone2: "",
      location: "",
      email: "",
    }),
  };
},
mounted() {
  let self = this;

  if (this.country) {
    this.form.id = this.country.id;
    this.form.name = this.country.name;
    this.form.slug = this.country.slug;
    this.form.code = this.country.code;
    this.form.status = this.country.status;
    this.form.phone1 = this.country.phone1 ;
    this.form.phone2 = this.country.phone2 ;
    this.form.location = this.country.location ;
    this.form.email = this.country.email;
  }

  $(".card-body").on("keyup", "#name", function (evt) {
    self.form.slug = self.sanitizeTitle($(evt.target).val());
  });
},
computed: {
  Flag_Image() {
    return this.country
      ? this.country.media.length > 0
        ? this.country.media[0].original_url
        : ""
      : "";
  },
},
methods: {
  sanitizeTitle(name) {
    return name.toLowerCase().replace(/\s*$/g, "").replace(/\s+/g, "-");
  },
  submit() {
    this.form.post(
      this.country
        ? route("country.update", this.country.id)
        : route("country.store"),
      {
        onSuccess: () => {
          this.form.reset();
          this.$root.showMessage(
            "success",
            '<span class="text-success">Success</span><br/>',
            "A Record Was Created Successfully!"
          );
        },
        onError: () => {
          this.$root.showMessage(
            "error",
            '<span class="text-danger">Error</span><br>',
            "Something went wrong!"
          );
        },
      }
    );
  },
},
};
</script>