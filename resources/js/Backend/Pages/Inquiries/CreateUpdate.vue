<template>
  <AppLayout>
    <div class="row">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h5>Inquiries</h5>
          <p>Manage Inquiries.</p>
        </div>
        <hr />
        <div class="card-body">
          <form id="formAccountSettings" @submit.prevent="submit">
            <div class="row">
              <!-- <div class="mb-3 col-md-6">
                <label for="type" class="form-label">Vehicle type</label>
                <input
                  class="form-control"
                  type="text"
                  id="vehicle_type"
                  name="vehicle_type"
                  v-model="form.vehicle_type"
                />
                <div class="text-danger">
                  {{ form.errors.vehicle_type }}
                </div>
              </div> -->

              <div class="mb-3 col-md-6">
                <label for="name" class="form-label">Name</label>
                <input class="form-control" type="text" id="name" name="name" v-model="form.name" disabled />
              </div>
              <div class="mb-3 col-md-6">
                <label for="email" class="form-label">Email</label>
                <input class="form-control" type="email" id="email" name="email" v-model="form.email" disabled />
              </div>
              <div class="mb-3 col-md-6">
                <label for="phone" class="form-label">Phone</label>
                <input class="form-control" type="text" id="phone" name="phone" v-model="form.phone" disabled />
              </div>
              <div class="mb-3 col-md-6">
                <label for="purchase" class="form-label">Purchase Time</label>
                <input class="form-control" type="text" id="purchase" name="purchase" v-model="form.purchase_time" disabled />
              </div>
              <div class="mb-3 col-md-12">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" rows="3" v-model="form.address" disabled></textarea>
              </div>
              <div class="mb-3 col-md-12">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" id="message" rows="3" v-model="form.message" disabled></textarea>
              </div>
              <div class="mb-3 col-md-12">
                <label for="" class="form-label">Path</label>
                <a class="form-control border-none text-primary text-decoration-underline" :href="inquiry.url" target="_blank" >{{ inquiry.url }}</a>
              </div>
              <div class="mb-3 col-md-6">
                <label for="payment" class="form-label">Payment Method</label>
                <input class="form-control" type="text" id="payment" name="payment" v-model="form.payment_method" disabled />
              </div>

              <SelectInputComponentVue id="status" label="Status" :error="form.errors.status"
                :options="[
                  {
                    id: 'done',
                    name: 'Done',
                  },
                  {
                    id: 'close',
                    name: 'Close',
                  },
                  {
                    id: 'pending',
                    name: 'Pending',
                  },
                  {
                    id: 'handling',
                    name: 'Handling',
                  },
                  {
                    id: 'reject',
                    name: 'Reject',
                  },
                ]" v-model="form.status" />
            </div>
            <div class="mt-2">
              <button type="submit" class="btn btn-main me-2" :disabled="form.processing">
                {{ inquiry ? "Update" : "Save" }}
              </button>
              <Link class="btn btn-outline-danger" :href="route('inquiry.index')">Cancel</Link>
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
import SelectInputComponentVue from "../../Components/SelectInputComponent.vue";
import Editor from "@tinymce/tinymce-vue";
import { ref } from "vue";

// import SuspendedUserAlert from "./Partials/SuspendedUserAlert.vue";

export default {
  components: {
    Link,
    AppLayout,
    FileInputComponent,
    // SuspendedUserAlert,
    SelectInputComponentVue,
    Editor,
  },
  // setup() {
  //   const apiKey = ref(import.meta.env.VITE_TINYMCE_API_KEY);
  //   return {
  //     apiKey
  //   }
  // },
  props: {
    errors: Object,
    inquiry:Object,
  },

  data() {
    return {
      apiKey:import.meta.env.VITE_TINYMCE_API_KEY,
      form: useForm({
        name:'',
        email:'',
        phone:'',
        purchase_time:'',
        address:'',
        message:'',
        status:'',
        payment_method:'',
        url:'',
        vehicle_id:'',
        type:'',
        id:''

              }),
      // password: useForm({
      //   id: "",
      //   confirm_password: "",
      //   password: "",
      //   password_confirmation: "",
      // }),
    };
  },
  mounted() {
    let self = this;

    if (this.inquiry) {
      this.form.id = this.inquiry.id
     this.form.name = this.inquiry.name
     this.form.email = this.inquiry.email
     this.form.phone = this.inquiry.phone
     this.form.purchase_time = this.inquiry.purchase_time
     this.form.address = this.inquiry.address
     this.form.message = this.inquiry.message
     this.form.payment_method = this.inquiry.payment_method
     this.form.status = this.inquiry.status
     this.form.url = this.inquiry.url
     this.form.vehicle_id = this.inquiry.vehicle_id
     this.form.type = this.inquiry.type
    }
    // $(".card-body").on("change", "#role", function (evt) {
    //   self.form.role = $(evt.target).val();
    // });
    // $(".card-body").on("change", "#branch", function (evt) {
    //   self.form.branch = $(evt.target).val();
    // });
  },
 
  methods: {
  
    submit() {
      this.form.post(
        this.inquiry ? route("inquiry.update") : route("inquiry.store"),
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
            this.form.reset("password", "password_confirmation");
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
<style scoped>
/* Flex container for checkboxes */
.features-container {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}

/* Checkbox Container */
.checkbox-container {
  display: flex;
  align-items: center;
  width: calc(25% - 1rem);
  /* Adjust the width to divide the space into 4 columns */
  margin-bottom: 0.5rem;
}

/* Hide the default checkbox */
.checkbox-container input[type="checkbox"] {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

/* Custom Checkbox Design */
.checkbox-container label {
  position: relative;
  padding-left: 30px;
  cursor: pointer;
  font-weight: 500;
  user-select: none;
}

/* Create the custom checkbox */
.checkbox-container label::before {
  content: "";
  position: absolute;
  left: 0;
  top: 0;
  width: 20px;
  height: 20px;
  background-color: #f1f1f1;
  border: 2px solid #ccc;
  border-radius: 4px;
  transition: background-color 0.2s, border-color 0.2s;
}

/* Show the checkmark when checked */
.checkbox-container input[type="checkbox"]:checked+label::before {
  background-color: #3d3232;
  border-color: #000000;
}

.checkbox-container input[type="checkbox"]:checked+label::after {
  content: "";
  position: absolute;
  left: 6px;
  top: 2px;
  width: 6px;
  height: 12px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}
.border-none{
  border:0px !important;
  
}
/* Hover Effect */
.checkbox-container label:hover::before {
  border-color: #007bff;
}
</style>