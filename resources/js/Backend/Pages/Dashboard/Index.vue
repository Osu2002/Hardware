<template>
    <AppLayout>
        <div class="container-fluid py-3">
            <!-- HOME BANNERS -->
            <div class="row g-3 mb-3">
                <div class="col-12">
                    <div class="card banner-card">
                        <div
                            class="card-header d-flex justify-content-between align-items-center"
                        >
                            <div class="fw-semibold">Home Banners</div>
                            <div class="small text-muted">
                                Total: {{ counts.homebanners_total ?? 0 }} •
                                Active: {{ counts.homebanners_active ?? 0 }}
                            </div>
                        </div>

                        <div class="card-body">
                            <div v-if="homebanners?.length" class="row g-3">
                                <!-- Carousel -->
                                <div class="col-lg-8">
                                    <div
                                        :id="carouselId"
                                        class="carousel slide"
                                        data-bs-ride="carousel"
                                    >
                                        <div
                                            class="carousel-inner rounded-3 overflow-hidden"
                                        >
                                            <div
                                                v-for="(b, idx) in homebanners"
                                                :key="b.id"
                                                class="carousel-item"
                                                :class="{ active: idx === 0 }"
                                            >
                                                <div class="banner-frame">
                                                    <img
                                                        v-if="b.image_url"
                                                        :src="b.image_url"
                                                        class="d-block w-100"
                                                        alt="Banner"
                                                    />
                                                    <div
                                                        v-else
                                                        class="banner-placeholder"
                                                    >
                                                        No Image
                                                    </div>

                                                    <div class="banner-overlay">
                                                        <div
                                                            class="fw-semibold"
                                                        >
                                                            {{ b.name }}
                                                        </div>
                                                        <div class="small">
                                                            <span
                                                                class="badge"
                                                                :class="
                                                                    b.status ===
                                                                    1
                                                                        ? 'bg-success'
                                                                        : 'bg-warning'
                                                                "
                                                            >
                                                                {{
                                                                    b.status ===
                                                                    1
                                                                        ? "Active"
                                                                        : "Inactive"
                                                                }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <button
                                            class="carousel-control-prev"
                                            type="button"
                                            :data-bs-target="'#' + carouselId"
                                            data-bs-slide="prev"
                                        >
                                            <span
                                                class="carousel-control-prev-icon"
                                                aria-hidden="true"
                                            ></span>
                                            <span class="visually-hidden"
                                                >Previous</span
                                            >
                                        </button>
                                        <button
                                            class="carousel-control-next"
                                            type="button"
                                            :data-bs-target="'#' + carouselId"
                                            data-bs-slide="next"
                                        >
                                            <span
                                                class="carousel-control-next-icon"
                                                aria-hidden="true"
                                            ></span>
                                            <span class="visually-hidden"
                                                >Next</span
                                            >
                                        </button>
                                    </div>
                                </div>

                                <!-- Thumbnail grid -->
                                <div class="col-lg-4">
                                    <div class="thumb-grid">
                                        <div
                                            v-for="b in homebanners"
                                            :key="'thumb-' + b.id"
                                            class="thumb-item"
                                        >
                                            <div class="thumb-img">
                                                <img
                                                    v-if="b.image_url"
                                                    :src="b.image_url"
                                                    alt="thumb"
                                                />
                                                <div
                                                    v-else
                                                    class="thumb-placeholder"
                                                >
                                                    No Image
                                                </div>
                                            </div>
                                            <div class="thumb-meta">
                                                <div
                                                    class="fw-semibold text-truncate"
                                                >
                                                    {{ b.name }}
                                                </div>
                                                <div class="small text-muted">
                                                    <span
                                                        class="badge"
                                                        :class="
                                                            b.status === 1
                                                                ? 'bg-success'
                                                                : 'bg-warning'
                                                        "
                                                    >
                                                        {{
                                                            b.status === 1
                                                                ? "Active"
                                                                : "Inactive"
                                                        }}
                                                    </span>
                                                    <span class="ms-2">{{
                                                        fmtDate(b.created_at)
                                                    }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="text-center text-muted py-5">
                                No home banners found.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI CARDS -->
            <div class="row g-3 mb-3">
                <div class="col-xxl-3 col-md-6">
                    <div class="card o-hidden widget-cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div
                                    class="icons-widgets p-3 rounded-circle me-3"
                                    style="background: #0d6efd"
                                >
                                    <i
                                        data-feather="package"
                                        class="text-white"
                                    ></i>
                                </div>
                                <div>
                                    <div class="text-muted">Products</div>
                                    <div class="h3 mb-0">
                                        {{ counts.products_total }}
                                    </div>
                                    <div class="small text-muted">
                                        Active: {{ counts.products_active }} •
                                        Discounted:
                                        {{ counts.products_discounted }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card o-hidden widget-cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div
                                    class="icons-widgets p-3 rounded-circle me-3"
                                    style="background: #198754"
                                >
                                    <i
                                        data-feather="tag"
                                        class="text-white"
                                    ></i>
                                </div>
                                <div>
                                    <div class="text-muted">Brands</div>
                                    <div class="h3 mb-0">
                                        {{ counts.brands_total }}
                                    </div>
                                    <div class="small text-muted">
                                        Active: {{ counts.brands_active }} •
                                        Featured: {{ counts.brands_featured }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card o-hidden widget-cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div
                                    class="icons-widgets p-3 rounded-circle me-3"
                                    style="background: #fd7e14"
                                >
                                    <i
                                        data-feather="grid"
                                        class="text-white"
                                    ></i>
                                </div>
                                <div>
                                    <div class="text-muted">Categories</div>
                                    <div class="h3 mb-0">
                                        {{ counts.categories_total }}
                                    </div>
                                    <div class="small text-muted">
                                        Active: {{ counts.categories_active }} •
                                        Featured:
                                        {{ counts.categories_featured }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card o-hidden widget-cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div
                                    class="icons-widgets p-3 rounded-circle me-3"
                                    style="background: #6f42c1"
                                >
                                    <i
                                        data-feather="sliders"
                                        class="text-white"
                                    ></i>
                                </div>
                                <div>
                                    <div class="text-muted">Attributes</div>
                                    <div class="h3 mb-0">
                                        {{ counts.attributes_total }}
                                    </div>
                                    <div class="small text-muted">
                                        Filterable:
                                        {{ counts.attributes_filterable }} •
                                        Variant: {{ counts.attributes_variant }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHARTS ROW 1 -->
            <div class="row g-3 mb-3">
                <div class="col-xl-8">
                    <div class="card">
                        <div
                            class="card-header d-flex justify-content-between align-items-center"
                        >
                            <div class="fw-semibold">
                                Products Created (Last 12 Months)
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas
                                ref="productsTrendCanvas"
                                height="120"
                            ></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card">
                        <div
                            class="card-header d-flex justify-content-between align-items-center"
                        >
                            <div class="fw-semibold">Products Status</div>
                        </div>
                        <div class="card-body">
                            <canvas ref="statusCanvas" height="140"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHARTS ROW 2 -->
            <div class="row g-3 mb-3">
                <div class="col-xl-6">
                    <div class="card">
                        <div
                            class="card-header d-flex justify-content-between align-items-center"
                        >
                            <div class="fw-semibold">Attribute Types</div>
                        </div>
                        <div class="card-body">
                            <canvas
                                ref="attributeTypesCanvas"
                                height="140"
                            ></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card">
                        <div
                            class="card-header d-flex justify-content-between align-items-center"
                        >
                            <div class="fw-semibold">
                                Top Brands (by Product Count)
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas ref="topBrandsCanvas" height="140"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RECENT PRODUCTS -->
            <div class="row g-3">
                <div class="col-12">
                    <div class="card">
                        <div
                            class="card-header d-flex justify-content-between align-items-center"
                        >
                            <div class="fw-semibold">Recent Products</div>
                            <Link
                                :href="route('product.index')"
                                class="btn btn-sm btn-primary"
                                >View All</Link
                            >
                        </div>

                        <div class="card-body table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>SKU</th>
                                        <th>Brand</th>
                                        <th>UOM</th>
                                        <th>Status</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Sale</th>
                                        <th>Discount</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="p in recentProducts" :key="p.id">
                                        <td class="fw-semibold">
                                            {{ p.name }}
                                        </td>
                                        <td>{{ p.sku }}</td>
                                        <td>{{ p.brand?.title ?? "-" }}</td>
                                        <td>{{ p.uom?.name ?? "-" }}</td>
                                        <td>
                                            <span
                                                class="badge"
                                                :class="
                                                    p.status === 1
                                                        ? 'bg-success'
                                                        : 'bg-warning'
                                                "
                                            >
                                                {{
                                                    p.status === 1
                                                        ? "Active"
                                                        : "Inactive"
                                                }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            {{ fmt(p.price) }}
                                        </td>
                                        <td class="text-end">
                                            {{ fmt(p.sale_price) }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge"
                                                :class="
                                                    p.discount_status
                                                        ? 'bg-primary'
                                                        : 'bg-secondary'
                                                "
                                            >
                                                {{
                                                    p.discount_status
                                                        ? "Yes"
                                                        : "No"
                                                }}
                                            </span>
                                        </td>
                                        <td>{{ fmtDate(p.created_at) }}</td>
                                    </tr>

                                    <tr v-if="!recentProducts?.length">
                                        <td
                                            colspan="9"
                                            class="text-center text-muted py-4"
                                        >
                                            No products found.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

// Option 1 (requires chart.js installed):
import Chart from "chart.js/auto";

// Option 2 (if you want to avoid /auto):
// import { Chart, registerables } from "chart.js";
// Chart.register(...registerables);

export default {
    components: { AppLayout, Link },

    props: {
        counts: { type: Object, required: true },
        charts: { type: Object, required: true },
        recentProducts: { type: Array, required: true },
        homebanners: { type: Array, required: true },
    },

    data() {
        return {
            chartInstances: [],
            carouselId: "homeBannerCarousel",
        };
    },

    mounted() {
        this.renderCharts();
    },

    beforeUnmount() {
        this.chartInstances.forEach((c) => c?.destroy?.());
        this.chartInstances = [];
    },

    methods: {
        renderCharts() {
            const trendCtx = this.$refs.productsTrendCanvas?.getContext("2d");
            if (trendCtx) {
                const c = new Chart(trendCtx, {
                    type: "line",
                    data: this.charts.productsTrend,
                    options: {
                        responsive: true,
                        plugins: { legend: { display: true } },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 } },
                        },
                    },
                });
                this.chartInstances.push(c);
            }

            const statusCtx = this.$refs.statusCanvas?.getContext("2d");
            if (statusCtx) {
                const c = new Chart(statusCtx, {
                    type: "doughnut",
                    data: this.charts.statusBreakdown,
                    options: {
                        responsive: true,
                        plugins: { legend: { position: "bottom" } },
                    },
                });
                this.chartInstances.push(c);
            }

            const attrCtx = this.$refs.attributeTypesCanvas?.getContext("2d");
            if (attrCtx) {
                const c = new Chart(attrCtx, {
                    type: "bar",
                    data: this.charts.attributeTypes,
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 } },
                        },
                    },
                });
                this.chartInstances.push(c);
            }

            const brandCtx = this.$refs.topBrandsCanvas?.getContext("2d");
            if (brandCtx) {
                const c = new Chart(brandCtx, {
                    type: "bar",
                    data: this.charts.topBrands,
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 } },
                        },
                    },
                });
                this.chartInstances.push(c);
            }
        },

        fmt(v) {
            if (v === null || v === undefined || v === "") return "-";
            const n = Number(v);
            if (Number.isNaN(n)) return String(v);
            return n.toLocaleString(undefined, {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2,
            });
        },

        fmtDate(v) {
            if (!v) return "-";
            try {
                return new Date(v).toLocaleDateString();
            } catch (e) {
                return String(v);
            }
        },
    },
};
</script>

<style scoped>
.widget-cards {
    border-radius: 14px;
}
.icons-widgets i {
    width: 20px;
    height: 20px;
}

.banner-card {
    border-radius: 16px;
    overflow: hidden;
}
.banner-frame {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    height: 340px;
}
.banner-frame img {
    width: 100%;
    height: 340px;
    object-fit: cover;
}
.banner-placeholder {
    height: 340px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f3f5;
    color: #6c757d;
    font-weight: 600;
}
.banner-overlay {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    padding: 14px 16px;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.65));
    color: #fff;
}

.thumb-grid {
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-height: 340px;
    overflow: auto;
    padding-right: 4px;
}
.thumb-item {
    display: flex;
    gap: 10px;
    align-items: center;
    padding: 8px;
    border-radius: 12px;
    background: #f8f9fa;
}
.thumb-img {
    width: 72px;
    height: 52px;
    border-radius: 10px;
    overflow: hidden;
    flex: 0 0 auto;
    background: #e9ecef;
}
.thumb-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.thumb-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 12px;
}
.thumb-meta {
    min-width: 0;
}
</style>
