<template>
  <AppLayout>
    <div class="row">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h5>Attribute</h5>
          <p>Create / Update attribute.</p>
        </div>
        <hr />
        <div class="card-body">
          <form @submit.prevent="submit">
            <div class="row">
              <div class="mb-3 col-md-4">
                <label for="code" class="form-label">Code</label>
                <input class="form-control" id="code" v-model="form.code" placeholder="colour, size, length" />
                <div class="text-danger">{{ form.errors.code }}</div>
              </div>

              <div class="mb-3 col-md-6">
                <label for="name" class="form-label">Name</label>
                <input class="form-control" id="name" v-model="form.name" placeholder="Colour, Size, Length" />
                <div class="text-danger">{{ form.errors.name }}</div>
              </div>

              <SelectInputComponentVue
                class="mb-3 col-md-2"
                id="status"
                label="Status"
                :options="[{id:'1',name:'Active'},{id:'0',name:'Inactive'}]"
                :error="form.errors.status"
                v-model="form.status"
              />

              <SelectInputComponentVue
                class="mb-3 col-md-4"
                id="type"
                label="Type"
                :options="typeOptions"
                :error="form.errors.type"
                v-model="form.type"
              />

              <div class="mb-3 col-md-4">
                <label for="unit" class="form-label">Unit (optional)</label>
                <input class="form-control" id="unit" v-model="form.unit" placeholder="mm, kg, L" />
                <div class="text-danger">{{ form.errors.unit }}</div>
              </div>

              <div class="mb-3 col-md-2 d-flex align-items-center">
                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="is_filterable" v-model="form.is_filterable" />
                  <label class="form-check-label" for="is_filterable">Filterable</label>
                </div>
              </div>

              <div class="mb-3 col-md-2 d-flex align-items-center">
                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="is_variant_option" v-model="form.is_variant_option" />
                  <label class="form-check-label" for="is_variant_option">Variant option</label>
                </div>
              </div>

              <div class="mb-3 col-md-2">
                <label for="sort_order" class="form-label">Sort</label>
                <input class="form-control" type="number" id="sort_order" v-model="form.sort_order" />
                <div class="text-danger">{{ form.errors.sort_order }}</div>
              </div>
            </div>

            <!-- Options (only when select or color) -->
            <div v-if="showOptions" class="row">
              <div class="col-12"><h6 class="mt-2">Options</h6></div>
              <div class="col-12">
                <div v-for="(opt, idx) in form.options" :key="idx" class="row g-2 align-items-end mb-2">
                  <div class="col-md-6">
                    <label :for="'opt_value_'+idx" class="form-label">Value</label>
                    <input class="form-control" :id="'opt_value_'+idx" v-model="opt.value" placeholder="Red / 4L / 10mm" />
                  </div>
                  <div class="col-md-3" v-if="form.type === 'color'">
                    <label :for="'opt_hex_'+idx" class="form-label">Hex</label>
                    <input class="form-control" :id="'opt_hex_'+idx" v-model="opt.hex" placeholder="#FF0000" />
                  </div>
                  <div class="col-md-2">
                    <label :for="'opt_sort_'+idx" class="form-label">Sort</label>
                    <input class="form-control" type="number" :id="'opt_sort_'+idx" v-model="opt.sort_order" />
                  </div>
                  <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger" @click="removeOption(idx)">
                      <i class="bx bx-trash"></i>
                    </button>
                  </div>
                </div>

                <button type="button" class="btn btn-outline-primary btn-sm" @click="addOption">
                  <i class="bx bx-plus"></i> Add Option
                </button>
              </div>
            </div>

            <div class="mt-3">
              <button type="submit" class="btn btn-main me-2" :disabled="form.processing">
                {{ attribute ? "Update" : "Save" }}
              </button>
              <Link class="btn btn-outline-danger" :href="route('attribute.index')">Cancel</Link>
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
import SelectInputComponentVue from "@/Components/SelectInputComponent.vue";

export default {
  components: { Link, AppLayout, SelectInputComponentVue },
  props: { errors: Object, attribute: Object },
  data() {
    return {
      typeOptions: [
        { id: "text", name: "Text" },
        { id: "number", name: "Number" },
        { id: "select", name: "Select (with options)" },
        { id: "boolean", name: "Boolean" },
        { id: "color", name: "Color (with hex)" },
      ],
      form: useForm({
        id: "",
        code: "",
        name: "",
        type: "text",
        unit: "",
        status: "1",
        is_filterable: true,
        is_variant_option: false,
        sort_order: 0,
        options: [], // [{value:'Red', hex:'#FF0000', sort_order:0}]
      }),
    };
  },
  mounted() {
    if (this.attribute) {
      const a = this.attribute;
      this.form.id = a.id;
      this.form.code = a.code || "";
      this.form.name = a.name || "";
      this.form.type = a.type || "text";
      this.form.unit = a.unit || "";
      this.form.status = String(a.status ?? "1");
      this.form.is_filterable = !!a.is_filterable;
      this.form.is_variant_option = !!a.is_variant_option;
      this.form.sort_order = a.sort_order ?? 0;
      // options from server when editing
      this.form.options = (a.options || []).map((o, i) => ({
        value: o.value, hex: o.hex || "", sort_order: o.sort_order ?? i
      }));
    }
  },
  computed: {
    showOptions() {
      return this.form.type === "select" || this.form.type === "color";
    },
  },
  methods: {
    addOption() { this.form.options.push({ value: "", hex: "", sort_order: this.form.options.length }); },
    removeOption(i) { this.form.options.splice(i, 1); },
    submit() {
      this.form.post(this.attribute ? route("attribute.update") : route("attribute.store"), {
        onSuccess: () => {
          this.form.reset();
          this.$root.showMessage("success", '<span class="text-success">Success</span><br/>', "Attribute saved!");
        },
        onError: () => {
          this.$root.showMessage("error", '<span class="text-danger">Error</span><br>', "Something went wrong!");
        },
      });
    },
  },
};
</script>
