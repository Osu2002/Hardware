<template>
  <section class="container-fluid backgroundColor w-100 pb-5">
    <div class="container pt-5">
      <div class="row align-items-center py-3">
        <div class="col-8">
          <h2 class="headText secondFontStyle text-left fw-semibold">Explore All Vehicles</h2>
        </div>
        <div class="col-4 text-end">
          <p class="viewAll fw-bold" type="button">Show All <i class="fa fa-arrow-circle-right ms-2 fs-5"
              aria-hidden="true"></i></p>
        </div>
      </div>
      <ul class="nav nav-pills mt-3 border-bottom" id="pills-tab" role="tablist">
        <li class="nav-item me-4" role="presentation" v-if="in_stock">
          <div class="tabName active fw-semibold pb-2" id="pills-home-tab" data-bs-toggle="pill"
            data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">In
            Stock
          </div>
        </li>
        <li class="nav-item mx-4" role="presentation" v-if="latest_cars">
          <div class="tabName fw-semibold pb-2" id="pills-profile-tab" data-bs-toggle="pill"
            data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile"
            aria-selected="false">Latest Cars</div>
        </li>
        <li class="nav-item mx-4" role="presentation" v-if="used_cars">
          <div class="tabName fw-semibold pb-2" id="pills-contact-tab" data-bs-toggle="pill"
            data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact"
            aria-selected="false">Used Cars</div>
        </li>
      </ul>
      <div class="tab-content broder-0" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab"
          tabindex="0" v-if="in_stock">
          <div class="row my-5">
            <div class="col-md-6 col-lg-3 mb-3" v-for="stock in in_stock" :key="stock.id">
              <div class="card mb-4 bg-white border">
                <img
                 :src="stock?.media[0]?.original_url"
                  class="card-img-top object-fit-cover image-height" alt="...">
                <div class="card-body mx-2">
                  <div class="row align-items-center">
                    <div class="col-md-12 border-bottom pb-2">
                      <h5 class="mt-2 vehicalName fw-semibold secondFontStyle" style="font-size: 16px;">
                        {{ stock?.manufacture?.title }} {{ stock?.vehicle_model?.title }}
                        </h5>
                    </div>
                    <div class="d-flex border-bottom ">
                      <div class="col-4 text-center mt-3">
                        <img src="/images/speedometer.png" width="30" height="30" alt="">
                        <p class="bodySmall secondFontStyle fw-semibold mt-2" style="font-size: 12px;">{{stock.mileage}} km</p>
                      </div>
                      <div class="col-4 text-center mt-3">
                        <img src="/images/gas-station.png" width="30" height="30" alt="">
                        <p class="bodySmall secondFontStyle  fw-semibold mt-2" style="font-size: 12px;">{{ stock.fuel_type }}</p>
                      </div>
                      <div class="col-4 text-center mt-3">
                        <img src="/images/gear-shift.png" width="30" height="30" alt="">
                        <p class="bodySmall secondFontStyle  fw-semibold mt-2" style="font-size: 12px;">{{ stock.transmission }}</p>
                      </div>
                    </div>
                    <div class="d-flex pt-4">
                      <div class="col-md-6 col-sm-6">
                        <p class="bodySmall secondFontStyle fw-semibold" style="font-size: 14px;">Rs. 16,300,000</p>
                      </div>
                      <div class="col-md-6 col-sm-6 text-end">
                        <p class="bodySmall secondFontStyle text-danger viewDetails" style="font-size: 14px;">View
                          Details</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0" v-if="latest_cars">
          <div class="row my-5">
            <div class="col-md-6 col-lg-3 mb-3" v-for="latestcar in latest_cars" :key="latestcar.id">
              <div class="card bg-white border">
                <img
                 :src="latestcar?.media[0]?.original_url"
                  class="card-img-top object-fit-cover image-height" alt="...">
                <div class="card-body mx-2">
                  <div class="row align-items-center">
                    <div class="col-md-12 border-bottom pb-2">
                      <h5 class="mt-2 vehicalName fw-semibold secondFontStyle" style="font-size: 16px;">
                        {{ latestcar?.manufacture?.title }} {{ latestcar?.vehicle_model?.title }}
                       </h5>
                    </div>
                    <div class="d-flex border-bottom ">
                      <div class="col-4 text-center mt-3">
                        <img src="/images/speedometer.png" width="30" height="30" alt="">
                        <p class="bodySmall secondFontStyle fw-semibold mt-2" style="font-size: 12px;">{{latestcar.mileage}} km</p>
                      </div>
                      <div class="col-4 text-center mt-3">
                        <img src="/images/gas-station.png" width="30" height="30" alt="">
                        <p class="bodySmall secondFontStyle  fw-semibold mt-2" style="font-size: 12px;">{{ latestcar.fuel_type }}</p>
                      </div>
                      <div class="col-4 text-center mt-3">
                        <img src="/images/gear-shift.png" width="30" height="30" alt="">
                        <p class="bodySmall secondFontStyle  fw-semibold mt-2" style="font-size: 12px;">{{ latestcar.transmission }}</p>
                      </div>
                    </div>
                    <div class="d-flex pt-4">
                      <div class="col-md-6">
                        <p class="bodySmall secondFontStyle fw-semibold" style="font-size: 14px;">Rs. 16,300,000</p>
                      </div>
                      <div class="col-md-6 text-end">
                        <p class="bodySmall secondFontStyle text-danger viewDetails" style="font-size: 14px;">View
                          Details</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0" v-if="used_cars">
          <div class="row my-5">
            <div class="col-md-6 col-lg-3 mb-3">
              <div class="card bg-white border">
                <img
                  src="https://autodirectstaging.quicksite.asia/wp-content/uploads/elementor/thumbs/front-view-118-qnf2r97xvfjis2sh324d314jucwcttt3brjg1ier34.webp"
                  class="card-img-top" alt="...">
                <div class="card-body mx-2">
                  <div class="row align-items-center">
                    <div class="col-md-12 border-bottom pb-2">
                      <h5 class="mt-2 vehicalName fw-semibold secondFontStyle" style="font-size: 16px;">
                        BYD eal</h5>
                    </div>
                    <div class="d-flex border-bottom ">
                      <div class="col-4 text-center mt-3">
                        <img src="/images/speedometer.png" width="30" height="30" alt="">
                        <p class="bodySmall secondFontStyle fw-semibold mt-2" style="font-size: 12px;">180 km/h</p>
                      </div>
                      <div class="col-4 text-center mt-3">
                        <img src="/images/gas-station.png" width="30" height="30" alt="">
                        <p class="bodySmall secondFontStyle  fw-semibold mt-2" style="font-size: 12px;">Electric</p>
                      </div>
                      <div class="col-4 text-center mt-3">
                        <img src="/images/gear-shift.png" width="30" height="30" alt="">
                        <p class="bodySmall secondFontStyle  fw-semibold mt-2" style="font-size: 12px;">Automatic</p>
                      </div>
                    </div>
                    <div class="d-flex pt-4">
                      <div class="col-md-6">
                        <p class="bodySmall secondFontStyle fw-semibold" style="font-size: 14px;">Rs. 16,300,000</p>
                      </div>
                      <div class="col-md-6 text-end">
                        <p class="bodySmall secondFontStyle text-danger viewDetails" style="font-size: 14px;">View
                          Details</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  props:{
    in_stock:Object,
    latest_cars:Object,
  }
}
</script>

<style scoped>
.image-height{
  height:200px
}
</style>