<template>
  <AppLayout>
    <div class="row">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h5>Subcategory</h5>
          <p>Manage Subcategories for a Category.</p>
        </div>
        <hr />
        <div class="card-body">
          <form @submit.prevent="submit">
            <div class="row">
              <SelectInputComponentVue
                id="category_id"
                label="Category"
                :error="form.errors.category_id"
                :options="categories"
                v-model="form.category_id"
              />

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
            </div>

            <hr class="my-3" />

            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="mb-0">Subcategory Set</h6>
              <button type="button" class="btn btn-sm btn-outline-primary" @click="addRow">
                + Add Subcategory
              </button>
            </div>

            <div v-for="(row, idx) in form.subcategories" :key="row._key" class="border rounded p-2 mb-2">
              <div class="row">
                <div class="mb-2 col-md-6">
                  <label class="form-label">Subcategory Name</label>
                  <input class="form-control" type="text" v-model="row.title" />
                  <div class="text-danger">{{ fieldError(idx, "title") }}</div>
                </div>

                <div class="mb-2 col-md-5">
                  <label class="form-label me-3">Subcategory Image (Optional)</label>
                  <br />
                  <FileInputComponent
                    :isRequired="false"
                    :id="'subcategory_image_' + idx"
                    :prvImage="row.image_url"
                    v-model="row.image"
                  />
                  <div class="text-danger">{{ fieldError(idx, "image") }}</div>
                </div>

                <div class="mb-2 col-md-1 d-flex align-items-end">
                  <button type="button" class="btn btn-outline-danger btn-sm w-100" @click="removeRow(idx)" :disabled="form.subcategories.length === 1">
                    Delete
                  </button>
                </div>
              </div>
            </div>

            <div class="mt-2">
              <button type="submit" class="btn btn-main me-2" :disabled="form.processing">
                {{ category ? "Update" : "Save" }}
              </button>
              <Link class="btn btn-outline-danger" :href="route('subcategory.index')">Cancel</Link>
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
    categories: Array,
    category: Object,      // selected category in edit mode (id,title) or null
    subcategories: Array,  // [{id,title,image_url}]
    status: [Number, String],
  },

  data() {
    return {
      form: useForm({
        category_id: "",
        status: "1",
        subcategories: [],
      }),
    };
  },

  mounted() {
    this.form.status = String(this.status ?? 1);

    if (this.category?.id) {
      this.form.category_id = String(this.category.id);
    }

    if (Array.isArray(this.subcategories) && this.subcategories.length) {
      this.form.subcategories = this.subcategories.map((s) => ({
        id: s.id,
        title: s.title || "",
        image_url: s.image_url || "",
        image: null,
        _key: "sub_" + (s.id || Math.random()),
      }));
    } else {
      this.form.subcategories = [this.newRow()];
    }
  },

  methods: {
    newRow() {
      return {
        id: null,
        title: "",
        image_url: "",
        image: null,
        _key: "new_" + Math.random().toString(36).slice(2),
      };
    },
    addRow() {
      this.form.subcategories.push(this.newRow());
    },
    removeRow(idx) {
      if (this.form.subcategories.length <= 1) return;
      this.form.subcategories.splice(idx, 1);
    },
    fieldError(idx, field) {
      return this.form.errors?.[`subcategories.${idx}.${field}`] || "";
    },
    submit() {
      const routeName = this.category ? "subcategory.update" : "subcategory.store";

      this.form.post(route(routeName), {
        forceFormData: true, // IMPORTANT for nested files
        onSuccess: () => {
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
