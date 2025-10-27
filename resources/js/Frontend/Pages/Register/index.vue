<template>
  <AppLayout>
    <Banner />
    <div class="container">
      <div
        class="row justify-content-center align-items-center py-5"
        style="min-height: 100vh"
      >
        <div class="col-12 col-sm-10 col-md-8 col-lg-5 px-3 px-sm-4">

          <div class="card bg-white shadow-sm border rounded-5">
            <div class="card-body mb-4">
              <div class="header text-center py-4">
                <img
                  src="/images/new_logo (1).png"
                  alt="Hiru Cars Logo"
                  class="login-logo mx-auto d-block"
                />
                <h4 class="text-center fw-bold">Create Your Account!</h4>
              </div>

              <form
                novalidate
                class="row input-fields gy-4 px-3"
                id="formAccountSetting"
                @submit.prevent="submit"
              >
                <!-- Full Name -->
                <div class="col-12">
                  <div class="input-group border">
                    <input
                      type="text"
                      class="form-control bg-light border-0 py-3 px-4"
                      placeholder="Full Name"
                      v-model="form.name"
                    />
                  </div>
                  <div v-if="form.errors.name" class="text-danger mt-2">
                    {{ form.errors.name }}
                  </div>
                </div>

                <!-- Email -->
                <div class="col-12">
                  <div class="input-group border">
                    <input
                      type="email"
                      class="form-control bg-light border-0 py-3 px-4"
                      :class="{ 'is-invalid': emailInvalid || form.errors.email }"
                      placeholder="Email"
                      v-model="form.email"
                    />
                  </div>
                  <!-- moved outside the input-group -->
                  <div
                    v-if="emailInvalid"
                    class="invalid-feedback d-block mt-2"
                  >
                    Please enter a valid email address.
                  </div>
                  <div
                    v-else-if="form.errors.email"
                    class="invalid-feedback d-block mt-2"
                  >
                    {{ form.errors.email }}
                  </div>
                </div>

                <!-- Mobile Number -->
                <div class="col-12">
                  <div class="input-group border">
                    <input
                      type="text"
                      class="form-control bg-light border-0 py-3 px-4"
                      placeholder="Mobile Number"
                      v-model="form.phone"
                    />
                  </div>
                  <div v-if="form.errors.phone" class="text-danger mt-2">
                    {{ form.errors.phone }}
                  </div>
                </div>

                <!-- Password -->
                <div class="col-12">
                  <div class="input-group border">
                    <input
                      id="password"
                      type="password"
                      class="form-control bg-light border-0 py-3 px-4"
                      placeholder="Password"
                      v-model="form.password"
                    />
                    <label
                      class="input-group-text border-0 border-radius-custom"
                      for="chk"
                    >
                       <i class="fa-regular fa-eye-slash pe-3 text-gradient" id="icon"></i>
                    </label>
                    <input type="checkbox" class="d-none" id="chk" />
                  </div>
                  <div v-if="passwordCriteriaError" class="text-danger mt-2">
                    Password must be at least 8 characters long and include at
                    least one letter and one number.
                  </div>
                  <div v-if="form.errors.password" class="text-danger mt-2">
                    {{ form.errors.password }}
                  </div>
                </div>

                <!-- Confirm Password -->
                <div class="col-12">
                  <div class="input-group border">
                    <input
                      type="password"
                      class="form-control bg-light border-0 py-3 px-4"
                      placeholder="Confirm Password"
                      v-model="form.confirm_password"
                    />
                  </div>
                  <div v-if="passwordMismatch" class="text-danger mt-2">
                    Passwords do not match.
                  </div>
                  <div
                    v-if="form.errors.confirm_password"
                    class="text-danger mt-2"
                  >
                    {{ form.errors.confirm_password }}
                  </div>
                </div>

                <!-- Submit Button -->
                <div class="col-12 mt-4">
                  <button
                   class="btn w-100 py-2 login-btn border-0 btn-gradient"
                    type="submit"
                    
                    :disabled="form.processing"
                  >
                    REGISTER
                  </button>
                </div>

                <input
                  type="hidden"
                  name="redirect"
                  v-model="form.redirect"
                />
              </form>

              <div class="row px-4 mt-2">
                <div class="col-12 mt-2 text-center">
                  <small>
                    Have an Account?
                    <Link
                      class="text-gradient"
                      :href="route('user.login', { redirect: form.redirect })"
                    >
                      Login
                    </Link>
                    
                  </small>
                </div>
                <div class="col-12 mt-4 text-center">
                  <Link class="text-decoration-underline" :href="route('index')">
                    <small>Back to Home Page</small>
                  </Link>
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
import Banner from "./partials/Banner.vue";

export default {
  components: { Link, AppLayout, Banner },

  props: {
    redirect: { type: String, default: null }
  },

  data() {
    return {
      form: useForm({
        name: "",
        email: "",
        phone: "",
        password: "",
        confirm_password: "",
        redirect: this.redirect,
      }),
      emailInvalid: false,
      passwordCriteriaError: false,
      passwordMismatch: false,
    };
  },

  mounted() {
    const pwd = document.getElementById("password");
    const chk = document.getElementById("chk");
    const icon = document.getElementById("icon");
    chk.onchange = () => {
      if (chk.checked) {
        pwd.type = "text";
        icon.classList.replace("fa-eye-slash", "fa-eye");
      } else {
        pwd.type = "password";
        icon.classList.replace("fa-eye", "fa-eye-slash");
      }
    };
  },

  methods: {
    submit() {
      // reset all client‐side flags
      this.emailInvalid = false;
      this.passwordCriteriaError = false;
      this.passwordMismatch = false;

      // 1) email format
      const emailPattern = /^[^\s@]+@[^\s@]+\.[A-Za-z]{2,3}$/;
      if (!emailPattern.test(this.form.email)) {
        this.emailInvalid = true;
        return;
      }

      // 2) password strength
      const pwd = this.form.password;
      const strongPattern = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/;
      if (!strongPattern.test(pwd)) {
        this.passwordCriteriaError = true;
        return;
      }

      // 3) password match
      if (pwd !== this.form.confirm_password) {
        this.passwordMismatch = true;
        return;
      }

      // 4) submit to server
      this.form.post(route("front.end.customer.store"), {
        onSuccess: () => {
          this.form.reset();
          this.$root.showMessage(
            "success",
            '<span class="text-success">Success</span><br/>',
            "A Record Was Created Successfully!"
          );
        },
        onError: () => {
          // clear passwords only if server rejected them
          if (
            this.form.errors.password ||
            this.form.errors.confirm_password
          ) {
            this.form.reset("password", "confirm_password");
          }

          // show email‐taken message if present
          if (this.form.errors.email) {
            this.$root.showMessage(
              "error",
              '<span class="text-danger">Error</span><br>',
              "The email has already been taken!"
            );
          }
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
@media (max-width: 1400px) {
  .form-control {
    padding-bottom: 0.5rem !important;
    padding-top: 0.5rem !important;
  }
  .input-fields {
    padding-right: 1rem !important;
    padding-left: 1rem !important;
  }
}


.text-gradient {
  background: linear-gradient(
    90deg,
    #415a77 0%,
    #0a3f79 0%,
    #163353 34%,
    #142334 67%,
    #0e1e30 80%,
    #0d1b2a 100%
  );
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.btn-gradient {
  background: linear-gradient(
    90deg,
    #415a77 0%,
    #0a3f79 0%,
    #163353 34%,
    #142334 67%,
    #0e1e30 80%,
    #0d1b2a 100%
  ) !important;
  color: #fff !important;
  border: none;
  transition: opacity 0.2s;
}
.btn-gradient:hover {
  opacity: 0.9;
}


.text-gradient {
  display: inline-block; /* ensures the gradient can clip properly */
  background: linear-gradient(
    90deg,
    #415a77 0%,
    #0a3f79 0%,
    #163353 34%,
    #142334 67%,
    #0e1e30 80%,
    #0d1b2a 100%
  );
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent !important;
}



</style>
