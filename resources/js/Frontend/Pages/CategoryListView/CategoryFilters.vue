<template>
  <aside class="filters">
    <!-- Header -->
    <div class="filters-header">
      <h3 class="filters-title">Filters</h3>

      <!-- RIGHT side actions -->
      <div class="filters-actions">
        <button
          v-if="hasAny"
          type="button"
          class="btn-clear"
          @click="clearAll"
        >
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
      isMobile: false,
      isOpen: true,
      perPage: 16,

      _mql: null,
      _mqlHandler: null,
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
      this._mql = window.matchMedia("(max-width: 768px)");

      const applyMode = (matches) => {
        this.isMobile = !!matches;

        // ✅ per-page rule
        this.perPage = this.isMobile ? 8 : 16;

        // ✅ dropdown behavior
        this.isOpen = !this.isMobile;
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
    }
  },

  beforeUnmount() {
    if (this._mql && this._mqlHandler) {
      if (this._mql.removeEventListener) this._mql.removeEventListener("change", this._mqlHandler);
      else if (this._mql.removeListener) this._mql.removeListener(this._mqlHandler);
    }
  },
  beforeDestroy() {
    if (this._mql && this._mqlHandler) {
      if (this._mql.removeEventListener) this._mql.removeEventListener("change", this._mqlHandler);
      else if (this._mql.removeListener) this._mql.removeListener(this._mqlHandler);
    }
  },

  methods: {
    toggleOpen() {
      this.isOpen = !this.isOpen;
    },

    buildParams() {
      const params = {};

      if (this.subLocal) params.sub = this.subLocal;
      if (this.brandLocal?.length) params.brands = this.brandLocal;
      if (this.sortLocal && this.sortLocal !== "latest") params.sort = this.sortLocal;

      // ✅ per-page always included
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

      this.$inertia.get(route("category.list", this.category.id), { per_page: this.perPage }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      });

      if (this.isMobile) this.isOpen = false;
    },
  },
};
</script>

<style scoped>
/* ✅ FULL FILTER STICKY (desktop + mobile) */
.filters {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 14px;
  position: sticky;
  top: 12px;
  z-index: 20;
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

/* ✅ dropdown toggle at RIGHT corner */
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
