<template>
  <aside class="filters-shell" ref="shell">
    <div class="filters" :class="{ 'is-desktop-sticky': !isMobile }">
      <div class="filters-header">
        <h3 class="filters-title">Filters</h3>

        <div class="filters-actions">
          <button v-if="hasAny" type="button" class="btn-clear" @click="clearAll">
            Clear
          </button>

          <button
            v-if="isMobile"
            type="button"
            class="btn-toggle"
            :aria-expanded="isOpen ? 'true' : 'false'"
            aria-controls="filtersBody"
            @click="toggleOpen"
            title="Toggle filters"
          >
            <svg
              class="toggle-icon"
              viewBox="0 0 24 24"
              width="18"
              height="18"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              :style="{ transform: isOpen ? 'rotate(180deg)' : 'rotate(0deg)' }"
            >
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </button>
        </div>
      </div>

      <div id="filtersBody" class="filters-body" v-show="!isMobile || isOpen">
        <!-- Subcategory (inner dropdown) -->
        <div class="filter-block" v-if="subcategories?.length">
          <button
            type="button"
            class="filter-head"
            @click="toggleInner('sub')"
            :aria-expanded="subOpen ? 'true' : 'false'"
          >
            <span class="filter-label">Subcategory</span>
            <svg
              class="inner-toggle-icon"
              viewBox="0 0 24 24"
              width="18"
              height="18"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              :style="{ transform: subOpen ? 'rotate(180deg)' : 'rotate(0deg)' }"
              aria-hidden="true"
            >
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </button>

          <div class="filter-content" v-show="subOpen">
            <label class="opt">
              <input type="radio" name="sub" :value="null" v-model="subLocal" />
              <span>All</span>
            </label>

            <label class="opt" v-for="s in subcategories" :key="s.id">
              <input type="radio" name="sub" :value="s.id" v-model="subLocal" />
              <span>{{ s.title }}</span>
            </label>
          </div>
        </div>

        <!-- Brand (inner dropdown) -->
        <div class="filter-block" v-if="brands?.length">
          <button
            type="button"
            class="filter-head"
            @click="toggleInner('brand')"
            :aria-expanded="brandOpen ? 'true' : 'false'"
          >
            <span class="filter-label">Brand</span>
            <svg
              class="inner-toggle-icon"
              viewBox="0 0 24 24"
              width="18"
              height="18"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              :style="{ transform: brandOpen ? 'rotate(180deg)' : 'rotate(0deg)' }"
              aria-hidden="true"
            >
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </button>

          <div class="filter-content" v-show="brandOpen">
            <label class="opt" v-for="b in brands" :key="b.id">
              <input type="checkbox" :value="b.id" v-model="brandLocal" />
              <span>{{ b.title }}</span>
            </label>
          </div>
        </div>

        <!-- ✅ Sort (NOW matches other dropdowns, no native select popup) -->
        <div class="filter-block">
          <button
            type="button"
            class="filter-head"
            @click="toggleInner('sort')"
            :aria-expanded="sortOpen ? 'true' : 'false'"
          >
            <span class="filter-label">Sort</span>

            <span class="filter-head-right">
              <span class="filter-value" :title="sortLabel">{{ sortLabel }}</span>
              <svg
                class="inner-toggle-icon"
                viewBox="0 0 24 24"
                width="18"
                height="18"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                :style="{ transform: sortOpen ? 'rotate(180deg)' : 'rotate(0deg)' }"
                aria-hidden="true"
              >
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>

          <div class="filter-content" v-show="sortOpen">
            <label class="opt opt-tight">
              <input
                type="radio"
                name="sort"
                value="latest"
                v-model="sortLocal"
                @change="onSortPick"
              />
              <span>Latest</span>
            </label>

            <label class="opt opt-tight">
              <input
                type="radio"
                name="sort"
                value="price_asc"
                v-model="sortLocal"
                @change="onSortPick"
              />
              <span>Price: Low to High</span>
            </label>

            <label class="opt opt-tight">
              <input
                type="radio"
                name="sort"
                value="price_desc"
                v-model="sortLocal"
                @change="onSortPick"
              />
              <span>Price: High to Low</span>
            </label>
          </div>
        </div>
      </div>
    </div>
  </aside>
</template>

<script>
export default {
  name: "ProductFilter",
  props: {
    subcategories: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
  },

  data() {
    return {
      subLocal: this.filters?.sub ?? null,
      brandLocal: Array.isArray(this.filters?.brands) ? [...this.filters.brands] : [],
      sortLocal: this.filters?.sort ?? "latest",

      // inner dropdowns (all views)
      subOpen: false,
      brandOpen: false,
      sortOpen: false,

      // ✅ <= 1024 treated as mobile dropdown for main filter
      isMobile: false,
      isOpen: true,
      perPage: 16,

      _mql: null,
      _mqlHandler: null,

      // desktop-only sticky helpers
      _productsEl: null,
      _ro: null,
      _onWinResize: null,
      _onInertiaFinish: null,

      // prevents apply() while syncing props -> local
      _syncingFromProps: false,

      // prevents per_page auto-fix from running multiple times
      _perPageInitDone: false,
    };
  },

  computed: {
    hasAny() {
      return !!(
        this.subLocal ||
        (this.brandLocal && this.brandLocal.length) ||
        (this.sortLocal && this.sortLocal !== "latest")
      );
    },

    sortLabel() {
      if (this.sortLocal === "price_asc") return "Price: Low to High";
      if (this.sortLocal === "price_desc") return "Price: High to Low";
      return "Latest";
    },
  },

  watch: {
    filters: {
      deep: true,
      handler(f) {
        this._syncingFromProps = true;

        this.subLocal = f?.sub ?? null;
        this.brandLocal = Array.isArray(f?.brands) ? [...f.brands] : [];
        this.sortLocal = f?.sort ?? "latest";

        this.$nextTick(() => {
          this._syncingFromProps = false;
          this.syncShellHeight();
        });
      },
    },

    subLocal() {
      if (this._syncingFromProps) return;
      this.apply();
    },

    brandLocal: {
      deep: true,
      handler() {
        if (this._syncingFromProps) return;
        this.apply();
      },
    },

    sortLocal() {
      if (this._syncingFromProps) return;
      this.apply();
    },
  },

  mounted() {
    if (typeof window !== "undefined" && window.matchMedia) {
      this._mql = window.matchMedia("(max-width: 1024px)");

      const applyMode = (matches, force = false) => {
        const nextMobile = !!matches;
        const prevMobile = this.isMobile;

        this.isMobile = nextMobile;
        this.perPage = this.isMobile ? 8 : 16;

        // ✅ main filter: default CLOSED on mobile, OPEN on desktop
        if (force || prevMobile !== nextMobile) {
          this.isOpen = this.isMobile ? false : true;
        }

        if (this.isMobile) {
          this.teardownStickyHelpers();
          this.resetShellHeight();
        } else {
          this.$nextTick(() => {
            this.bindStickyToProducts();
            this.syncShellHeight();
          });
        }
      };

      applyMode(this._mql.matches, true);

      this.$nextTick(() => this.ensurePerPageOnce());

      this._mqlHandler = (e) => {
        const wasMobile = this.isMobile;
        applyMode(e.matches, false);

        if (wasMobile !== this.isMobile) {
          this.applyPerPageOnly();
        }
      };

      if (this._mql.addEventListener) this._mql.addEventListener("change", this._mqlHandler);
      else if (this._mql.addListener) this._mql.addListener(this._mqlHandler);
    } else {
      this.isMobile = false;
      this.isOpen = true;
      this.$nextTick(() => {
        this.bindStickyToProducts();
        this.syncShellHeight();
      });
    }
  },

  beforeUnmount() {
    this.teardownStickyHelpers();

    if (this._mql && this._mqlHandler) {
      if (this._mql.removeEventListener) this._mql.removeEventListener("change", this._mqlHandler);
      else if (this._mql.removeListener) this._mql.removeListener(this._mqlHandler);
    }
  },

  beforeDestroy() {
    this.teardownStickyHelpers();

    if (this._mql && this._mqlHandler) {
      if (this._mql.removeEventListener) this._mql.removeEventListener("change", this._mqlHandler);
      else if (this._mql.removeListener) this._mql.removeListener(this._mqlHandler);
    }
  },

  methods: {
    toggleOpen() {
      this.isOpen = !this.isOpen;
    },

    toggleInner(which) {
      if (which === "sub") this.subOpen = !this.subOpen;
      if (which === "brand") this.brandOpen = !this.brandOpen;
      if (which === "sort") this.sortOpen = !this.sortOpen;
      this.$nextTick(() => this.syncShellHeight());
    },

    onSortPick() {
      // close just the Sort dropdown after choosing
      this.sortOpen = false;
      this.$nextTick(() => this.syncShellHeight());
    },

    ensurePerPageOnce() {
      if (this._perPageInitDone) return;
      this._perPageInitDone = true;

      if (typeof window === "undefined") return;
      const desired = this.isMobile ? 8 : 16;

      const url = new URL(window.location.href);
      const current = Number(url.searchParams.get("per_page") || 0);

      if (!current || current !== desired) {
        this.applyPerPageOnly();
      }
    },

    // ===== desktop sticky boundary helpers =====
    getProductsEl() {
      if (typeof document === "undefined") return null;
      return document.querySelector(".category-products");
    },

    bindStickyToProducts() {
      if (this.isMobile) return;
      if (this._onInertiaFinish || this._onWinResize || this._ro) return;

      const el = this.getProductsEl();
      this._productsEl = el;

      if (el && typeof window !== "undefined" && "ResizeObserver" in window) {
        this._ro = new ResizeObserver(() => this.syncShellHeight());
        this._ro.observe(el);
      }

      this._onWinResize = () => this.syncShellHeight();
      window.addEventListener("resize", this._onWinResize, { passive: true });

      this._onInertiaFinish = () => {
        if (this.isMobile) return;

        const nextEl = this.getProductsEl();
        if (nextEl && nextEl !== this._productsEl) {
          if (this._ro && this._productsEl) {
            try { this._ro.unobserve(this._productsEl); } catch (_) {}
          }
          this._productsEl = nextEl;
          if (this._ro && this._productsEl) this._ro.observe(this._productsEl);
        }
        this.$nextTick(() => this.syncShellHeight());
      };

      window.addEventListener("inertia:finish", this._onInertiaFinish);
    },

    syncShellHeight() {
      if (this.isMobile) return;

      const shell = this.$refs?.shell;
      if (!shell || typeof window === "undefined") return;

      const products = this._productsEl || this.getProductsEl();
      if (!products) return;

      const h = products.offsetHeight || products.getBoundingClientRect().height || 0;
      if (h > 0) shell.style.minHeight = `${Math.ceil(h)}px`;
    },

    resetShellHeight() {
      const shell = this.$refs?.shell;
      if (shell) shell.style.minHeight = "";
    },

    teardownStickyHelpers() {
      if (this._ro && this._productsEl) {
        try { this._ro.unobserve(this._productsEl); } catch (_) {}
      }
      if (this._ro) {
        try { this._ro.disconnect(); } catch (_) {}
      }
      this._ro = null;

      if (typeof window !== "undefined") {
        if (this._onWinResize) window.removeEventListener("resize", this._onWinResize);
        if (this._onInertiaFinish) window.removeEventListener("inertia:finish", this._onInertiaFinish);
      }

      this._onWinResize = null;
      this._onInertiaFinish = null;
      this._productsEl = null;
    },

    // ===== filters logic =====
    buildParams() {
      const params = {};

      if (this.subLocal) params.sub = this.subLocal;
      if (this.brandLocal?.length) params.brands = this.brandLocal;
      if (this.sortLocal && this.sortLocal !== "latest") params.sort = this.sortLocal;

      params.per_page = this.perPage;
      return params;
    },

    apply() {
      const params = this.buildParams();

      this.$inertia.get(route("featuredproducts.index"), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      });

      if (this.isMobile) this.isOpen = false;
    },

    applyPerPageOnly() {
      const params = this.buildParams();

      this.$inertia.get(route("featuredproducts.index"), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      });

      if (this.isMobile) this.isOpen = false;
    },

    clearAll() {
      this.subLocal = null;
      this.brandLocal = [];
      this.sortLocal = "latest";

      this.subOpen = false;
      this.brandOpen = false;
      this.sortOpen = false;

      this.$inertia.get(
        route("featuredproducts.index"),
        { per_page: this.perPage },
        {
          preserveState: true,
          preserveScroll: true,
          replace: true,
        }
      );

      if (this.isMobile) this.isOpen = false;
    },
  },
};
</script>

<style scoped>
.filters-shell {
  position: relative;
  align-self: start;
}

.filters {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 14px;
  z-index: 20;
}

/* ✅ sticky ONLY on large desktop */
.filters.is-desktop-sticky {
  position: sticky;
  top: 12px;
}

.filters-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 10px;
}

.filters-title {
  margin: 0;
  font-size: 1rem;
  font-weight: 800;
  color: #111827;
}

.filters-actions {
  display: inline-flex;
  align-items: center;
  gap: 10px;
}

.btn-clear {
  border: 0;
  background: transparent;
  color: #ef4444;
  font-weight: 700;
  cursor: pointer;
  white-space: nowrap;
}

.btn-toggle {
  border: 1px solid #e5e7eb;
  background: #ffffff;
  border-radius: 10px;
  width: 34px;
  height: 34px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.btn-toggle:hover {
  background: #f9fafb;
}

.toggle-icon {
  transition: transform 0.18s ease;
  color: #111827;
}

.filters-body {
  max-width: 100%;
}

.filter-block {
  padding: 12px 0;
  border-top: 1px solid #f3f4f6;
}

/* inner dropdown head */
.filter-head {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  background: transparent;
  border: 0;
  padding: 0;
  margin: 0;
  cursor: pointer;
  text-align: left;
}

.filter-label {
  font-weight: 800;
  color: #111827;
  font-size: 0.9rem;
}

.filter-head-right {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  min-width: 0;
}

.filter-value {
  font-size: 0.85rem;
  font-weight: 700;
  color: #6b7280;
  max-width: 160px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.inner-toggle-icon {
  transition: transform 0.18s ease;
  color: #111827;
  flex: 0 0 auto;
}

.filter-content {
  margin-top: 8px;
  max-width: 100%;
}

/* options */
.opt {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.9rem;
  color: #374151;
  margin: 6px 0;
  cursor: pointer;
  user-select: none;
}

.opt-tight {
  padding: 6px 0;
}

@media (max-width: 768px) {
  .filters { padding: 12px; }
  .filter-block { padding: 10px 0; }
  .filter-value { max-width: 120px; }
}
</style>
