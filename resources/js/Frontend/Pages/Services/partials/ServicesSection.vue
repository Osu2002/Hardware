<template>
    <section class="container my-5">
        <div class="row align-items-start overflow-hidden rounded-4">
            <!-- left image -->
            <div class="col-lg-6 p-0">
                <img
                    src="/images/F2ZKktFWQAEV7We.jpeg"
                    alt="World Auto Dealers"
                    class="w-100 h-100 object-fit-cover"
                />
            </div>

            <!-- right content -->
            <div class="col-lg-6 d-flex flex-column p-3 p-lg-4">
                <!-- pulled up by reducing top padding -->
                <h2 class="fw-bold mb-3">World Auto Dealers</h2>

                <p class="mb-3">
                    Bearing the corporate name <strong>World Auto Dealers</strong> on our
                    humble threshold, we stepped into the arena of the
                    automobile industry in the year 2003 and specialized
                    ourselves in importing Japanese vehicles to elevate life
                    style of our fellow Sri Lankans. Becoming one of the
                    dominant forces in the automobile market we have built
                    ourselves on the trust capital earned through our valued
                    customers. Capitalizing on clean transparent business deals,
                    we provide the auction sheet to the client that encloses the
                    grades and a-z details of the vehicle in topic.
                </p>
                <p>
                    As an ever growing organization we are honour-bound to
                    enhance our services provided to all our much treasured
                    clients, therefore since 2010 we have given our customers to
                    select their most preferred vehicle from the Japan auto
                    auction, by letting them know of the real condition of the
                    vehicle. This facility is now further enhanced having an
                    opportunity of direct bidding facility of buying vehicles,
                    directly from live auto auctions in Japan. Due to this
                    complete crystal-clear transparency the buyer is made known
                    regarding the most essential factors of the vehicle such as,
                </p>

                <!-- full-height flex container so last item sits at bottom -->
                <ul class="list-unstyled d-flex flex-column h-100 mt-4">
                    <li class="d-flex align-items-start mb-2">
                        <i class="fa-solid fa-circle-check me-2 check-icon"></i>
                        Auction grade (exterior and interior grades) of the
                        vehicle
                    </li>
                    <li class="d-flex align-items-start mb-2">
                        <i class="fa-solid fa-circle-check me-2 check-icon"></i>
                        Actual selling price of the vehicle sold at the auction.
                    </li>
                    <li class="d-flex align-items-start mb-2">
                        <i class="fa-solid fa-circle-check me-2 check-icon"></i>
                        Original mileage
                    </li>
                    <li class="d-flex align-items-start mb-2">
                        <i class="fa-solid fa-circle-check me-2 check-icon"></i>
                        Its condition options
                    </li>
                    <!-- pushes itself to bottom -->
                    <li class="d-flex align-items-start mt-auto">
                        <i class="fa-solid fa-circle-check me-2 check-icon"></i>
                        Color
                    </li>
                </ul>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: "World Auto Dealers Section",
};
</script>

<style scoped>
.object-fit-cover {
    object-fit: cover;
    height: 100%;
    border-radius: 4%;
}

/* your FA icon styling */
.check-icon {
    color: #405ef255;
    font-size: 1.25rem;
    line-height: 1;
}
</style>
