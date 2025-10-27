<template>
  <AppLayout>
    <div class="row">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h5>Vehicle Manufacture</h5>
          <p>Manage Vehicle Manufacture.</p>
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
                <SelectInputComponentVue class="w-100" id="vehicle_type" :isMultiple="true" label="Vehicle type"
                  v-model="form.vehicle_type" :options="type" />
              </div>

              <!-- <div class="mb-3 col-md-6">
                <label for="manufacture" class="form-label"
                  >Manufacture Name</label
                >
                <input
                  class="form-control"
                  type="text"
                  id="manufacture_name"
                  name="manufacture_name"
                  v-model="form.manufacture_name"
                />
                <div class="text-danger">
                  {{ form.errors.manufacture_name }}
                </div>
              </div> -->

              <div class="mb-3 col-md-6">
                <SelectInputComponentVue class="w-100" id="manufacture_name" label="Manufacture Name"
                  v-model="form.manufacture_name" :options="manufacture" @change="getModels" />
              </div>



              <div class="mb-3 col-md-6">
                <SelectInputComponentVue class="w-100" id="model" label="Model" v-model="form.model" :options="model" />
              </div>
              <!-- 
              <div class="mb-3 col-md-6">
                <SelectInputComponentVue class="w-100" id="ex_color" label="Ex color" v-model="form.ex_color"
                  :options="color" />
              </div> -->





              <!-- <div class="mb-3 col-md-6">
                <SelectInputComponentVue class="w-100" id="in_color" label="In color" v-model="form.in_color"
                  :options="color" />
              </div> -->



              <!-- checkboxes  -->
              <!-- <div class="mb-3 col-md-12">
                <label class="form-label">Features</label>
                <div class="features-container">
                  <div v-for="(feature, index) in features" :key="index" class="checkbox-container">
                    <input type="checkbox" :id="'feature-' + feature.id" :value="feature.id" v-model="form.features" />
                    <label :for="'feature-' + feature.id">{{
                      feature.name
                    }}</label>
                  </div>
                </div>
              </div> -->


              <div class="mb-3 col-md-6">
                <label for="year" class="form-label">Year</label>
                <input class="form-control" type="text" id="year" name="year" v-model="form.year" />
                <div class="text-danger">
                  {{ form.errors.year }}
                </div>
              </div>





              <!-- <div class="mb-3 col-md-6">
                <label for="auction_grade" class="form-label">Auction Grade</label>
                <input class="form-control" type="text" id="auction_grade" name="auction_grade"
                  v-model="form.auction_grade" />
                <div class="text-danger">
                  {{ form.errors.auction_grade }}
                </div>
              </div> -->

              <!-- <div class="mb-3 col-md-6">
                <label for="interior_condition" class="form-label">Interior Condition</label>
                <input class="form-control" type="text" id="interior_condition" name="interior_condition"
                  v-model="form.interior_condition" />
                <div class="text-danger">
                  {{ form.errors.interior_condition }}
                </div>
              </div> -->




              <div class="mb-3 col-md-6">
                <SelectInputComponentVue class="w-100" id="availability" label="Availability"
                  v-model="form.availability" :error="form.errors.availability" :options="[
                    { id: 'Available', name: 'Available' },
                    { id: 'Arriving', name: 'Arriving' },
                    { id: 'Sold', name: 'Sold' },
                  ]" />
              </div>


              <!-- FUEL TYPES (multi-select) -->
              <!-- <div class="mb-3 col-md-6">
                <label class="form-label">Fuel Types</label>
                <SelectInputComponentVue class="form-select" :isMultiple="true" v-model="form.fuel_types">
                  <option value="Diesel">Diesel</option>
                  <option value="Petrol">Petrol</option>
                  <option value="Hybrid">Hybrid</option>
                  <option value="Electric">Electric</option>
                </SelectInputComponentVue>
              </div> -->

              <div class="mb-3 col-md-6">
                <label for="monthly_price" class="form-label">Price</label>

                <div class="input-group">
                  <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                      aria-expanded="false">
                      {{ form.monthly_price_currency }}
                    </button>
                    <ul class="dropdown-menu">
                      <li>
                        <a class="dropdown-item" href="#" @click.prevent="form.monthly_price_currency = 'USD'">
                          USD
                        </a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="#" @click.prevent="form.monthly_price_currency = 'LKR'">
                          LKR
                        </a>
                      </li>
                    </ul>
                  </div>
                  <input class="form-control" type="text" id="monthly_price" name="monthly_price"
                    v-model="form.monthly_price" placeholder="Amount" />
                </div>
                <div class="text-danger">{{ form.errors.monthly_price }}</div>
              </div>

              <div class="mb-3 col-md-11">
                <SelectInputComponentVue id="countries" label="Countries" v-model="form.countries"
                  :options="countriesOptions" :isMultiple="true" :isRequired="true" />
              </div>



              <!-- inside <form> … <div class="row"> -->
              <template v-for="(section, i) in form.gradeSections" :key="i">
                <label class="form-label">Grades</label>

                <div class="card mb-4 border shadow-sm">
                  <div class="card-body ">

                    <!-- Grade + +/- buttons -->
                    <div class="mb-3">

                      <label class="form-label font-medium font">Vehicle Grade</label>
                      <span style=""> – {{ i + 1 }}</span>
                      <div class="d-flex">
                        <input v-model="section.grade" class="form-control me-2"
                          placeholder="e.g. GR SPORT 4WD 5SEATER" />
                        <button v-if="form.gradeSections.length > 1" type="button" class="btn btn-outline-danger me-2"
                          @click="removeSection(i)">−</button>
                        <button v-if="i === form.gradeSections.length - 1" type="button" class="btn btn-outline-success"
                          @click="addSection">+</button>
                      </div>
                    </div>

                    <div class="row">
                      <!-- Engine CC -->
                      <div class="mb-3 col-md-6">
                        <label class="form-label">Engine CC</label>
                        <div v-for="(cc, idx) in section.engine_capacities" :key="idx" class="d-flex mb-2">
                          <input v-model="section.engine_capacities[idx]" class="form-control me-2"
                            placeholder="e.g. 1200 CC" />
                          <button v-if="section.engine_capacities.length > 1" type="button"
                            class="btn btn-outline-danger me-2"
                            @click="section.engine_capacities.splice(idx, 1)">−</button>
                          <button v-if="idx === section.engine_capacities.length - 1" type="button"
                            class="btn btn-outline-success" @click="section.engine_capacities.push('')">+</button>
                        </div>
                      </div>

                      <!-- Transmission -->
                      <div class="mb-3 col-md-6">
                        <label class="form-label">Transmission</label>
                        <select name="transmission" id="transmission" v-model="section.transmission"
                          class="form-select">
                          <option value="Auto">Auto</option>
                          <option value="Manual">Manual</option>
                          <option value="Triptonic">Triptonic</option>
                        </select>
                      </div>

                      <!-- Drive Type -->
                      <div class="mb-3 col-md-6">
                        <label class="form-label">Drive Type</label>
                        <select name="drive_type" id="drive_type" v-model="section.drive_type" class="form-select">
                          <option value="Right Side Driving">Right Side Driving</option>
                          <option value="Left Side Driving">Left Side Driving</option>
                        </select>
                      </div>

                      <!-- Fuel Types -->
                      <!-- Fuel Types -->
                      <div class="mb-3 col-md-6">
                        <label class="form-label">Fuel Types</label>
                        <MultiSelect v-model="section.fuel_types" :options="fuelTypeOptions" />
                        <div class="text-danger">{{ form.errors.fuel_types }}</div>
                      </div>

                      <!-- ← NEW: Chassis Nos -->
                      <div class="mb-3 col-md-6">
                        <label class="form-label">Chassis Nos</label>
                        <div v-for="(cn, j) in section.chassis_nos" :key="j" class="d-flex mb-2">
                          <input v-model="section.chassis_nos[j]" class="form-control me-2" placeholder="Chassis No" />
                          <button v-if="section.chassis_nos.length > 1" type="button"
                            class="btn btn-outline-danger me-2" @click="section.chassis_nos.splice(j, 1)">−</button>
                          <button v-if="j === section.chassis_nos.length - 1" type="button"
                            class="btn btn-outline-success" @click="section.chassis_nos.push('')">+</button>
                        </div>
                      </div>
                      <!-- Description field -->
                      <div class="mb-3 col-md-6">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" v-model="section.description"
                          class="form-control" placeholder="Enter short description"></textarea>
                        <!-- <div class="text-danger">{{ form.errors.description }}</div> -->
                      </div>


                    </div>
                  </div>
                </div>
              </template>








              <!-- TinyMCE Editor -->
              <div>
                <Editor v-model="form.editorContent" :api-key="apiKey" :init="{
                  plugins: 'lists link image table code help wordcount',
                }" />
              </div>

              <div class="mb-3 col-md-12">
                <div class="row">
                  <div class="col-md-6">
                    <label for="image" class="form-label me-3">Image</label>
                    <br />
                    <FileInputComponent :isRequired="false" id="vehicle_image" :prvImage="Vehicle_Image"
                      v-model="form.vehicle_image" />
                    <div class="text-danger">{{ form.errors.vehicle_image }}</div>
                  </div>
                  <div class="col-md-6">
                    <!-- Image Upload Section -->
                    <div class="mb-4" style="padding: 0.79rem 1.125rem;">
                      <h6>Gallery Images</h6>

                      <!-- Upload New Button -->
                      <div class="upload-button">
                        <label class="btn btn-outline-primary">
                          Upload Images
                          <input type="file" multiple @change="handleImageUpload" hidden />
                        </label>
                      </div>

                      <div class="image-upload-container mt-2" v-if="form.uploaded_Images.length > 0">
                        <div class v-for="(image, index) in form.uploaded_Images" :key="index">
                          <div class="image-box">
                            <img :src="image.preview" alt="Uploaded Image" class="img-fluid" />
                            <button type="button" class="remove-btn" @click="removeUploadedImage(index)">
                              <i class="fas fa-times"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                      <!-- <p class="mt-2">Accepts images, videos, or 3D models</p> -->
                    </div>
                  </div>
                </div>
              </div>
              <div class="mb-3 col-md-6">

                <SelectInputComponentVue id="featured" label="Is Featured" :error="form.errors.featured" :options="[
                  {
                    id: '1',
                    name: 'Enable',
                  },
                  {
                    id: '0',
                    name: 'Disable',
                  },
                ]" v-model="form.featured" />

                <SelectInputComponentVue id="latest" label="Is Latest" :error="form.errors.latest" :options="[
                  {
                    id: '1',
                    name: 'Enable',
                  },
                  {
                    id: '0',
                    name: 'Disable',
                  },
                ]" v-model="form.latest" />

                <SelectInputComponentVue id="status" label="status" :error="form.errors.status" :options="[
                  {
                    id: '1',
                    name: 'Active',
                  },
                  {
                    id: '0',
                    name: 'Inactive',
                  },
                ]" v-model="form.status" />
              </div>
            </div>
            <div class="mt-2">
              <button type="submit" class="btn btn-main me-2" :disabled="form.processing">
                {{ vehicle ? "Update" : "Save" }}
              </button>
              <Link class="btn btn-outline-danger" :href="route('vehicle_manufacture.index')">Cancel</Link>
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
import SelectInputComponentVue from "@/Components/SelectInputComponent.vue";
import Editor from "@tinymce/tinymce-vue";
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import MultiSelect from '@/components/MultiSelect.vue';

// import SuspendedUserAlert from "./Partials/SuspendedUserAlert.vue";

export default {
  components: {
    Link,
    AppLayout,
    FileInputComponent,
    MultiSelect,
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
    allRoles: Object,
    backendUser: Object,
    branches: Array,
    features: Array,
    service_types: Object,
    vehicle_manufacture: Object,
    vehicle: Object,
    type: Object,
    manufacture: Object,
    color: Object,
    model: Object,
    countries: Array,

  },

  data() {
    return {
      fuelTypeOptions: [
        { id: 'Diesel', name: 'Diesel' },
        { id: 'Petrol', name: 'Petrol' },
        { id: 'Hybrid', name: 'Hybrid' },
        { id: 'Electric', name: 'Electric' },
      ],
      apiKey: '4ueqybvvojou72t5k8dn775enh0tfchqobzfd9upg1ov5p11',
      form: useForm({
        id: "",
        vehicle_type: "",
        manufacture_name: "",
        model: "",
        seo_url: "",
        ex_color: "",
        in_color: "",
        features: [],
        year: "",
        chassis_no: "",
        // condition: "",
        seats: "",
        doors: "",
        passengers: "",
        engine_capacity: "",
        mileage: "",
        countries: "",

        fuel_efficiency: "", //

        // drive_type: "",
        auction_grade: "",
        interior_condition: "",
        availability: "",
        vehicle_status: '',
        editorContent: "",
        featured: "",
        latest: "",
        status: "",
        vehicle_image: "",
        monthly_price: "",
        monthly_price_currency: "LKR",
        initial_payment: "",
        initial_payment_currency: "LKR",
        uploaded_Images: [],


        gradeSections: [
          {
            grade: '',
            engine_capacities: [''],
            transmission: '',
            drive_type: '',
            fuel_types: [],
            chassis_nos: [''],
            description: ''
          }
        ],

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
    if (!this.vehicle) return;

    this.form.id = this.vehicle.id;
    this.form.vehicle_type = JSON.parse(this.vehicle.vehicle_type_id || '[]');
    this.form.manufacture_name = this.vehicle.manufacture_id;
    this.form.model = this.vehicle.vehicle_model_id;
    this.form.year = this.vehicle.year;
    this.form.availability = this.vehicle.availability;
    this.form.editorContent = this.vehicle.editorContent;
    this.form.featured = this.vehicle.featured;
    this.form.latest = this.vehicle.latest;
    this.form.status = this.vehicle.status;
    this.form.monthly_price = this.vehicle.monthly_price;
    this.form.monthly_price_currency = this.vehicle.monthly_price_currency || 'LKR';
    this.form.initial_payment = this.vehicle.initial_payment;
    this.form.initial_payment_currency = this.vehicle.initial_payment_currency || 'LKR';
    this.form.countries = JSON.parse(this.vehicle.countries || '[]');

    let sections = Array.isArray(this.vehicle.grades)
      ? this.vehicle.grades
      : JSON.parse(this.vehicle.grades || '[]');

    this.form.gradeSections = sections.length
      ? sections
      : [
        {
          grade: '',
          engine_capacities: [''],
          transmission: '',
          drive_type: '',
          fuel_types: [],
          chassis_nos: [''],
          description: ''

        },
      ];

    if (this.vehicle.media && this.vehicle.media.length) {
      this.vehicle.media
        .filter(m => m.custom_properties?.type === 'vehicle_gallery')
        .forEach(media => {
          this.form.uploaded_Images.push({
            file: media,
            preview: media.original_url,
          });
        });
    }
  },

  watch: {

    'form.monthly_price_currency': {
      handler(newVal) {
        this.form.initial_payment_currency = newVal
      },

    },
    'form.countries': {
      handler(newVal) {
        console.log('Countries updated:', newVal);
      },
      immediate: true
    }

  },
  computed: {
    countriesOptions() {
      return this.countries.map((country) => ({
        id: country.id,
        name: country.name,
      }));
    },
    Vehicle_Image() {
      if (this.vehicle && this.vehicle.media.length > 0) {
        const mainImage = this.vehicle.media.find(
          media => media.custom_properties?.type === "vehicle_main"
        );
        return mainImage ? mainImage.original_url : "";
      }
      return "";
    },
    initialPaymentError() {
      const init = parseFloat(this.form.initial_payment) || 0
      const monthly = parseFloat(this.form.monthly_price) || 0
      return init > monthly
        ? 'Initial payment cannot exceed Full Price'
        : ''
    }
  },
  methods: {
    addSection() {
      this.form.gradeSections.push({
        grade: '',
        engine_capacities: [''],
        transmission: '',
        drive_type: '',
        fuel_types: [],
        chassis_nos: [''],
        description: ''

      });
    },

    removeSection(index) {
      this.form.gradeSections.splice(index, 1);
    },

    handleImageUpload(event) {
      const files = Array.from(event.target.files);
      files.forEach(file => {
        if (file && file.type.startsWith("image/")) {
          const reader = new FileReader();
          reader.onload = e => {
            this.form.uploaded_Images.push({ file, preview: e.target.result });
          };
          reader.readAsDataURL(file);
        } else {
          console.error("Selected file is not an image:", file);
        }
      });
    },
    // removeUploadedImage(index) {
    //   this.form.uploaded_Images.splice(index, 1);
    // },
    removeUploadedImage(index) {
      const image = this.form.uploaded_Images[index];
      if (image.file.original_url && image.preview) {
        Inertia.post(
          route("vehicle.image.remove"),
          { imageUrl: image.preview, product_id: this.vehicle.id },
          {
            onSuccess: () => { this.form.uploaded_Images.splice(index, 1); },
            onError: errors => { console.error("Error deleting image:", errors); }
          }
        );
      } else {
        this.form.uploaded_Images.splice(index, 1);
      }
    },
    getModels() {
      this.$inertia.reload({ method: "POST", data: { _method: "GET", manufacture_id: this.form.manufacture_name } });
    },
    submit() {
      if (this.initialPaymentError) return;
      const first = this.form.gradeSections[0] || {};
      this.form.fuel_types = (first.fuel_types || []).map(o => o.id);
      this.form.transmission = first.transmission;
      this.form.drive_type = first.drive_type;
      this.form.fuel_types = first.fuel_types;
      this.form.description = first.description;
      this.form.post(
        this.vehicle ? route("vehicle.update") : route("vehicle.store"),
        {
          onSuccess: () => { this.form.reset(); this.$root.showMessage("success", '<span class="text-success">Success</span><br/>', "A Record Was Created Successfully!"); },
          onError: () => { this.form.reset("password", "password_confirmation"); this.$root.showMessage("error", '<span class="text-danger">Error</span><br>', "Something went wrong!"); }
        }
      );
    },
  },
};
</script>

<style scoped>
.image-upload-container {
  display: flex;
  flex-wrap: wrap;
}

.remove-btn {
  position: absolute;
  top: 0;
  right: 0;
  /* background: red; */
  color: red;
  border: none;
  border-radius: 50%;
}

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

/* Hover Effect */
.checkbox-container label:hover::before {
  border-color: #007bff;
}

.upload-button {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}

.upload-button label {
  cursor: pointer;
}

.image-box {
  position: relative;
  width: 100px;
  height: 100px;
  overflow: hidden;
  border: 1px solid #ddd;
  border-radius: 5px;
  margin: 3px;
}

.image-box img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-btn {
  position: absolute;
  top: 5px;
  right: 5px;
  background-color: red;
  border: none;
  color: white;
  cursor: pointer;
  font-size: 1rem;
}

.image-upload-container {
  border: 1px dotted #848484;
  padding: 10px;
  border-radius: 5px;
}

.border {
  border: 1px solid #3a4857 !important;
}
</style>
