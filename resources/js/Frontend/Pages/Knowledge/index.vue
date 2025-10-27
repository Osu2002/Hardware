<template>
    <AppLayout>

        <NewBanner />

        <faq />

        <NewAuctionGuide_1 />

        <NewAuctionGuide_2 />

        <NewAuctionGuide_3 />
        
        <ServicesGrid />

    </AppLayout>
</template>

<script>
import AppLayout from '@@/Layouts/AppLayout.vue';
// import Banner from './partials/Banner.vue';
// import FeatureSection from './partials/feature-section.vue';
// import Text from './partials/text.vue';
// import VideoSection from './partials/video-section.vue';
// import AuctionSheetGuide from './partials/auction-sheet-guide.vue';

import NewBanner from './partials/NewBanner.vue';
import NewAuctionGuide_1 from './partials/NewAuctionGuide_1.vue';
import NewAuctionGuide_2 from './partials/NewAuctionGuide_2.vue';
import NewAuctionGuide_3 from './partials/NewAuctionGuide_3.vue';
import ServicesGrid from './partials/ServicesGrid.vue';
import Faq from './partials/Faq.vue';
export default {
    components: {
        AppLayout,
        // Banner,
        // FeatureSection,
        // Text,
        // VideoSection,
        // AuctionSheetGuide,

        NewBanner,
        NewAuctionGuide_1,
        NewAuctionGuide_2,
        NewAuctionGuide_3,
        ServicesGrid,
        Faq,
    }
}
</script>

<style></style>