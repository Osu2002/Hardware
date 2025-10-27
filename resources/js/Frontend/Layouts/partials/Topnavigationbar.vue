<template>
    <nav :class="['mainNavBar w-100 position-fixed p-0', { 'navbar-view-details': isViewDetailsRoute() }]"
        style="z-index: 1001">
        <nav class="navbar navbar-expand-lg p-2 border-bottom w-100">
            <div class="container-fluid">
                <div class="collapse navbar-collapse" id="navbarNav">
                    <nav class="countries-list">
                        <template v-if="
                            $page.props.countries &&
                            $page.props.countries.filter(c => ['sri lanka', 'australia'].includes(c.name.toLowerCase())).length
                        ">
                            <Link v-for="country in $page.props.countries.filter(c =>
                                ['sri lanka', 'australia'].includes(c.name.toLowerCase())
                            )" :key="country.id" :href="route('available', {
                                countrySlug: country.name
                                    .toLowerCase()
                                    .replace(/\s+/g, '-'),
                            })" preserve-state preserve-scroll style="font-size: 15px" @click="selectCountry(country)"
                                :class="{
                                    active:
                                        selectedCountry &&
                                        selectedCountry.id === country.id,
                                }">
                            <img :src="country?.media?.length > 0
                                ? country?.media[0].original_url
                                : ''" height="50" width="50" class="flag-small" :alt="`${country.name} Flag`" />
                            <span>{{ country.name }}</span>
                            </Link>
                        </template>
                        <span v-else>No countries available</span>
                    </nav>
                </div>


                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav align-items-center flex-row">
                        <!-- User Links -->
                        <div v-if="$page.props.logged_customer">
                            <Link class="nav-link  fw-bold " type="button" :href="route('profile')">
                            <i class="fa-solid fa-user me-2" style="color: white;"></i>MY ACCOUNT</Link>
                        </div>
                        <div class="d-flex" v-else>
                            <li class="nav-item">
                                <Link class="nav-link " style="color: white;" :href="route('user.login')">LOGIN</Link>
                            </li>
                            <li class="nav-item">
                                <Link class="nav-link " style="color: white;" :href="route('register')">REGISTER</Link>
                            </li>
                        </div>
                        <!-- <li class="nav-item me-2">
                            <Link class="nav-link  fw-bold" style="color: white;" :href="route('dealer.register')">
                            BECOME A DEALER
                            </Link>
                        </li> -->
                        <!-- <li class="nav-item ">
                            <Link class="nav-link" style="color: white;" :href="route('knowledge.center')">
                            KNOWLEDGE CENTER
                            </Link>
                        </li> -->
                        <template v-if="$page.props.countries">
                            <li v-for="country in $page.props.countries.filter(c =>
                                ['sri lanka', 'australia'].includes(c.name.toLowerCase()))" :key="country.id"
                                class="nav-item phone-item me-2">
                                <a :href="'tel:' + country.phone"
                                    class="nav-link linkHovernew rounded-pill py-1 fw-bold px-2" style="font-size: 13px"
                                    @click="selectCountry(country)"
                                    :aria-label="'Call ' + country.name + ' at ' + country.phone1">
                                    <img :src="country?.media?.length > 0
                                        ? country?.media[0].original_url
                                        : ''" height="20" width="30" class="flag-small me-1"
                                        :alt="country.name + ' Flag'" aria-hidden="true" />
                                    {{ country.phone1 }}
                                </a>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </nav>
        <nav class="navbar navbar-expand-lg p-0 border-bottom w-100 py-3 navbar-second"
            :class="{ 'navbar-live-auction': isAuctionShowRoute() }">

            <div class="container-fluid">
                <div class="row mobile-navbar navbar-toggler border-0 align-items-center">
                    <div class="col-4">
                        <button class="navbar-toggler bg-white" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarNavOne" aria-controls="navbarNavOne" aria-expanded="false"
                            aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>
                    <div class="col-4">
                        <!-- <div class="navbar-toggler text-white border-0 fw-bold">
                           World Auto Dealers
                        </div> -->
                    </div>
                    <div class="col-4 text-end">
                        <div class="navbar-toggler border-0">
                            <div class="dropdown">
                                <button class="border-0 marginformobile" data-bs-toggle="dropdown" aria-expanded="false"
                                    style="background-color: transparent">
                                    <i class="fa-solid fa-user text-white"></i>
                                </button>

                                <ul class="dropdown-menu" v-if="$page.props.logged_customer">
                                    <li>
                                        <Link class="dropdown-item pb-2" :href="route('profile')">My Account</Link>
                                    </li>
                                    <li>
                                        <Link class="dropdown-item pb-2" :href="route(
                                            'front.end.customer.logout'
                                        )
                                            ">Logout</Link>
                                    </li>
                                </ul>
                                <ul class="dropdown-menu" v-else>
                                    <li>
                                        <Link class="dropdown-item pb-2" :href="route('user.login')">LOGIN</Link>
                                    </li>
                                    <li>
                                        <Link class="dropdown-item pb-2" :href="route('register')">REGISTER</Link>
                                    </li>
                                </ul>
                                <ul class="dropdown-menu">
                                    <li>
                                        <Link class="dropdown-item pb-2" :href="route('dealer.register')">BECOME A
                                        DEALER</Link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="collapse navbar-collapse justify-content-center justify-content-lg-around"
                    id="navbarNavOne">

                    <ul class="navbar-nav align-items-center responsiveNavbar">



                        <!-- <li class="nav-item logoSm d-none">
                            <Link class="nav-link mx-3" :href="route('index')">
                            <img :src="$page.url.includes('/view-details') ? '/images/World Auto Dealers Logo - 3 Colored.png' : '/images/World Auto Dealers Logo - 3 Colored.png'"
                                alt="World Auto DealersLogo" />
                            </Link>
                        </li> -->

                        <li class="nav-item">
                            <Link class="nav-link linkstyledSeconRow mx-1" :class="{
                                active: isActive('index'),
                                linkHoverSeconRow: $page.url.includes('/view-details'),
                                'white-text': $page.url.includes('/view-details') || $page.url.includes('/auction-stat')
                            }" :href="route('index')">
                            Home
                            </Link>
                        </li>
                        <li class="nav-item">
                            <Link class="nav-link linkstyledSeconRow mx-1" :class="{
                                'linkHoverSeconRow': $page.url.includes('/view-details'),
                                active: isActive('available'),
                                'white-text': $page.url.includes('/view-details') || $page.url.includes('/auction-stat')
                            }" :href="route('available')">
                            Local Stocks
                            </Link>
                        </li>
                        <li class="nav-item">
                            <Link class="nav-link linkstyledSeconRow mx-1" :class="{
                                'linkHoverSeconRow': $page.url.includes('/view-details'),
                                active: isActive('live.auction'),
                                'white-text': $page.url.includes('/view-details') || $page.url.includes('/auction-stat')
                            }" :href="route('live.auction')">
                            Live Auction In Japan
                            </Link>
                        </li>
                        <li class="nav-item logoMd">
                            <Link class="nav-link mx-3" :href="route('index')">
                            <!-- <img :src="$page.url.includes('/view-details') ? '/images/HWorld Auto Dealersblack.png' : '/images/World Auto Dealers.png'"
                                alt="World Auto Dealers Logo" /> -->
                            <img :src="isDetailPage
                                ? '/images/new_logo.png'
                                : '/images/new_logo (3).png'" alt="World Auto Dealers Logo" />
                            </Link>
                        </li>

                        <!-- <li class="nav-item">
                            <Link class="nav-link linkstyledSeconRow mx-1" :class="{
                                'linkHoverSeconRow': $page.url.includes('/view-details'),
                                active: isActive('testimonials'),
                                'white-text': $page.url.includes('/view-details')
                            }" :href="route('testimonials')">
                            Fuel Savings
                            </Link>
                        </li> -->
                        <li class="nav-item">
                            <Link class="nav-link linkstyledSeconRow mx-1" :class="{
                                'linkHoverSeconRow': $page.url.includes('/view-details'), active: isActive('knowledge.center'),
                                'white-text': $page.url.includes('/view-details') || $page.url.includes('/auction-stat')
                            }" :href="route('knowledge.center')">
                            Knowledge Center
                            </Link>
                        </li>

                        <li class="nav-item">
                            <Link class="nav-link linkstyledSeconRow mx-1" :class="{
                                'linkHoverSeconRow': $page.url.includes('/view-details'), active: isActive('about'),
                                'white-text': $page.url.includes('/view-details') || $page.url.includes('/auction-stat'),
                            }" :href="route('about')">
                            About
                            </Link>
                        </li>
                        <li class="nav-item">
                            <Link class="nav-link linkstyledSeconRow mx-1"
                                :class="{ 'linkHoverSeconRow': $page.url.includes('/view-details'), active: isActive('contact'), 'white-text': $page.url.includes('/view-details') || $page.url.includes('/auction-stat'), }"
                                :href="route('contact')">
                            Contact
                            </Link>
                        </li>



                        <!-- Mobile-only: centered “phone” links that actually select the country -->
                        <li class="nav-item w-100 d-lg-none">
                            <div class="d-flex justify-content-center align-items-center py-2">
                                <template v-if="$page.props.countries">
                                    <Link v-for="country in $page.props.countries
                                        .filter(c => ['sri lanka', 'australia']
                                            .includes(c.name.toLowerCase()))" :key="country.id" :href="route('available', {
                                                countrySlug: country.name.toLowerCase().replace(/\s+/g, '-')
                                            })" preserve-state preserve-scroll @click="selectCountry(country)"
                                        class="nav-link d-inline-flex align-items-center mx-2"
                                        style="font-size:16px; color:#fff;">
                                    <img :src="country.media?.[0].original_url || ''" alt="flag" class="flag-small me-1"
                                        height="16" width="24" />
                                    <span>{{ country.phone1 }}</span>
                                    </Link>
                                </template>
                            </div>
                        </li>


                    </ul>
                </div>
            </div>
        </nav>
    </nav>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";

export default {
    components: {
        Link,
    },
    data() {
        return {
            index: 0,
        };
    },
    mounted() {
        window.addEventListener('scroll', this.handleScroll);
    },
    beforeDestroy() {
        window.removeEventListener('scroll', this.handleScroll);
    },
    methods: {
        isViewDetailsRoute() {
            return window.location.pathname === '/view-details';
        },
        isAuctionShowRoute() {
            const p = window.location.pathname
            return p.startsWith('/live-auction/')
                || p.startsWith('/auction/')
        },
        handleScroll() {
            const navbar = document.querySelector('.navbar-second');
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        },
        isActive(routeName) {
            // turn the full URL into just its pathname
            const url = route(routeName)
            const pathname = new URL(url, window.location.origin).pathname
            return window.location.pathname === pathname
        }
    },

    computed: {
        isDetailPage() {
            const u = this.$page.url
            return (
                u.includes("/view-details") ||
                u.includes("/live-auction/") ||
                u.startsWith("/auction/") ||
                u.startsWith("/auction-stat/")
            )
        }
    }

};
</script>

<style>
.nav-link.active {
    color: #000 !important;
    text-decoration: underline;
    border-bottom: none !important;
}


.linkstyledSeconRow,
.linkstyledSeconRow:focus {
    font-size: 17px !important;
    color: white;
}

.phone-item {
    display: flex;
    align-items: center;
    border: 1px solid #fff;
    border-radius: 50px;
}

.flag-small {
    width: 30px !important;
    height: 16px !important;
    object-fit: cover;
}

/* .navbar-second {
    background: none !important;
    background-color: transparent !important;
    box-shadow: none !important;
    backdrop-filter: none !important;
    transition: background-color 0.3s ease, backdrop-filter 0.3s ease;
} */
.navbar {
    background: linear-gradient(90deg, #0D1B2A 0%, #0D1B2A 50%, #1B263B 75%, #0D1B2A 88%, #0D1B2A 100%) !important;
}

.navbar-second {
    background: none !important;
    background-color: transparent !important;
    box-shadow: none !important;
    backdrop-filter: blur(0px) !important;
    -webkit-backdrop-filter: blur(10px) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    position: sticky;
    top: 0;
    z-index: 1000;
    transition: background-color 0.3s, backdrop-filter 0.3s;
}

.navbar-second .navbar-nav .nav-item:not(:last-child) {
    margin-right: 2.5rem;
}


@media (max-width: 1024px) {
    .navbar-second {
        background-color: rgba(6, 40, 77, 0.411) !important;
        backdrop-filter: blur(10px) !important;
    }

    .navbar-second .navbar-nav .nav-item {
        margin-right: 0 !important;
    }
}

.navbar-second.navbar-scrolled {
    background-color: #4983c0a1 !important;
    backdrop-filter: blur(30px) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.mainNavBar .navbar:first-child .nav-link {
    color: #ffffff !important;
}

.navbar-second {
    position: sticky;
    top: 0;
    z-index: 1000;
}

.navbar-second.border-bottom {
    border-color: transparent !important;
}

/* Existing Styles */
.countries-list {
    display: flex;
    gap: 1rem;
}

.countries-list a {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    color: #ffffff;
}

.countries-list a.active {
    font-weight: bold;
    color: #ffffff;
}

.countries-list img {
    width: 40px;
    height: 20px;
}

.contact-info {
    display: flex;
    gap: 1.5rem;
}

.contact-info a {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    color: #ffffff;
}

.linkHovernew {
    background-color: #dee5ee00;
    color: white;
}

.linkHovernew:focus {
    background-color: #0d6dfd00;
    color: white;
}

.linkHovernew:hover {
    background-color: #5896f400;
    color: white;
}

.linkHovernew i {
    color: #0d6dfdb7;
}

@media (max-width: 1367px) {
    .linkHoverSeconRow {
        font-size: 12px !important;
    }
}

.dropdown-item:hover {
    background-color: #000000;
    color: #000000 !important;
    transition: 0.3s ease;
    border-radius: 6px;
}

.white-text {
    color: rgb(0, 0, 0) !important;
}

@media (max-width: 992px) {

    .navbar-view-details .navbar-second .responsiveNavbar .nav-link,
    .navbar-second.navbar-live-auction .responsiveNavbar .nav-link {
        color: #fff !important;
    }

    .navbar-second .responsiveNavbar .nav-link,
    .navbar-second.navbar-view-details .responsiveNavbar .nav-link {
        color: #fff !important;
    }
}

.navbar-second.navbar-view-details {
    background-color: #333333 !important;
    backdrop-filter: none !important;
}

/* .linkHoverSeconRow:hover {
    color: rgb(0, 0, 0) !important;
} */

.linkstyledSeconRow,
.linkstyledSeconRow:focus {
    font-size: 17px !important;
    color: white;
}


.navbar-second.navbar-scrolled {
    background-color: #4983c0a1 !important;
    backdrop-filter: blur(30px) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
}

@media (max-width: 992px) {

    .navbar-second .navbar-nav {
        justify-content: center !important;
    }

    .navbar-second .navbar-nav .nav-item {
        margin-right: 0 !important;
    }

    .navbar-second .responsiveNavbar {

        row-gap: 1rem;
        padding: 1rem 0;
    }


}


.navbar-second .linkstyledSeconRow {
    color: #fdf7f7;
    position: relative;
    transition: color 0.2s ease;
}

/* the “underline” pseudo-element */
.navbar-second .linkstyledSeconRow::after {
    content: "";
    position: absolute;
    bottom: -4px;
    /* space under the text */
    left: 50%;
    width: 0;
    height: 2px;
    background: #fff;
    transform: translateX(-50%);
    transition: width 0.3s ease;
}

/* on hover: grow the line from the center outwards */
.navbar-second .linkstyledSeconRow:hover {
    color: #fff;
}

.navbar-second .linkstyledSeconRow:hover::after {
    width: 100%;
}

/* active link stays white + keeps its underline */
.navbar-second .linkstyledSeconRow.active,
.navbar-second .linkstyledSeconRow.router-link-active {
    color: #fff !important;
}

.navbar-second .linkstyledSeconRow.active::after,
.navbar-second .linkstyledSeconRow.router-link-active::after {
    width: 100%;
}


/* 1) Select the first nested .navbar inside .navbar-view-details */
.navbar-view-details>.navbar:first-of-type .nav-link,
.navbar-view-details>.navbar:first-of-type .nav-link:hover {
    color: #000 !important;
}

/* 2) If you’re using the underline pseudo-element on those same links */
.navbar-view-details>.navbar:first-of-type .nav-link::after {
    background: #000 !important;
}

/* default: hidden */
.nav-item2.mobile-only {
    display: none;
}

/* small screens up to 767px */
@media (max-width: 767px) {
    .nav-item2.mobile-only {
        display: block;
    }
}

/* Tablet and below: make links a bit smaller */
@media (max-width: 768px) {
    .navbar-second .linkstyledSeconRow {
        font-size: 0.9rem !important;
        /* was 1.0625rem (17px), now ~14px */
        padding: 0.25rem 0.5rem !important;
    }

    .navbar-second .responsiveNavbar .nav-item {
        margin-right: 0.75rem !important;
    }
}

/* Phone landscape / small tablets */
@media (max-width: 576px) {
    .navbar-second .linkstyledSeconRow {
        font-size: 0.8rem !important;
        /* ~13px */
        padding: 0.2rem 0.4rem !important;
    }

    .navbar-second .responsiveNavbar .nav-item {
        margin-right: 0.5rem !important;
    }
}

/* Phone portrait and very narrow */
@media (max-width: 1260px) {
    .navbar-second .linkstyledSeconRow {
        font-size: 1rem !important;
        /* ~11px */
        padding: 0.18rem 0.4rem !important;
    }

    .navbar-second .responsiveNavbar .nav-item {
        margin-right: 0.4rem !important;
    }
}
</style>
