<template>
  <!-- ✅ shell becomes the sticky boundary (same height as product list) -->
  <aside class="filters-shell" ref="shell">
    <div class="filters" :class="{ 'is-desktop-sticky': !isMobile }">
      <!-- Header -->
      <div class="filters-header">
        <h3 class="filters-title">Filters</h3>

        <!-- RIGHT side actions -->
        <div class="filters-actions">
          <button v-if="hasAny" type="button" class="btn-clear" @click="clearAll">
            Clear
          </button>

          <!-- Mobile dropdown toggle (RIGHT CORNER) -->
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

      <!-- Body (collapsible on mobile) -->
      <div id="filtersBody" class="filters-body" v-show="!isMobile || isOpen">
        <!-- Subcategory -->
        <div class="filter-block" v-if="subcategories?.length">
          <div class="filter-label">Subcategory</div>

          <label class="opt">
            <input type="radio" name="sub" :value="null" v-model="subLocal" />
            <span>All</span>
          </label>

          <label class="opt" v-for="s in subcategories" :key="s.id">
            <input type="radio" name="sub" :value="s.id" v-model="subLocal" />
            <span>{{ s.title }}</span>
          </label>
        </div>

        <!-- Brand -->
        <div class="filter-block" v-if="brands?.length">
          <div class="filter-label">Brand</div>

          <label class="opt" v-for="b in brands" :key="b.id">
            <input type="checkbox" :value="b.id" v-model="brandLocal" />
            <span>{{ b.title }}</span>
          </label>
        </div>

        <!-- Sort -->
        <div class="filter-block">
          <div class="filter-label">Sort</div>
          <select class="sort-select" v-model="sortLocal" @change="apply">
            <option value="latest">Latest</option>
            <option value="price_asc">Price: Low to High</option>
            <option value="price_desc">Price: High to Low</option>
          </select>
        </div>
      </div>
    </div>
  </aside>
</template>

<script>
export default {
  name: "CategoryFilters",
  props: {
    category: { type: Object, required: true },
    subcategories: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
  },

  data() {
    return {
      subLocal: this.filters?.sub ?? null,
      brandLocal: Array.isArray(this.filters?.brands) ? [...this.filters.brands] : [],
      sortLocal: this.filters?.sort ?? "latest",

      // responsive dropdown + per page
      // ✅ "mobile/tablet" includes iPad Pro and below => sticky OFF
      isMobile: false,
      isOpen: true,
      perPage: 16,

      _mql: null,
      _mqlHandler: null,

      // ✅ sticky-until-end helpers (desktop only)
      _productsEl: null,
      _ro: null,
      _onWinResize: null,
      _onInertiaFinish: null,
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
  },

  watch: {
    filters: {
      deep: true,
      handler(f) {
        this.subLocal = f?.sub ?? null;
        this.brandLocal = Array.isArray(f?.brands) ? [...f.brands] : [];
        this.sortLocal = f?.sort ?? "latest";

        // keep sticky boundary correct after props update (desktop only)
        this.$nextTick(() => this.syncShellHeight());
      },
    },

    subLocal() {
      this.apply();
    },
    brandLocal: {
      deep: true,
      handler() {
        this.apply();
      },
    },
  },

  mounted() {
    if (typeof window !== "undefined" && window.matchMedia) {
      // ✅ sticky ONLY for large screens.
      // iPad Pro portrait width is 1024px, so we treat <= 1024 as "not desktop sticky".
      this._mql = window.matchMedia("(max-width: 1024px)");

      const applyMode = (matches) => {
        // matches => <=1024 (tablet/mobile)
        this.isMobile = !!matches;

        // per-page rule
        this.perPage = this.isMobile ? 8 : 16;

        // dropdown behavior
        this.isOpen = !this.isMobile;

        // ✅ enable/disable sticky helpers depending on mode
        if (this.isMobile) {
          this.teardownStickyHelpers();
          this.resetShellHeight();
        } else {
          // (re)bind sticky helpers for desktop
          this.$nextTick(() => {
            this.bindStickyToProducts();
            this.syncShellHeight();
          });
        }
      };

      applyMode(this._mql.matches);

      this._mqlHandler = (e) => {
        const wasMobile = this.isMobile;
        applyMode(e.matches);

        // If mode changed, reload with correct per_page
        if (wasMobile !== this.isMobile) {
          this.applyPerPageOnly();
        }
      };

      if (this._mql.addEventListener) this._mql.addEventListener("change", this._mqlHandler);
      else if (this._mql.addListener) this._mql.addListener(this._mqlHandler);
    } else {
      // fallback: assume desktop
      this.isMobile = false;
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

  // Vue 2 fallback
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

    // =========================
    // ✅ Sticky boundary helpers (DESKTOP ONLY)
    // =========================
    getProductsEl() {
      if (typeof document === "undefined") return null;
      return document.querySelector(".category-products");
    },

    bindStickyToProducts() {
      // avoid double-binding
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
            try {
              this._ro.unobserve(this._productsEl);
            } catch (_) {}
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
        try {
          this._ro.unobserve(this._productsEl);
        } catch (_) {}
      }
      if (this._ro) {
        try {
          this._ro.disconnect();
        } catch (_) {}
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

    // =========================
    // Filters logic
    // =========================
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

      this.$inertia.get(route("category.list", this.category.id), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      });

      if (this.isMobile) this.isOpen = false;
    },

    applyPerPageOnly() {
      const params = this.buildParams();

      this.$inertia.get(route("category.list", this.category.id), params, {
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

      this.$inertia.get(
        route("category.list", this.category.id),
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
/* ✅ shell defines the sticky boundary (desktop only, but safe always) */
.filters-shell {
  position: relative;
  align-self: start;
}

/* base card */
.filters {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 14px;
  z-index: 20;
}

/* ✅ sticky ONLY on desktop/large screens (when !isMobile) */
.filters.is-desktop-sticky {
  position: sticky;
  top: 12px;
}

/* header */
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

.filter-block {
  padding: 12px 0;
  border-top: 1px solid #f3f4f6;
}

.filter-label {
  font-weight: 800;
  color: #111827;
  font-size: 0.9rem;
  margin-bottom: 8px;
}

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

.sort-select {
  width: 100%;
  height: 38px;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  padding: 0 10px;
  outline: none;
}

/* mobile polish */
@media (max-width: 768px) {
  .filters {
    padding: 12px;
  }
  .filter-block {
    padding: 10px 0;
  }
}
</style>
