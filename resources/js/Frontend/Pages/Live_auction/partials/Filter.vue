<template>
  <section class="filter-section">
    <div class="filter-container">
      <div class="filter-wrapper">
        <div class="filter-content">
          <div class="filter-grid">
            <div class="filter-field">
              <label for="make-select" class="filter-label text-gradient">Make</label>
              <SelectInputComponent
                id="manufacture"
                v-model="form.manufacturer"
                placeholder="SELECT MAKE"
                @change="onChangeField"
                :options="$page.props.manufactures?.map(m => ({ id: m.MARKA_NAME, name: m.MARKA_NAME }))"
              />
            </div>

            <div class="filter-field">
              <label for="model-select" class="filter-label text-gradient">Model</label>
              <SelectInputComponent
                id="model"
                v-model="form.model"
                placeholder="SELECT MODEL"
                @change="onChangeField"
                :options="$page.props.models?.map(m => ({ id: m.MODEL_NAME, name: m.MODEL_NAME }))"
              />
            </div>

            <div class="filter-field">
              <label for="engine" class="filter-label text-gradient">Engine CC</label>
              <SelectInputComponent
                id="engine"
                v-model="form.engine"
                placeholder="SELECT ENGINE CC"
                @change="onChangeField"
                :options="$page.props.engineCapacity?.map(m => ({ id: m.ENG_V, name: m.ENG_V }))"
              />
            </div>

            <div class="filter-field">
              <label for="days" class="filter-label text-gradient">Select Year</label>
              <div class="year-range">
                <SelectInputComponent
                  id="year_from"
                  v-model="form.year_from"
                  placeholder="FROM"
                  @change="onChangeField"
                  :options="$page.props.years?.map(m => ({ id: m.YEAR, name: m.YEAR }))"
                />
                <SelectInputComponent
                  id="year_to"
                  v-model="form.year_to"
                  placeholder="TO"
                  @change="onChangeField"
                  :options="$page.props.years?.map(m => ({ id: m.YEAR, name: m.YEAR }))"
                />
              </div>
            </div>

            <div class="filter-field">
              <label for="chassis" class="filter-label text-gradient">Chassis ID</label>
              <SelectInputComponent
                id="chassis"
                v-model="form.chassis"
                placeholder="SELECT CHASSIS ID"
                @change="onChangeField"
                :options="$page.props.chassisNumbers?.map(m => ({ id: m.KUZOV, name: m.KUZOV }))"
              />
            </div>

            <div class="filter-field">
              <label for="grade" class="filter-label text-gradient">Auction Grade</label>
              <SelectInputComponent
                id="grade"
                v-model="form.grade"
                placeholder="SELECT AUCTION GRADE"
                @change="onChangeField"
                :options="$page.props.Condition?.map(m => ({ id: m.RATE, name: m.RATE }))"
              />
            </div>

            <div class="filter-field">
              <label for="lot_no" class="filter-label text-gradient">Lot No</label>
              <input
                v-model="form.lot_no"
                @input="onChangeField"
                class="form-control filter-input"
                type="text"
                placeholder="ENTER LOT NO"
              />
            </div>

            <div class="filter-field" v-if="advance_search">
              <label for="available_days" class="filter-label text-gradient">Auction Available Days</label>
              <SelectInputComponent
                id="available_days"
                v-model="form.available_days"
                placeholder="SELECT AUCTION DATE"
                @change="onChangeField"
                :options="$page.props.auctionDates?.map(m => ({ id: m.AUCTION_DATE, name: m.AUCTION_DATE }))"
              />
            </div>

            <div class="filter-field" v-if="advance_search">
              <label for="colors" class="filter-label text-gradient">Color</label>
              <SelectInputComponent
                id="colors"
                v-model="form.color"
                placeholder="SELECT COLOR"
                @change="onChangeField"
                :options="$page.props.colorQuery?.map(m => ({ id: m.COLOR, name: m.COLOR }))"
              />
            </div>

            <div class="filter-field" v-if="advance_search">
              <label class="filter-label text-gradient">Mileage</label>
              <div class="mileage-range">
                <SelectInputComponent
                  id="mileage_from"
                  v-model="form.mileage_from"
                  placeholder="FROM"
                  @change="onChangeField"
                  :options="$page.props.Mileage?.map(m => ({ id: m.MILEAGE, name: m.MILEAGE }))"
                />
                <SelectInputComponent
                  id="mileage_to"
                  v-model="form.mileage_to"
                  placeholder="TO"
                  @change="onChangeField"
                  :options="$page.props.Mileage?.map(m => ({ id: m.MILEAGE, name: m.MILEAGE }))"
                />
              </div>
            </div>

            <div class="filter-field" v-if="advance_search">
              <label for="price" class="filter-label text-gradient">Start Price</label>
              <SelectInputComponent
                id="price"
                v-model="form.start_price"
                placeholder="SELECT START PRICE"
                @change="onChangeField"
                :options="$page.props.PriceQuery?.map(m => ({ id: m.AVG_PRICE, name: m.AVG_PRICE }))"
              />
            </div>

            <div class="filter-field">
              <button
                type="button"
                class="advance-search-btn text-gradient"
                @click.prevent="advanceFilter"
              >
                {{ advance_search ? "Hide advance search" : "Advance search" }}
                <i
                  :class="[
                    'fa-solid',
                    advance_search ? 'fa-angles-up' : 'fa-angles-down',
                    'ps-2',
                  ]"
                ></i>
              </button>
            </div>
          </div>

          <div class="filter-actions">
            <div class="action-buttons">
              <button
                type="button"
                class="btn btn-reset text-gradient"
                @click.prevent="resetFilter"
              >
                RESET
              </button>
              <button
                type="button"
                class="btn btn-search btn-gradient"
                @click.prevent="filterData"
              >
                SEARCH
              </button>
            </div>
          </div>
        </div>

        <div class="loading-overlay" v-if="loading">
          <div class="loading-content">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="loading-text text-gradient">Please Wait..</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>


<script>
import SelectInputComponent from "./SelectInputComponent.vue";


export default {
    components: {
        SelectInputComponent,
    },
    data() {
        return {
            index: "",
            form: {
                available_days: 0,
                manufacturer: 0,
                model: 0,
                year_from: 0,
                year_to: 0,
                chassis: 0,
                engine: 0,
                color: "",
                lot_no: "",
                grade: 0,
                start_price: 0,
                mileage_from: 0,
                mileage_to: 0,
                search: false,
            },
            loading: false,
            advance_search: false,

            // https://nikoba.com/live-auction/?available_days=0&manufacturer=0&demo_model=0&undefined=0&undefined=0&demo_chassis=0&demo_engine=0&demo_color=0&lotNo=&available_days=0&page=1
        };
    },
    props: {
        reload: {
            type: Boolean,
            default: false,
        },
    },
    computed: {
        auctionDateOptions() {
            return this.$page.props.auctionDates.map((value, index) => ({
                id: index,
                name: value?.AUCTION_DATE,
            }));
        },
    },
    mounted() {
        this.setQueryData();

        if (this.reload) {
            this.$inertia.reload({
                data: { ...this.form, search: false },
                onSuccess: () => {
                    this.form.search = false;
                },
            });
        }
        // console.log(this.form)

        $(
            "#manufacture, #model, #engine, #year_from, #year_to, #grade, #chassis,  #available_days, #colors, #mileage_from, #mileage_to, #price"
        ).on("select2:open", function () {
            setTimeout(function () {
                let searchField = document.querySelector(
                    ".select2-container--open .select2-search__field"
                );
                if (searchField) {
                    searchField.focus();
                }
            }, 100);
        });
    },
    methods: {
        resetFilter() {
            this.form = {
                available_days: 0,
                manufacturer: 0,
                model: 0,
                year_from: 0,
                year_to: 0,
                chassis: 0,
                engine: 0,
                color: 0,
                lot_no: "",
                grade: 0,
                start_price: 0,
                mileage_from: 0,
                mileage_to: 0,
                search: false,
            };
        },
        advanceFilter() {
            this.loading = true;
            if (this.advance_search == false) {
                this.advance_search = true;
                setTimeout(() => {
                    this.loading = false;
                }, 500);
            } else {
                this.advance_search = false;
                setTimeout(() => {
                    this.loading = false;
                }, 500);
            }
        },
        // onChangeField(evt) {
        //     console.log("jdsfhsdj");
        //     if (
        //         this.$page.props.requestQuery.manufacturer !=
        //         this.form.manufacturer
        //     ) {
        //         this.form.model = 0;
        //         this.form.year_from = 0;
        //         this.form.year_to = 0;
        //         this.form.chassis = 0;

        //         this.form.color = 0;
        //         this.form.grade = 0;
        //         this.form.start_price = 0;
        //         this.mileage_from = 0;
        //         this.mileage_to = 0;
        //     }
        //     this.form.search = false;
        //     this.loading = true;
        //     this.$inertia.reload({
        //         method: "POST",
        //         data: { ...this.form, _method: "GET" },
        //         onSuccess: () => {
        //             this.setQueryData();
        //             this.loading = false;
        //         },
        //     });
        // },


        onChangeField() {
            this.loading = true;
            this.filterData();
            },
            
        setQueryData() {
            this.form.available_days =
                this.$page.props.requestQuery.available_days ?? 0;
            this.form.manufacturer =
                this.$page.props.requestQuery.manufacturer ?? 0;
            this.form.model = this.$page.props.requestQuery.model ?? 0;
            this.form.year_from = this.$page.props.requestQuery.year_from ?? 0;
            this.form.year_to = this.$page.props.requestQuery.year_to ?? 0;
            this.form.chassis = this.$page.props.requestQuery.chassis ?? 0;
            this.form.engine = this.$page.props.requestQuery.engine ?? 0;
            this.form.color = this.$page.props.requestQuery.color ?? 0;
            this.form.lot_no = this.$page.props.requestQuery.lot_no ?? "";
            this.form.grade = this.$page.props.requestQuery.grade ?? 0;
            this.form.start_price =
                this.$page.props.requestQuery.start_price ?? 0;
            this.form.mileage_from =
                this.$page.props.requestQuery.mileage_from ?? 0;
            this.form.mileage_to =
                this.$page.props.requestQuery.mileage_to ?? 0;
            this.form.search = false;
        },
        filterData() {
            this.loading = true;
            if (this.reload) {
                this.$inertia.reload({
                    methods: "POST",
                    data: { ...this.form, search: true, _method: "GET" },
                    
                    onSuccess: () => {
                        this.setQueryData();
                        this.advance_search = true;
                    },
                     onFinish: () => {
                  this.loading = false;  // ← hide overlay
                },
                });
            } else {
                this.$inertia.visit(route("live.auction"), {
                    data: { ...this.form, search: true },


                     onStart: () => {
                   this.loading = true;   // (optional duplicate)
                },
                    onSuccess: () => {
                        this.setQueryData();
                        this.advance_search = true;
                    },
                    onFinish: () => {
                    this.loading = false;
                },
                });
            }
        },
    },
};
</script>

<style scoped>
/* Layout */
.filter-section {
    margin-top: 0rem;
    width: 100%;
    /* background-color: #ffffff; */
    padding: 3rem 0;
}

.filter-container {
    width: 85%;
    max-width: 1600px;
    margin: 0 auto;
    padding: 0 2rem;
}

.filter-wrapper {
    position: relative;
    background-color: #ffffff;
    border-radius: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.filter-wrapper:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
}

.filter-content {
    padding: 2.5rem;
}

/* Grid Layout */
.filter-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.filter-field {
    display: flex;
    flex-direction: column;
}

/* Form Elements */
.filter-label {
    display: block;
    font-size: 0.9rem;
    font-weight: 600;
    color: #1a237e;
    margin-bottom: 0.75rem;
}

.filter-input {
    border: 1px solid #e0e0e0;
    border-radius: 0.75rem;
    padding: 0.875rem;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    font-size: 0.95rem;
    height: 42px;
    width: 100%;
    background-color: #fff;
}

.filter-input:focus {
    border-color: #1a237e;
    box-shadow: 0 0 0 2px rgba(26, 35, 126, 0.1);
    outline: none;
}

/* Range Inputs */
.year-range,
.mileage-range {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

/* Actions */
.filter-actions {
    display: flex;
    justify-content: center;
    align-items: center;
    padding-top: 1.5rem;
    border-top: 1px solid #e0e0e0;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    width: 100%;
}

/* Buttons */
.advance-search-btn {
    background: none;
    border: none;
    color: #1a237e;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    cursor: pointer;
    transition: color 0.3s ease;
    font-size: 0.95rem;
    width: 100%;
    text-align: left;
    margin-top: 1.5rem;
}

.advance-search-btn:hover {
    color: #3dad0d;
}

.btn {
    min-width: 160px;
    padding: 0.875rem 2rem;
    border-radius: 0.75rem;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.btn-reset {
    background-color: #f5f5f5;
    color: #1a237e;
    border: 1px solid #e0e0e0;
}

.btn-reset:hover {
    background-color: #e0e0e0;
}

.btn-search {
    background-color: #1a237e;
    color: white;
    border: none;
}

.btn-search:hover {
    background-color: #3949ab;
}

/* Loading State */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0.95);
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 1.5rem;
    backdrop-filter: blur(4px);
}

.loading-content {
    text-align: center;
}

.loading-text {
    margin-top: 1rem;
    color: #1a237e;
    font-weight: 500;
    font-size: 0.95rem;
}

/* Select2 Customization */
:deep(.select2-container .select2-selection--single) {
    height: 42px !important;
    border: 1px solid #e0e0e0 !important;
    border-radius: 0.75rem !important;
    background-color: #fff !important;
}

:deep(
        .select2-container--default
            .select2-selection--single
            .select2-selection__rendered
    ) {
    line-height: 42px !important;
    padding-left: 0.875rem !important;
    font-size: 0.95rem !important;
}

:deep(
        .select2-container--default
            .select2-selection--single
            .select2-selection__arrow
    ) {
    height: 40px !important;
}

:deep(
        .select2-container--default
            .select2-selection--single
            .select2-selection__placeholder
    ) {
    color: #6c757d !important;
}

/* Responsive Design */
@media (max-width: 1400px) {
    .filter-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 1200px) {
    .filter-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .filter-section {
        padding: 2rem 0;
    }

    .filter-container {
        padding: 0 1rem;
    }

    .filter-content {
        padding: 1.5rem;
    }

    .filter-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .filter-actions {
        flex-direction: column;
        gap: 1rem;
    }

    .action-buttons {
        width: 100%;
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }
}

/* Global gradient text */
.text-gradient {
  display: inline-block;
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

/* Global gradient button */
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


</style>
