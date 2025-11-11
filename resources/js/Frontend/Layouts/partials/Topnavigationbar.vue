<template>
    <header class="mh-header">
        <!-- TOP: brand + quick actions + mobile hamburger -->
        <div class="mh-top">
            <div class="mh-wrap">
                <Link :href="route('index')" class="mh-brand">
                    <img
                        :src="logoSrc"
                        alt="Mahinda Hardware & Electrical"
                        class="mh-logo"
                    />
                    <span class="mh-brand-text"
                        >Mahinda Hardware & Electrical</span
                    >
                </Link>

                <!-- quick actions -->
                <ul class="mh-quick">
                    <li>
                        <Link href="#" class="mh-quick-item">
                            <i class="fa-regular fa-heart"></i>
                            <div class="t">
                                <span class="muted">WELCOME</span
                                ><strong>WISH LIST</strong>
                            </div>
                        </Link>
                    </li>
                    <li>
                        <Link
                            :href="
                                loggedIn
                                    ? route('profile')
                                    : route('user.login')
                            "
                            class="mh-quick-item"
                        >
                            <i class="fa-regular fa-user"></i>
                            <div class="t">
                                <span class="muted">HELLO</span
                                ><strong>YOUR ACCOUNT</strong>
                            </div>
                        </Link>
                    </li>
                    <li>
                        <Link href="#" class="mh-quick-item">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <div class="t">
                                <span class="muted">MY CART</span
                                ><strong
                                    >Rs. {{ formatMoney(cartTotal) }}</strong
                                >
                            </div>
                        </Link>
                    </li>
                </ul>

                <!-- mobile burger -->
                <button
                    class="mh-burger"
                    @click="drawer = true"
                    aria-label="Open menu"
                >
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- NAV + SEARCH -->
        <div class="mh-navrow" :class="{ 'is-scrolled': scrolled }">
            <div class="mh-wrap">
                <nav class="mh-menu">
                    <!-- Product Range: mega menu -->
                    <div
                        class="mitem has-mega"
                        @mouseenter="mega = true"
                        @mouseleave="mega = false"
                        @focusin="mega = true"
                        @focusout="mega = false"
                    >
                        <span class="mtext">PRODUCT RANGE</span>
                        <div class="mega" v-show="mega">
                            <div class="mega-inner">
                                <div class="mega-col">
                                    <h6>Top Categories</h6>
                                    <ul class="grid">
                                        <li
                                            v-for="c in topCategories"
                                            :key="c.id"
                                        >
                                            <Link
                                                :href="categoryHref(c)"
                                                class="grid-item"
                                            >
                                                <span class="dot"></span>
                                                <span class="label">{{
                                                    c.title
                                                }}</span>
                                            </Link>
                                        </li>
                                    </ul>
                                    <Link
                                        :href="route('index')"
                                        class="view-all"
                                        >View all categories</Link
                                    >
                                </div>
                                <div class="mega-col">
                                    <h6>Popular Brands</h6>
                                    <ul class="brandlist">
                                        <li v-for="b in topBrands" :key="b.id">
                                            <Link
                                                :href="brandHref(b)"
                                                class="brand-item"
                                                >{{ b.title }}</Link
                                            >
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <Link
                        :href="route('index')"
                        class="mitem"
                        :class="{ active: isActive('index') }"
                    >
                        HOME
                    </Link>

                    <div
                        class="mitem has-dd"
                        @mouseenter="brandsOpen = true"
                        @mouseleave="brandsOpen = false"
                    >
                        <span class="mtext">BRANDS</span>
                        <ul class="dd" v-show="brandsOpen">
                            <li v-for="b in brandDropdown" :key="b.id">
                                <Link :href="brandHref(b)">{{ b.title }}</Link>
                            </li>
                            <li class="all">
                                <Link :href="route('index')">All Brands</Link>
                            </li>
                        </ul>
                    </div>

                    <Link :href="route('index')" class="mitem">FEATURED</Link>

                    <Link :href="route('index')" class="mitem sale">
                        SPECIAL OFFERS
                        <span class="badge-sale">sale</span>
                    </Link>

                    <a href="#" class="mitem">CAREERS</a>
                    <Link
                        :href="route('contact')"
                        class="mitem"
                        :class="{ active: isActive('contact') }"
                        >CONTACT US</Link
                    >
                </nav>

                <!-- Search -->
                <form class="mh-search" @submit.prevent="submitSearch">
                    <input
                        v-model="q"
                        type="text"
                        placeholder="SEARCH ENTIRE STORE HERE..."
                        aria-label="Search"
                    />
                    <button
                        class="btn-search"
                        type="submit"
                        aria-label="Search"
                    >
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <button
                        class="btn-gear"
                        type="button"
                        aria-label="More filters"
                    >
                        <i class="fa-solid fa-gear"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- MOBILE DRAWER -->
        <transition name="fade">
            <div
                v-if="drawer"
                class="drawer-mask"
                @click="drawer = false"
            ></div>
        </transition>
        <transition name="slide">
            <aside v-if="drawer" class="drawer" role="dialog" aria-modal="true">
                <div class="drawer-head">
                    <span>Menu</span>
                    <button
                        class="close"
                        @click="drawer = false"
                        aria-label="Close"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="drawer-search">
                    <form @submit.prevent="submitSearch">
                        <input
                            v-model="q"
                            type="text"
                            placeholder="Search products…"
                        />
                        <button type="submit">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </form>
                </div>

                <ul class="drawer-list">
                    <li>
                        <Link :href="route('index')" @click="drawer = false"
                            >Home</Link
                        >
                    </li>

                    <li class="acc">
                        <button @click="acc.product = !acc.product">
                            Product Range
                            <i
                                :class="[
                                    'fa-solid',
                                    acc.product
                                        ? 'fa-chevron-up'
                                        : 'fa-chevron-down',
                                ]"
                            ></i>
                        </button>
                        <ul v-show="acc.product">
                            <li v-for="c in topCategories" :key="c.id">
                                <Link
                                    :href="categoryHref(c)"
                                    @click="drawer = false"
                                    >{{ c.title }}</Link
                                >
                            </li>
                            <li>
                                <Link
                                    :href="route('index')"
                                    @click="drawer = false"
                                    >All Categories</Link
                                >
                            </li>
                        </ul>
                    </li>

                    <li class="acc">
                        <button @click="acc.brands = !acc.brands">
                            Brands
                            <i
                                :class="[
                                    'fa-solid',
                                    acc.brands
                                        ? 'fa-chevron-up'
                                        : 'fa-chevron-down',
                                ]"
                            ></i>
                        </button>
                        <ul v-show="acc.brands">
                            <li v-for="b in brandDropdown" :key="b.id">
                                <Link
                                    :href="brandHref(b)"
                                    @click="drawer = false"
                                    >{{ b.title }}</Link
                                >
                            </li>
                            <li>
                                <Link
                                    :href="route('index')"
                                    @click="drawer = false"
                                    >All Brands</Link
                                >
                            </li>
                        </ul>
                    </li>

                    <li>
                        <Link :href="route('index')" @click="drawer = false"
                            >Featured</Link
                        >
                    </li>
                    <li>
                        <Link :href="route('index')" @click="drawer = false"
                            >Special Offers</Link
                        >
                    </li>
                    <li>
                        <a href="#" @click.prevent="drawer = false">Careers</a>
                    </li>
                    <li>
                        <Link :href="route('contact')" @click="drawer = false"
                            >Contact Us</Link
                        >
                    </li>
                </ul>
            </aside>
        </transition>
    </header>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";

export default {
    name: "HardwareNavbarPro",
    components: { Link },
    props: {
        cartTotal: { type: Number, default: 0 },
        logoSrc: { type: String, default: "/images/rohana-logo.png" },
        categories: Array, // optional – will fall back to $page.props.categories
        brands: Array, // optional – will fall back to $page.props.brands
    },
    data() {
        return {
            q: "",
            mega: false,
            brandsOpen: false,
            drawer: false,
            scrolled: false,
            acc: { product: true, brands: false },
        };
    },
    computed: {
        loggedIn() {
            return !!this.$page?.props?.logged_customer;
        },
        cats() {
            return this.categories ?? this.$page?.props?.categories ?? [];
        },
        brs() {
            return this.brands ?? this.$page?.props?.brands ?? [];
        },
        topCategories() {
            // show featured first then top N
            const list = [...this.cats];
            list.sort(
                (a, b) =>
                    (b.featured | 0) - (a.featured | 0) ||
                    a.title.localeCompare(b.title)
            );
            return list.slice(0, 10);
        },
        topBrands() {
            const list = [...this.brs];
            list.sort(
                (a, b) =>
                    (b.featured | 0) - (a.featured | 0) ||
                    a.title.localeCompare(b.title)
            );
            return list.slice(0, 10);
        },
        brandDropdown() {
            return this.topBrands.slice(0, 8);
        },
    },
    mounted() {
        window.addEventListener("scroll", this.onScroll, { passive: true });
    },
    beforeUnmount() {
        window.removeEventListener("scroll", this.onScroll);
    },
    methods: {
        onScroll() {
            this.scrolled = window.scrollY > 8;
        },
        submitSearch() {
            this.$inertia.get(
                route("index"),
                { q: this.q },
                { preserveState: true, preserveScroll: true }
            );
        },
        isActive(name) {
            try {
                const url = route(name);
                const path = new URL(url, window.location.origin).pathname;
                return window.location.pathname === path;
            } catch {
                return false;
            }
        },
        formatMoney(n) {
            return (n || 0).toFixed(2);
        },
        categoryHref(c) {
            // adjust to your real route; using query param fallback
            return this.$page.url.includes("?")
                ? `${route("index")}&category=${this.slug(c.slug || c.title)}`
                : `${route("index")}?category=${this.slug(c.slug || c.title)}`;
        },
        brandHref(b) {
            return this.$page.url.includes("?")
                ? `${route("index")}&brand=${this.slug(b.slug || b.title)}`
                : `${route("index")}?brand=${this.slug(b.slug || b.title)}`;
        },
        slug(s = "") {
            return String(s)
                .trim()
                .toLowerCase()
                .replace(/\s+/g, "-")
                .replace(/[^a-z0-9-]/g, "");
        },
    },
};
</script>

<style scoped>
/* ===== Sticky + palette (your gradient) ===== */
.mh-header {
    position: sticky;
    top: 0;
    z-index: 1001;
    width: 100%;
    --grad: linear-gradient(
        90deg,
        #415a77 0%,
        #0a3f79 0%,
        #163353 34%,
        #142334 67%,
        #0e1e30 80%,
        #0d1b2a 100%
    );
    --text: #fff;
    --muted: #dfe4ff;
    --icon: #ffd400;
    --divider: rgba(255, 255, 255, 0.18);
    --pill-bg: rgba(255, 255, 255, 0.06);
}

/* ===== shared container ===== */
.mh-wrap {
    max-width: 1320px;
    margin: 0 auto;
    padding: 0 12px;
}

/* ===== top ===== */
.mh-top {
    background: var(--grad);
}
.mh-top .mh-wrap {
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.mh-brand {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    color: var(--text);
    text-decoration: none;
}
.mh-logo {
    width: 44px;
    height: 44px;
    border-radius: 999px;
    object-fit: contain;
}
.mh-brand-text {
    font-size: 26px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: var(--text);
}

.mh-quick {
    display: flex;
    gap: 26px;
    list-style: none;
    margin: 0;
    padding: 0;
}
.mh-quick-item {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    color: var(--text);
    text-decoration: none;
}
.mh-quick-item i {
    color: var(--icon);
    font-size: 20px;
}
.mh-quick-item .t {
    display: flex;
    flex-direction: column;
    line-height: 1.05;
}
.mh-quick-item .t .muted {
    color: var(--muted);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}
.mh-quick-item .t strong {
    color: var(--text);
    font-size: 13px;
    font-weight: 800;
    text-transform: uppercase;
}

.mh-burger {
    display: none;
    background: transparent;
    border: none;
    color: #fff;
    font-size: 22px;
}

/* ===== nav + search row ===== */
.mh-navrow {
    background: var(--grad);
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    transition: box-shadow 0.2s ease;
}
.mh-navrow.is-scrolled {
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
}
.mh-navrow .mh-wrap {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 16px;
    align-items: end;
    padding-top: 8px;
    padding-bottom: 12px;
}

.mh-menu {
    display: flex;
    gap: 28px;
    align-items: flex-end;
}
.mitem {
    position: relative;
    display: inline-block;
    padding: 0 2px 8px;
    color: #fff;
    font-weight: 800;
    font-size: 13.5px;
    text-transform: uppercase;
    text-decoration: none;
}
.mitem::after {
    content: "";
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    bottom: -2px;
    height: 2px;
    width: 0;
    background: #fff;
    transition: width 0.25s ease;
}
.mitem:hover::after,
.mitem.active::after {
    width: 100%;
}

/* sale badge */
.mitem.sale {
    padding-top: 16px;
}
.badge-sale {
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: #ffd400;
    color: #1f2937;
    font-weight: 800;
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 3px;
}

/* dropdowns / mega */
.has-dd {
    position: relative;
}
.dd {
    position: absolute;
    top: 100%;
    left: 0;
    min-width: 220px;
    background: #0e1e30;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 6px;
    padding: 8px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
}
.dd li {
    list-style: none;
}
.dd a {
    display: block;
    padding: 8px 10px;
    color: #e7efff;
    text-decoration: none;
    border-radius: 4px;
}
.dd a:hover {
    background: rgba(255, 255, 255, 0.08);
}
.dd .all a {
    color: #fff;
    font-weight: 700;
}

/* mega */
.has-mega {
    position: relative;
}
.mega {
    position: absolute;
    top: 100%;
    left: 0;
    width: 720px;
    background: #0e1e30;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 10px;
    padding: 16px;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45);
}
.mega-inner {
    display: grid;
    grid-template-columns: 1.6fr 1fr;
    gap: 16px;
}
.mega-col h6 {
    color: #fff;
    font-weight: 800;
    font-size: 12px;
    letter-spacing: 0.6px;
    text-transform: uppercase;
    margin: 0 0 8px;
}
.grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 6px 10px;
    margin: 0;
    padding: 0;
    list-style: none;
}
.grid-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #e7efff;
    text-decoration: none;
    padding: 6px 6px;
    border-radius: 6px;
}
.grid-item:hover {
    background: rgba(255, 255, 255, 0.08);
}
.grid-item .dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #ffd400;
}
.grid-item .label {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 260px;
}
.view-all {
    display: inline-block;
    margin-top: 8px;
    color: #fff;
    font-weight: 700;
    text-decoration: none;
}
.brandlist {
    display: grid;
    grid-template-columns: 1fr;
    gap: 4px;
    margin: 0;
    padding: 0;
    list-style: none;
}
.brand-item {
    display: block;
    color: #e7efff;
    text-decoration: none;
    padding: 6px 6px;
    border-radius: 6px;
}
.brand-item:hover {
    background: rgba(255, 255, 255, 0.08);
}

/* search */
.mh-search {
    display: grid;
    grid-template-columns: 1fr auto auto;
    align-items: center;
    background: var(--pill-bg);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    overflow: hidden;
    backdrop-filter: blur(2px);
}
.mh-search input {
    height: 44px;
    padding: 0 14px;
    background: transparent;
    border: none;
    outline: none;
    color: #eaf1ff;
    font-weight: 700;
    letter-spacing: 0.2px;
}
.mh-search input::placeholder {
    color: #e0e7ff;
}
.mh-search .btn-search,
.mh-search .btn-gear {
    height: 44px;
    width: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    background: transparent;
    border: none;
    cursor: pointer;
}
.mh-search .btn-search:hover,
.mh-search .btn-gear:hover {
    background: rgba(255, 255, 255, 0.1);
}

/* ===== mobile ===== */
@media (max-width: 992px) {
    .mh-top .mh-wrap {
        height: auto;
        padding: 10px 12px;
        gap: 10px;
    }
    .mh-quick {
        display: none;
    } /* keep header clean on mobile */
    .mh-burger {
        display: block;
    }
    .mh-navrow .mh-wrap {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    .mh-menu {
        display: none;
    } /* hidden; we use drawer */
}
@media (max-width: 640px) {
    .mh-brand-text {
        font-size: 18px;
    }
    .mh-logo {
        width: 40px;
        height: 40px;
    }
}

/* ===== drawer ===== */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
.slide-enter-active,
.slide-leave-active {
    transition: transform 0.2s ease;
}
.slide-enter-from,
.slide-leave-to {
    transform: translateX(-100%);
}

.drawer-mask {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.4);
    z-index: 1000;
}
.drawer {
    position: fixed;
    inset: 0 auto 0 0;
    width: 86vw;
    max-width: 360px;
    background: #0e1e30;
    color: #fff;
    z-index: 1001;
    display: flex;
    flex-direction: column;
}
.drawer-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 14px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12);
}
.drawer-head .close {
    background: transparent;
    border: none;
    color: #fff;
    font-size: 20px;
}
.drawer-search form {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 8px;
    padding: 12px;
}
.drawer-search input {
    height: 42px;
    padding: 0 12px;
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: #0b1624;
    color: #eaf1ff;
}
.drawer-search button {
    width: 42px;
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: #0b1624;
    color: #fff;
}
.drawer-list {
    list-style: none;
    padding: 8px 0 18px;
    margin: 0;
}
.drawer-list > li > a {
    display: block;
    padding: 12px 14px;
    color: #fff;
    text-decoration: none;
}
.drawer-list > li > a:hover {
    background: rgba(255, 255, 255, 0.08);
}
.drawer-list .acc {
    border-top: 1px solid rgba(255, 255, 255, 0.12);
}
.drawer-list .acc button {
    width: 100%;
    text-align: left;
    padding: 12px 14px;
    background: transparent;
    border: none;
    color: #fff;
    font-weight: 700;
    display: flex;
    justify-content: space-between;
}
.drawer-list .acc ul {
    list-style: none;
    margin: 0;
    padding: 0 0 8px 0;
}
.drawer-list .acc ul li a {
    display: block;
    padding: 10px 22px;
    color: #e7efff;
    text-decoration: none;
}
.drawer-list .acc ul li a:hover {
    background: rgba(255, 255, 255, 0.08);
}
</style>
