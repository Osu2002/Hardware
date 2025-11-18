<template>
  <AppLayout>
    <div class="row">
      <div class="col-md-12">
        <div class="card mb-4">
          <div class="card-header pb-0">
            <h5>Attributes</h5>
            <p>Manage product attributes.</p>
            <div class="d-flex">
              <Link class="btn btn-main btn-sm" :href="route('attribute.create')">
                <i class="bx bx-plus"></i> Create
              </Link>
            </div>
          </div>

          <div class="card-body">
            <data-table
              ref="datatable"
              :id="'mytable'"
              :url="route('attribute.getdata')"
              :columns="columns"
              :columnDefs="columnDefs"
              class="text-center"
            >
              <template #header>
                <tr>
                  <th width="10%">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="form-check-input" id="selectAll" @click="selectAll($event)" />
                      <label class="form-check-label" for="selectAll"></label>
                    </div>
                  </th>
                  
                  <th>Code</th>
                  <th>Name</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Variant?</th>
                  <th>Action</th>
                </tr>
              </template>
            </data-table>
          </div>
        </div>
      </div>

      <!-- delete modal -->
      <div class="modal modal-top fade" id="deleteConfirm" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
        <div class="modal-dialog modal-sm" role="document">
          <div class="modal-content">
            <div class="modal-header px-3 pb-2"><h5 class="modal-title">Are You Sure?</h5></div>
            <form @submit.prevent="deleteSelectedItems">
              <div class="modal-body py-0 px-3"><p>Delete selected attribute(s)?</p></div>
              <div class="modal-footer px-3 pb-3">
                <button type="button" class="btn btn-sm btn-label-secondary" data-bs-dismiss="modal" @click="resetForm">Close</button>
                <button type="submit" class="btn btn-sm btn-primary">Yes</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- /delete modal -->
    </div>
  </AppLayout>
</template>

<script>
import { Link, useForm } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import DataTable from "@/Components/DataTable.vue";

export default {
  components: { Link, AppLayout, DataTable },
  data() {
    return {
      columns: [
        { mData: "check", name: "check", orderable: false, searchable: false },
        { mData: "code", name: "code", orderable: true, searchable: true },
        { mData: "name", name: "name", orderable: true, searchable: true },
        { mData: "type", name: "type", orderable: true, searchable: true },
        { mData: "status", name: "status", orderable: true },
        { mData: "is_variant_option", name: "is_variant_option", orderable: true },
        { mData: "action", name: "action", orderable: false },
      ],
      columnDefs: [{ className: "text-center", targets: [] }],
      order: [[1, "asc"]],
      selectedRows: [],
      form: useForm({ id: "", status: "" }),
    };
  },
  mounted() {
    $("#mytable tbody").on("click", "tr .action_delete", (evt) => {
      const id = $(evt.target).data("item-id");
      this.selectedRows = [];
      this.getSelectedItems(id);
    });
    $("#mytable tbody").on("click", "tr .action_edit", (evt) => {
      const id = $(evt.target).data("item-id");
      this.$inertia.visit(route("attribute.edit", id));
    });
    $("#mytable tbody").on("click", "tr .action_status_change", (evt) => {
      const id = $(evt.target).data("item-id");
      const status = $(evt.target).data("status");
      this.updateStatus(id, status);
    });
    $("#mytable tbody").on("click", ".item-check input[type=checkbox]", (evt) => {
      const val = $(evt.target).val();
      if ($(evt.target).prop("checked") && !this.selectedRows.includes(val)) this.getSelectedItems(val);
      else this.removeUnselectedItem(val);
    });
  },
  methods: {
    reloadTable() { this.$refs.datatable.reloadDatatable(); },
    getSelectedItems(v) { this.selectedRows.push(v); },
    selectAll(evt) {
      const self = this;
      if ($(evt.target).is(":checked")) {
        $(".item-check input[type=checkbox]").prop("checked", true);
        $(".item-check input[type=checkbox]:checked").each(function () {
          if (!self.selectedRows.includes(this.value)) self.getSelectedItems(this.value);
        });
      } else {
        $(".item-check input[type=checkbox]").prop("checked", false);
        $(".item-check input[type=checkbox]").each(function () {
          if (self.selectedRows.includes(this.value)) self.removeUnselectedItem(this.value);
        });
      }
    },
    removeUnselectedItem(v) {
      this.selectedRows = this.selectedRows.filter((x) => x != v);
      if (this.selectedRows.length <= 0) $("#selectAll").prop("checked", false);
    },
    updateStatus(id, status) {
      // expects backend route attribute.change.status that toggles status like other modules
      this.form.id = id; this.form.status = status;
      this.form.put(route("attribute.change.status"), {
        preserveScroll: true,
        onSuccess: () => { this.form.reset(); this.reloadTable(); this.$root.showMessage("success", '<span class="text-success">Success</span><br/>', "Status updated!"); },
        onError: () => { this.$root.showMessage("error", '<span class="text-danger">Error</span><br>', "Something went wrong!"); },
      });
    },
    deleteSelectedItems() {
      this.form.transform((d) => ({ ...d, ids: [...this.selectedRows] }))
        .post(route("attribute.delete"), {
          onSuccess: () => { this.resetForm(); this.reloadTable(); this.$root.showMessage("success", '<span class="text-success">Success</span><br/>', "Deleted!"); },
          onError: () => { this.$root.showMessage("error", '<span class="text-danger">Error</span><br>', "Something went wrong!"); },
          onFinish: () => { $("#deleteConfirm").modal("hide"); this.resetForm(); },
        });
    },
    resetForm() { this.form.reset("id","status"); },
  },
};
</script>
