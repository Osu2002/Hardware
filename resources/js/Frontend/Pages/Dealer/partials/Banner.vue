<template>
    <div class="carousel slide">
        <div class="carousel-inner">
            <div class="carousel-item d-block custom_slide_height">
                <img src="https://img.freepik.com/free-photo/concentrated-young-caucasian-businessman_171337-734.jpg?t=st=1721620535~exp=1721624135~hmac=345d5370e1bbf1fb68fe208d77236611fbded9ccd5533721611828bda7c226a2&w=740"
                    class="d-block w-100 swiperImg custom_slide_height" alt="...">
                <div class="carousel-caption custom_caption_position" style="top: 40%;">
                    <div class="caption">
                        <div class="p-4">
                            <h1 class="main-topic-banner" style="font-size:3.5rem; font-weight:700">Nikoba Dealer App
                            </h1>


                        </div>
                        <div class="store-buttons  justify-content-center gap-3 flex-wrap">
                            <a href="https://apps.apple.com/lk/app/nikoba-dealer/id6743689787" target="_blank"
                                class="market-btn apple-btn" role="button">
                                <span class="market-button-subtitle">Download on the</span>
                                <span class="market-button-title">App Store</span>
                            </a>
                            <a href="https://play.google.com/store/apps/details?id=lk.nikoba.admin.nikoba&pcampaignid=web_share"
                                target="_blank" class="market-btn google-btn" role="button">
                                <span class="market-button-subtitle">Download on the</span>
                                <span class="market-button-title">Google Play</span>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {

}
</script>

<style scoped>
.custom_slide_height {
    height: 47vh !important;
}

@media (max-width: 768px) {
    .main-topic-banner {
        font-size: 2rem !important;
    }

    .custom_caption_position {
        top: 25% !important;
    }
}


.store-btn {
    font-size: 1rem;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.store-btn:hover {
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .store-btn {
        font-size: 0.9rem;
        padding: 0.6rem 1.2rem;
    }
}

@media (max-width: 576px) {
    .store-buttons {
        flex-direction: column !important;
        align-items: center;
        gap: 1rem;
    }

    .store-btn {
        width: 90%;
        justify-content: center;
    }
}

.market-btn {
    display: inline-block;
    padding: 0.3125rem 0.875rem;
    padding-left: 2.8125rem;
    transition: border-color 0.25s ease-in-out, background-color 0.25s ease-in-out;
    border: 1px solid #e7e7e7;
    background-position: center left 0.75rem;
    background-color: #fff;
    background-size: 1.5rem 1.5rem;
    background-repeat: no-repeat;
    text-decoration: none;
    margin: 10px;
    border-radius: 8px;
}

.market-btn .market-button-title {
    display: block;
    color: #222;
    font-size: 1.125rem;
}

.market-btn .market-button-subtitle {
    display: block;
    margin-bottom: -0.25rem;
    color: #888;
    font-size: 0.75rem;
}

.market-btn:hover {
    background-color: #f7f7f7;
    text-decoration: none;
}

.apple-btn {
    background-image: url('https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg');
}

.google-btn {
    background-image: url('https://upload.wikimedia.org/wikipedia/commons/d/d0/Google_Play_Arrow_logo.svg');
}

.windows-btn {
    background-image: url('https://upload.wikimedia.org/wikipedia/commons/4/48/Windows_logo_-_2021.svg');
}

.amazon-btn {
    background-image: url('https://upload.wikimedia.org/wikipedia/commons/a/a9/Amazon_logo.svg');
}

@media (max-width: 500px) {
    .store-buttons a {
        width: 60% !important;
    }
}
</style>