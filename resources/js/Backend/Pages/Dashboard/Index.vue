<template>
    <AppLayout>
        <div class="container-fluid py-3">
            <!-- KPI CARDS -->
            <div class="row g-3 mb-3">
                <div class="col-xxl-3 col-md-6">
                    <div class="card kpi-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="kpi-icon" style="background: #0d6efd">
                                <i
                                    data-feather="package"
                                    class="text-white"
                                ></i>
                            </div>
                            <div>
                                <div class="text-muted">Products</div>
                                <div class="h3 mb-0">
                                    {{ n(counts.products_total) }}
                                </div>
                                <div class="small text-muted">
                                    Active: {{ n(counts.products_active) }} •
                                    Discounted:
                                    {{ n(counts.products_discounted) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card kpi-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="kpi-icon" style="background: #198754">
                                <i data-feather="tag" class="text-white"></i>
                            </div>
                            <div>
                                <div class="text-muted">Brands</div>
                                <div class="h3 mb-0">
                                    {{ n(counts.brands_total) }}
                                </div>
                                <div class="small text-muted">
                                    Active: {{ n(counts.brands_active) }} •
                                    Featured: {{ n(counts.brands_featured) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card kpi-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="kpi-icon" style="background: #fd7e14">
                                <i data-feather="grid" class="text-white"></i>
                            </div>
                            <div>
                                <div class="text-muted">Categories</div>
                                <div class="h3 mb-0">
                                    {{ n(counts.categories_total) }}
                                </div>
                                <div class="small text-muted">
                                    Active: {{ n(counts.categories_active) }} •
                                    Featured:
                                    {{ n(counts.categories_featured) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card kpi-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="kpi-icon" style="background: #6f42c1">
                                <i
                                    data-feather="sliders"
                                    class="text-white"
                                ></i>
                            </div>
                            <div>
                                <div class="text-muted">Attributes</div>
                                <div class="h3 mb-0">
                                    {{ n(counts.attributes_total) }}
                                </div>
                                <div class="small text-muted">
                                    Filterable:
                                    {{ n(counts.attributes_filterable) }} •
                                    Variant: {{ n(counts.attributes_variant) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHARTS -->
            <div class="row g-3 mb-3">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header fw-semibold">
                            Products Created (Last 12 Months)
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
                        <div class="card-header fw-semibold">
                            Products Status
                        </div>
                        <div class="card-body">
                            <canvas ref="statusCanvas" height="140"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header fw-semibold">
                            Attribute Types
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
                        <div class="card-header fw-semibold">
                            Top Brands (by Product Count)
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
import Chart from "chart.js/auto";

export default {
    components: { AppLayout, Link },

    props: {
        counts: { type: Object, required: true },
        charts: { type: Object, required: true },
        recentProducts: { type: Array, required: true },
    },

    data() {
        return { chartInstances: [] };
    },

    mounted() {
        this.renderCharts();
    },

    beforeUnmount() {
        this.chartInstances.forEach((c) => c?.destroy?.());
        this.chartInstances = [];
    },

    methods: {
        n(v) {
            const x = Number(v);
            return Number.isFinite(x) ? x : 0;
        },

        renderCharts() {
            const make = (ref, type, data, options) => {
                const ctx = this.$refs[ref]?.getContext("2d");
                if (!ctx) return;
                const c = new Chart(ctx, { type, data, options });
                this.chartInstances.push(c);
            };

            make("productsTrendCanvas", "line", this.charts.productsTrend, {
                responsive: true,
                plugins: { legend: { display: true } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            });

            make("statusCanvas", "doughnut", this.charts.statusBreakdown, {
                responsive: true,
                plugins: { legend: { position: "bottom" } },
            });

            make("attributeTypesCanvas", "bar", this.charts.attributeTypes, {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            });

            make("topBrandsCanvas", "bar", this.charts.topBrands, {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            });
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
.kpi-card {
    border-radius: 14px;
}
.kpi-icon {
    width: 46px;
    height: 46px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
}
.kpi-icon i {
    width: 20px;
    height: 20px;
}
</style>
