<template>
    <section class="auction-guide-section py-5">
        <div class="container-fluid">
            <div class="row g-6">
                <div class="col-lg-5 order-2">
                    <div class="h-100">
                        <div class="image-loader-container position-relative h-100 w-100">
                        <!-- shimmer while loading -->
                        <div v-if="auctionSheetLoaded === false" class="skeleton-placeholder"></div>

                        <!-- actual image -->
                        <img
                            src="images/auction-sheet.jpg"
                            alt="Happy customers with their car"
                            class="img-fluid rounded-4 h-100 w-100 object-fit-cover"
                            :class="{ 'is-loaded': auctionSheetLoaded }"
                            @load="auctionSheetLoaded = true"
                        />
                        </div>

                    </div>
                </div>
                <div class="col-lg-7 order-lg-2 order-1 px-lg-5">
                    <h2 class="section-title mb-4">Auction Sheet Guide</h2>
                    <div class="mb-4">
                        Through this auction guide you will get help to know the exact condition of the car mentioned in
                        the auction sheet. You just have to go through the points in following and read the auction
                        sheet;
                        you will be clear about the car placed in the auction. Please note that this auction sheet is
                        prepared
                        by the third party, the experts of auction. There is no hand of the seller in preparing it so
                        you will
                        get the actual picture of the car.
                    </div>
                    <h3 class="section-sub-title">Auction Sheet Guide</h3>
                    <div class="auction-table mb-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Grade</th>
                                    <th scope="col">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold">S</td>
                                    <td>Brand New,
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">6-9</td>
                                    <td>Like new in condition, under 5,000 Km
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">5</td>
                                    <td>Vehicle is like new. All parts are new. No repair needed on vehicle. Normally
                                        given to vehicles less than 3 years.
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold ">4.5</td>
                                    <td>The condition of vehicle is good. Only few scratches or dents as in normal cars
                                    </td>
                                </tr>
                                <tr>

                                    <td class="fw-bold">4</td>
                                    <td>The condition of vehicle is good. Only few scratches or dents as in normal cars
                                    </td>
                                </tr>
                                <tr>

                                    <td class="fw-bold">3.5</td>
                                    <td>Vehicle has few scratches or dents are visible. This grade is given to vehicle
                                        in average condition
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">3</td>
                                    <td>There are various scratches or dents. Some paints faults
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">2</td>
                                    <td>Nonstandard car. Badly stained or modified car
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">1</td>
                                    <td>Inferior car or badly modified car
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">RA or R1</td>
                                    <td>The vehicle had been in a minor accident but repaired to an acceptable standard
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">R or A</td>
                                    <td>A vehicle which has been in an accident where some parts have been repaired or
                                        replaced.
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">***</td>
                                    <td>Bad Accident
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </section>


    <section class="auction-guide-section mb-5" style="justify-items: center;">
        <div class="col-lg-6 col-sm-9">
            <div class="auction-table mb-4">
                <h4 class="text-center fw-bold mb-4">Interior Auction Grade</h4>
                <table class="table">
                    <!-- <thead>
                        <tr>
                            <th scope="col">Grade</th>
                            <th scope="col">Details</th>
                        </tr>
                    </thead> -->
                    <tbody>
                        <tr>
                            <td class="fw-bold">A</td>
                            <td>Like new, no faults
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">B</td>
                            <td>Clean condition, slightly dirty
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">C</td>
                            <td>Clean but with cigarette burns
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold ">D</td>
                            <td>Dirty or stinky or big rips on seat
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>



        <div class="col-lg-6 ">
            <div class="container my-5">
                <h4 class="text-center fw-bold mb-4">SAMPLE AUCTION SHEET WITH ENGLISH TRANSLATION</h4>
                <div class="row">
                    <div class="col-md-6" >
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <strong>AC</strong> <span>Air conditioner</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <strong>FA/AT</strong> <span>Automatic transmission</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <strong>F5</strong> <span>Manual (5-speed) transmission</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <strong>PS</strong> <span>Power steering</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <strong>SR</strong> <span>Sunroof</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <strong>AW</strong> <span>Original alloy wheels</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <strong>NAVI</strong> <span>Navigator</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <strong>AAC</strong> <span>Climate control air conditioner</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <strong>CA</strong> <span>Column automatic transmission</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <strong>F6</strong> <span>Manual (6-speed) transmission</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <strong>PW</strong> <span>Power windows</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <strong>TV</strong> <span>Television</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <strong>KAWA</strong> <span>Leather seats</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <strong>AIRBAGU</strong> <span>Airbag</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

</template>

<script>
export default {
    data() {
    return {
      auctionSheetLoaded: false,
      // …any other flags you already have
    }
  }

}
</script>

<style scoped>
.auction-guide-section {
    margin: 2rem;
}

table td:first-child,
table th:first-child {
    border-right: 1.5px solid #dee2e6;
    /* change color and width as needed */
    padding-left: 1rem;
    /* optional: add spacing */
}

@media (max-width: 576px) {

    .auction-guide-section {
        margin: 0 0.5rem 0 0.5rem;
    }
}

/* ensure loader container fills parent */
.image-loader-container {
  width: 100%;
  height: 100%;
  overflow: hidden;
}

/* shimmer placeholder */
.skeleton-placeholder {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: linear-gradient(
    to right,
    #e0e0e0 0%, #f8f8f8 50%, #e0e0e0 100%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: .5rem; /* match your .rounded-4 */
}

/* fade-in transition on the image */
.img-fluid {
  transition: opacity 0.5s ease;
  opacity: 0;
}
.img-fluid.is-loaded {
  opacity: 1;
}

@keyframes shimmer {
  0%   { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

</style>