<template>
  <AppLayout>
    <div class="row">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h5>Attribute Set</h5>
          <p>Create / Update attribute set.</p>
        </div>
        <hr />
        <div class="card-body">
          <form @submit.prevent="submit">
            <div class="row">
              <div class="mb-3 col-md-5">
                <label for="name" class="form-label">Name</label>
                <input class="form-control" id="name" v-model="form.name" placeholder="e.g., Paint, Steel, Electrical" />
                <div class="text-danger">{{ form.errors.name }}</div>
              </div>

              <SelectInputComponentVue
                class="mb-3 col-md-3"
                id="status"
                label="Status"
                :options="[{id:'1',name:'Active'},{id:'0',name:'Inactive'}]"
                :error="form.errors.status"
                v-model="form.status"
              />

              <div class="mb-3 col-md-2">
                <label for="sort_order" class="form-label">Sort</label>
                <input class="form-control" type="number" id="sort_order" v-model="form.sort_order" />
                <div class="text-danger">{{ form.errors.sort_order }}</div>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-12">
                <h6 class="mb-2">Attributes in this set</h6>

                <div class="table-responsive">
                  <table class="table table-sm align-middle">
                    <thead>
                      <tr>
                        <th style="width: 40%">Attribute</th>
                        <th style="width: 20%">Code</th>
                        <th style="width: 15%">Required</th>
                        <th style="width: 15%">Sort</th>
                        <th style="width: 10%"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(row, idx) in form.map" :key="idx">
                        <td>
                          <select class="form-select" v-model="row.attribute_id">
                            <option disabled value="">Select attribute</option>
                            <option v-for="a in attributes" :key="a.id" :value="a.id">{{ a.name }}</option>
                          </select>
                        </td>
                        <td>
                          <span class="badge bg-label-primary">
                            {{ codeFor(row.attribute_id) }}
                          </span>
                        </td>
                        <td>
                          <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" v-model="row.is_required" />
                          </div>
                        </td>
                        <td>
                          <input class="form-control" type="number" v-model="row.sort_order" />
                        </td>
                        <td>
                          <button type="button" class="btn btn-outline-danger btn-sm" @click="removeRow(idx)">
                            <i class="bx bx-trash"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <button type="button" class="btn btn-outline-primary btn-sm" @click="addRow">
                  <i class="bx bx-plus"></i> Add Attribute
                </button>
              </div>
            </div>

            <div class="mt-3">
              <button type="submit" class="btn btn-main me-2" :disabled="form.processing">
                {{ set ? "Update" : "Save" }}
              </button>
              <Link class="btn btn-outline-danger" :href="route('attribute-set.index')">Cancel</Link>
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
    set: Object,           // when editing
    attributes: Array      // [{id,name,code}]
  },
  data() {
    return {
      form: useForm({
        id: "",
        name: "",
        status: "1",
        sort_order: 0,
        // map rows: { attribute_id, is_required, sort_order }
        map: [],
      }),
    };
  },
  mounted() {
    if (this.set) {
      this.form.id = this.set.id;
      this.form.name = this.set.name || "";
      this.form.status = String(this.set.status ?? "1");
      this.form.sort_order = this.set.sort_order ?? 0;
      this.form.map = (this.set.attributes || []).map((a, i) => ({
        attribute_id: a.id,
        is_required: !!a.pivot?.is_required,
        sort_order: a.pivot?.sort_order ?? i,
      }));
    }
    if (!this.form.map.length) this.addRow(); // start with one row
  },
  methods: {
    addRow() { this.form.map.push({ attribute_id: "", is_required: false, sort_order: this.form.map.length }); },
    removeRow(i) { this.form.map.splice(i, 1); },
    codeFor(id) {
      const a = this.attributes.find(x => x.id === id);
      return a ? a.code : '-';
    },
    submit() {
      this.form.post(this.set ? route("attribute-set.update") : route("attribute-set.store"), {
        onSuccess: () => {
          this.$root.showMessage("success", '<span class="text-success">Success</span><br/>', "Attribute set saved!");
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
