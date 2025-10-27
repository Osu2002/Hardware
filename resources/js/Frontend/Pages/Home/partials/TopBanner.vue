<template>
  <section class="hero-section">
    <!-- <img src="/images/Background.png" class="hero-img" alt="Background" /> -->
    <ImageSlider :images="sliderImages" />

    <!-- add advanced-open class when expanded -->
    <div class="filter-overlay filter-row" :class="{ 'advanced-open': advance_search }">
      <div class="col-md-12">
        <div class="filters-container">
          
          <!-- BASIC FILTERS -->
          <div class="basic-filters">
            <div class="row mt-4">
              <div class="col-6 mb-4">
                <SelectInputComponent
                  v-model="form.manufacturer"
                  placeholder="Select Make"
                  @change="onChangeField"
                  :options="$page.props.manufactures.map(m => ({ id: m.MARKA_NAME, name: m.MARKA_NAME }))"
                  class="field-input"
                />
              </div>
              <div class="col-6 mb-4">
                <SelectInputComponent
                  v-model="form.model"
                  placeholder="Select Model"
                  @change="onChangeField"
                  :options="$page.props.models.map(m => ({ id: m.MODEL_NAME, name: m.MODEL_NAME }))"
                  class="field-input"
                />
              </div>
            </div>

            <div class="row">
              <div class="col-6 mb-4">
                <SelectInputComponent
                  v-model="form.year_from"
                  placeholder="Select Year From"
                  @change="onChangeField"
                  :options="$page.props.years.map(m => ({ id: m.YEAR, name: m.YEAR }))"
                  class="field-input"
                />
              </div>
              <div class="col-6 mb-4">
                <SelectInputComponent
                  v-model="form.year_to"
                  placeholder="To"
                  @change="onChangeField"
                  :options="$page.props.years.map(m => ({ id: m.YEAR, name: m.YEAR }))"
                  class="field-input"
                />
              </div>
            </div>

            <div class="row">
              <div class="col-6 mb-4">
                <SelectInputComponent
                  v-model="form.engine"
                  placeholder="Engine CC"
                  @change="onChangeField"
                  :options="$page.props.engineCapacity.map(m => ({ id: m.ENG_V, name: m.ENG_V }))"
                  class="field-input"
                />
              </div>
              <div class="col-6 mb-4">
                <SelectInputComponent
                  v-model="form.chassis"
                  placeholder="Chassis ID"
                  @change="onChangeField"
                  :options="$page.props.chassisNumbers.map(m => ({ id: m.KUZOV, name: m.KUZOV }))"
                  class="field-input"
                />
              </div>
            </div>

            <div class="row">
              <div class="col-6 mb-4">
                <SelectInputComponent
                  v-model="form.grade"
                  placeholder="Auction Grade"
                  @change="onChangeField"
                  :options="$page.props.Condition.map(m => ({ id: m.RATE, name: m.RATE }))"
                  class="field-input"
                />
              </div>
              <div class="col-6 mb-4">
                <SelectInputComponent
                  v-model="form.start_price"
                  placeholder="Select Start Price"
                  @change="onChangeField"
                  :options="$page.props.PriceQuery.map(m => ({ id: m.AVG_PRICE, name: m.AVG_PRICE }))"
                  class="field-input"
                />
              </div>
            </div>
          </div>

          <!-- ADVANCED FILTERS (appears to the right on desktop, stacked on mobile) -->
          <div class="advanced-filters" v-if="advance_search">
            <div class="row">
              <div class="col-6 mb-4">
                <SelectInputComponent
                  v-model="form.available_days"
                  placeholder="Select Auction Date"
                  @change="onChangeField"
                  :options="$page.props.auctionDates.map(m => ({ id: m.AUCTION_DATE, name: m.AUCTION_DATE }))"
                  class="field-input"
                />
              </div>
              <div class="col-6 mb-4">
                <SelectInputComponent
                  v-model="form.color"
                  placeholder="Select Color"
                  @change="onChangeField"
                  :options="$page.props.colorQuery.map(m => ({ id: m.COLOR, name: m.COLOR }))"
                  class="field-input"
                />
              </div>
            </div>
            <div class="row">
              <div class="col-6 mb-4">
                <SelectInputComponent
                  v-model="form.mileage_from"
                  placeholder="Mileage From"
                  @change="onChangeField"
:options="Array.isArray($page.props.Mileage)
              ? $page.props.Mileage.map(m => ({ id: m.MILEAGE, name: m.MILEAGE }))
              : []"                  class="field-input"
                />
              </div>
              <div class="col-6 mb-4">
                <SelectInputComponent
  v-model="form.mileage_to"
  placeholder="To"
  @change="onChangeField"
  :options="Array.isArray($page.props.Mileage)
              ? $page.props.Mileage.map(m => ({ id: m.MILEAGE, name: m.MILEAGE }))
              : []"
  class="field-input"
/>

              </div>
            </div>
          </div>
        </div>

        <!-- Logos row -->
        <div class="col-12 logos-row" v-if="filteredManufactures.length">
          <a
            v-for="m in filteredManufactures"
            :key="m.id"
            :href="`/live-auction?manufacturer=${encodeURIComponent(m.name)}&search=true`"
            class="logo-btn"
          >
            <img
              v-if="m.media?.[0]?.original_url"
              :src="m.media[0].original_url"
              :alt="m.name"
              @error="handleImageError(m.name)"
            />
          </a>
        </div>

        <!-- Reset/Search/Advanced buttons -->
        <div class="d-flex justify-content-between align-items-center button-row mt-4">
          <button class="btn btn-outline-reset" @click.prevent="resetFilter">
            Reset
          </button>
          <button class="btn btn-search" @click.prevent="filterData">
            <i class="fa fa-search me-2"></i> Search
          </button>
          <div class="text-end">
            <button class="btn btn-advanced" @click.prevent="advanceFilter">
              {{ advance_search ? "HIDE SEARCH" : "ADVANCE SEARCH" }}
              <i
                :class="[
                  'fa-solid',
                  advance_search ? 'fa-angles-left' : 'fa-angles-right',
                ]"
              ></i>
            </button>
          </div>
        </div>

        <!-- Loading overlay -->
        <div
          v-if="loading"
          class="loading-overlay d-flex justify-content-center align-items-center"
        >
          <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading…</span>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>


<script>
import SelectInputComponent from "../partials/SelectInputComponent.vue";
import ImageSlider from './imageSlider.vue';

export default {
  components: { SelectInputComponent, ImageSlider },
  props: {
    reload: Boolean,
    category_manufactures: { type: Array, default: () => [] },
  },
  data() {
    return {
      sliderImages: [

      
        // '/images/Background.png',
         '/images/13.png',
        '/images/background1.jpg',
        '/images/background5.jpg',

        '/images/background2.jpg',
        '/images/background4.jpg',
        '/images/background6.jpg',
        '/images/background7.jpg',
        // '/images/background8.jpg',





      ],
      form: {

        manufacturer: null,
        model: null,
        year_from: null,
        year_to: null,
        engine: null,
        chassis: null,
        grade: null,
        start_price: null,
        available_days: null,
        color: null,
        mileage_from: null,
        mileage_to: null,
      },
      loading: false,
      advance_search: false,
    };
  },
  computed: {
    filteredManufactures() {
      const desiredManufacturers = ["Toyota", "Honda", "Mitsubishi", "Nissan", "Suzuki"].map(name => name.toLowerCase());
      const filtered = this.category_manufactures.filter((m) =>
        desiredManufacturers.includes(m.name.toLowerCase())
      );
      return filtered.sort((a, b) => {
        return desiredManufacturers.indexOf(a.name.toLowerCase()) - desiredManufacturers.indexOf(b.name.toLowerCase());
      });
    },
  },
  mounted() {
    if (this.reload) {
      this.$inertia.reload({ data: { ...this.form, search: false } });
    }
    console.log("Received category_manufactures:", this.category_manufactures);
    console.log("Filtered manufactures:", this.filteredManufactures);
    this.filteredManufactures.forEach((m) => {
      console.log(`Manufacturer: ${m.name}, Media:`, m.media);
      if (m.media?.[0]?.original_url) {
        console.log(`Image URL for ${m.name}:`, m.media[0].original_url);
      } else {
        console.warn(`No image URL for ${m.name}`);
      }
    });
  },
  methods: {
    onChangeField() {
      this.loading = true;
      this.$inertia.reload({
        method: "POST",
        data: { ...this.form, _method: "GET" },
        onSuccess: () => (this.loading = false),
      });
    },
    resetFilter() {
      Object.keys(this.form).forEach((k) => (this.form[k] = null));
      this.advance_search = false;
    },
    
    filterData() {
      this.$inertia.visit(route("live.auction"), {
        data: { ...this.form, search: true },
        onSuccess: () => {
          // this.setQueryData();
          this.advance_search = true;
        },
      });
    },
    advanceFilter() {
      this.advance_search = !this.advance_search;
    },
    handleImageError(manufacturerName) {
      console.warn(`Image failed to load for ${manufacturerName}. Check the URL:`, this.filteredManufactures.find(m => m.name === manufacturerName)?.media?.[0]?.original_url);
    },
  },
};
</script>


<style scoped>
.hero-section {
  position: relative;
  width: 100%;
  min-height: 100vh;
}

.hero-img {
  width: 100%;
  height: 100vh !important;
  object-fit: cover;
}


.filter-overlay {
  position: absolute;
  top: 58%;
  left: 15%;
  transform: translateY(-50%);
  width: 600px;

  background: linear-gradient(to bottom,
      #01112e8c 0%,
      #07162c2f 25%,
      #102b4752 69%,
      #88888881 100%);
  backdrop-filter: blur(5px);
  border-radius: 20px;
  padding: 1.5rem;
  box-shadow: 0 12px 24px #03307456;
  z-index: 10;
}

.filter-search {
  display: grid !important;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1rem 1.5rem;
}

.filter-search>.col-6 {
  flex: none !important;
  width: auto !important;
  max-width: none !important;
}


.filter-search>.col-12.logos-row {
  grid-column: 1 / -1;
}


.logos-row {
  gap: 1.75rem;

}

.logo-btn {
  margin: 0 1rem;
  flex: 1 1 0;
  min-width: 0;
  text-align: center;
}

.logo-btn img {
  max-height: 50px;
  width: auto;
  object-fit: contain;
  opacity: 0.85;
  transition: opacity 0.2s;
}

.logo-btn img:hover {
  opacity: 1;
}


.mobile-padding {
  margin-top: 10px;
  margin-bottom: 9px;
}

.field-label {
  display: block;
  margin-bottom: -0.99rem;
  color: #f1f1f1;
  font-weight: 500;
}

.field-input {
  height: 40px;
}

.field-input>>>.select2-selection__rendered,
.field-input>>>.select2-selection,
.field-input>>>.select2-selection__placeholder {
  height: 40px;
  line-height: 40px;
  font-size: 1rem;
  font-weight: 500;
  border-radius: 10px !important;
  color: #1F2937 !important;
  font-family: 'Poppins', sans-serif !important;
}

.field-input>>>.select2-container--default .select2-selection--single .select2-selection__arrow {
  height: 40px !important;
}


.field-input>>>input.form-control {
  border-radius: 60px !important;

  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}


.button-row {
  gap: 1rem;
}

.btn-outline-reset {
  flex: 1;
  background: transparent;
  color: #f1f1f1;
  border: 1px solid rgba(241, 241, 241, 0.7);
  border-radius: 20px;
  font-size: 1.1rem;
  padding: 0.5rem 0;
   
}

.btn-outline-reset:hover {
  background: rgba(241, 241, 241, 0.2);
}

.btn-search {
  flex: 1;
  background: #0f172a;
  color: #fff;
  font-size: 1.1rem;
  border: 1px solid white;
  border-radius: 20px;
  padding: 0.5rem 0;
}

.btn-search:hover {
  background: #1e293b;
}

.btn-advanced {
  flex: 1;
  background: transparent;
  color: #C8E2FF;
  border: none;
  text-align: right;
  font-size: 1.1rem;
  padding-right: 2.5rem;
}

.btn-advanced i {
  margin-left: 0.25rem;
}


.loading-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 20px;
}

/* Responsive Breakpoints */
@media (max-width: 1024px) and (min-width: 769px) {
  .filter-overlay {
    width: 53%;
    padding: 1rem;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  }

  .logo-btn img {
    max-height: 40px;
  }
}

@media (max-width: 768px) {
  .filter-overlay {
    width: 90%;
    padding: 0.75rem;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  }

  .filter-row {
    margin-top: 50px;

  }

  .filter-search {
    grid-template-columns: 1fr !important;
    gap: 0.75rem 1rem !important;
  }


  .logos-row {
    gap: 1rem;
  }

  .logo-btn img {
    max-height: 35px;
  }

 

  .btn-outline-reset,
  .btn-search,
  .btn-advanced {
    width: 100%;
    text-align: center;
    padding: 0.5rem 0;
  }

  .btn-advanced {
    padding-right: 0;
  }

   .filter-overlay .advanced-filters {
    max-height: 0;                /* start closed */
    opacity: 0;                   /* invisible */
    overflow: hidden;             /* clip contents */
    padding-top: 0;               /* collapse padding */
    padding-bottom: 0;
    transition: 
      max-height 0.4s ease,      /* smooth height change */
      opacity 0.3s ease 0.1s,     /* fade in/out slightly delayed */
      padding 0.4s ease;          /* padding grows/shrinks */
  }

  .filter-overlay.advanced-open .advanced-filters {
    max-height: 800px;            /* tall enough for all your fields */
    opacity: 1;                   /* fully visible */
    padding-top: 1rem;            /* restore spacing */
    padding-bottom: 1rem;
  }

  .button-row {
    display: flex;
    flex-direction: column;
    align-items: center; /* so width:auto buttons get centered */
    gap: 1rem;
  }

  .filter-overlay .advanced-filters {
    max-height: 0 !important;
    opacity: 0 !important;
    overflow: hidden !important;
    transition: max-height 0.5s ease, opacity 0.5s ease, padding 0.5s ease;
    /* optionally collapse padding for a tighter slide: */
    padding-top: 0 !important;
    padding-bottom: 0 !important;
  }

  /* expanded state */
  .filter-overlay.advanced-open .advanced-filters {
    max-height: 800px !important;   /* large enough to show all fields */
    opacity: 1 !important;
    padding-top: 1rem !important;   /* restore any vertical padding */
    padding-bottom: 1rem !important;
  }
  

 
}

/* Fine-tune for tiny screens (576px and below) */
@media (max-width: 576px) {
  .filter-overlay {
    width: 95%;
    padding: 0.5rem;
  }

  .logos-row {
    gap: 0.5rem;
  }

  .logo-btn img {
    max-height: 30px;
  }


  .filter-search {
    gap: 0.5rem !important;
  }

  .button-row {
    gap: 1rem;
  }

  .btn-outline-reset,
  .btn-search,
  .btn-advanced {
    font-size: 0.9rem;
    padding: 0.25rem 0;
  }
}

/* Specific adjustments for iPhone SE  */
@media (max-width: 375px) {

  .hero-section {

    margin-top: 56px !important;
  }


  .filter-overlay {

    margin-top: 1rem !important;
  }
}


@media (min-width: 769px) {
  /* 1) Turn the filters container into a 2-column grid */
  /* .filters-container {
    display: grid;
    grid-template-columns: 1fr auto; 
    column-gap: 1rem;
    align-items: start;            
  } */

  /* 2) Let basic-filters flow as before—no extra margin or padding */
  

  /* 3) Pull advanced out of the document flow, aligned to grid’s second column */
  .advanced-filters {
    /* no margin-top; grid’s align-items takes care of vertical alignment */
    padding-left: 0;   /* clear any leftover gutter hacks */
    padding-right: 0;
  }

  /* 4) Animate the overlay width as before */
  /* .filter-overlay {
    transition: width 0.3s ease;
     
  } */

  
 /* .filter-overlay.advanced-open {
    width: 900px !important;
  } */

  /* 2) Force the advanced-filters block to sit in that rightmost 300px */
  /* .filter-overlay.advanced-open .advanced-filters {
    width: 300px !important;
     margin-top: 1.5rem !important;
  } */

  /* 3) Stack each of the four .col-6s into full-width rows of 300px */
  /* .filter-overlay.advanced-open .advanced-filters .col-6 {
    flex: 0 0 100% !important;
    max-width: 100%     !important;
  } */
}

@media (min-width: 1025px) {
  .filters-container {
    display: grid;
    grid-template-columns: 1fr auto;
    column-gap: 1rem;
    align-items: start;
  }

  .filter-overlay {
    transition: width 0.3s ease;
  }
  .filter-overlay.advanced-open {
    width: 900px !important;
  }
  .filter-overlay.advanced-open .advanced-filters {
    width: 300px !important;
    margin-top: 1.5rem !important;
  }
  .filter-overlay.advanced-open .advanced-filters .col-6 {
    flex: 0 0 100% !important;
    max-width: 100%     !important;
  }
}


@media (max-width: 1024px) {
  /* full-width overlay & stack everything */
  .filter-overlay {
    width: 90%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  }

  /* turn the filters container back into normal block */
  .filters-container {
    display: block !important;
  }

  /* collapse advanced up top, then drop it down underneath */
  .filter-overlay .advanced-filters {
    max-height: 0 !important;
    opacity: 0 !important;
    overflow: hidden !important;
    transition: max-height 0.4s ease, opacity 0.4s ease;
  }
  .filter-overlay.advanced-open .advanced-filters {
    max-height: 500px !important;
    opacity: 1 !important;
  }

  /* buttons stack & extra spacing */
  .button-row {
    display: flex;
    flex-direction: column;
        /* space between Reset/Search/Advanced */
  }
  .btn-outline-reset {
    margin-bottom: 0.5rem; /* extra breathing room */
  }
}

@media (max-width: 1024px) and (min-width: 769px) {
  /* Stack the buttons and add space */
  .button-row {
    display: flex;
    flex-direction: column;
    gap: 1rem;            /* space between each button */
    align-items: stretch; /* make children fill the width */
  }

  /* Make Reset and Search full-width, larger text & padding */
  .btn-outline-reset,
  .btn-search {
    width: 100%;
    font-size: 1.2rem;    /* bump up the label size */
    padding: 0.75rem 0;    /* taller tap area */
  }

  /* If you’d like the “Hide Search” button to match too: */
  .btn-advanced {
    width: 100%;
    font-size: 1.1rem;
    padding: 0.75rem 0;
    text-align: center;
  }
}





</style>
