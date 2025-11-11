<template>
  <AppLayout>
    <div class="row">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h5>Category</h5>
          <p>Manage Categories.</p>
        </div>
        <hr />
        <div class="card-body">
          <form id="formAccountSettings" @submit.prevent="submit">
            <div class="row">
              <div class="mb-3 col-md-6">
                <label for="title" class="form-label">Title</label>
                <input class="form-control" type="text" id="title" name="title" v-model="form.title" />
                <div class="text-danger">{{ form.errors.title }}</div>
              </div>

              <SelectInputComponentVue
                id="status"
                label="Status"
                :error="form.errors.status"
                :options="[
                  { id: '1', name: 'Active' },
                  { id: '0', name: 'Inactive' },
                ]"
                v-model="form.status"
              />

              <SelectInputComponentVue
                id="featured"
                label="Featured"
                :error="form.errors.featured"
                :options="[
                  { id: '1', name: 'Yes' },
                  { id: '0', name: 'No' },
                ]"
                v-model="form.featured"
              />

              <div class="mb-3 col-md-6">
                <label for="vehicle_type_image" class="form-label me-3">Category Image</label>
                <br />
                <!-- Keep SAME binding & id to preserve image field/collection -->
                <FileInputComponent
                  :isRequired="false"
                  id="vehicle_type_image"
                  :prvImage="vehicle_type_image"
                  v-model="form.vehicle_type_image"
                />
                <div class="text-danger">{{ form.errors.vehicle_type_image }}</div>
              </div>
            </div>

            <div class="mt-2">
              <button type="submit" class="btn btn-main me-2" :disabled="form.processing">
                {{ category ? "Update" : "Save" }}
              </button>
              <Link class="btn btn-outline-danger" :href="route('category.index')">Cancel</Link>
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
import SelectInputComponentVue from "@/Components/SelectInputComponent.vue";

export default {
  components: { Link, AppLayout, FileInputComponent, SelectInputComponentVue },

  props: {
    errors: Object,
    category: Object, // renamed from vehicle_types → category
  },

  data() {
    return {
      form: useForm({
        id: "",
        title: "",
        status: "",
        featured: "",
        // Keep SAME request key to avoid changing server/media handling:
        vehicle_type_image: "",
      }),
    };
  },

  mounted() {
    if (this.category) {
      this.form.id = this.category.id;
      this.form.title = this.category.title;
      this.form.featured = this.category.featured;
      this.form.status = this.category.status;
    }
  },

  computed: {
    // Keep SAME name for preview to reuse existing media collection/path
    vehicle_type_image() {
      return this.category ? (this.category.media.length > 0 ? this.category.media[0].original_url : "") : "";
    },
  },

  methods: {
    submit() {
      this.form.post(this.category ? route("category.update") : route("category.store"), {
        onSuccess: () => {
          this.form.reset();
          this.$root.showMessage("success", '<span class="text-success">Success</span><br/>', "Saved successfully!");
        },
        onError: () => {
          this.$root.showMessage("error", '<span class="text-danger">Error</span><br>', "Something went wrong!");
        },
      });
    },
  },
};
</script>
