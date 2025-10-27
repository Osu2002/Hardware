<template>
  <AppLayout>
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <h5>Fuel List</h5>
        <Link :href="route('fuels.create')" class="btn btn-main">
          <i class="bx bx-plus"></i> Create
        </Link>
      </div>
      <div class="card-body">
        <data-table
          ref="datatable"
          id="mytable"
          :url="route('fuels.getdata')"
          :columns="columns"
          :columnDefs="columnDefs"
        >
          <template #header>
            <tr>
              <th>Fuel Type</th>
              <th>Price (Rs)</th>
              <th class="text-center">Actions</th>
            </tr>
          </template>
        </data-table>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import DataTable  from '@/Components/DataTable.vue'
import { Link }  from '@inertiajs/inertia-vue3'

export default {
  components: { AppLayout, DataTable, Link },
  data() {
    return {
      columns: [
        { data:'fuel_type', name:'fuel_type' },
        { data:'price',     name:'price'     },
        { data:'action',    name:'action', orderable:false, searchable:false }
      ],
      columnDefs: [{
        targets: 2, className:'text-center',
        render:(d,t,r)=>`
          <button data-id="${r.id}" class="btn btn-sm btn-primary edit">
            <i class="bx bx-edit"></i>
          </button>
          <button data-id="${r.id}" class="btn btn-sm btn-danger delete">
            <i class="bx bx-trash"></i>
          </button>`
      }]
    }
  },
  mounted(){
    const vm = this

    // Edit button just navigates — page reload will trigger the watcher below
    $("#mytable")
      .on("click","button.edit", function(){
        const id = $(this).data("id")
        vm.$inertia.visit(route("fuels.edit", id))
      })
      .on("click","button.delete", function(){
        const id = $(this).data("id")
        if (confirm("Delete this entry?")) {
          vm.$inertia.delete(route("fuels.destroy", id), {
            onSuccess() {
              vm.$refs.datatable.reloadDatatable()
            }
          })
        }
      })
  },
  watch: {
    // When returning from Edit (flash.success === 'Updated.') reload table
    '$page.props.flash.success'(msg) {
      if (msg === 'Updated.') {
        this.$refs.datatable.reloadDatatable()
      }
    }
  }
}
</script>
