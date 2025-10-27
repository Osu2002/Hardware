<template>
  <AppLayout>
    <div class="row">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h5>Home Banner</h5>
          <p>Manage home banner.</p>
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
                  id="name"
                  name="Name"
                  v-model="form.name"
                />
                <div class="text-danger">
                  {{ form.errors.name }}
                </div>
              </div>

              <!-- TinyMCE Editor -->
              <div>
                <label for="description" class="form-label">Description</label>
                <Editor
                  v-model="form.editorContent"
                  :api-key="apiKey"
                  :init="{
                    plugins: 'lists link image table code help wordcount',
                  }"
                />
              </div>

              <div class="mb-3 col-md-6">
                <label for="image" class="form-label me-3">Image</label>
                <br />
                <FileInputComponent
                  :isRequired="false"
                  id="image"
                  :prvImage="image"
                  v-model="form.image"
                />
                <div class="text-danger">
                  {{ form.errors.image }}
                </div>

                <!-- <div class="multi-file-upload">
                  <label for="" class="form-label">UPLOADED IMAGES</label>
                  <div
                    class="drop-area col-xl-4 col-md-4"
                    @dragover="handleDragOver"
                    @drop="handleDrop"
                  >
                    <div class="row">
                      <div
                        v-for="(image, index) in form.galery_images"
                        :key="index"
                        class="uploaded-image"
                      >
                        <img
                          style="width: 80px; height: 80px; object-fit: cover"
                          :src="image.previewURL"
                          alt="Uploaded Image"
                          class="img-thumbnail"
                        />
                        <button
                          type="button"
                          class="close-icon"
                          @click="removeImage(index)"
                        >
                          <i class="fa fa-times" style="font-size: 15px"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div> -->
                <!-- <div class="text-danger">
                  {{ form.errors.images }}
                </div> -->
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
            <div class="mt-2">
              <button
                type="submit"
                class="btn btn-main me-2"
                :disabled="form.processing"
              >
                {{ homebanner ? "Update" : "Save" }}
              </button>
              <Link
                class="btn btn-outline-danger"
                :href="route('homebanner.index')"
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
import SelectInputComponentVue from "../../../Components/SelectInputComponent.vue";
import Editor from "@tinymce/tinymce-vue";

// import SuspendedUserAlert from "./Partials/SuspendedUserAlert.vue";

export default {
  components: {
    Link,
    AppLayout,
    Editor,
    FileInputComponent,
    // SuspendedUserAlert,
    SelectInputComponentVue,
  },

  props: {
    errors: Object,
    allRoles: Object,
    backendUser: Object,
    branches: Array,
    service_types: Object,
    vehicle_manufacture: Object,
    testimonial: Object,
    homebanner: Object,
  },

  data() {
    return {
      apiKey:import.meta.env.VITE_TINYMCE_API_KEY,
      form: useForm({
        id: "",
        name: "",
        status: "",
        image: "",
        editorContent: "",
        // galery_images: [],
        // removed_images: [],
        // selected_images: [],
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

    if (this.homebanner) {
      this.form.id = this.homebanner.id;
      this.form.name = this.homebanner.name;
      this.form.status = this.homebanner.status;
      this.form.editorContent = this.homebanner.editorContent;
    }

    var imgs = this.branch?.media.filter(
      (element) => element.custom_properties.type == "galery"
    );

    if (imgs && imgs.length > 0) {
      imgs.forEach((element) => {
        this.form.images.push(element);
      });
      this.form.galery_images =
        imgs.map((image) => ({
          previewURL: image.original_url,
        })) || [];
    }
    // $(".card-body").on("change", "#role", function (evt) {
    //   self.form.role = $(evt.target).val();
    // });
    // $(".card-body").on("change", "#branch", function (evt) {
    //   self.form.branch = $(evt.target).val();
    // });
  },
  computed: {
    image() {
      return this.homebanner ? this.homebanner.media.length > 0 ? this.homebanner.media[0].original_url : "" : "";
    },
  },
  methods: {
    submit() {
      this.form.post(
        this.homebanner
          ? route("homebanner.update")
          : route("homebanner.store"),
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
  handleDragOver(event) {
    event.preventDefault();
  },
  handleDrop(event) {
    event.preventDefault();
    const files = event.dataTransfer.files;
    console.log("Dropped files:", files);
    for (let i = 0; i < files.length; i++) {
      const file = files[i];
      file.previewURL = URL.createObjectURL(file);
      this.form.images.push(files[i]);
    }
  },
  removeImage(index) {
    this.form.removed_images.push(this.form.images[index]);
    this.form.galery_images.splice(index, 1)[0];
  },
};
</script>
<style scoped>
.uploaded-image {
  position: relative;
  width: 100px;
  object-fit: cover;
  padding-right: 5px;
  padding-bottom: 5px;
}

.close-icon {
  position: absolute;
  top: 1px;
  right: 13px;
  background: transparent;
  color: white;
  border: 0;
  background: red;
  padding-left: 8px;
  padding-right: 8px;
  border-radius: 20px;
}
</style>