<template>
    <section class="mt-5" v-if="$root.getUser()">
        <div class="row border py-4 rounded shadow-sm">
            <div class="col-md-4">
                <select class="form-select mb-4" aria-label="Default select example" v-model="form.year"
                    @change="onChangeField">
                    <option value="0" selected>SELECT YEAR</option>
                    <option v-for="value in $page.props.statsYears" :key="value.YEAR" :value="value.YEAR">{{ value.YEAR
                    }}</option>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select mb-4" aria-label="Default select example" v-model="form.chassis_no"
                    @change="onChangeField">
                    <option value="0" selected>SELECT CHASSIS ID</option>
                    <option v-for="value in $page.props.statsChassis" :key="value.TAG0" :value="value.KUZOV">{{
                        value.KUZOV }}</option>
                </select>
            </div>
            <!-- <div class="col-md-4">
                <select class="form-select mb-4" aria-label="Default select example">
                    <option value="0" selected>SELECT AUCTION GRADE</option>
                    <option v-for="value in $page.props.statsChassis" :key="value.TAG0" :value="value.KUZOV">{{ value.KUZOV }}</option>
                </select>
            </div> -->
            <div class="col-md-4">
                <select class="form-select mb-4" aria-label="Default select example" v-model="form.rate"
                    @change="onChangeField">
                    <option value="0" selected>SELECT CONDITION</option>
                    <option v-for="value in $page.props.statsConditions" :key="value.TAG0" :value="value.RATE">{{
                        value.RATE }}</option>
                </select>
            </div>
            <!-- Engine Capacity -->
            <div class="col-md-4">
                <select class="form-select mb-4" v-model="form.engine" @change="onChangeField">
                    <option value="0" selected>SELECT ENGINE CAPACITY</option>
                    <option v-for="value in $page.props.statsengine" :key="value.TAG0" :value="value.ENG_V">
                        {{ value.ENG_V }}
                    </option>
                </select>
            </div>

            <!-- Lot Number -->
            <div class="col-md-4">
                <select class="form-select mb-4" v-model="form.lot_no" @change="onChangeField">
                    <option value="0" selected>SELECT LOT NUMBER</option>
                    <option v-for="value in $page.props.statsslot" :key="value.LOT" :value="value.LOT">
                        {{ value.LOT }}
                    </option>
                </select>
            </div>

            <!-- Auction Date -->
            <div class="col-md-4">
                <select class="form-select mb-4" v-model="form.au_date" @change="onChangeField">
                    <option value="0" selected>SELECT AUCTION DATE</option>
                    <option v-for="value in $page.props.statAuDate" :key="value.AUCTION_DATE"
                        :value="value.AUCTION_DATE">
                        {{ value.AUCTION_DATE }}
                    </option>
                </select>
            </div>




        </div>
        <section class="my-5">
            <div class="table-responsive">
                <table class="table table-striped border" style="font-size:13px; vertical-align:middle;">
                    <thead>
                        <tr style="vertical-align:baseline;">
                            <th class="bg-custom text-black" style="min-width:100px;">IMAGE</th>
                            <th class="bg-custom text-black" style="min-width:80px;">LOT NO</th>
                            <th class="bg-custom text-black" style="min-width:180px;">DATE AUCTION</th>
                            <th class="bg-custom text-black" style="min-width:130px;">YEAR - CHASSIS ID</th>
                            <th class="bg-custom text-black" style="min-width:180px;">ENGINE CAPACITY - EQUIP BOX</th>
                            <th class="bg-custom text-black" style="min-width:130px;">MILEAGE(KM)- CONDITION</th>
                            <th class="bg-custom text-black" style="min-width:70px;">START</th>
                            <th class="bg-custom text-black" style="min-width:70px;">SOLD FOR</th>
                            <th class="bg-custom text-black" style="min-width:80px;">STATUS</th>
                            <th class="bg-custom text-black" style="min-width:80px;">COLOR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="statItem in paginatedStats" :key="statItem.ID">
                            <td>
                                <img :src="statItem.IMAGES" alt="" style="height:50px;">
                            </td>


                            <td class="text-danger fw-bold">
                                <!-- <Link class="text-danger"  :href="
                                route('auction.stat', {
                                    id: statItem.ID,
                                    model: $root.stringToSlug(
                                        statItem.MARKA_NAME
                                    ),
                                    slug:
                                        $root.stringToSlug(statItem.MARKA_NAME) +
                                        '-' +
                                        $root.stringToSlug(statItem.MODEL_NAME),
                                })
                            "> {{ statItem.LOT }}</Link> -->
                               <a
                                    :href="route('auction.stat', {
                                    id: statItem.ID,
                                    model:  $root.stringToSlug(statItem.MARKA_NAME),
                                    slug:   $root.stringToSlug(statItem.MARKA_NAME) + '-' + $root.stringToSlug(statItem.MODEL_NAME)
                                    })"
                                    target="_blank"
                                    class="text-danger text-decoration-underline"
                                >
                                    {{ statItem.LOT }}
                                </a>
                                
                            </td>

                            <td>
                                {{ statItem.AUCTION_DATE }}
                                {{ statItem.AUCTION }}
                            </td>
                            <td>
                                {{ statItem.YEAR }} - {{ statItem.KUZOV }} {{ statItem.GRADE }}
                            </td>
                            <td>
                                {{ statItem.KPP }} {{ statItem.ENG_V }}CC - {{ statItem.EQUIP }}
                            </td>
                            <td>
                                {{ statItem.MILEAGE }}KM
                                <span class="text-danger fw-bold text-decoration-underline" type="button"
                                    data-bs-toggle="offcanvas" data-bs-target="#auction"
                                    aria-controls="offcanvasScrolling">{{ statItem.RATE }}</span>
                            </td>
                            <td>
                                {{ statItem.START }}¥
                            </td>
                            <td>{{ statItem.FINISH }}¥
                            </td>
                            <td>
                                <span :class="`${statusColorClass(statItem.STATUS)} p-1 rounded px-2`"> {{
                                    (statItem.STATUS == "" || !statItem.STATUS) ? '-' : statItem.STATUS }}</span>
                            </td>
                            <td>
                                {{ statItem.COLOR }}
                            </td>
                        </tr>
                        <tr class="text-center" v-if="this.$page.props.vehicleStatsList.length == 0">
                            <td colspan="10"> No results found..</td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </section>
        <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="auction"
            aria-labelledby="offcanvasWithBothOptionsLabel">
            <div class="offcanvas-header">
                <!-- <h5 class="offcanvas-title" id="offcanvasWithBothOptionsLabel">The points of auction guide is as follows</h5> -->
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <h6 class="fw-bold">Exterior Auction Grade</h6>
                <table class="table table-striped mt-5 text-start" style="font-size:12px;">
                    <thead>
                        <th class="fw-bold">Grade</th>
                        <th>Details</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>S</td>
                            <td>Brand New,</td>
                        </tr>
                        <tr>
                            <td>6-9</td>
                            <td>Like new in condition, under 5,000 Km</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Vehicle is like new. All parts are new. No repair needed on vehicle. Normally given to
                                vehicles less than 3 years.</td>
                        </tr>
                        <tr>
                            <td>4.5</td>
                            <td>The condition of vehicle is good. Only few scratches or dents as in normal cars
                            </td>
                        </tr>
                        <tr>
                            <td>4 </td>
                            <td>The condition of vehicle is good. Only few scratches or dents as in normal cars</td>
                        </tr>
                        <tr>
                            <td>3.5 </td>
                            <td>Vehicle has few scratches or dents are visible. This grade is given to vehicle in
                                average condition</td>
                        </tr>
                        <tr>
                            <td>3 </td>
                            <td>There are various scratches or dents. Some paints faults</td>
                        </tr>
                        <tr>
                            <td>2 </td>
                            <td> Nonstandard car. Badly stained or modified car</td>
                        </tr>
                        <tr>
                            <td>1 </td>
                            <td>Inferior car or badly modified car</td>
                        </tr>
                        <tr>
                            <td>RA or R1 </td>
                            <td>The vehicle had been in a minor accident but repaired to an acceptable standard
                            </td>
                        </tr>
                        <tr>
                            <td>R or A </td>
                            <td>A vehicle which has been in an accident where some parts have been repaired or replaced.
                            </td>
                        </tr>
                        <tr>
                            <td>*** </td>
                            <td>Bad Accident</td>
                        </tr>


                    </tbody>
                </table>
                <h6 class="fw-bold mt-5">Interior Auction Grade</h6>
                <table class="table table-striped mt-5 text-start" style="font-size:12px;">
                    <thead>
                        <th class="fw-bold">Grade</th>
                        <th>Details</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>A</td>
                            <td>Like new, no faults</td>
                        </tr>
                        <tr>
                            <td>B</td>
                            <td>Clean condition, slightly dirty</td>
                        </tr>
                        <tr>
                            <td>C</td>
                            <td>Clean but with cigarette burns</td>
                        </tr>
                        <tr>
                            <td>D</td>
                            <td>Dirty or stinky or big rips on seat
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <!-- Showing X–Y… on left -->
            <small class="text-muted">
                Showing {{ firstItem }} to {{ lastItem }} of {{ totalItems }} results
            </small>

            <!-- pagination on right -->
            <ul class="pagination mb-0">
                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                    <button class="page-link" @click="changePage(currentPage - 1)" :disabled="currentPage === 1">
                        Previous
                    </button>
                </li>

                <!-- only these pages -->
                <li class="page-item" v-for="p in visiblePageNumbers" :key="p" :class="{ active: p === currentPage }">
                    <button class="page-link" @click="changePage(p)">{{ p }}</button>
                </li>

                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                    <button class="page-link" @click="changePage(currentPage + 1)"
                        :disabled="currentPage === totalPages">
                        Next
                    </button>
                </li>
            </ul>
        </div>


    </section>
    <section v-else class="flex items-center justify-center h-96 bg-gray-50">
        <div class="text-center p-8 rounded-xl shadow-md bg-white">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Please log in</h2>
            <p class="text-gray-600 mb-6">You need to be logged in to view your statistics and insights.</p>
            <Link :href="route('user.login', { redirect: $page.url })" class="btn btn-primary">
            Login
            </Link>
        </div>
    </section>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';

export default {
    components: {
        Link
    },

    data() {
        return {
            index: '',
            form: {
                manufacture: 0,
                model: 0,
                chassis_no: 0,
                year: 0,
                rate: 0,
                engine: 0,
                engine: 0,   // ← new
                lot_no: 0,   // ← new
                au_date: 0,

                page: 1

            },
            currentPage: 1,
            perPage: 10,

        }
    },
    computed: {

        paginatedStats() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.$page.props.vehicleStatsList.slice(start, start + this.perPage);
        },
        totalPages() {
            return Math.ceil(this.$page.props.vehicleStatsList.length / this.perPage);
        },
        totalItems() {
            return this.$page.props.vehicleStatsList.length;
        },
        firstItem() {
            return (this.currentPage - 1) * this.perPage + 1;
        },
        lastItem() {
            return Math.min(this.currentPage * this.perPage, this.totalItems);
        },
        visiblePageNumbers() {
            const total = this.totalPages;
            const curr = this.currentPage;
            const max = 3;               // show at most 3 buttons

            // center the window on `curr`
            let start = Math.max(1, curr - Math.floor(max / 2));
            let end = start + max - 1;

            // if we ran off the right, shift back
            if (end > total) {
                end = total;
                start = Math.max(1, total - max + 1);
            }

            const pages = [];
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            return pages;
        }
    },
    mounted() {
        console.log(this.$page.props.details)
        this.form.manufacture = this.$page.props.details.MARKA_NAME;
        this.form.model = this.$page.props.details.MODEL_NAME;
        this.form.year = this.$page.props.details.YEAR;
        this.form.rate = this.$page.props.details.RATE;
        this.setQueryData();
        this.$inertia.reload({ method: "POST", data: { '_method': "GET", ...this.form, manufacture: this.$page.props.details.MARKA_NAME, model: this.$page.props.details.MODEL_NAME, rate: this.$page.props.details.RATE }, onSuccess: () => { this.setQueryData() } });
    },
    methods: {
        statusColorClass(status) {
            return status == 'SOLD' ? 'bg-danger text-white' : status == 'NOT SOLD' ? 'bg-success text-white' : 'text-dark';
        },
        resetFilter() {
            this.form = {
                manufacture: 0,
                model: 0,
                chassis_no: 0,
                year: 0,
                rate: 0,
                engine: 0,
                page: 1
            }
        },
        setQueryData() {
            this.form.manufacture = this.$page.props.requestQuery.manufacturer ?? this.$page.props.details.MARKA_NAME;
            this.form.model = this.$page.props.requestQuery.model ?? this.$page.props.details.MODEL_NAME;
            this.form.year = this.$page.props.requestQuery.year ?? this.$page.props.details.YEAR;
            this.form.chassis_no = this.$page.props.requestQuery.chassis_no ?? 0;
            this.form.engine = this.$page.props.requestQuery.engine ?? 0;
            this.form.rate = this.$page.props.requestQuery.rate ?? 0;
            this.form.page = this.$page.props.requestQuery.page ?? 0;
            this.form.engine = this.$page.props.requestQuery.engine ?? 0;
            this.form.lot_no = this.$page.props.requestQuery.lot_no ?? 0;
            this.form.au_date = this.$page.props.requestQuery.au_date ?? 0;
        },
        onChangeField(evt) {
            this.currentPage = 1;
            if (this.$page.props.requestQuery.year != this.form.year) {
                this.form.chassis_no = 0;
                this.form.rate = 0;
            }
            this.$inertia.reload({
                method: "POST",
                data: { "_method": "GET", ...this.form, },
                onSuccess: () => {
                    this.setQueryData()
                }
            });
        },
        changePage(page) {
            if (page < 1 || page > this.totalPages) return;
            this.currentPage = page;
        },

        goToStat(statItem) {
            // build the URL you want to visit
            const detailUrl = route('auction.stat', {
                id: statItem.ID,
                model: this.$root.stringToSlug(statItem.MARKA_NAME),
                slug:
                    this.$root.stringToSlug(statItem.MARKA_NAME) +
                    '-' +
                    this.$root.stringToSlug(statItem.MODEL_NAME),
            })

            if (this.$page.props.logged_customer) {
                // user is logged in → go straight there
                Inertia.visit(detailUrl)
            } else {
                // not logged in → send to login with a redirect back
                Inertia.visit(
                    route('user.login', { redirect: detailUrl })
                )
            }
        },
    }
}
</script>

<style scoped>
.form-select {
    box-shadow: none;
}

.bg-custom {
    background-color: aqua;
}
</style>
