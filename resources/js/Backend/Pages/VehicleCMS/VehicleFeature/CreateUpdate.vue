<template>
  <AppLayout>
    <div class="row">
      <div class="col-md-12">
        <div class="card mb-4">
          <div class="card-header pb-0">
            <h5>Vehicle Features</h5>
            <p>Manage Features.</p>
          </div>
          <hr />
          <div class="card-body">
            
            <form id="formAccountSettings" @submit.prevent="submit">
              <div class="row">
                <div class="mb-3 col-md-12">
                  <label for="title" class="form-label">name</label>
                  <input class="form-control" type="text" id="name" name="name" v-model="form.name" />
                  <div class="text-danger">
                    {{ form.errors.name }}
                  </div>
                </div>
                <div class="mb-3 col-md-6">
                  <SelectInputComponentVue class="w-100" id="status" label="Status" v-model="form.status" :options="[{id: 1, name:'Activate'}, {id: 0, name:'Deactivate'}]"/>
                </div>
              </div>
              <div class="mt-2">
                <button type="submit" class="btn btn-main me-2" :disabled="form.processing">
                  {{  vehicle_feature ? "Update" : "Save" }}
                </button>
                <Link class="btn btn-outline-danger" :href="route('vehicle.feature')">Cancel</Link>
              </div>
            </form>
          </div>
        </div>
    </div>
    </div>
  </AppLayout>
</template>


<script>
import { Link, useForm } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import SelectInputComponentVue from '../../../Components/SelectInputComponent.vue';


export default {
  components: {
    Link,
    AppLayout,
    SelectInputComponentVue
  },

  props: {
    errors: Object,
    backendUser: Object,
    vehicle_feature:Object,
    
  },

  data() {
    return {
      form: useForm({
        id: "",
        name: "",
        status: "",
       
       
      }),
    };
  },
  mounted() {
    let self = this;

    if (this.vehicle_feature) {
      this.form.id = this.vehicle_feature.id;
      this.form.status = this.vehicle_feature.status;
      this.form.name = this.vehicle_feature.name;
    
    }
    $(".card-body").on("change", "#role", function (evt) {
      self.form.role = $(evt.target).val();
    });
    $(".card-body").on("change", "#branch", function (evt) {
      self.form.branch = $(evt.target).val();
    });
  },
  computed: {
    // profileImage() {
    //   return this.backendUser ? this.backendUser.profile_photo_path ?? "" : "";
    // },
  },
  methods: {
    submit() {
      this.form.post(
        this.vehicle_feature
          ? route("vehicle.feature.update")
          : route("vehicle.feature.storedata"),
        {
          onSuccess: () => {
            this.form.reset();
            this.$root.showMessage(
              "success",
              '<span class="text-success">Success</span><br/>',
              "A Record Was Created Successfully! "
            );
          },
          onError: () => {
            this.form.reset();
            this.$root.showMessage(
              "error",
              '<span class="text-danger">Error</span><br>',
              "Something went wrong! "
            );
          },
        }
      );
    },
    
    },
   
  
};
</script>
