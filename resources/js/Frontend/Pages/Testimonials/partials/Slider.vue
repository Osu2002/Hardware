<template>
    <section class="container my-5">
        <div class="row g-4 align-items-center">
            <!-- LEFT PANEL -->
            <div class="col-lg-6">
                <div class="p-5 rounded-4 bg-light leasing-panel">
                    <h3 class="mb-4 fw-bold">Vehicle Leasing Facilities</h3>
                    <form @submit.prevent="calculate">
                        <div class="row gy-3 gx-3">
                            <!-- Brand -->
                            <div class="col-md-6">
                                <div class="bg-white rounded-4 p-2">
                                    <label
                                        class="form-label small text-secondary"
                                    >
                                        Make of your current car
                                    </label>
                                    <select
                                        v-model="brand"
                                        class="form-select border-0 p-0"
                                    >
                                        <option disabled value="">
                                            Select Vehicle Brand
                                        </option>
                                        <option v-for="b in brands" :key="b">
                                            {{ b }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <!-- Model -->
                            <div class="col-md-6">
                                <div class="bg-white rounded-4 p-2">
                                    <label
                                        class="form-label small text-secondary"
                                    >
                                        Model of your current car
                                    </label>
                                    <select
                                        v-model="model"
                                        class="form-select border-0 p-0"
                                    >
                                        <option disabled value="">
                                            Select Vehicle Model
                                        </option>
                                        <option v-for="m in models" :key="m">
                                            {{ m }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <!-- Type -->
                            <div class="col-md-6">
                                <div class="bg-white rounded-4 p-2">
                                    <label
                                        class="form-label small text-secondary"
                                    >
                                        Vehicle you are interested in?
                                    </label>
                                    <select
                                        v-model="type"
                                        class="form-select border-0 p-0"
                                    >
                                        <option disabled value="">
                                            Select Vehicle Type
                                        </option>
                                        <option v-for="t in types" :key="t">
                                            {{ t }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <!-- Button -->
                            <div class="col-md-6 d-grid">
                                <button
                                    type="submit"
                                    class="btn btn-gradient rounded-4 p-2 btn-lg"
                                >
                                    Calculate
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- RIGHT IMAGE -->
            <div class="col-lg-6">
                <div class="position-relative">
                  <!-- 1) placeholder until imageLoaded flips true -->
                  <div
                    v-if="!leasingImageLoaded"
                    class="skeleton-placeholder"
                  ></div>

                  <!-- 2) your illustration, fading in -->
                  <img
                    src="/images/h93.jpg (1).png"
                    alt="Leasing Illustration"
                    class="w-100 rounded-4 object-fit-cover"
                    :class="{ 'is-loaded': leasingImageLoaded }"
                    @load="onLeasingImageLoad"
                  />
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: "LeasingFacilities",
    data() {
        return {
            brand: "",
            model: "",
            type: "",
            brands: ["Toyota", "Honda", "Nissan", "Mazda"],
            models: ["Model A", "Model B", "Model C"],
            types: ["Sedan", "SUV", "Hatchback", "Coupe"],
            imageloaded: {},
            leasingImageLoaded: false
        };
    },
    methods: {
        calculate() {
            alert(
                `Brand: ${this.brand}\nModel: ${this.model}\nType: ${this.type}`
            );
        },
        onLeasingImageLoad() {
          this.leasingImageLoaded = true;
        },
    },
};
</script>

<style scoped>
/* Panel tweaks */
.leasing-panel {
    /* extra bottom padding so bg-light sits lower */
    padding-bottom: 3rem;
}

/* override Bootstrap light background */
.bg-light {
    background-color: #f2f2f2 !important;
}

/* keep the selects pill-shaped */
.bg-white {
    background-color: #fff !important;
}

/* remove default form-select border & height */
.form-select {
    height: auto;
}

/* cover image */
.object-fit-cover {
    object-fit: cover;
    height: 100%;
}

/* big gradient button */
.btn-gradient {
    background: linear-gradient(135deg, #3a3f5c, #1e1f2d);
    color: #fff;
    border: none;
    border-radius: 0.75rem;
}
.btn-gradient:hover {
    background: linear-gradient(135deg, #1e1f2d, #3a3f5c);
}

.skeleton-placeholder {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: linear-gradient(
    to right,
    #e0e0e0 0%,
    #f8f8f8 50%,
    #e0e0e0 100%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 16px; /* matches your rounded-4 */
}

@keyframes shimmer {
  0%   { background-position: -200% 0; }
  100% { background-position:  200% 0; }
}

/* fade-in the image */
.object-fit-cover {
  object-fit: cover;
  height: 100%;
  opacity: 0;
  transition: opacity 0.5s ease;
}
.object-fit-cover.is-loaded {
  opacity: 1;
}
</style>
