<template>
  <AppLayout>
    <Banner />

    <div class="container">
      <div
        class="row justify-content-center align-items-center py-5"
        style="min-height: 100vh"
      >
        <!-- 
          col-12    → full width on xs (<576px)
          col-sm-10 → 10/12 on sm (≥576px)
          col-md-8  →  8/12 on md (≥768px)
          col-lg-5  →  5/12 on lg (≥992px and up)
        -->
        <div class="col-12 col-sm-10 col-md-8 col-lg-5 px-3 px-sm-4">
          <div class="card bg-white shadow-sm border rounded-5">
            <div class="card-body mb-4">
              <div class="header text-center py-4">
                <img
                  src="/images/new_logo (1).png"
                  alt="Hiru Cars Logo"
                  class="login-logo mx-auto d-block"
                />
                <h4 class="text-center fw-bold mt-3">
                  Sign In To Your Account
                </h4>
              </div>

              <div
                v-if="form.errors.username || form.errors.password"
                class="alert alert-danger text-center"
              >
                Incorrect email or password.
              </div>

              <form
                class="row input-fields gy-4 px-3"
                id="formAccountSetting"
                @submit.prevent="submit"
              >
                <div class="col-12">
                  <div class="input-group border">
                    <input
                      type="text"
                      class="form-control bg-light border-0 py-3 px-4"
                      placeholder="Username"
                      id="username"
                      v-model="form.username"
                    />
                    <span class="input-group-text border-0">
                      <i class="fa-solid fa-user pe-3 text-gradient"></i>
                    </span>
                  </div>
                </div>

                <div class="col-12">
                  <div class="input-group border">
                    <input
                      type="password"
                      class="form-control bg-light border-0 py-3 px-4"
                      placeholder="Password"
                      id="password"
                      v-model="form.password"
                    />
                    <label
                      for="chk"
                      class="input-group-text border-0 border-radius-custom"
                    >
                      <i
                        class="fa-regular fa-eye-slash pe-3 text-gradient"
                        id="icon"
                      ></i>
                    </label>
                    <input type="checkbox" class="d-none" id="chk" />
                  </div>
                </div>

                <div class="col-6">
                  <div class="form-check">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      id="flexCheckDefault"
                    />
                    <label
                      class="form-check-label"
                      for="flexCheckDefault"
                    >
                      <small> Remember Me</small>
                    </label>
                  </div>
                </div>
                <div class="col-6 text-end">
                  <Link
                    class="text-gradient"
                    style="font-size: small;"
                    :href="route('forgotpassword')"
                  >
                    Forgot Password
                  </Link>
                </div>

                <input type="hidden" name="redirect" v-model="form.redirect" />

                <div class="col-12 mt-4">
                  <button
                    class="btn w-100 py-2 login-btn border-0 btn-gradient"
                    type="submit"
                    :disabled="form.processing"
                  >
                    LOGIN
                  </button>
                </div>
              </form>

              <div class="row px-4 mt-2">
                <div class="col-12 mt-2 text-center">
                  <small>
                    No Account yet?
                    <Link
                      class="text-gradient"
                      :href="route('register', { redirect: form.redirect })"
                    >
                      Register
                    </Link>
                  </small>
                </div>
                <div class="col-12 text-center py-3">
                  Or Sign Up using
                </div>
                <div class="col-12 d-flex justify-content-center">
                  <!-- <a :href="route('social.oauth', 'google')"> -->
                  <a :href="route('social.oauth', ['google', { redirect: form.redirect }])">
                    <div class="google px-3">
                      <img
                        src="/images/icons/search (1).png"
                        width="35"
                        alt="Google"
                      />
                    </div>
                  </a>
                  <!-- <a :href="route('social.oauth', 'facebook')"> -->
                    <!-- <a :href="route('social.oauth', ['facebook', { redirect: form.redirect }])">
                    <div class="facebook px-3">
                      <img
                        src="/images/icons/facebook (1).png"
                        width="35"
                        alt="Facebook"
                      />
                    </div>
                  </a> -->
                </div>
                <div class="col-12 mt-4 text-center">
                  <Link
                    class="text-decoration-underline"
                    :href="route('index')"
                  >
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
import AppLayout from '@@/Layouts/AppLayout.vue';
import { Link, useForm } from "@inertiajs/inertia-vue3";
import FileInputComponent from "@/Components/FileInputComponent.vue";
import Banner from './partials/Banner.vue';

// import SelectInputComponentVue from '../../Components/SelectInputComponent.vue';

// import SuspendedUserAlert from "./Partials/SuspendedUserAlert.vue";

export default {
  components: {
    AppLayout,
    Link,
    FileInputComponent,
    Banner,
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
    // redirect: { type: String, default: null },
     redirect: { type: String, default: null },
  },

  data() {
    return {
      form: useForm({
        id: "",
        username: "",
        password: "",
        // redirect: this.redirect,
         redirect: this.redirect,
      }),
    };
  },

  methods: {
     submit() {
     // the `redirect` field is already in `this.form`
     this.form.post(route("front.end.customer.login"), {
       onSuccess: () => {
         this.form.reset();
         this.$root.showMessage(
           "success",
           '<span class="text-success">Success</span><br/>',
           "Logging Successful!"
         );
       },
       onError: () => {
         this.form.reset("password");
       },
     });
   },
  },

};
</script>


<style scoped>

.login-logo {
  display: block !important;
  max-width: 250px !important;   /* make it bigger */
  height: auto !important;
  margin-bottom: 1.5rem !important; 
}

.form-control {
  box-shadow: none;
  border-top-left-radius: 18px;
  border-bottom-left-radius: 18px;
}

.input-group-text {
  border-top-right-radius: 18px;
  border-bottom-right-radius: 18px;
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
  font-size: 15px
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


/* gradient button background */
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


</style>
