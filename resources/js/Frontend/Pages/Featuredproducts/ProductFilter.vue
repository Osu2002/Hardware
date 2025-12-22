<template>
  <aside class="filters-shell" ref="shell">
    <div class="filters" :class="{ 'is-desktop-sticky': !isMobile }">
      <!-- Header -->
      <div class="filters-header">
        <h3 class="filters-title">Filters</h3>

        <div class="filters-actions">
          <!-- Match image: always visible; disabled if nothing selected -->
          <button type="button" class="btn-clear" :disabled="!hasAny" @click="clearAll">
            Clear
          </button>

          <!-- Mobile main collapse toggle (kept from original) -->
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

      <!-- Body -->
      <div id="filtersBody" class="filters-body" :class="{ 'is-open': !isMobile || isOpen }">
        <div class="filters-body-inner">
          <!-- Subcategory -->
          <div class="filter-block" v-if="subcategories?.length">
            <button
              type="button"
              class="filter-head"
              @click="toggleInner('sub')"
              :aria-expanded="subOpen ? 'true' : 'false'"
              aria-controls="subPanel"
            >
              <span class="filter-label">Subcategory</span>
              <svg
                class="inner-toggle-icon"
                viewBox="0 0 24 24"
                width="16"
                height="16"
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

            <div id="subPanel" class="filter-content" :class="{ 'is-open': subOpen }">
              <div class="filter-content-inner">
                <label class="opt">
                  <input type="radio" name="sub" :value="null" v-model="subLocal" />
                  <span class="radio-custom"></span>
                  <span class="opt-label">All</span>
                </label>

                <label class="opt" v-for="s in subcategories" :key="s.id">
                  <input type="radio" name="sub" :value="s.id" v-model="subLocal" />
                  <span class="radio-custom"></span>
                  <span class="opt-label">{{ s.title }}</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Brand -->
          <div class="filter-block" v-if="brands?.length">
            <button
              type="button"
              class="filter-head"
              @click="toggleInner('brand')"
              :aria-expanded="brandOpen ? 'true' : 'false'"
              aria-controls="brandPanel"
            >
              <span class="filter-label">Brand</span>
              <svg
                class="inner-toggle-icon"
                viewBox="0 0 24 24"
                width="16"
                height="16"
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

            <div id="brandPanel" class="filter-content" :class="{ 'is-open': brandOpen }">
              <div class="filter-content-inner">
                <label class="opt" v-for="b in brands" :key="b.id">
                  <input type="checkbox" :value="b.id" v-model="brandLocal" />
                  <span class="checkbox-custom"></span>
                  <span class="opt-label">{{ b.title }}</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Sort -->
          <div class="filter-block">
            <button
              type="button"
              class="filter-head"
              @click="toggleInner('sort')"
              :aria-expanded="sortOpen ? 'true' : 'false'"
              aria-controls="sortPanel"
            >
              <span class="filter-label">Sort</span>

              <span class="filter-head-right">
                <span class="filter-value" :title="sortLabel">{{ sortLabel }}</span>
                <svg
                  class="inner-toggle-icon"
                  viewBox="0 0 24 24"
                  width="16"
                  height="16"
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

            <div id="sortPanel" class="filter-content" :class="{ 'is-open': sortOpen }">
              <div class="filter-content-inner">
                <label class="opt">
                  <input
                    type="radio"
                    name="sort"
                    value="latest"
                    v-model="sortLocal"
                    @change="onSortPick"
                  />
                  <span class="radio-custom"></span>
                  <span class="opt-label">Latest</span>
                </label>

                <label class="opt">
                  <input
                    type="radio"
                    name="sort"
                    value="price_asc"
                    v-model="sortLocal"
                    @change="onSortPick"
                  />
                  <span class="radio-custom"></span>
                  <span class="opt-label">Price: Low to High</span>
                </label>

                <label class="opt">
                  <input
                    type="radio"
                    name="sort"
                    value="price_desc"
                    v-model="sortLocal"
                    @change="onSortPick"
                  />
                  <span class="radio-custom"></span>
                  <span class="opt-label">Price: High to Low</span>
                </label>
              </div>
            </div>
          </div>
          <!-- /Sort -->
        </div>
      </div>
      <!-- /Body -->
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

      subOpen: false,
      brandOpen: false,
      sortOpen: false,

      // main (mobile) collapse
      isMobile: false,
      isOpen: true,
      perPage: 16,

      _mql: null,
      _mqlHandler: null,

      // desktop sticky helpers
      _productsEl: null,
      _ro: null,
      _onWinResize: null,
      _onInertiaFinish: null,

      _syncingFromProps: false,
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

        // main filter: default CLOSED on mobile, OPEN on desktop
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
    // Vue2 compatibility
    this.teardownStickyHelpers();

    if (this._mql && this._mqlHandler) {
      if (this._mql.removeEventListener) this._mql.removeEventListener("change", this._mqlHandler);
      else if (this._mql.removeListener) this._mql.removeListener(this._mqlHandler);
    }
  },

  methods: {
    toggleOpen() {
      this.isOpen = !this.isOpen;
      this.$nextTick(() => this.syncShellHeight());
    },

    toggleInner(which) {
      if (which === "sub") this.subOpen = !this.subOpen;
      if (which === "brand") this.brandOpen = !this.brandOpen;
      if (which === "sort") this.sortOpen = !this.sortOpen;
      this.$nextTick(() => this.syncShellHeight());
    },

    onSortPick() {
      // keep: close only sort accordion
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

      // NOTE: kept consistent with original file => DO NOT auto-close on mobile
    },

    applyPerPageOnly() {
      const params = this.buildParams();

      this.$inertia.get(route("featuredproducts.index"), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      });

      // NOTE: kept consistent with original file => DO NOT auto-close on mobile
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

      // NOTE: kept consistent with original file => DO NOT auto-close on mobile
    },
  },
};
</script>

<style scoped>
/* ========== SHELL ========== */
.filters-shell {
  position: relative;
  align-self: start;
  max-width: 100%;
}

/* ========== MAIN CARD (variables must live on a real element; :root breaks under scoped CSS) ========== */
.filters {
  --filter-accent: #071c61;
  --filter-bg: #ffffff;
  --filter-border: #e2e8f0;
  --filter-border-light: #f1f5f9;
  --filter-text: #1e293b;
  --filter-text-muted: #64748b;
  --filter-text-light: #94a3b8;
  --filter-hover: #f8fafc;
  --filter-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 4px 12px rgba(0, 0, 0, 0.04);
  --filter-shadow-hover: 0 2px 8px rgba(0, 0, 0, 0.08);
  --filter-radius: 14px;
  --filter-radius-sm: 8px;
  --filter-transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);

  background: var(--filter-bg);
  border: 1px solid var(--filter-border);
  border-radius: var(--filter-radius);
  padding: 16px;
  z-index: 20;
  box-shadow: var(--filter-shadow);
  transition: box-shadow var(--filter-transition);
  max-width: 100%;
}

.filters:hover {
  box-shadow: var(--filter-shadow-hover);
}

.filters.is-desktop-sticky {
  position: sticky;
  top: 16px;
}

/* ========== HEADER ========== */
.filters-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding-bottom: 12px;
  border-bottom: 1px solid var(--filter-border-light);
  margin-bottom: 4px;
}

.filters-title {
  margin: 0;
  font-size: 1rem;
  font-weight: 600;
  color: var(--filter-text);
  letter-spacing: 0.04em;
}

.filters-actions {
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

/* Clear: always visible; disabled when empty */
.btn-clear {
  border: none;
  background: transparent;
  color: #dc2626;
  font-size: 0.8125rem;
  font-weight: 500;
  cursor: pointer;
  padding: 6px 10px;
  border-radius: var(--filter-radius-sm);
  transition: all var(--filter-transition);
}

.btn-clear:hover:not(:disabled) {
  background: #fef2f2;
  color: #b91c1c;
}

.btn-clear:disabled {
  opacity: 0.5;
  cursor: default;
}

.btn-clear:focus-visible {
  outline: 2px solid var(--filter-accent);
  outline-offset: 2px;
}

/* Mobile toggle */
.btn-toggle {
  border: 1px solid var(--filter-border);
  background: var(--filter-bg);
  border-radius: var(--filter-radius-sm);
  width: 36px;
  height: 36px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all var(--filter-transition);
}

.btn-toggle:hover {
  background: var(--filter-hover);
  border-color: var(--filter-text-light);
}

.btn-toggle:focus-visible {
  outline: 2px solid var(--filter-accent);
  outline-offset: 2px;
}

.toggle-icon {
  transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  color: var(--filter-text);
}

/* ========== BODY (MAIN ACCORDION) ========== */
.filters-body {
  display: grid;
  grid-template-rows: 0fr;
  opacity: 0;
  transition: grid-template-rows 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease;
}

.filters-body.is-open {
  grid-template-rows: 1fr;
  opacity: 1;
}

.filters-body-inner {
  overflow: hidden;
}

/* ========== FILTER BLOCK ========== */
.filter-block {
  padding: 12px 0;
  border-top: 1px solid var(--filter-border-light);
}

.filter-block:first-child {
  border-top: none;
  padding-top: 8px;
}

/* ========== FILTER HEAD ========== */
.filter-head {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  background: transparent;
  border: none;
  padding: 6px 8px;
  margin: -6px -8px;
  border-radius: var(--filter-radius-sm);
  cursor: pointer;
  text-align: left;
  transition: background var(--filter-transition);
}

.filter-head:hover {
  background: var(--filter-hover);
}

.filter-head:focus-visible {
  outline: 2px solid var(--filter-accent);
  outline-offset: 2px;
}

.filter-label {
  font-weight: 500;
  color: var(--filter-text);
  font-size: 1rem;
  letter-spacing: 0.04rem;
}

.filter-head-right {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  min-width: 0;
}
/* Put this near the top, after .filters { ... } starts, or add as a separate rule */

.filters {
  font-family: "Abadi MT Condensed Light", "Abadi MT Condensed", "Abadi MT", Abadi,
    system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
}

/* Optional: ensure every inner element inherits it */
.filters * {
  font-family: inherit;
}


.filter-value {
  font-size: 0.8125rem;
  font-weight: 500;
  color: var(--filter-text-muted);
  max-width: 140px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.inner-toggle-icon {
  transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  color: var(--filter-text-light);
  flex-shrink: 0;
}

/* ========== FILTER CONTENT (INNER ACCORDION) ========== */
.filter-content {
  display: grid;
  grid-template-rows: 0fr;
  opacity: 0;
  transition: grid-template-rows 0.25s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.2s ease;
}

.filter-content.is-open {
  grid-template-rows: 1fr;
  opacity: 1;
}

.filter-content-inner {
  overflow: hidden;
  padding-top: 8px;
}

/* ========== OPTION ROWS ========== */
.opt {
  position: relative; /* important: keeps hidden input from floating to page corner */
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.875rem;
  color: var(--filter-text);
  padding: 8px 10px;
  margin: 2px -10px;
  border-radius: var(--filter-radius-sm);
  cursor: pointer;
  user-select: none;
  transition: background var(--filter-transition);
}

.opt:hover {
  background: var(--filter-hover);
}

.opt-label {
  flex: 1;
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  font-weight: 400;
  color: var(--filter-text);
}

/* ========== HIDE NATIVE INPUTS SAFELY ========== */
.opt input[type="radio"],
.opt input[type="checkbox"] {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}

/* ========== CUSTOM RADIO ========== */
.radio-custom {
  width: 18px;
  height: 18px;
  border: 2px solid var(--filter-border);
  border-radius: 50%;
  background: var(--filter-bg);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all var(--filter-transition);
}

.radio-custom::after {
  content: "";
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: transparent;
  transform: scale(0);
  transition: background var(--filter-transition), transform var(--filter-transition);
}

.opt input[type="radio"]:checked + .radio-custom {
  border-color: var(--filter-accent);
}

.opt input[type="radio"]:checked + .radio-custom::after {
  background: var(--filter-accent);
  transform: scale(1);
}

/* ========== CUSTOM CHECKBOX ========== */
.checkbox-custom {
  width: 18px;
  height: 18px;
  border: 2px solid var(--filter-border);
  border-radius: 4px;
  background: var(--filter-bg);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all var(--filter-transition);
}

.checkbox-custom::after {
  content: "";
  width: 10px;
  height: 10px;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 12 12' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10 3L4.5 8.5L2 6' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
  opacity: 0;
  transform: scale(0.5);
  transition: opacity var(--filter-transition), transform var(--filter-transition);
}

.opt input[type="checkbox"]:checked + .checkbox-custom {
  border-color: var(--filter-accent);
  background: var(--filter-accent);
}

.opt input[type="checkbox"]:checked + .checkbox-custom::after {
  opacity: 1;
  transform: scale(1);
}

/* ========== RESPONSIVE ========== */
@media (max-width: 768px) {
  .filters {
    padding: 14px;
  }

  .filter-block {
    padding: 10px 0;
  }

  .filter-value {
    max-width: 100px;
  }

  .opt {
    padding: 10px 8px;
    margin: 2px -8px;
  }
}

@media (max-width: 400px) {
  .filters {
    padding: 12px;
    border-radius: 12px;
  }

  .filters-header {
    padding-bottom: 10px;
  }

  .filters-title {
    font-size: 0.9375rem;
  }

  .filter-value {
    max-width: 80px;
    font-size: 0.75rem;
  }
}
</style>
