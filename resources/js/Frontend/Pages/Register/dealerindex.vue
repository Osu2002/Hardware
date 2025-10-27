<template>
  <AppLayout>
        <Banner />

  <div class="container" mar>
    <div class="row justify-content-center align-items-center py-5" style="min-height: 100vh">
      <div class="col-md-12 px-4">
        <div class="card bg-white shadow-sm border rounded-5">
          <div class="card-body mb-4">
            <div class="header text-center py-4">
              <!-- <img src="/images/main-logo (1).png" class="pb-3" alt="" /> -->
                          <img
            src="/images/new_logo (1).png"
            alt="Hiru Cars Logo"
            class="login-logo mx-auto d-block"
              />

              <h4 class="text-center fw-bold">Create Your Dealer Account!</h4>
            </div>
            <form class="row input-fields gy-4 px-3" id="formAccountSetting" @submit.prevent="submit">
              <div class="col-12">
                <h5 class="fw-bold">Personal Details
                </h5>
              </div>
              <div class="col-6">
                <div class="input-group border">
                  <input type="text" class="form-control bg-light border-0 py-3 px-4" placeholder="Full Name" id="name"
                    v-model="form.name" />
                </div>
              </div>
              <div class="col-6">
                <div class="input-group border">
                  <input type="email" class="form-control bg-light border-0 py-3 px-4" id="email" v-model="form.email"
                    placeholder="Email" />
                </div>
                <!-- Display the error message if email already exists -->
                <div v-if="form.errors.email" class="text-danger mt-2">
                  {{ form.errors.email }}
                </div>
              </div>

              <div class="col-6">
                <div class="input-group border">
                  <input type="text" class="form-control bg-light border-0 py-3 px-4" placeholder="Mobile Number"
                    id="mobile_no" v-model="form.mobile_no" />
                </div>
              </div>
              <div class="col-12">

              </div>
              <div class="col-6">
                <div class="input-group border">
                  <input type="password" class="form-control bg-light border-0 py-3 px-4" placeholder="Password"
                    id="password" v-model="form.password" />
                  <label class="input-group-text border-0 border-radius-custom" for="chk"><i
                      class="fa-regular fa-eye-slash pe-3" id="icon" style="color: #6a3090"></i></label>
                  <input type="checkbox" class="d-none" id="chk">
                </div>
              </div>
              <div class="col-6">
                <div class="input-group border">
                  <input type="password" class="form-control bg-light border-0 py-3 px-4" placeholder="Confirm Password"
                    id="password_confirmation" v-model="form.password_confirmation" />
                </div>
                <!-- Display password mismatch error -->
                <div v-if="passwordMismatch" class="text-danger mt-2">
                  Passwords do not match.
                </div>
              </div>
              <div class="col-12">
                <h5 class="fw-bold">Bank Details
                </h5>
              </div>
              <div class="col-6">
                <div class="input-group border">
                  <input type="text" class="form-control bg-light border-0 py-3 px-4" placeholder="Bank Name"
                    id="bank_name" v-model="form.bank_name" />
                </div>
              </div>
              <div class="col-6">
                <div class="input-group border">
                  <input type="text" class="form-control bg-light border-0 py-3 px-4" placeholder="Branch" id="branch"
                    v-model="form.branch" />
                </div>
              </div>
              <div class="col-6">
                <div class="input-group border">
                  <input type="number" class="form-control bg-light border-0 py-3 px-4" placeholder="Account Number"
                    id="acc_number" v-model="form.acc_number" />
                </div>
              </div>
              <div class="col-6">
                <div class="input-group border">
                  <input type="text" class="form-control bg-light border-0 py-3 px-4" placeholder="Payee Name"
                    id="payee_name" v-model="form.payee_name" />
                </div>
              </div>
              <div class="col-6">
                <div class="input-group border">
                  <input type="text" class="form-control bg-light border-0 py-3 px-4" placeholder="Branch Code"
                    id="branch_code" v-model="form.branch_code" />
                </div>
              </div>
              <div class="mb-3 col-md-6">
                <label for="passbook_image" class="form-label me-3">Passbook Image</label>
                <br />
                <FileInputComponent :isRequired="false" id="passbook_image" :prvImage="passbookImage"
                  v-model="form.passbook_image" />
                <div class="text-danger">
                  {{ form.errors.passbook_image }}
                </div>
              </div>



              <div class="col-12 mt-4">
                <button class="btn btn-primary w-100 py-2 login-btn border-0" type="submit"
                  style="background-color: #6a3090" :disabled="form.processing">
                  REGISTER
                </button>
              </div>
            </form>
            <div class="row px-4 mt-2">

              <div class="col-12 mt-4 text-center">
                <Link class="text-decoration-underline" :href="route('index')"><small>Back to Home Page</small></Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </AppLayout>

</template>

<script>
import { Link, useForm } from "@inertiajs/inertia-vue3";
import AppLayout from "@@/Layouts/AppLayout.vue";
import FileInputComponent from "@@/Components/FileInputComponent.vue";
import Banner from "./Banner.vue";
export default {
  components: {
    Link,
    AppLayout,
    FileInputComponent,
    Banner
  },
  mounted() {
    const pwd = document.getElementById("password");
    const chk = document.getElementById("chk");
    const icon = document.getElementById("icon");

    chk.onchange = function (e) {
      if (chk.checked) {
        pwd.type = "text";
        icon.classList.add("fa-eye");
        icon.classList.remove("fa-eye-slash");
      } else {
        pwd.type = "password";
        icon.classList.add("fa-eye-slash");
      }
    };
  },


  props: {
    errors: Object,
    allRoles: Object,
  },

  data() {
    return {
      form: useForm({
        name: "",
        email: "",
        mobile_no: "",
        password: "",
        password_confirmation: "",

        bank_name: "",
        branch: "",
        acc_number: "",
        payee_name: "",
        branch_code: "",
        passbook_image: null,
      }),
      passwordMismatch: false, // To track if passwords match
    };
  },
  computed: {
    passbookImage() {
      return this.backendUser ? this.backendUser.passbook_image ?? "" : "";
    },
  },
  methods: {
    submit() {
      // Check if passwords match
      if (this.form.password !== this.form.password_confirmation) {
        this.passwordMismatch = true;
        return;
      } else {
        this.passwordMismatch = false;
      }

      // Proceed with form submission if passwords match
      this.form.post(route("front.end.dealer.store"), {
        onSuccess: () => {
          this.form.reset();
          this.$root.showMessage(
            "success",
            '<span class="text-success">Success</span><br/>',
            "A Record Was Created Successfully!"
          );
        },
        onError: () => {
          if (this.form.errors.email) {
            this.$root.showMessage(
              "error",
              '<span class="text-danger">Error</span><br>',
              "The email has already been taken!"
            );
          }
          this.form.reset("password", "password_confirmation");
        },
      });
    },
  },
};
</script>

<style scoped>

.login-logo {
  display: block !important;
  max-width: 250px !important;   
  height: auto !important;
  margin-bottom: 1.5rem !important; 
}
.form-control {
  box-shadow: none;
  border-radius: 18px;
}

.input-group {
  border-radius: 18px;
}

.form-check-input {
  box-shadow: none;
}

.login-btn {
  border-radius: 18px;
}

.login-btn {
  border-radius: 18px;
}

.border-radius-custom {
  border-top-right-radius: 18px !important;
  border-bottom-right-radius: 18px !important;
}

.fa-eye-slash {
  font-size: 15px;
}

@media (max-width:1400px) {
  .form-control {
    padding-bottom: 0.5rem !important;
    padding-top: 0.5rem !important;
  }

  .input-fields {
    padding-right: 1rem !important;
    padding-left: 1rem !important;
  }

}
</style>
