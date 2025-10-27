<template>
  <AppLayout>
    <div class="row">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h5>Advertisements</h5>
          <p>Manage Advertisements.</p>
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
              <div class="col-md-12">

                 <!-- TinyMCE Editor -->
                 <label for="" class="form-label me-3"
                  >Description</label>
              <div>
                <Editor
                  v-model="form.editorContent"
                  :api-key="apiKey"
                  :init="{
                    plugins: 'lists link image table code help wordcount',
                  }"
                />
              </div>
              </div>


              <div class="mb-3 col-md-6 mt-3">
                <label for="vehicleType_image" class="form-label me-3"
                  >Upload Image</label
                >
                <br />
                <FileInputComponent
                  :isRequired="false"
                  id="advertisement_image"
                  :prvImage="advertisement_image"
                  v-model="form.advertisement_image"
                />
                <div class="text-danger">
                  {{ form.errors.advertisement_image }}
                </div>
              </div>
            </div>
            <div class="mt-2">
              <button
                type="submit"
                class="btn btn-main me-2"
                :disabled="form.processing"
              >
                {{ advertisements ? "Update" : "Save" }}
              </button>
              <Link
                class="btn btn-outline-danger"
                :href="route('advertisements.index')"
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
import Editor from "@tinymce/tinymce-vue";
// import SuspendedUserAlert from "./Partials/SuspendedUserAlert.vue";

export default {
  components: {
    Link,
    AppLayout,
    FileInputComponent,
    Editor,
    // SuspendedUserAlert,
    SelectInputComponentVue
  },

  props: {
    errors: Object,
    allRoles: Object,
    backendUser: Object,
    branches: Array,
    service_types: Object,
    advertisements: Object,
  },

  data() {
    return {
      apiKey:import.meta.env.VITE_TINYMCE_API_KEY,
      form: useForm({
        id: "",
        title: "",
        status: "",
        editorContent:"",
        advertisement_image: "",
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

    if (this.advertisements) {
      this.form.id = this.advertisements.id;
      this.form.title = this.advertisements.title;
      this.form.status = this.advertisements.status;
      this.form.editorContent = this.advertisements.editorContent;
      
    }
    // $(".card-body").on("change", "#role", function (evt) {
    //   self.form.role = $(evt.target).val();
    // });
    // $(".card-body").on("change", "#branch", function (evt) {
    //   self.form.branch = $(evt.target).val();
    // });
  },
  computed: {
    advertisement_image() {
      return this.advertisements ? this.advertisements.media.length > 0 ? this.advertisements.media[0].original_url: "" : "";
    },
  },
  methods: {
    submit() {
      this.form.post(
        this.advertisements
          ? route("advertisements.update")
          : route("advertisements.store"),
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
