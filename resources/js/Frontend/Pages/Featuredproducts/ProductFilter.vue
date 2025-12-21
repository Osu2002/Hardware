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

      <transition name="pf-collapse">
        <div id="filtersBody" class="filters-body" v-show="!isMobile || isOpen">
          <!-- Subcategory (inner dropdown) -->
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

            <transition name="pf-accordion">
              <div id="subPanel" class="filter-content" v-show="subOpen">
                <label class="opt">
                  <input type="radio" name="sub" :value="null" v-model="subLocal" />
                  <span class="opt-text">All</span>
                </label>

                <label class="opt" v-for="s in subcategories" :key="s.id">
                  <input type="radio" name="sub" :value="s.id" v-model="subLocal" />
                  <span class="opt-text">{{ s.title }}</span>
                </label>
              </div>
            </transition>
          </div>

          <!-- Brand (inner dropdown) -->
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

            <transition name="pf-accordion">
              <div id="brandPanel" class="filter-content" v-show="brandOpen">
                <label class="opt" v-for="b in brands" :key="b.id">
                  <input type="checkbox" :value="b.id" v-model="brandLocal" />
                  <span class="opt-text">{{ b.title }}</span>
                </label>
              </div>
            </transition>
          </div>

          <!-- ✅ Sort (matches other dropdowns, no native select popup) -->
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

            <transition name="pf-accordion">
              <div id="sortPanel" class="filter-content" v-show="sortOpen">
                <label class="opt opt-tight">
                  <input
                    type="radio"
                    name="sort"
                    value="latest"
                    v-model="sortLocal"
                    @change="onSortPick"
                  />
                  <span class="opt-text">Latest</span>
                </label>

                <label class="opt opt-tight">
                  <input
                    type="radio"
                    name="sort"
                    value="price_asc"
                    v-model="sortLocal"
                    @change="onSortPick"
                  />
                  <span class="opt-text">Price: Low to High</span>
                </label>

                <label class="opt opt-tight">
                  <input
                    type="radio"
                    name="sort"
                    value="price_desc"
                    v-model="sortLocal"
                    @change="onSortPick"
                  />
                  <span class="opt-text">Price: High to Low</span>
                </label>
              </div>
            </transition>
          </div>
        </div>
      </transition>
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

      // ✅ DO NOT auto-close on mobile after filtering
    },

    applyPerPageOnly() {
      const params = this.buildParams();

      this.$inertia.get(route("featuredproducts.index"), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      });

      // ✅ DO NOT auto-close on mobile after per_page sync
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

      // ✅ DO NOT auto-close on mobile when clearing
    },
  },
};
</script>

<style scoped>
.filters-shell {
  position: relative;
  align-self: start;
  max-width: 100%;
}

/* premium card */
.filters {
  --accent: #071c61;
  --bg: #ffffff;
  --muted: #64748b;
  --text: #0f172a;
  --border: #e6e8ef;
  --divider: #eef2f7;
  --soft: #f8fafc;

  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 14px;
  z-index: 20;
  box-shadow: 0 14px 32px rgba(15, 23, 42, 0.08);
  max-width: 100%;
}

/* ✅ sticky ONLY on large desktop */
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
  padding-bottom: 10px;
  border-bottom: 1px solid var(--divider);
  margin-bottom: 8px;
}

.filters-title {
  margin: 0;
  font-size: 1.05rem;
  line-height: 1.2;
  font-weight: 700;
  letter-spacing: -0.01em;
  color: var(--text);
}

.filters-actions {
  display: inline-flex;
  align-items: center;
  gap: 10px;
}

/* clear pill */
.btn-clear {
  border: 1px solid rgba(185, 28, 28, 0.28);
  background: rgba(185, 28, 28, 0.08);
  color: #b91c1c;
  font-weight: 600;
  font-size: 0.82rem;
  padding: 6px 10px;
  border-radius: 999px;
  cursor: pointer;
  white-space: nowrap;
  transition: background 160ms ease, border-color 160ms ease, transform 160ms ease;
}

.btn-clear:hover {
  background: rgba(185, 28, 28, 0.12);
  border-color: rgba(185, 28, 28, 0.36);
}

.btn-clear:active {
  transform: translateY(1px);
}

.btn-toggle {
  border: 1px solid var(--border);
  background: var(--bg);
  border-radius: 12px;
  width: 36px;
  height: 36px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 160ms ease, border-color 160ms ease, box-shadow 160ms ease;
}

.btn-toggle:hover {
  background: var(--soft);
  border-color: #d7dbe6;
}

.toggle-icon {
  transition: transform 180ms ease;
  color: var(--text);
}

/* body */
.filters-body {
  max-width: 100%;
}

/* sections */
.filter-block {
  padding: 12px 0;
  border-top: 1px solid var(--divider);
}

.filter-block:first-of-type {
  border-top: 0;
  padding-top: 10px;
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
  padding: 8px 10px;
  margin: 0;
  cursor: pointer;
  text-align: left;
  border-radius: 12px;
  transition: background 160ms ease, box-shadow 160ms ease, transform 160ms ease;
  color: inherit;
}

.filter-head:hover {
  background: rgba(7, 28, 97, 0.04);
}

.filter-head:active {
  transform: translateY(1px);
}

.filter-label {
  font-weight: 600;
  color: var(--text);
  font-size: 0.92rem;
  line-height: 1.25;
  letter-spacing: -0.01em;
}

.filter-head-right {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  min-width: 0;
}

.filter-value {
  font-size: 0.85rem;
  font-weight: 500;
  color: var(--muted);
  max-width: 170px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.inner-toggle-icon {
  transition: transform 180ms ease;
  color: var(--muted);
  flex: 0 0 auto;
}

/* dropdown content panel */
.filter-content {
  margin-top: 8px;
  padding: 6px 10px 2px;
  max-width: 100%;
  overflow: hidden;
}

/* options */
.opt {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  font-size: 0.92rem;
  line-height: 1.35;
  color: #334155;
  margin: 6px 0;
  cursor: pointer;
  user-select: none;
  padding: 8px 10px;
  border-radius: 12px;
  transition: background 160ms ease, border-color 160ms ease;
  max-width: 100%;
}

.opt:hover {
  background: rgba(2, 6, 23, 0.03);
}

.opt-tight {
  padding: 7px 10px;
}

.opt-text {
  font-weight: 450;
  min-width: 0;
  word-break: break-word;
  overflow-wrap: anywhere;
}

/* custom controls */
.opt input[type="checkbox"],
.opt input[type="radio"] {
  appearance: none;
  -webkit-appearance: none;
  width: 18px;
  height: 18px;
  flex: 0 0 18px;
  margin: 2px 0 0;
  border: 1.5px solid #cbd5e1;
  background: #ffffff;
  display: inline-grid;
  place-content: center;
  cursor: pointer;
  position: relative;
  transition: background 160ms ease, border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
}

.opt input[type="radio"] {
  border-radius: 999px;
}

.opt input[type="checkbox"] {
  border-radius: 6px;
}

.opt input[type="radio"]::after {
  content: "";
  width: 8px;
  height: 8px;
  border-radius: 999px;
  background: #ffffff;
  transform: scale(0);
  transition: transform 160ms ease;
}

.opt input[type="checkbox"]::after {
  content: "";
  width: 9px;
  height: 5px;
  border-left: 2px solid #ffffff;
  border-bottom: 2px solid #ffffff;
  transform: rotate(-45deg) scale(0);
  transition: transform 160ms ease;
  margin-top: -1px;
}

/* checked state */
.opt input[type="checkbox"]:checked,
.opt input[type="radio"]:checked {
  background: var(--accent);
  border-color: var(--accent);
}

.opt input[type="radio"]:checked::after {
  transform: scale(1);
}

.opt input[type="checkbox"]:checked::after {
  transform: rotate(-45deg) scale(1);
}

/* hover/focus polish */
.opt input[type="checkbox"]:hover,
.opt input[type="radio"]:hover {
  border-color: rgba(7, 28, 97, 0.55);
}

.btn-toggle:focus-visible,
.btn-clear:focus-visible,
.filter-head:focus-visible,
.opt input[type="checkbox"]:focus-visible,
.opt input[type="radio"]:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px rgba(7, 28, 97, 0.18);
  border-color: rgba(7, 28, 97, 0.7);
}

/* ===== transitions (mobile main + inner accordions) ===== */
.pf-collapse-enter-active,
.pf-collapse-leave-active,
.pf-accordion-enter-active,
.pf-accordion-leave-active {
  overflow: hidden;
  will-change: max-height, opacity, transform;
}

.pf-collapse-enter-active,
.pf-collapse-leave-active {
  transition: max-height 280ms cubic-bezier(0.22, 1, 0.36, 1), opacity 220ms ease,
    transform 220ms ease;
}

.pf-collapse-enter-from,
.pf-collapse-leave-to {
  max-height: 0;
  opacity: 0;
  transform: translateY(-6px);
}

.pf-collapse-enter-to,
.pf-collapse-leave-from {
  max-height: 1400px;
  opacity: 1;
  transform: translateY(0);
}

.pf-accordion-enter-active,
.pf-accordion-leave-active {
  transition: max-height 240ms cubic-bezier(0.22, 1, 0.36, 1), opacity 180ms ease,
    transform 180ms ease;
}

.pf-accordion-enter-from,
.pf-accordion-leave-to {
  max-height: 0;
  opacity: 0;
  transform: translateY(-4px);
}

.pf-accordion-enter-to,
.pf-accordion-leave-from {
  max-height: 900px;
  opacity: 1;
  transform: translateY(0);
}

@media (prefers-reduced-motion: reduce) {
  .toggle-icon,
  .inner-toggle-icon,
  .pf-collapse-enter-active,
  .pf-collapse-leave-active,
  .pf-accordion-enter-active,
  .pf-accordion-leave-active {
    transition: none !important;
  }

  .btn-clear,
  .btn-toggle,
  .filter-head,
  .opt,
  .opt input[type="checkbox"],
  .opt input[type="radio"] {
    transition: none !important;
  }
}

/* responsive polish */
@media (max-width: 1024px) {
  .filters {
    padding: 12px;
    border-radius: 16px;
  }
}

@media (max-width: 768px) {
  .filters {
    padding: 12px;
  }

  .filters-title {
    font-size: 1rem;
  }

  .filter-block {
    padding: 10px 0;
  }

  .filter-head {
    padding: 8px 8px;
  }

  .filter-content {
    padding: 6px 8px 2px;
  }

  .filter-value {
    max-width: 140px;
  }
}

@media (max-width: 360px) {
  .filters-actions {
    gap: 8px;
  }

  .filter-value {
    max-width: 110px;
  }
}
</style>
