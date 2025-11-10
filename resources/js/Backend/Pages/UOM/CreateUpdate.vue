<!-- resources/js/Pages/Catalog/Uom/CreateUpdate.vue -->
<template>
  <AppLayout>
    <div class="row">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h5>Unit of Measure</h5>
          <p>Create / Update UoM.</p>
        </div>
        <hr />
        <div class="card-body">
          <form @submit.prevent="submit">
            <div class="row">
              <div class="mb-3 col-md-4">
                <label for="code" class="form-label">Code</label>
                <input class="form-control" type="text" id="code" v-model="form.code" placeholder="pcs, kg, m, L" />
                <div class="text-danger">{{ form.errors.code }}</div>
              </div>

              <div class="mb-3 col-md-6">
                <label for="name" class="form-label">Name</label>
                <input class="form-control" type="text" id="name" v-model="form.name" placeholder="Pieces, Kilogram, Meter" />
                <div class="text-danger">{{ form.errors.name }}</div>
              </div>

              <SelectInputComponentVue
                class="mb-3 col-md-2"
                id="status"
                label="Status"
                :error="form.errors.status"
                :options="[{ id: '1', name: 'Active' }, { id: '0', name: 'Inactive' }]"
                v-model="form.status"
              />

              <div class="mb-3 col-md-3">
                <label for="sort_order" class="form-label">Sort Order</label>
                <input class="form-control" type="number" id="sort_order" v-model="form.sort_order" />
                <div class="text-danger">{{ form.errors.sort_order }}</div>
              </div>
            </div>

            <div class="mt-2">
              <button type="submit" class="btn btn-main me-2" :disabled="form.processing">
                {{ uom ? "Update" : "Save" }}
              </button>
              <Link class="btn btn-outline-danger" :href="route('uom.index')">Cancel</Link>
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

  props: {
    errors: Object,
    uom: Object, // when editing
  },

  data() {
    return {
      form: useForm({
        id: "",
        code: "",
        name: "",
        status: "1",
        sort_order: 0,
      }),
    };
  },

  mounted() {
    if (this.uom) {
      this.form.id = this.uom.id;
      this.form.code = this.uom.code;
      this.form.name = this.uom.name;
      this.form.status = String(this.uom.status ?? "1");
      this.form.sort_order = this.uom.sort_order ?? 0;
    }
  },

  methods: {
    submit() {
      this.form.post(this.uom ? route("uom.update") : route("uom.store"), {
        onSuccess: () => {
          this.form.reset();
          this.$root.showMessage("success", '<span class="text-success">Success</span><br/>', "UoM saved!");
        },
        onError: () => {
          this.$root.showMessage("error", '<span class="text-danger">Error</span><br>', "Something went wrong!");
        },
      });
    },
  },
};
</script>
