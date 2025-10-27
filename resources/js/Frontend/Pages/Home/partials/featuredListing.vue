<template>
  <section class="container featuredListing pt-5">
    <div class="row mb-4">
      <div class="col-8">
        <h2 class="headText secondFontStyle text-left fw-semibold">Available Stock in Sri Lanka</h2>
      </div>
      <div class="col-md-4 text-center">
        <!-- <div class="btn-grp nav-pills" id="pills-tab" role="tablist">
              <input type="radio" class="btn-check" name="options-outlined" id="success-outlined" autocomplete="off" checked>
              <label class="btn btnColor border rounded py-2 px-5 me-1 text-white active btn-hover" for="success-outlined" id="pills-home-tab1" data-bs-toggle="pill" data-bs-target="#pills-new"  role="tab"
              aria-controls="pills-home" aria-selected="true">New</label>
              <input type="radio" class="btn-check" name="options-outlined" id="danger-outlined" autocomplete="off">
              <label class="btn btnColor border broder-0 rounded py-2 px-5 ms-1 text-white btn-hover" for="danger-outlined" id="pills-profile-tab1" data-bs-toggle="pill" data-bs-target="#pills-used"  role="tab"
              aria-controls="pills-profile" aria-selected="false">Used</label>
          </div> -->
      </div>
    </div>
    <div class="tab-content" id="pills-tabContent">
      <div class="tab-pane fade show active" id="pills-new" role="tabpanel" aria-labelledby="pills-home-tab1"
        tabindex="0">
        <div class="row mb-5">
          <div class="col-md-12 col-xl-6">
            <!-- show first ittem of the array -->
            <Link class="card w-100 border mb-4" v-if="firstFeaturedItem"
              :href="route('product', { model: $root.stringToSlug(firstFeaturedItem.manufacture.title), slug: ($root.stringToSlug(firstFeaturedItem.manufacture.title) + '-' + $root.stringToSlug(firstFeaturedItem.vehicle_model.title)), id: firstFeaturedItem.id })">
            <img :src="firstFeaturedItem?.media[0]?.original_url"
              class="card-img-top object-fit-cover w-100 custom_image_height" alt="...">
            <div class="card-body pb-0">
              <div class="row px-2 align-items-center">
                <div class="col-md-12">
                  <Link
                    :href="route('product', { model: $root.stringToSlug(firstFeaturedItem.manufacture.title), slug: ($root.stringToSlug(firstFeaturedItem.manufacture.title) + '-' + $root.stringToSlug(firstFeaturedItem.vehicle_model.title)), id: firstFeaturedItem.id })">
                  <h5 class="mt-2 pb-3 fw-semibold border-bottom secondFontStyle text-black">{{
                    firstFeaturedItem?.manufacture?.title }} {{ firstFeaturedItem?.vehicle_model?.title }}</h5>
                  </Link>
                </div>
                <div class="col-md-3">
                  <p class="body yearText secondFontStyle w-50 text-center text-white py-1 bg-dark"> {{ firstFeaturedItem
                    ? firstFeaturedItem.year : ''}}</p>
                </div>
                <div class="col-md-3">
                  <p class="body secondFontStyle fw-normal">{{ firstFeaturedItem ? firstFeaturedItem.TransmissionString :
                    ''}}</p>
                </div>
                <div class="col-md-3">
                  <!-- <p class="body secondFontStyle fw-normal">Electric</p> -->
                </div>
                <div class="col-md-3 col-sm-12">
                  <p class="body secondFontStyle priceText fw-bold text-black">{{ firstFeaturedItem ?
                    firstFeaturedItem.mileage : ''}} KM</p>
                </div>
              </div>
            </div>
            </Link>
          </div>
          <div class="col-md-6 col-xl-6">
            <div class="row">
              <div class="col-6" v-for="item in $page.props.featuredVehicles.slice(1)" :key="item">
                <Link class="card w-100 border col-6 mb-4" :href="route('product', { model: $root.stringToSlug(item.manufacture.title), slug: ($root.stringToSlug(item.manufacture.title) + '-' + $root.stringToSlug(item.vehicle_model.title)), id: item.id })">
                  <img :src="item?.media[0]?.original_url" class="card-img-top object-fit-cover custom_height"
                    alt="...">
                  <div class="card-body pb-0">
                    <div class="row px-2 align-items-center">
                      <div class="col-md-12">
                        <Link
                          :href="route('product', { model: $root.stringToSlug(item.manufacture.title), slug: ($root.stringToSlug(item.manufacture.title) + '-' + $root.stringToSlug(item.vehicle_model.title)), id: item.id })">
                        <h5 class="mt-2 vehicalName fw-semibold secondFontStyle text-black">
                          {{ item?.manufacture?.title }} {{ item?.vehicle_model?.title }}</h5>
                        </Link>

                      </div>
                      <div class="col-md-12">
                        <p class="body border-bottom priceText priceTextSmall secondFontStyle fw-bold pb-2 text-black">
                          {{ item.mileage ?? 0 }} KM
                          
                        </p>
                      </div>
                      <div class="col-md-4 p-0">
                        <p class="bodySmall yearText secondFontStyle w-50 text-center text-white py-1 bg-dark">
                          {{ item.year }}</p>
                      </div>
                      <div class="col-md-4 p-0">
                        <p class="bodySmall secondFontStyle">{{ item.TransmissionString ?? '' }}</p>
                      </div>
                      <div class="col-md-4 p-0">
                        <!-- <p class="bodySmall secondFontStyle">Electric</p> -->
                      </div>
                    </div>
                  </div>
                </Link>
              </div>

              <!-- <div class="card border  mt-4 mb-4 col-6">
                <img
                  src="https://autodirectstaging.quicksite.asia/wp-content/uploads/elementor/thumbs/byd-atto-3-studio-720x405px-qnk0sgkwan9cxb2atz0r46vde0quo6wlidqnodf2cg.jpg"
                  class="card-img-top" alt="...">
                <div class="card-body pb-0">
                  <div class="row px-2 align-items-center">
                    <div class="col-md-12">
                      <h5 class="mt-2 vehicalName fw-semibold secondFontStyle">BYD
                        ATTO 3</h5>
                    </div>
                    <div class="col-md-12">
                      <p class="body border-bottom priceText priceTextSmall secondFontStyle fw-bold pb-2 text-black">Rs.
                        11,900,000
                      </p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall yearText secondFontStyle w-50 text-center text-white py-1 bg-dark"> 2024</p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall secondFontStyle">Automatic</p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall secondFontStyle">Electric</p>
                    </div>
                  </div>
                </div>
              </div> -->
            </div>

            <!-- </div> -->
            <!-- <div class="col-md-6 col-xl-3"> -->
            <!-- <div class="card w-100 border">
                <img
                  src="https://autodirectstaging.quicksite.asia/wp-content/uploads/elementor/thumbs/bmw-i5-edrive40-review-27-qnlqnhn0pnw8dk6gjm9lp7h95001rq2315jezfdo68.jpg"
                  class="card-img-top" alt="...">
                <div class="card-body pb-0">
                  <div class="row px-2 align-items-center">
                    <div class="col-md-12">
                      <h5 class="mt-2 vehicalName fw-semibold secondFontStyle">BMW
                        I5 EDRIVE 40</h5>
                    </div>
                    <div class="col-md-12">
                      <p class="body border-bottom priceText priceTextSmall secondFontStyle fw-bold pb-2 text-black">Rs.
                        31,500,000
                      </p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall yearText secondFontStyle w-50 text-center text-white py-1 bg-dark"> 2024</p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall secondFontStyle">Automatic</p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall secondFontStyle">Electric</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card border w-100 mt-4">
                <img
                  src="https://autodirectstaging.quicksite.asia/wp-content/uploads/elementor/thumbs/front-view-118-qnf2r97xvfjis2sh324d314jucwcttt3brjg1ier34.webp"
                  class="card-img-top" alt="...">
                <div class="card-body pb-0">
                  <div class="row px-2 align-items-center">
                    <div class="col-md-12">
                      <h5 class="mt-2 vehicalName fw-semibold secondFontStyle">BYD
                        Seal</h5>
                    </div>
                    <div class="col-md-12">
                      <p class="body border-bottom priceText priceTextSmall secondFontStyle fw-bold pb-2 text-black">Rs.
                        16,300,000
                      </p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall yearText secondFontStyle w-50 text-center text-white py-1 bg-dark"> 2024</p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall secondFontStyle">Automatic</p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall secondFontStyle">Electric</p>
                    </div>
                  </div>
                </div>
              </div> -->
          </div>
        </div>
      </div>
      <!-- <div class="tab-pane fade" id="pills-used" role="tabpanel" aria-labelledby="pills-profile-tab1" tabindex="0">
          <div class="row mb-5">
            <div class="col-md-12 col-xl-6">
              <div class="card w-100 border">
                <img
                  src="https://autodirectstaging.quicksite.asia/wp-content/uploads/elementor/thumbs/bmw-i5-edrive40-review-27-qnlqnhn6sxvjcrnpabydbtd33dylma31n3gxsctqew.jpg"
                  class="card-img-top" alt="...">
                <div class="card-body pb-0">
                  <div class="row px-2 align-items-center">
                    <div class="col-md-12">
                      <h5 class="mt-2 pb-3 fw-semibold border-bottom secondFontStyle">BMW
                        I5 EDRIVE 40</h5>
                    </div>
                    <div class="col-md-3">
                      <p class="body yearText secondFontStyle w-50 text-center text-white py-1 bg-dark"> 2024</p>
                    </div>
                    <div class="col-md-3">
                      <p class="body secondFontStyle fw-normal">Automatic</p>
                    </div>
                    <div class="col-md-3">
                      <p class="body secondFontStyle fw-normal">Electric</p>
                    </div>
                    <div class="col-md-3 col-sm-12">
                      <p class="body secondFontStyle priceText fw-bold">Rs. 31,500,000</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-xl-3">
              <div class="card w-100 border">
                <img
                  src="https://autodirectstaging.quicksite.asia/wp-content/uploads/elementor/thumbs/BYD_DOLPHIN-01@2x-qnlvrm8vhbyanoq1d7io4qrua8ghl9170i3jaqddb4.jpg"
                  class="card-img-top" alt="...">
                <div class="card-body pb-0">
                  <div class="row px-2 align-items-center">
                    <div class="col-md-12">
                      <h5 class="mt-2 vehicalName fw-semibold secondFontStyle">
                        BYD DOLPHIN</h5>
                    </div>
                    <div class="col-md-12">
                      <p class="body border-bottom priceText priceTextSmall secondFontStyle fw-bold pb-2">Rs. 7,800,000
                      </p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall yearText secondFontStyle w-50 text-center text-white py-1 bg-dark"> 2024</p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall secondFontStyle">Automatic</p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall secondFontStyle">Electric</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card border w-100 mt-4">
                <img
                  src="https://autodirectstaging.quicksite.asia/wp-content/uploads/elementor/thumbs/byd-atto-3-studio-720x405px-qnk0sgkwan9cxb2atz0r46vde0quo6wlidqnodf2cg.jpg"
                  class="card-img-top" alt="...">
                <div class="card-body pb-0">
                  <div class="row px-2 align-items-center">
                    <div class="col-md-12">
                      <h5 class="mt-2 vehicalName fw-semibold secondFontStyle">BYD
                        ATTO 3</h5>
                    </div>
                    <div class="col-md-12">
                      <p class="body border-bottom priceText priceTextSmall secondFontStyle fw-bold pb-2">Rs. 11,900,000
                      </p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall yearText secondFontStyle w-50 text-center text-white py-1 bg-dark"> 2024</p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall secondFontStyle">Automatic</p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall secondFontStyle">Electric</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-xl-3">
              <div class="card w-100 border">
                <img
                  src="https://autodirectstaging.quicksite.asia/wp-content/uploads/elementor/thumbs/bmw-i5-edrive40-review-27-qnlqnhn0pnw8dk6gjm9lp7h95001rq2315jezfdo68.jpg"
                  class="card-img-top" alt="...">
                <div class="card-body pb-0">
                  <div class="row px-2 align-items-center">
                    <div class="col-md-12">
                      <h5 class="mt-2 vehicalName fw-semibold secondFontStyle">BMW
                        I5 EDRIVE 40</h5>
                    </div>
                    <div class="col-md-12">
                      <p class="body border-bottom priceText priceTextSmall secondFontStyle fw-bold pb-2">Rs. 31,500,000
                      </p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall yearText secondFontStyle w-50 text-center text-white py-1 bg-dark"> 2024</p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall secondFontStyle">Automatic</p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall secondFontStyle">Electric</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card border w-100 mt-4">
                <img
                  src="https://autodirectstaging.quicksite.asia/wp-content/uploads/elementor/thumbs/front-view-118-qnf2r97xvfjis2sh324d314jucwcttt3brjg1ier34.webp"
                  class="card-img-top" alt="...">
                <div class="card-body pb-0">
                  <div class="row px-2 align-items-center">
                    <div class="col-md-12">
                      <h5 class="mt-2 vehicalName fw-semibold secondFontStyle">BYD
                        Seal</h5>
                    </div>
                    <div class="col-md-12">
                      <p class="body border-bottom priceText priceTextSmall secondFontStyle fw-bold pb-2">Rs. 16,300,000
                      </p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall yearText secondFontStyle w-50 text-center text-white py-1 bg-dark"> 2024</p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall secondFontStyle">Automatic</p>
                    </div>
                    <div class="col-md-4 p-0">
                      <p class="bodySmall secondFontStyle">Electric</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div> -->
    </div>
  </section>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';

export default {
  components: {
    Link
  },
  data() {
    return {}
  },
  methods: {
    stringToSlug(str) {
      return str
        .toLowerCase()                // Convert the string to lowercase
        .trim()                        // Remove whitespace from both ends
        .replace(/[^\w\s-]/g, '')      // Remove all non-word characters (except for hyphens and spaces)
        .replace(/\s+/g, '-')          // Replace spaces with hyphens
        .replace(/--+/g, '-')          // Replace multiple hyphens with a single hyphen
        .replace(/^-+|-+$/g, '');      // Remove hyphens from the start and end of the string
    },
  },
  computed: {
    firstFeaturedItem: function () {
      const item = this.$page.props.featuredVehicles?.at(0)
      return item;
    }
  },
  mounted() { }
}
</script>

<style scoped>
.btn-grp .active {
  background-color: #222b5e !important;
}

.btn-hover {
  background-color: #4d5685 !important;
}

@media (max-width:1365px) {
  .custom_image_height {
    height: 480px !important;
  }

  .custom_height {
    height: 149px !important;
  }
}

@media (max-width:1920px) {
  .custom_image_height {
    height: 530px;
  }

  .custom_height {
    height: 173px !important;
  }
}
</style>
