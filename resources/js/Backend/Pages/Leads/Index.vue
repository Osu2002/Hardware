<template>
  <AppLayout>
    <div class="row">
      <div class="col-md-12">
        <div class="card mb-4">
          <div class="card-header pb-0">
            <h5>Leads</h5>
            <p>Manage Leads.</p>
            <div class="d-flex">
              <Link
                class="btn btn-main btn-sm"
                :href="route('lead.create')"
                v-if="$root.hasPermission('lead.create')"
              >
                <i class="bx bx-plus"></i> Create
              </Link>
            </div>
          </div>
          <div class="card-body">
            <data-table
              ref="datatable"
              :id="'mytable'"
              :url="route('lead.getdata')"
              :columns="columns"
              :columnDefs="columnDefs"
            >
              <template #header>
                <tr>
                  <th width="10%">
                    <div class="custom-control custom-checkbox">
                      <input
                        type="checkbox"
                        class="form-check-input"
                        id="selectAll"
                        @click="selectAll($event)"
                      />
                      <label class="form-check-label" for="selectAll"></label>
                    </div>
                  </th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </template>
            </data-table>
          </div>
        </div>
      </div>
      <!-- model -->
      <div
        class="modal modal-top fade"
        id="deleteConfirm"
        tabindex="-1"
        aria-hidden="true"
        data-bs-backdrop="false"
      >
        <div class="modal-dialog modal-sm" role="document">
          <div class="modal-content">
            <div class="modal-header px-3 pb-2">
              <h5 class="modal-title" id="exampleModalLabel2">Are You Sure?</h5>
            </div>
            <form id="formAccountDelete" @submit.prevent="deleteSelectedItems">
              <div class="modal-body py-0 px-3">
                <div class="row">
                  <div class="col mb-1">
                    <p>Are you sure you want to delete?</p>
                  </div>
                </div>
              </div>
              <div class="modal-footer px-3 pb-3">
                <button
                  type="button"
                  class="btn btn-sm btn-label-secondary"
                  data-bs-dismiss="modal"
                  @click="resetForm"
                >Close</button>
                <button type="submit" class="btn btn-sm btn-primary">Yes</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- model -->
      <!-- Bootstrap Modal -->
      <div
        class="modal fade"
        id="viewModal"
        tabindex="-1"
        aria-labelledby="viewModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-lg">
          <!-- Increased width for better layout -->
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="viewModalLabel">Lead Details</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-md-12">
                  <p>
                    <strong>Vehicle List</strong>
                  </p>
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>URL</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Iterate over the vehicle_data array -->
                      <tr
                        v-for="vehicle in lead_data?.lead?.vehicle_data"
                        :key="vehicle.vehicle_id"
                      >
                        <td>{{ vehicle.name }}</td>
                        <td>{{ vehicle.type }}</td>
                        <td>
                          <a :href="vehicle.url" target="_blank">View Vehicle</a>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <hr />
                <div class="col-md-6">
                  <p>
                    <strong>Name : </strong>
                    <span id="modal-item-name">{{ lead_data?.lead?.name }}</span>
                  </p>
                </div>
                <div class="col-md-6">
                  <p>
                    <strong>Email : </strong>
                    <span id="modal-item-email">{{ lead_data?.lead?.email }}</span>
                  </p>
                </div>
                <div class="col-md-6">
                  <p>
                    <strong>Phone : </strong>
                    <span id="modal-item-phone">{{ lead_data?.lead?.phone }}</span>
                  </p>
                </div>
                <div class="col-md-6">
                  <p>
                    <strong>Address : </strong>
                    <span id="modal-item-address">{{ lead_data?.lead?.address }}</span>
                  </p>
                </div>
                <div class="col-md-6">
                  <p>
                    <strong>Message : </strong>
                    <span id="modal-item-message">{{ lead_data?.lead?.message }}</span>
                  </p>
                </div>
                <div class="col-md-6">
                  <p>
                    <strong>Purchase Time : </strong>
                    <span id="modal-item-purchase_time">{{ lead_data?.lead?.purchase_time }}</span>
                  </p>
                </div>
                <div class="col-md-6">
                  <p>
                    <strong>Payment Method : </strong>
                    <span id="modal-item-payment_method">{{ lead_data?.lead?.payment_method }}</span>
                  </p>
                </div>
                <hr />
                <div class="col-md-6">
                  <p>
                    <strong>Dealer Name : </strong>
                    <span id="modal-item-dealer_id">{{ lead_data?.dealer?.name }}</span>
                  </p>
                </div>
                <div class="col-md-6">
                  <p>
                    <strong>Status : </strong>
                    <span id="modal-item-status" class="text-capitalize">
                      {{
                      lead_data?.lead?.status === 'pending' ? 'Pending' :
                      lead_data?.lead?.status === 'contacted' ? 'Client Contacted' :
                      lead_data?.lead?.status === 'negotiating' ? 'Negotiating' :
                      lead_data?.lead?.status === 'advance_paid' ? 'Advance Paid' :
                      lead_data?.lead?.status === 'awaiting_for_shipping' ? 'Awaiting for Shipping' :
                      lead_data?.lead?.status === 'cancelled' ? 'Cancelled' :
                      'Unknown Status'
                      }}
                    </span>
                  </p>
                </div>
                <hr />
                <div class="col-md-12">
                  <p>
                    <strong>Status Updates</strong>
                  </p>
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Status</th>
                        <th>Time</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Iterate over the vehicle_data array -->
                      <tr
                        v-for="statusData in lead_data?.lead?.status_data"
                        :key="statusData.status"
                      >
                      <td>
  {{
    statusData.status === 'pending' ? 'Pending' :
    statusData.status === 'contacted' ? 'Client Contacted' :
    statusData.status === 'negotiating' ? 'Negotiating' :
    statusData.status === 'advance_paid' ? 'Advance Paid' :
    statusData.status === 'awaiting_for_shipping' ? 'Awaiting for Shipping' :
    statusData.status === 'cancelled' ? 'Cancelled' :
    'Unknown Status'
  }}
</td>
                        <td>{{ statusData.changed_at }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="fas fa-times"></i>
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
<script>
import { Link, useForm } from "@inertiajs/inertia-vue3";

import AppLayout from "@/Layouts/AppLayout.vue";
import DataTable from "@/Components/DataTable.vue";

export default {
  components: {
    Link,
    AppLayout,
    DataTable
  },

  data() {
    return {
      columns: [
        {
          data: "check",
          name: "check",
          orderable: false,
          searchable: false
        },
        {
          data: "name",
          name: "name",
          orderable: true,
          searchable: true
        },
        {
          data: "email",
          name: "email",
          orderable: true,
          searchable: true
        },
        {
          data: "phone",
          name: "phone",
          orderable: true,
          searchable: true
        },

        { data: "status", name: "status", orderable: true },
        { data: "action", name: "action", orderable: true }
      ],
      columnDefs: [{ className: "text-center", targets: [] }],
      order: [[1, "desc"]],

      form: useForm({
        id: "",
        status: ""
      }),
      lead_data: ""
    };
  },
  mounted() {
    $("#mytable tbody").on("click", "tr .action_delete", evt => {
      const data = $(evt.target).data("item-id");
      this.selectedRows = [];
      this.getSelectedItems(data);
    });
    $("#mytable tbody").on("click", "tr .action_edit", evt => {
      const id = $(evt.target).data("item-id");
      this.$inertia.visit(route("lead.edit", id));
    });
    $("#mytable tbody").on("click", "tr .action_view", evt => {
      const id = $(evt.target).data("item-id");
      const self = this;
      $.ajax({
        url: route("lead.itemdata"),
        type: "GET",
        data: { id: id },
        success: function(response) {
          let vehicleData = JSON.parse(response.lead.vehicle_data);
          let statusData = JSON.parse(response.lead.status_data);

          self.lead_data = response;
          self.lead_data.lead.vehicle_data = vehicleData;
          self.lead_data.lead.status_data = statusData;
          console.log(response);

          // Show the modal
          $("#viewModal").modal("show");
        },
        error: function() {
          alert("Failed to fetch data.");
        }
      });
    });

    $("#mytable tbody").on("click", "tr .action_status_change", evt => {
      const id = $(evt.target).data("item-id");
      const status = $(evt.target).data("status");
      this.updateStatus(id, status);
      // console.log(id);
      // console.log(status);
    });

    $("#mytable tbody").on("click", ".item-check input[type=checkbox]", evt => {
      const checkedVal = $(evt.target).val();
      if (
        $(evt.target).prop("checked") &&
        !this.selectedRows.includes(checkedVal)
      ) {
        this.getSelectedItems(checkedVal);
      } else {
        this.removeUnselectedItem(checkedVal);
      }
    });
  },
  methods: {
    reloadTable() {
      this.$refs.datatable.reloadDatatable();
    },
    getSelectedItems(value) {
      this.selectedRows.push(value);
      if (this.selectedRows.length > 0) {
        this.$data.isDisabled = false;
      }
    },
    selectAll(evt) {
      var self = this;
      if ($(evt.target).is(":checked")) {
        $(".item-check input[type=checkbox]").prop("checked", true);
        $(".item-check input[type=checkbox]:checked").each(function() {
          if (!self.selectedRows.includes(this.value)) {
            self.getSelectedItems(this.value);
          }
        });
        this.$data.isDisabled = false;
      } else {
        $(".item-check input[type=checkbox]").prop("checked", false);
        $(".item-check input[type=checkbox]").each(function() {
          if (self.selectedRows.includes(this.value)) {
            self.removeUnselectedItem(this.value);
          }
        });
        this.$data.isDisabled = true;
      }
    },
    removeUnselectedItem(value) {
      this.selectedRows = this.selectedRows.filter(function(val) {
        return val != value;
      });
      if (this.selectedRows.length <= 0) {
        $("#selectAll").prop("checked", false);
        this.$data.isDisabled = true;
      }
    },
    updateStatus(id, status) {
      this.form.id = id;
      this.form.status = status;
      this.form.put(route("lead.status.update"), {
        preserveScroll: true,
        onSuccess: () => {
          this.form.reset();
          this.reloadTable();
          this.$root.showMessage(
            "success",
            '<span class="text-success">Success</span><br/>',
            "Staus Updated Successfully! "
          );
        },
        onError: () => {
          this.$root.showMessage(
            "error",
            '<span class="text-danger">Error</span><br>',
            "Something went wrong! "
          );
        }
      });
    },
    deleteSelectedItems() {
      this.form
        .transform(data => ({
          ...data,
          ids: [...this.selectedRows]
        }))
        .post(route("lead.delete"), {
          onSuccess: () => {
            this.resetForm();
            this.reloadTable();
            this.$root.showMessage(
              "success",
              '<span class="text-success">Success</span><br/>',
              "A Record Was Created Successfully! "
            );
          },
          onError: () => {
            this.$root.showMessage(
              "error",
              '<span class="text-danger">Error</span><br>',
              "Something went wrong! "
            );
          },
          onFinish: () => {
            $("#deleteConfirm").modal("hide");
            this.resetForm();
          }
        });
    },
    resetForm() {
      this.form.reset("id", "status");
    }
  }
};
</script>