<template>
    <section class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center mb-5">
                <h2 class="section-title">Export Vehicle Statistics</h2>
                <p class="section-description">
                    Select the make, model, and engine size to generate and download the 2023 - 2025 auction statistics
                    for
                    your vehicle.
                </p>
            </div>
            <div class="col-lg-10">
                <div v-if="loading" class="loader-wrapper">
                    <div class="spinner"></div>
                </div>
                <div class="row g-4" v-else>
                    <!-- Manufacturer -->

                    <div class="col-md-4">
                        <SelectInputComponent id="manufacture" v-model="form.manufacturer" placeholder="Select Make"
                            @change="onChangeField"
                            :options="$page.props.manufactures.map(m => ({ id: m.MARKA_NAME, name: m.MARKA_NAME }))"
                            class="field-input" />
                    </div>

                    <!-- Model -->
                    <div class="col-md-4">
                        <SelectInputComponent id="model" v-model="form.model" placeholder="Select Model"
                            @change="onChangeField"
                            :options="$page.props.models.map(m => ({ id: m.MODEL_NAME, name: m.MODEL_NAME }))"
                            class="field-input" />
                    </div>

                    <!-- Engine -->
                    <div class="col-md-4">
                        <SelectInputComponent id="engine" v-model="form.engine" placeholder="Engine CC"
                            :options="$page.props.engineCapacity.map(m => ({ id: m.ENG_V, name: m.ENG_V }))"
                            class="field-input" />
                    </div>

                    <!-- auctiongrade -->
                    <div class="col-md-4">
                        <SelectInputComponent id="auctiongrade" v-model="form.auctiongrade" placeholder="SELECT Auction Grade"
                            @change="onChangeField" :options="[
                                { id: null, name: 'All' },
                                ...($page.props.auctionGrades?.map(m => ({ id: m.RATE, name: m.RATE })) || [])
                            ]" />
                    </div>
                    <!-- chassis -->
                    <div class="col-md-4">
                        <SelectInputComponent id="chassis" v-model="form.chassis" placeholder="SELECT CHASSIS ID"
                            @change="onChangeField" :options="[
                                { id: null, name: 'All' },
                                ...($page.props.chassisNumbers?.map(m => ({ id: m.KUZOV, name: m.KUZOV })) || [])
                            ]" />
                    </div>

                    <!-- grade -->
                    <div class="col-md-4">
                        <SelectInputComponent id="grads" v-model="form.grade" placeholder="SELECT GRADE"
                            @change="onChangeField" :options="[
                                { id: null, name: 'All' },
                                ...($page.props.grades?.map(m => ({ id: m.GRADE, name: m.GRADE })) || [])
                            ]" />
                    </div>




                    <!-- Search Button -->
                    <div class="col-md-6 d-flex justify-content-end">
                        <button class="btn btn-search px-4 py-2" @click.prevent="viewData" :disabled="!isFormValid">
                            <i class="fa fa-eye me-2"></i> View Statistics
                        </button>
                    </div>
                    <div class="col-md-6 d-flex justify-content-start">
                        <button class="btn btn-search px-4 py-2" @click.prevent="filterData" :disabled="!isFormValid">
                            <i class="fa fa-download me-2"></i> Download Vehicle List
                        </button>
                    </div>
                </div>

            </div>
            <div class="col-lg-10 mt-5" v-if="statistics && statistics.length">
                <h4 class="mb-4 text-center">Yearly Auction Statistics</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Year</th>
                                <th>Vehicle Count</th>
                                <th>Average Price (¥)</th>
                                <th>Min Price (¥)</th>
                                <th>Max Price (¥)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="stat in statistics" :key="stat.year">
                                <td>{{ stat.year }}</td>
                                <td>{{ stat.vehicles }}</td>
                                <td>{{ formatCurrency(stat.average_finish_price) }}</td>
                                <td>{{ formatCurrency(stat.min_price) }}</td>
                                <td>{{ formatCurrency(stat.max_price) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</template>


<script>

import { useForm } from "@inertiajs/inertia-vue3";
import SelectInputComponent from "../Home/partials/SelectInputComponent.vue";
import axios from "axios";

export default {
    components: {
        SelectInputComponent
    },

    data() {
        return {
            form: useForm({
                manufacturer: '',
                model: '',
                engine: '',
                chassis: '',
                grade: '',
                auctiongrade: ''
            }),
            loading: false,
            statistics: null,
        };
    },
    computed: {
        isFormValid() {
            return this.form.manufacturer && this.form.model && this.form.engine;
        }
    },
    mounted() {
        $('#manufacture, #model, #engine').on('select2:open', function () {
            setTimeout(function () {
                let searchField = document.querySelector('.select2-container--open .select2-search__field');
                if (searchField) {
                    searchField.focus();
                }
            }, 100);
        });
    },
    watch: {
        'form.manufacturer'(newVal, oldVal) {
            if (newVal !== oldVal) {
                this.form.model = '';
                this.form.engine = '';

            }
        },
        'form.model'(newVal, oldVal) {
            if (newVal !== oldVal) {
                this.form.engine = '';

            }
        }
    },
    methods: {
        formatCurrency(value) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'JPY',
                minimumFractionDigits: 0,
            }).format(value);
        },
        onChangeField() {
            this.loading = true;
            this.$inertia.reload({
                method: "POST",
                data: { ...this.form, _method: "GET" },
                onSuccess: () => (this.loading = false),
            });
        },


        filterData() {
            const params = {
                manufacturer: this.form.manufacturer,
                model: this.form.model,
                engine: this.form.engine,
                chassis: this.form.chassis,
                grade: this.form.grade,
                auctiongrade: this.form.auctiongrade,
            };

            axios.get(route('export.models'), {
                params: params,
                responseType: 'blob',
            })
                .then(response => {
                    console.log(response);
                    const blob = new Blob([response.data], { type: response.headers['content-type'] });
                    const link = document.createElement('a');
                    const contentDisposition = response.headers['content-disposition'];
                    let fileName = 'download.xlsx';

                    if (contentDisposition && contentDisposition.indexOf('attachment') !== -1) {
                        const matches = /filename="?(.+)"?/.exec(contentDisposition);
                        if (matches != null && matches[1]) fileName = matches[1];
                    }

                    link.href = window.URL.createObjectURL(blob);
                    link.download = fileName;
                    link.click();
                    window.URL.revokeObjectURL(link.href);
                })
                .catch(error => {
                    console.error('Download failed', error);
                    alert('Download failed. Please try again.');
                });
        },
        viewData() {
            const params = {
                manufacturer: this.form.manufacturer,
                model: this.form.model,
                engine: this.form.engine,
                chassis: this.form.chassis,
                grade: this.form.grade,
                auctiongrade: this.form.auctiongrade,
            };

            axios.get(route('view.stats'), {
                params: params,

            })
                .then(response => {
                    // console.log(response.data);
                    this.statistics = response.data.statistics;

                })
                .catch(error => {
                    console.error(error);

                });
        }

    }
}
</script>

<style scoped>
.btn-search {
    background: linear-gradient(135deg, #0f172a, #1e293b);
    color: #fff;
    font-size: 1rem;
    font-weight: 500;
    border: none;
    border-radius: 30px;
    transition: all 0.3s ease-in-out;
}

.btn-search:hover {
    background: linear-gradient(135deg, #1e293b, #334155);
    transform: scale(1.03);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
}

.field-input {
    width: 100%;
}

.btn-search:disabled {
    background: #cbd5e1;
    color: #6b7280;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.loader-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 200px;
}

.spinner {
    width: 48px;
    height: 48px;
    border: 4px solid #d1d5db;
    border-top: 4px solid #0f172a;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
</style>
