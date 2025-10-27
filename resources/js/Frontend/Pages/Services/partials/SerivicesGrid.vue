<template>
    <section class="container-fluid my-5">
        <!-- Heading -->
        <div class="text-center mb-4">
            <h2 class="fw-bold">How To Order Step By Step</h2>
        </div>

        <!-- Steps Grid -->
        <div class="row g-4">
            <div
                v-for="step in steps"
                :key="step.number"
                class="col-6 col-md-3"
            >
                <div
                    class="step-card rounded overflow-hidden position-relative"
                >
                    <!-- the image -->
                    <img
                        :src="step.img"
                        :alt="step.title"
                        class="w-100 h-100 object-fit-cover"
                    />
                    <!-- gradient overlay -->
                    <div
                        class="overlay position-absolute top-0 start-0 w-100 h-100"
                    ></div>
                    <!-- number + title -->
                    <div
                        class="step-info position-absolute top-0 start-0 p-3 text-white"
                    >
                        <div class="step-number fw-bold">{{ step.number }}</div>
                        <div class="step-title">{{ step.title }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: "OrderSteps",
    data() {
        return {
            steps: [
                {
                    number: "01",
                    title: "Dream to buy your dreaming car",
                    img: "/images/Link.png",
                },
                {
                    number: "02",
                    title: "Select From Auction",
                    img: "/images/Link (1).png",
                },
                {
                    number: "03",
                    title: "Generate Enquiry",
                    img: "/images/3.png",
                },
                {
                    number: "04",
                    title: "Complete Deposit",
                    img: "/images/4 (2).png",
                },
                {
                    number: "05",
                    title: "Successful Bid",
                    img: "/images/5.png",
                },
                {
                    number: "06",
                    title: "Vehicle Shipment",
                    img: "/images/6.png",
                },
                {
                    number: "07",
                    title: "Vehicle Delivery",
                    img: "/images/7.png",
                },
                {
                    number: "08",
                    title: "Drive With Pleasure",
                    img: "/images/8.png",
                },
            ],
        };
    },
};
</script>

<style scoped>
.step-card {
    /* keep cards square */
    aspect-ratio: 1 / 1;
}

/* make the img cover the card */
.object-fit-cover {
    object-fit: cover;
}

/* the dark gradient from bottom up */
.overlay {
    background: linear-gradient(
        180deg,
        rgba(0, 0, 0, 0.2) 0%,
        rgba(0, 0, 0, 0.7) 100%
    );
}

/* number and title styling */
.step-info .step-number {
    font-size: 1.25rem;
    letter-spacing: 0.05em;
}
.step-info .step-title {
    margin-top: 0.25rem;
    font-size: 0.9rem;
}
</style>
