<template>
  <AppLayout>
    <div class="row">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h5>Product</h5>
          <p>Create / Update a product.</p>
        </div>
        <hr />
        
        <div class="card-body">
          <form @submit.prevent="submit">
            <div class="row">
              <div class="mb-3 col-md-6">
                <label class="form-label">Name</label>
                <input class="form-control" v-model="form.name" />
                <div class="text-danger">{{ form.errors.name }}</div>
              </div>
              <div class="mb-3 col-md-3">
                <label class="form-label">SKU</label>
                <input class="form-control" v-model="form.sku" />
                <div class="text-danger">{{ form.errors.sku }}</div>
              </div>
              <SelectInputComponent
                class="mb-3 col-md-3"
                id="status"
                label="Status"
                :options="[{id:'1',name:'Active'},{id:'0',name:'Inactive'}]"
                :error="form.errors.status"
                v-model="form.status"
              />
            </div>

            <div class="row">
              <SelectInputComponent
                class="mb-3 col-md-4"
                id="brand"
                label="Brand"
                :options="brands"
                option-label="name"
                option-value="id"
                v-model="form.brand_id"
              />
              <SelectInputComponent
                class="mb-3 col-md-4"
                id="uom"
                label="Unit of Measure"
                :options="uoms"
                option-label="name"
                option-value="id"
                v-model="form.uom_id"
              />
              <SelectInputComponent
                class="mb-3 col-md-4"
                id="attribute_set"
                label="Attribute Set"
                :options="attributeSets"
                option-label="name"
                option-value="id"
                v-model="form.attribute_set_id"
              />
            </div>

            <div class="row">
              <SelectInputComponent
                class="mb-3 col-md-6"
                id="primary_category_id"
                label="Primary Category (optional)"
                :options="categories"
                option-label="title"
                option-value="id"
                v-model="form.primary_category_id"
              />
              <div class="mb-3 col-md-6">
                <label class="form-label">Categories</label>
                <select class="form-select" v-model="form.categories" multiple size="6">
                  <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.title }}</option>
                </select>
                <div class="text-muted small">Hold Ctrl / Cmd to multi-select</div>
              </div>
            </div>

            <div class="row">
              <div class="mb-3 col-md-3">
                <label class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" v-model="form.price" />
              </div>
              <div class="mb-3 col-md-3">
                <label class="form-label">Sale Price</label>
                <input type="number" step="0.01" class="form-control" v-model="form.sale_price" />
              </div>
              <div class="mb-3 col-md-2">
                <label class="form-label">Sort</label>
                <input type="number" class="form-control" v-model="form.sort_order" />
              </div>
            </div>

             <div class="mb-3 col-md-4">
    <label class="form-label d-block">Apply Discount</label>
    <div class="form-check form-check-inline">
      <input
        class="form-check-input"
        type="radio"
        id="discount_no"
        value="0"
        v-model="form.discount_status"
      />
      <label class="form-check-label" for="discount_no">No</label>
    </div>
    <div class="form-check form-check-inline">
      <input
        class="form-check-input"
        type="radio"
        id="discount_yes"
        value="1"
        v-model="form.discount_status"
      />
      <label class="form-check-label" for="discount_yes">Yes</label>
    </div>
    <div class="text-danger">{{ form.errors.discount_status }}</div>

    <!-- Discount fields appear only if Apply Discount = Yes -->
    <div v-if="form.discount_status === '1'" class="mt-2">
      <label class="form-label">Discount</label>
      <div class="input-group">
        <select class="form-select" v-model="form.discount_type">
          <option value="">-- Select Type --</option>
          <option value="percent">Percentage (%)</option>
          <option value="amount">Amount (LKR)</option>
        </select>
        <input
          type="number"
          step="0.01"
          class="form-control"
          v-model="form.discounted_amount"
        />
      </div>
      <div class="text-danger">
        {{ form.errors.discount_type || form.errors.discounted_amount }}
      </div>
      <div class="form-text" v-if="form.discount_type === 'percent'">
        Enter discount percentage (0–100).
      </div>
      <div class="form-text" v-else-if="form.discount_type === 'amount'">
        Enter discount amount in LKR.
      </div>
    </div>
  </div>

            <div class="row">
              <div class="mb-3 col-md-6">
                <label class="form-label">Short Description</label>
                <textarea class="form-control" rows="3" v-model="form.short_description"></textarea>
              </div>
              <div class="mb-3 col-md-6">
                <label class="form-label">Description</label>
                <textarea class="form-control" rows="6" v-model="form.description"></textarea>
              </div>
            </div>

            <!-- Dynamic Attributes -->
            <div v-if="attributes && attributes.length" class="row mt-2">
              <div class="col-12">
                <h6 class="mb-2">Attributes</h6>
              </div>
              <div class="col-md-4 mb-3" v-for="a in attributes" :key="a.id">
                <label class="form-label">
                  {{ a.name }}
                  <span v-if="a.unit" class="text-muted">({{ a.unit }})</span>
                </label>
                <input v-if="['text','number','color'].includes(a.type)" class="form-control" v-model="attrValue[a.id]" :type="a.type === 'number' ? 'number' : 'text'">
                <select v-else-if="a.type==='boolean'" class="form-select" v-model="attrValue[a.id]">
                  <option :value="''">--</option>
                  <option value="1">Yes</option>
                  <option value="0">No</option>
                </select>
                <!-- for select type you'd render options if you keep them -->
              </div>
            </div>

            <!-- Images -->
            <div class="row mt-2">
              <div class="mb-3 col-md-12">
                <label class="form-label me-3">Images</label>
                <input type="file" class="form-control" multiple @change="onFiles">
                <div class="mt-2 d-flex gap-2 flex-wrap">
                  <img v-for="(url,i) in previews" :key="i" :src="url" style="height:70px;border:1px solid #eee;padding:2px;border-radius:6px;" />
                </div>
              </div>
            </div>

            <div class="mt-3">
              <button type="submit" class="btn btn-main me-2" :disabled="form.processing">
                {{ product ? "Update" : "Save" }}
              </button>
              <Link class="btn btn-outline-danger" :href="route('product.index')">Cancel</Link>
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
import SelectInputComponent from "@/Components/SelectInputComponent.vue";

export default {
  components: { Link, AppLayout, SelectInputComponent },
  props: {
    product: Object,
    brands: Array,
    uoms: Array,
    categories: Array,
    attributeSets: Array,
    attributes: Array,    // [{id,code,name,type,unit}]
    images: Array,        // existing urls
  },
  data() {
    return {
      form: useForm({
        id: "",
        name: "",
        sku: "",
        status: "1",
        sort_order: 0,
        brand_id: "",
        uom_id: "",
        attribute_set_id: "",
        primary_category_id: "",
        categories: [],
        price: "",
        sale_price: "",
        short_description: "",
        description: "",
        attributes_map: [], // [{attribute_id,value}]
        images: [],
        discount_status: "0",       
        discount_type: "",         
        discounted_amount: "",  
      }),
      attrValue: {},    // attribute_id => value
      previews: this.images || [],
    };
  },
  mounted() {
    if (this.product) {
      const p = this.product;
      this.form.id = p.id;
      this.form.name = p.name ?? "";
      this.form.sku = p.sku ?? "";
      this.form.status = String(p.status ?? "1");
      this.form.sort_order = p.sort_order ?? 0;
      this.form.brand_id = p.brand_id ?? "";
      this.form.uom_id = p.uom_id ?? "";
      this.form.attribute_set_id = p.attribute_set_id ?? "";
      this.form.primary_category_id = p.primary_category_id ?? "";
      this.form.categories = (p.categories || []).map(c => c.id);
      this.form.price = p.price ?? "";
      this.form.sale_price = p.sale_price ?? "";
      this.form.short_description = p.short_description ?? "";
      this.form.description = p.description ?? "";
      this.form.discount_status = String(p.discount_status ?? "0");
      this.form.discount_type = p.discount_type ?? "";
      this.form.discounted_amount = p.discounted_amount ?? "";

      // existing attribute values
      (p.attribute_values || p.attributeValues || []).forEach(row => {
        this.attrValue[row.attribute_id] = row.value ?? "";
      });
    }
  },
  methods: {
    onFiles(e) {
      const files = Array.from(e.target.files || []);
      this.form.images = files;
      // previews
      this.previews = files.map(f => URL.createObjectURL(f));
    },
    submit() {
      // pack attributes
      this.form.attributes_map = Object.keys(this.attrValue).map(id => ({
        attribute_id: Number(id),
        value: this.attrValue[id],
      }));

      const url = this.product ? route("product.update") : route("product.store");
      this.form.post(url, {
        forceFormData: true, // to allow files
        onSuccess: () => {
          this.$root.showMessage("success", '<span class="text-success">Success</span><br/>', "Product saved!");
          this.form.reset();
        },
        onError: () => {
          this.$root.showMessage("error", '<span class="text-danger">Error</span><br>', "Something went wrong!");
        },
      });
    },
  },
};
</script>
