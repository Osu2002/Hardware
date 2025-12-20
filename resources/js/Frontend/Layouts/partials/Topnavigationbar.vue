<template>
    <div class="mh-shell">
        <!-- ================= FULL NAVBAR (top of page) ================= -->
        <header
            class="mh-header mh-header-main"
            :class="{ 'is-hidden': scrolled }"
        >
            <!-- TOP: logo + quick actions + mobile hamburger -->
            <div class="mh-top">
                <div class="mh-wrap mh-top-wrap">
                    <Link
                        :href="route('index')"
                        class="mh-brand"
                        aria-label="Mahinda Hardware &amp; Electrical"
                    >
                        <img
                            :src="logoSrc"
                            alt="Mahinda Hardware & Electrical"
                            class="mh-logo"
                        />
                    </Link>

                    <!-- quick actions (desktop) -->
                    <ul class="mh-quick">
                        <li>
                            <Link href="#" class="mh-quick-item">
                                <i class="fa-regular fa-heart"></i>
                                <div class="t">
                                    <span class="muted">WELCOME</span>
                                    <strong>WISH LIST</strong>
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
                                    <span class="muted">HELLO</span>
                                    <strong>YOUR ACCOUNT</strong>
                                </div>
                            </Link>
                        </li>
                        <li>
                            <Link href="#" class="mh-quick-item">
                                <i class="fa-solid fa-cart-shopping"></i>
                                <div class="t">
                                    <span class="muted">MY CART</span>
                                    <strong>Rs. {{ formatMoney(cartTotal) }}</strong>
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
            <div class="mh-navrow">
                <div class="mh-wrap mh-nav-wrap">
                    <nav class="mh-menu">
                        <!-- Categories dropdown -->
<!-- ✅ UPDATED: Categories (Category + Subcategory dropdown) -->
<div
  class="mitem has-dd mh-cats"
  @mouseenter="mega = true"
  @mouseleave="mega = false; activeCatId = null"
>
  <button
    type="button"
    class="mtext-btn mh-dd-btn"
    :class="{ 'is-open': mega }"
    :aria-expanded="mega ? 'true' : 'false'"
    aria-haspopup="menu"
    @click.stop="toggleMega"
  >
    <span>CATEGORIES</span>
    <i class="fa-solid fa-chevron-down dd-icon" aria-hidden="true"></i>
  </button>

  <transition name="mh-mega">
    <div
      class="mega mh-mega"
      :class="{ 'has-right': !!activeCategory }"
      v-show="mega"
      role="menu"
      aria-label="Categories"
      @keydown="onMegaKeydown"
    >
      <!-- LEFT: categories -->
      <div class="mega-left" role="none">
        <div
          v-for="c in topCategories"
          :key="c.id"
          class="mega-item"
          :class="{ active: Number(activeCatId) === Number(c.id) }"
          @mouseenter="activeCatId = c.id"
          role="none"
        >
          <Link
            :href="categoryHref(c)"
            class="mega-link"
            role="menuitem"
            @click="mega = false"
          >
            <span class="mh-dd-text">{{ c.title }}</span>
            <i
              v-if="(c.subcategories || []).length"
              class="fa-solid fa-chevron-right"
              aria-hidden="true"
            ></i>
          </Link>
        </div>
      </div>

      <!-- RIGHT: subcategories -->
      <div class="mega-right" v-if="activeCategory" role="none">
        <div class="mega-right-title" role="none">
          {{ activeCategory.title }}
        </div>

        <div v-if="activeSubcategories.length" class="mega-sublist" role="none">
          <Link
            v-for="s in activeSubcategories"
            :key="s.id"
            :href="subcategoryHref(activeCategory, s)"
            class="mega-sublink"
            role="menuitem"
            @click="mega = false"
          >
            <span class="mh-dd-text">{{ s.title }}</span>
          </Link>
        </div>

        <div v-else class="mega-empty" role="none">No subcategories</div>
      </div>
    </div>
  </transition>
</div>

                        <Link
                            :href="route('index')"
                            class="mitem"
                            :class="{ active: isActive('index') }"
                        >
                            HOME
                        </Link>

                        <!-- Brands dropdown -->
                       <!-- <div
  class="mitem has-dd"
  @mouseenter="brandsOpen = true"
  @mouseleave="brandsOpen = false"
>
  <button
    type="button"
    class="mtext-btn"
    :aria-expanded="brandsOpen ? 'true' : 'false'"
    aria-haspopup="menu"
    @click.stop="brandsOpen = !brandsOpen"
  >
    <span>BRANDS</span>
    <i class="fa-solid fa-chevron-down dd-icon"></i>
  </button>

  <ul class="dd" v-show="brandsOpen" role="menu" aria-label="Brands">
    <li v-for="b in brandDropdown" :key="b.id" role="none">
      <Link :href="brandHref(b)" role="menuitem">
        {{ b.title }}
      </Link>
    </li>
    <li class="all" role="none">
      <Link :href="route('index')" role="menuitem">All Brands</Link>
    </li>
  </ul>
</div> -->

                      <Link :href="route('featuredproducts.index')" class="mitem">FEATURED</Link>


                      <Link :href="route('specialoffers.index')" class="mitem">
  SPECIAL OFFERS
</Link>



                        <Link
                            :href="route('contact')"
                            class="mitem"
                            :class="{ active: isActive('contact') }"
                        >
                            CONTACT US
                        </Link>
                    </nav>

                    <!-- Search -->
                    <form class="mh-search" @submit.prevent="submitSearch">
                        <input
                            v-model="q"
                            type="text"
                            placeholder="Search entire store here..."
                            aria-label="Search"
                        />
                        <button
                            class="btn-search"
                            type="submit"
                            aria-label="Search"
                        >
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- ================= COMPACT NAVBAR (on scroll) ================= -->
        <header
            class="mh-header mh-header-compact"
            :class="{ 'is-visible': scrolled }"
        >
            <div class="mh-wrap mh-compact-wrap">
                <!-- small logo -->
                <Link
                    :href="route('index')"
                    class="mh-brand mini"
                    aria-label="Mahinda Hardware &amp; Electrical"
                >
                    <img
                        :src="logoSrc"
                        alt="Mahinda Hardware & Electrical"
                        class="mh-logo mini"
                    />
                </Link>

                <!-- nav (desktop) -->
               <nav class="mh-menu mh-menu-compact">
  <!-- ✅ CATEGORIES (compact) -->
  <div
    class="mitem has-dd mh-cats"
    @mouseenter="megaCompact = true"
    @mouseleave="megaCompact = false; activeCatIdCompact = null"
  >
    <button
      type="button"
      class="mtext-btn mh-dd-btn"
      :class="{ 'is-open': megaCompact }"
      :aria-expanded="megaCompact ? 'true' : 'false'"
      aria-haspopup="menu"
      @click.stop="toggleMegaCompact"
    >
      <span>CATEGORIES</span>
      <i class="fa-solid fa-chevron-down dd-icon" aria-hidden="true"></i>
    </button>

    <transition name="mh-mega">
      <div
        class="mega mh-mega"
        :class="{ 'has-right': !!activeCategoryCompact }"
        v-show="megaCompact"
        role="menu"
        aria-label="Categories"
        @keydown="onMegaKeydownCompact"
      >
        <!-- LEFT: categories -->
        <div class="mega-left" role="none">
          <div
            v-for="c in topCategories"
            :key="c.id"
            class="mega-item"
            :class="{ active: Number(activeCatIdCompact) === Number(c.id) }"
            @mouseenter="activeCatIdCompact = c.id"
            role="none"
          >
            <Link
              :href="categoryHref(c)"
              class="mega-link"
              role="menuitem"
              @click="megaCompact = false"
            >
              <span class="mh-dd-text">{{ c.title }}</span>
              <i
                v-if="(c.subcategories || []).length"
                class="fa-solid fa-chevron-right"
                aria-hidden="true"
              ></i>
            </Link>
          </div>
        </div>

        <!-- RIGHT: subcategories -->
        <div class="mega-right" v-if="activeCategoryCompact" role="none">
          <div class="mega-right-title" role="none">
            {{ activeCategoryCompact.title }}
          </div>

          <div v-if="activeSubcategoriesCompact.length" class="mega-sublist" role="none">
            <Link
              v-for="s in activeSubcategoriesCompact"
              :key="s.id"
              :href="subcategoryHref(activeCategoryCompact, s)"
              class="mega-sublink"
              role="menuitem"
              @click="megaCompact = false"
            >
              <span class="mh-dd-text">{{ s.title }}</span>
            </Link>
          </div>

          <div v-else class="mega-empty" role="none">No subcategories</div>
        </div>
      </div>
    </transition>
  </div>

  <!-- HOME -->
  <Link
    :href="route('index')"
    class="mitem"
    :class="{ active: isActive('index') }"
  >
    HOME
  </Link>

  <!-- ✅ BRANDS (compact) -->
  <!-- <div
    class="mitem has-dd"
    @mouseenter="brandsOpenCompact = true"
    @mouseleave="brandsOpenCompact = false"
  >
    <button
      type="button"
      class="mtext-btn"
      :aria-expanded="brandsOpenCompact ? 'true' : 'false'"
      aria-haspopup="menu"
      @click.stop="brandsOpenCompact = !brandsOpenCompact"
    >
      <span>BRANDS</span>
      <i class="fa-solid fa-chevron-down dd-icon" aria-hidden="true"></i>
    </button>

    <ul class="dd" v-show="brandsOpenCompact" role="menu" aria-label="Brands">
      <li v-for="b in brandDropdown" :key="b.id" role="none">
        <Link :href="brandHref(b)" role="menuitem" @click="brandsOpenCompact = false">
          {{ b.title }}
        </Link>
      </li>
      <li class="all" role="none">
        <Link :href="route('index')" role="menuitem" @click="brandsOpenCompact = false">
          All Brands
        </Link>
      </li>
    </ul>
  </div> -->

<Link :href="route('featuredproducts.index')" class="mitem">FEATURED</Link>

 <Link :href="route('specialoffers.index')" class="mitem">OFFERS</Link>


  <Link
    :href="route('contact')"
    class="mitem"
    :class="{ active: isActive('contact') }"
  >
    CONTACT
  </Link>
</nav>


                <!-- Search + icons -->
                <div class="mh-compact-right">
                    <form
                        class="mh-search mh-search-compact"
                        @submit.prevent="submitSearch"
                    >
                        <input
                            v-model="q"
                            type="text"
                            placeholder="Search..."
                            aria-label="Search"
                        />
                        <button
                            class="btn-search"
                            type="submit"
                            aria-label="Search"
                        >
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </form>

                    <div class="mh-compact-actions">
                        <Link href="#" class="icon-btn" aria-label="Cart">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </Link>
                        <Link
                            :href="
                                loggedIn
                                    ? route('profile')
                                    : route('user.login')
                            "
                            class="icon-btn"
                            aria-label="Your account"
                        >
                            <i class="fa-regular fa-user"></i>
                        </Link>
                    </div>

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
        </header>

        <!-- ================= MOBILE DRAWER ================= -->
        <transition name="fade">
            <div
                v-if="drawer"
                class="drawer-mask"
                @click="drawer = false"
            ></div>
        </transition>
        <transition name="slide">
            <aside
                v-if="drawer"
                class="drawer"
                role="dialog"
                aria-modal="true"
            >
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
                        <Link
                            :href="route('index')"
                            @click="drawer = false"
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

                    <!-- <li class="acc">
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
                    </li> -->

                    <li>
                       <Link :href="route('featuredproducts.index')" @click="drawer = false">Featured</Link>

                    </li>
                    <li>
                       <Link :href="route('specialoffers.index')" @click="drawer = false">Special Offers</Link>

                    </li>
                    <li>
                        <Link
                            :href="route('contact')"
                            @click="drawer = false"
                            >Contact Us</Link
                        >
                    </li>
                </ul>
            </aside>
        </transition>

        <!-- spacer so content is not hidden behind fixed headers -->
        <div class="mh-spacer"></div>
    </div>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";

export default {
    name: "HardwareNavbarPro",
    components: { Link },
    props: {
        cartTotal: { type: Number, default: 0 },
        logoSrc: { type: String, default: "/images/mahindalogo.png" },
        categories: Array, // optional – falls back to $page.props.categories / $page.props.navCategories
        brands: Array,     // optional – falls back to $page.props.brands / $page.props.navBrands
    },
    data() {
        return {
            q: "",
            mega: false,
            megaCompact: false,
            brandsOpen: false,
            brandsOpenCompact: false,
            drawer: false,
            scrolled: false,
            acc: { product: true, brands: false },
            activeCatId: null,
            activeCatIdCompact: null,
        };
    },
    computed: {
        loggedIn() {
            return !!this.$page?.props?.logged_customer;
        },

        // ✅ FIX: robust fallbacks across all pages
        cats() {
            return (
                this.categories ??
                this.$page?.props?.categories ??
                this.$page?.props?.navCategories ??
                []
            );
        },
        brs() {
            return (
                this.brands ??
                this.$page?.props?.brands ??
                this.$page?.props?.navBrands ??
                []
            );
        },

        activeCategoryCompact() {
            if (!this.activeCatIdCompact) return null;
            return (this.topCategories || []).find(
                (c) => Number(c.id) === Number(this.activeCatIdCompact)
            ) || null;
        },
        activeSubcategoriesCompact() {
            return this.activeCategoryCompact?.subcategories || [];
        },

        activeCategory() {
            if (!this.activeCatId) return null;
            return (this.topCategories || []).find(
                (c) => Number(c.id) === Number(this.activeCatId)
            ) || null;
        },
        activeSubcategories() {
            return this.activeCategory?.subcategories || [];
        },

        topCategories() {
            return (this.cats || []).slice(0, 10);
        },
        topBrands() {
            return (this.brs || []).slice(0, 10);
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
        toggleMega() {
            this.mega = !this.mega;
            if (this.mega) this.activeCatId = null;
        },

        // ✅ ADD: compact version used in template
        toggleMegaCompact() {
            this.megaCompact = !this.megaCompact;
            if (this.megaCompact) this.activeCatIdCompact = null;
        },

        onScroll() {
            this.scrolled = window.scrollY > 80;
        },

        submitSearch() {
            this.$inertia.get(
                route("index"),
                { q: this.q },
                { preserveState: true, preserveScroll: true }
            );
        },

        categoryHref(c) {
            return route("category.list", c.id);
        },

        // ✅ FIX: match CategoryListController which expects `sub`
        subcategoryHref(cat, sub) {
            const base = route("category.list", cat.id);
            return base + "?sub=" + encodeURIComponent(sub.id);
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

        brandHref(b) {
            return route("index", { brand: b.slug || this.slug(b.title) });
        },

        slug(s = "") {
            return String(s)
                .trim()
                .toLowerCase()
                .replace(/\s+/g, "-")
                .replace(/[^a-z0-9-]/g, "");
        },

        onMegaKeydown(e) {
            const k = e.key;

            if (k === "Escape") {
                e.preventDefault();
                this.mega = false;
                this.activeCatId = null;
                return;
            }

            if (!["ArrowDown", "ArrowUp", "ArrowRight", "ArrowLeft"].includes(k)) return;

            e.preventDefault();

            const root = e.currentTarget;

            if (k === "ArrowRight") {
                const firstSub = root.querySelector(".mega-right a.mega-sublink");
                if (firstSub) return firstSub.focus();
            }

            if (k === "ArrowLeft") {
                const activeCat = root.querySelector(".mega-item.active a.mega-link");
                if (activeCat) return activeCat.focus();
            }

            const items = Array.from(
                root.querySelectorAll("a.mega-link, a.mega-sublink")
            ).filter((el) => el && el.offsetParent !== null);

            if (!items.length) return;

            const idx = items.indexOf(document.activeElement);
            const dir = k === "ArrowDown" ? 1 : -1;
            const next = items[(Math.max(0, idx) + dir + items.length) % items.length];
            next && next.focus();
        },

        // ✅ ADD: compact keydown handler used in template
        onMegaKeydownCompact(e) {
            const k = e.key;

            if (k === "Escape") {
                e.preventDefault();
                this.megaCompact = false;
                this.activeCatIdCompact = null;
                return;
            }

            if (!["ArrowDown", "ArrowUp", "ArrowRight", "ArrowLeft"].includes(k)) return;

            e.preventDefault();

            const root = e.currentTarget;

            if (k === "ArrowRight") {
                const firstSub = root.querySelector(".mega-right a.mega-sublink");
                if (firstSub) return firstSub.focus();
            }

            if (k === "ArrowLeft") {
                const activeCat = root.querySelector(".mega-item.active a.mega-link");
                if (activeCat) return activeCat.focus();
            }

            const items = Array.from(
                root.querySelectorAll("a.mega-link, a.mega-sublink")
            ).filter((el) => el && el.offsetParent !== null);

            if (!items.length) return;

            const idx = items.indexOf(document.activeElement);
            const dir = k === "ArrowDown" ? 1 : -1;
            const next = items[(Math.max(0, idx) + dir + items.length) % items.length];
            next && next.focus();
        },
    },
};
</script>

<style scoped>
.mh-shell {
    position: relative;
    z-index: 50;
    --nav-bg: #ffffff;
    --nav-border: #e5e7eb;
    --nav-text: #12355a;
    --nav-muted: #6b7280;
    --nav-primary: #0b3c80;
    --nav-sale: #c1121f;
}

/* shared container */
.mh-wrap {
    max-width: 1320px;
    margin: 0 auto;
    padding: 0 16px;
}

/* main + compact headers */
.mh-header {
    left: 0;
    right: 0;
    top: 0;
    background: var(--nav-bg);
    border-bottom: 1px solid var(--nav-border);
}

.mh-header-main,
.mh-header-compact {
    position: fixed;
    z-index: 1001;
    transition: transform 0.25s ease, opacity 0.25s ease, box-shadow 0.2s ease;
}

.mh-header-main {
    box-shadow: 0 1px 0 rgba(15, 23, 42, 0.04);
}

.mh-header-main.is-hidden {
    transform: translateY(-100%);
    opacity: 0;
    pointer-events: none;
}

.mh-header-compact {
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.12);
    transform: translateY(-100%);
    opacity: 0;
}

.mh-header-compact.is-visible {
    transform: translateY(0);
    opacity: 1;
}

/* spacer so content clears fixed header */
.mh-spacer {
    height: 140px;
}
@media (min-width: 1024px) {
    .mh-spacer {
        height: 144px;
    }
}

/* ===== full header: top row ===== */
.mh-top {
    border-bottom: 1px solid var(--nav-border);
}
.mh-top-wrap {
    height: 72px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.mh-brand {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
}
.mh-logo {
    height: 116px;
    width: auto;
    object-fit: contain;
}
.mh-brand.mini .mh-logo.mini {
    height: 90px;
}

/* quick actions */
.mh-quick {
    display: flex;
    gap: 28px;
    list-style: none;
    margin: 0;
    padding: 0;
}
.mh-quick-item {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    color: var(--nav-text);
    text-decoration: none;
}
.mh-quick-item i {
    font-size: 18px;
}
.mh-quick-item .t {
    display: flex;
    flex-direction: column;
    line-height: 1.1;
}
.mh-quick-item .muted {
    font-size: 11px;
    text-transform: uppercase;
    color: var(--nav-muted);
    font-weight: 600;
    letter-spacing: 0.06em;
}
.mh-quick-item strong {
    font-size: 13px;
    text-transform: uppercase;
    color: var(--nav-text);
    font-weight: 700;
}

/* burger */
.mh-burger {
    display: none;
    background: transparent;
    border: none;
    color: var(--nav-text);
    font-size: 22px;
}

/* ===== full header: nav row ===== */
.mh-navrow {
    background: var(--nav-bg);
}
.mh-nav-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 10px;
    padding-bottom: 12px;
    gap: 16px;
}

/* nav menu */
.mh-menu {
    display: flex;
    align-items: center;
    gap: 18px;
    white-space: nowrap;
}

.mitem {
    position: relative;
    display: inline-block;
    padding: 4px 2px 8px;
    color: var(--nav-text);
    font-weight: 700;
    font-size: 13px;
    text-transform: uppercase;
    text-decoration: none;
    letter-spacing: 0.04em;
}
.mitem::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: 1px;
    height: 2px;
    background: var(--nav-primary);
    transform: scaleX(0);
    transform-origin: center;
    transition: transform 0.2s ease;
}
.mitem:hover::after,
.mitem.active::after {
    transform: scaleX(1);
}

/* sale link badge */
.mitem.sale {
    padding-top: 14px;
}
.badge-sale {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--nav-sale);
    color: #fff;
    font-weight: 700;
    font-size: 10px;
    padding: 2px 7px;
    border-radius: 3px;
    text-transform: uppercase;
}
.badge-sale.small {
    top: -10px;
    right: -6px;
    left: auto;
}

/* dropdowns */
.has-dd {
    position: relative;
}
.mtext-btn {
    border: none;
    background: transparent;
    padding: 0;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: var(--nav-text);
    font-weight: 700;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    cursor: pointer;
}
.dd-icon {
    font-size: 9px;
}

.dd {
    position: absolute;
    top: 100%;
    left: 0;
    margin-top: 6px;
    min-width: 220px;
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid var(--nav-border);
    padding: 6px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.16);
    z-index: 2000;
}
.dd li {
    list-style: none;
}
.dd a {
    display: block;
    padding: 8px 10px;
    font-size: 13px;
    color: var(--nav-text);
    text-decoration: none;
    border-radius: 4px;
}
.dd a:hover {
    background: #f3f4f6;
}
.dd .all a {
    font-weight: 700;
}

/* Search */
.mh-search {
    display: flex;
    align-items: center;
    max-width: 380px;
    flex: 1 0 auto;
    margin-left: auto;
    border-radius: 999px;
    border: 1px solid var(--nav-border);
    background: #f9fafb;
}
.mh-search input {
    border: none;
    outline: none;
    background: transparent;
    padding: 0 14px;
    height: 40px;
    font-size: 13px;
    color: var(--nav-text);
}
.mh-search input::placeholder {
    color: var(--nav-muted);
}
.mh-search .btn-search {
    width: 46px;
    height: 40px;
    border: none;
    border-left: 1px solid var(--nav-border);
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}
.mh-search .btn-search i {
    font-size: 14px;
}

/* ===== compact header ===== */
.mh-compact-wrap {
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.mh-menu-compact .mitem {
    padding-bottom: 4px;
    font-size: 13px;
}

.mh-compact-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

.mh-search-compact {
    max-width: 260px;
    height: 36px;
}
.mh-search-compact input {
    height: 36px;
    font-size: 12px;
}
.mh-search-compact .btn-search {
    height: 36px;
}

.mh-compact-actions {
    display: flex;
    align-items: center;
    gap: 6px;
}
.icon-btn {
    width: 34px;
    height: 34px;
    border-radius: 999px;
    border: 1px solid var(--nav-border);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--nav-text);
    text-decoration: none;
}

/* ===== mobile ===== */
@media (max-width: 992px) {
    .mh-top-wrap {
        height: 64px;
        padding: 8px 16px;
    }
    .mh-quick {
        display: none;
    }
    .mh-burger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .mh-nav-wrap {
        padding-bottom: 10px;
    }
    .mh-menu {
        display: none;
    }
    .mh-search {
        max-width: none;
        width: 100%;
    }
    .mh-header-compact .mh-search-compact {
        display: none;
    }
    .mh-header-compact .mh-compact-actions {
        display: none;
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

.mega{
  position:absolute;
  top:100%;
  left:0;
  margin-top:10px;
  height:520px;
  display:flex;
  background:#c4c5cd;
  border:2px solid #f2c94c;
  z-index:2000;

  width:300px;                 /* ✅ ONLY left panel by default */
  transition: width .15s ease; /* optional */
}

.mega.has-right{
  width:720px;                 /* ✅ expand ONLY after hover */
}

.mega-left{
  width:300px;
  overflow:auto;
  padding:10px 0;
  border-right: none;          /* ✅ no divider if right panel hidden */
}


.mega.has-right .mega-left{
  border-right:2px solid #f2c94c; /* ✅ divider only when right exists */
}

.mega-item{ padding:0 10px; }
.mega-link{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:14px 14px;
  color:#fff;
  text-decoration:none;
  font-weight:700;
  text-transform:uppercase;
  border-radius:6px;
}
.mega-item.active .mega-link,
.mega-link:hover{
  background:rgba(255,255,255,0.08);
}

.mega-right{
  flex:1;
  padding:14px;
  overflow:auto;
}
.mega-right-title{
  color:#f2c94c;
  font-weight:800;
  text-transform:uppercase;
  margin-bottom:10px;
}
.mega-sublist{ display:flex; flex-direction:column; }
.mega-sublink{
  padding:12px 10px;
  color:#fff;
  text-decoration:none;
  font-weight:700;
  text-transform:uppercase;
  border-radius:6px;
}
.mega-sublink:hover{
  background:rgba(255,255,255,0.08);
}
.mega-empty{
  color:rgba(255,255,255,0.75);
  padding:10px;
}

/* ✅ UPDATED: Premium mega dropdown (matches navbar + Brands dropdown) */
.mh-dd-btn .dd-icon {
  opacity: 0.85;
  transition: transform 0.18s ease, opacity 0.18s ease;
}
.mh-dd-btn.is-open .dd-icon {
  transform: rotate(180deg);
  opacity: 1;
}

/* Smooth open/close (fade + slight translate) */
.mh-mega-enter-active,
.mh-mega-leave-active {
  transition: opacity 0.14s ease, transform 0.16s ease;
}
.mh-mega-enter-from,
.mh-mega-leave-to {
  opacity: 0;
  transform: translateY(6px);
}
@media (prefers-reduced-motion: reduce) {
  .mh-mega-enter-active,
  .mh-mega-leave-active {
    transition: none;
  }
}

/* Container */
.mh-mega {
  position: absolute;
  top: 100%;
  left: 0;
  margin-top: 10px;

  /* ✅ same background as Brands dropdown */
  background: #ffffff;
  border: 1px solid var(--nav-border);
  border-radius: 12px;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.16);
  overflow: hidden;
  z-index: 2000;

  /* responsive safety */
  max-width: calc(100vw - 32px);
  max-height: calc(100vh - 140px);
  display: flex;

  /* subtle polish */
  backdrop-filter: saturate(140%);
}

/* Width behavior (keeps your existing logic) */
.mh-mega {
  width: min(92vw, 360px);
}
.mh-mega.has-right {
  width: min(92vw, 760px);
}

/* Panels */
.mh-mega .mega-left {
  width: 320px;
  padding: 8px;
  overflow: auto;
  max-height: min(70vh, 520px);
}
.mh-mega:not(.has-right) .mega-left {
  width: 100%;
}

.mh-mega.has-right .mega-left {
  border-right: 1px solid var(--nav-border);
}

.mh-mega .mega-right {
  flex: 1;
  min-width: 240px;
  padding: 10px;
  overflow: auto;
  max-height: min(70vh, 520px);
}

/* Typography matches navbar (same size/weight/uppercase/spacing) */
.mh-mega .mega-link,
.mh-mega .mega-sublink {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;

  padding: 10px 12px;
  border-radius: 10px;

  color: var(--nav-text);
  font-weight: 700;
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  text-decoration: none;

  transition: background-color 0.15s ease, box-shadow 0.15s ease,
    transform 0.15s ease, color 0.15s ease;
}

/* ✅ same hover background as Brands dropdown */
.mh-mega .mega-link:hover,
.mh-mega .mega-sublink:hover {
  background: #f3f4f6;
}

.mh-mega .mega-link i,
.mh-mega .mega-sublink i {
  font-size: 10px;
  opacity: 0.75;
  transition: opacity 0.15s ease, transform 0.15s ease;
}

/* Active category state (clear but premium) */
.mh-mega .mega-item.active .mega-link {
  background: rgba(11, 60, 128, 0.08);
  box-shadow: inset 0 0 0 1px rgba(11, 60, 128, 0.18);
}
.mh-mega .mega-item.active .mega-link i {
  opacity: 1;
}

/* Focus-visible (strong + navbar-themed) */
.mh-mega .mega-link:focus-visible,
.mh-mega .mega-sublink:focus-visible,
.mh-dd-btn:focus-visible {
  outline: 2px solid rgba(11, 60, 128, 0.55);
  outline-offset: 2px;
}

/* Long names: ellipsis desktop, wrap on smaller screens */
.mh-mega .mh-dd-text {
  flex: 1 1 auto;
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Subcategory header */
.mh-mega .mega-right-title {
  padding: 6px 8px;
  margin: 2px 0 6px;
  color: var(--nav-primary);
  font-weight: 800;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

/* Empty state */
.mh-mega .mega-empty {
  padding: 10px 8px;
  color: var(--nav-muted);
  font-size: 13px;
}

/* Tablet + small laptop: balanced sizing */
@media (max-width: 1200px) {
  .mh-mega.has-right {
    width: min(94vw, 720px);
  }
  .mh-mega .mega-left {
    width: 300px;
  }
}

/* Mobile-safe layout (even if your main menu is hidden on <=992) */
@media (max-width: 992px) {
  .mh-mega {
    width: calc(100vw - 24px) !important;
    left: 12px;
    right: 12px;
    margin-top: 8px;
  }

  .mh-mega.has-right {
    flex-direction: column;
  }

  .mh-mega .mega-left {
    width: 100%;
    max-height: 38vh;
    border-right: none !important;
    border-bottom: 1px solid var(--nav-border);
  }

  .mh-mega .mega-right {
    max-height: 38vh;
  }

  .mh-mega .mh-dd-text {
    white-space: normal;
    overflow: visible;
    text-overflow: clip;
    word-break: break-word;
  }

  /* larger tap targets */
  .mh-mega .mega-link,
  .mh-mega .mega-sublink {
    padding: 12px 12px;
    border-radius: 12px;
  }
}
/* ✅ ONLY UPDATED: light separators under each Category item (premium, subtle) */
.mh-mega .mega-item {
  padding: 0; /* (we moved spacing into link for clean separators) */
  position: relative;
}
.mh-mega .mega-item:not(:last-child)::after {
  content: "";
  position: absolute;
  left: 12px;
  right: 12px;
  bottom: 0;
  height: 1px;
  background: rgba(17, 24, 39, 0.08); /* subtle line like premium menus */
}

/* keep comfortable spacing */
.mh-mega .mega-link {
  margin: 0;
}

/* (optional) subtle separator for subcategories too */
.mh-mega .mega-sublist .mega-sublink:not(:last-child) {
  border-bottom: 1px solid rgba(17, 24, 39, 0.06);
}


</style>
