<template>
  <AppLayout>
    <div class="row">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h5>Brand</h5>
          <p>Create / Update Brand.</p>
        </div>
        <hr />
        <div class="card-body">
          <form id="formBrand" @submit.prevent="submit">
            <div class="row">
              <div class="mb-3 col-md-6">
                <label for="title" class="form-label">Brand Name</label>
                <input class="form-control" type="text" id="title" v-model="form.title" />
                <div class="text-danger">{{ form.errors.title }}</div>
              </div>

              <div class="mb-3 col-md-6">
                <label for="slug" class="form-label">Slug</label>
                <input class="form-control" type="text" id="slug" v-model="form.slug" placeholder="e.g. bosch" />
                <div class="text-danger">{{ form.errors.slug }}</div>
              </div>

              <SelectInputComponentVue
                id="status"
                label="Status"
                :error="form.errors.status"
                :options="[{ id: '1', name: 'Active' }, { id: '0', name: 'Inactive' }]"
                v-model="form.status"
              />

              <SelectInputComponentVue
                id="featured"
                label="Featured"
                :error="form.errors.featured"
                :options="[{ id: '1', name: 'Yes' }, { id: '0', name: 'No' }]"
                v-model="form.featured"
              />

              <div class="mb-3 col-md-6">
                <label for="website_url" class="form-label">Website URL</label>
                <input class="form-control" type="url" id="website_url" v-model="form.website_url" placeholder="https://example.com" />
                <div class="text-danger">{{ form.errors.website_url }}</div>
              </div>

              <div class="mb-3 col-md-6">
                <label for="support_email" class="form-label">Support Email</label>
                <input class="form-control" type="email" id="support_email" v-model="form.support_email" />
                <div class="text-danger">{{ form.errors.support_email }}</div>
              </div>

              <div class="mb-3 col-md-6">
                <label for="hotline_phone" class="form-label">Hotline Phone</label>
                <input class="form-control" type="text" id="hotline_phone" v-model="form.hotline_phone" />
                <div class="text-danger">{{ form.errors.hotline_phone }}</div>
              </div>

              <div class="mb-3 col-md-4">
                <label for="country" class="form-label">Country</label>
                <input class="form-control" type="text" id="country" v-model="form.country" placeholder="e.g. Germany" />
                <div class="text-danger">{{ form.errors.country }}</div>
              </div>

              <div class="mb-3 col-md-2">
                <label for="founded_year" class="form-label">Founded Year</label>
                <input class="form-control" type="number" min="1800" max="2100" id="founded_year" v-model="form.founded_year" />
                <div class="text-danger">{{ form.errors.founded_year }}</div>
              </div>

              <div class="mb-3 col-md-6">
                <label for="short_description" class="form-label">Short Description</label>
                <textarea id="short_description" class="form-control" rows="2" v-model="form.short_description"></textarea>
                <div class="text-danger">{{ form.errors.short_description }}</div>
              </div>

              <div class="mb-3 col-md-6">
                <label for="long_description" class="form-label">Long Description</label>
                <textarea id="long_description" class="form-control" rows="5" v-model="form.long_description"></textarea>
                <div class="text-danger">{{ form.errors.long_description }}</div>
              </div>

              <div class="mb-3 col-md-6">
                <label for="seo_title" class="form-label">SEO Title</label>
                <input class="form-control" type="text" id="seo_title" v-model="form.seo_title" />
                <div class="text-danger">{{ form.errors.seo_title }}</div>
              </div>

              <div class="mb-3 col-md-6">
                <label for="seo_description" class="form-label">SEO Description</label>
                <textarea id="seo_description" class="form-control" rows="2" v-model="form.seo_description"></textarea>
                <div class="text-danger">{{ form.errors.seo_description }}</div>
              </div>

              <div class="mb-3 col-md-2">
                <label for="sort_order" class="form-label">Sort Order</label>
                <input class="form-control" type="number" id="sort_order" v-model="form.sort_order" />
                <div class="text-danger">{{ form.errors.sort_order }}</div>
              </div>

              <div class="mb-3 col-md-5">
                <label for="brand_logo" class="form-label me-3">Brand Logo</label><br />
                <FileInputComponent :isRequired="false" id="brand_logo" :prvImage="brand_logo_preview" v-model="form.brand_logo" />
                <div class="text-danger">{{ form.errors.brand_logo }}</div>
              </div>

              <div class="mb-3 col-md-5">
                <label for="brand_banner" class="form-label me-3">Brand Banner (optional)</label><br />
                <FileInputComponent :isRequired="false" id="brand_banner" :prvImage="brand_banner_preview" v-model="form.brand_banner" />
                <div class="text-danger">{{ form.errors.brand_banner }}</div>
              </div>
            </div>

            <div class="mt-2">
              <button type="submit" class="btn btn-main me-2" :disabled="form.processing">
                {{ brand ? "Update" : "Save" }}
              </button>
              <Link class="btn btn-outline-danger" :href="route('brand.index')">Cancel</Link>
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

export default {
  components: { Link, AppLayout, FileInputComponent, SelectInputComponentVue },
  props: { errors: Object, brand: Object },
  data() {
    return {
      form: useForm({
        id: "",
        title: "",
        slug: "",
        status: "1",
        featured: "0",
        website_url: "",
        hotline_phone: "",
        support_email: "",
        country: "",
        founded_year: "",
        short_description: "",
        long_description: "",
        seo_title: "",
        seo_description: "",
        sort_order: 0,
        brand_logo: "",
        brand_banner: "",
      }),
    };
  },
  mounted() {
    if (this.brand) {
      this.form.id = this.brand.id;
      this.form.title = this.brand.title ?? "";
      this.form.slug = this.brand.slug ?? "";
      this.form.status = String(this.brand.status ?? "1");
      this.form.featured = String(this.brand.featured ?? "0");
      this.form.website_url = this.brand.website_url ?? "";
      this.form.hotline_phone = this.brand.hotline_phone ?? "";
      this.form.support_email = this.brand.support_email ?? "";
      this.form.country = this.brand.country ?? "";
      this.form.founded_year = this.brand.founded_year ?? "";
      this.form.short_description = this.brand.short_description ?? "";
      this.form.long_description = this.brand.long_description ?? "";
      this.form.seo_title = this.brand.seo_title ?? "";
      this.form.seo_description = this.brand.seo_description ?? "";
      this.form.sort_order = this.brand.sort_order ?? 0;
    }
  },
  computed: {
    brand_logo_preview() {
      if (!this.brand || !this.brand.media) return "";
      const m = Array.isArray(this.brand.media) ? this.brand.media : [];
      const logo = m.find(x => (x.collection_name || "").includes("brand_logo")) || m[0];
      return logo ? (logo.original_url || logo.url || "") : "";
    },
    brand_banner_preview() {
      if (!this.brand || !this.brand.media) return "";
      const m = Array.isArray(this.brand.media) ? this.brand.media : [];
      const banner = m.find(x => (x.collection_name || "").includes("brand_banner"));
      return banner ? (banner.original_url || banner.url || "") : "";
    },
  },
  methods: {
    submit() {
      this.form.post(this.brand ? route("brand.update") : route("brand.store"), {
        onSuccess: () => { this.form.reset(); this.$root.showMessage("success", '<span class="text-success">Success</span><br/>', "Brand saved!"); },
        onError: () => { this.$root.showMessage("error", '<span class="text-danger">Error</span><br>', "Something went wrong!"); },
      });
    },
  },
};
</script>
