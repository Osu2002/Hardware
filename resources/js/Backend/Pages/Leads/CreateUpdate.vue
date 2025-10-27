<template>
    <AppLayout>
        <div class="row">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h5>Leads</h5>
                    <p>Manage Leads.</p>
                </div>
                <hr />
                <div class="card-body">
                    <form id="formAccountSettings" @submit.prevent="submit">
                        <div class="row">
                            <!-- <div class="row">
                <div class="mb-3 col-md-12">
                  <label for="url" class="form-label">Vehicle URL</label>
                  <input class="form-control" type="text" id="url" name="url" v-model="form.url" />

                  <button
                    type="button"
                    class="btn btn-primary mt-2"
                    @click="validateAndExtract"
                  >Search</button>
                  <div class="text-danger">{{ form.errors.url }}</div>
                </div>
                <div class="mb-3 col-md-6">
                  <label for="type" class="form-label">Vehicle</label>
                  <input
                    class="form-control"
                    type="text"
                    id="vehicle"
                    name="vehicle"
                    v-model="form.vehicle"
                    disabled
                  />
                  <div class="text-danger">{{ form.errors.vehicle }}</div>
                </div>

                <div class="mb-3 col-md-6">
                  <label for="type" class="form-label">type</label>
                  <input
                    class="form-control"
                    type="text"
                    id="type"
                    name="type"
                    v-model="form.type"
                    disabled
                  />
                  <div class="text-danger">{{ form.errors.type }}</div>
                </div>
              </div>-->
                            <div>
                                <div class="row">
                                    <div class="mb-3 col-md-12">
                                        <label for="url" class="form-label"
                                            >Vehicle URL</label
                                        >
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="url"
                                            name="url"
                                            v-model="form.url"
                                        />

                                        <button
                                            type="button"
                                            class="btn btn-primary mt-2"
                                            @click="validateAndExtract"
                                        >
                                            Search
                                        </button>
                                        <div class="text-danger">
                                            {{ form.errors.url }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label for="vehicle" class="form-label"
                                            >Vehicle</label
                                        >
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="vehicle"
                                            name="vehicle"
                                            v-model="form.vehicle"
                                            disabled
                                        />
                                        <div class="text-danger">
                                            {{ form.errors.vehicle }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label for="type" class="form-label"
                                            >Type</label
                                        >
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="type"
                                            name="type"
                                            v-model="form.type"
                                            disabled
                                        />
                                        <div class="text-danger">
                                            {{ form.errors.type }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <button
                                            type="button"
                                            class="btn btn-primary mt-2 w-100"
                                            @click="addVehicle"
                                            :disabled="!form.vehicle"
                                        >
                                            Add Vehicle
                                        </button>
                                    </div>
                                </div>

                                <!-- Table to Display Added Vehicles -->
                                <div v-if="form.vehicles.length" class="mb-3">
                                    <h5 class="mt-3">Selected Vehicles</h5>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Vehicle</th>
                                                <th>Vehicle Id</th>
                                                <th>Type</th>
                                                <th>URL</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(
                                                    vehicle, index
                                                ) in form.vehicles"
                                                :key="index"
                                            >
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ vehicle.name }}</td>
                                                <td>
                                                    {{ vehicle.vehicle_id }}
                                                </td>
                                                <td>{{ vehicle.type }}</td>
                                                <td>{{ vehicle.url }}</td>
                                                <td>
                                                    <button
                                                        class="btn btn-danger btn-sm"
                                                        @click="
                                                            removeVehicle(index)
                                                        "
                                                    >
                                                        Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr />
                            <SelectInputComponentVue
                                id="customer_id"
                                label="Search Customer"
                                :error="form.errors.customer_id"
                                :options="
                                    customers.map((el) => ({
                                        id: el.id,
                                        name: `${el.phone} (${
                                            el.name ? el.name : ''
                                        }) (${el.email})`,
                                    }))
                                "
                                v-model="form.customer_id"
                                :isRequired="false"
                            />

                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label"
                                    >Name</label
                                >
                                <input
                                    class="form-control"
                                    type="text"
                                    id="name"
                                    name="name"
                                    v-model="form.name"
                                />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label"
                                    >Email</label
                                >
                                <input
                                    class="form-control"
                                    type="email"
                                    id="email"
                                    name="email"
                                    v-model="form.email"
                                />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="phone" class="form-label"
                                    >Phone</label
                                >
                                <input
                                    class="form-control"
                                    type="text"
                                    id="phone"
                                    name="phone"
                                    v-model="form.phone"
                                />
                            </div>
                            <!-- <div class="mb-3 col-md-6">
                <label for="purchase" class="form-label">Purchase Time</label>
                <input class="form-control" type="text" id="purchase" name="purchase" v-model="form.purchase_time" />
              </div>-->
                            <SelectInputComponentVue
                                id="purchase_time"
                                label="Purchase Time"
                                :error="form.errors.purchase_time"
                                :options="[
                                    {
                                        id: 'Immediately',
                                        name: 'Immediately',
                                    },
                                    {
                                        id: 'Next Week',
                                        name: 'Next Week',
                                    },
                                    {
                                        id: 'Two Weeks',
                                        name: 'Two Weeks',
                                    },
                                    {
                                        id: 'One Month',
                                        name: 'One Month',
                                    },
                                    {
                                        id: 'Not Sure',
                                        name: 'Not Sure',
                                    },
                                ]"
                                v-model="form.purchase_time"
                            />
                            <div class="mb-3 col-md-12">
                                <label for="address" class="form-label"
                                    >Address</label
                                >
                                <textarea
                                    class="form-control"
                                    id="address"
                                    rows="3"
                                    v-model="form.address"
                                ></textarea>
                            </div>
                            <div class="mb-3 col-md-12">
                                <label for="message" class="form-label"
                                    >Message</label
                                >
                                <textarea
                                    class="form-control"
                                    id="message"
                                    rows="3"
                                    v-model="form.message"
                                ></textarea>
                            </div>

                            <SelectInputComponentVue
                                id="payment"
                                label="Payment Method"
                                :error="form.errors.payment_method"
                                :options="[
                                    {
                                        id: 'Full Lease',
                                        name: 'Full Lease',
                                    },
                                    {
                                        id: 'Half Lease',
                                        name: 'Half Lease',
                                    },
                                    {
                                        id: 'Cash',
                                        name: 'Cash',
                                    },
                                    {
                                        id: 'Full Loan',
                                        name: 'Full Loan',
                                    },
                                    {
                                        id: 'Half Loan',
                                        name: 'Half Loan',
                                    },
                                ]"
                                v-model="form.payment_method"
                            />

                            <SelectInputComponentVue
                                v-if="
                                    $page.props.user.roles[0].name ==
                                    'Super Admin'
                                "
                                id="status"
                                label="Status"
                                :error="form.errors.status"
                                :options="[
                                    {
                                        id: 'contacted',
                                        name: 'Client Contacted',
                                    },
                                    {
                                        id: 'negotiating',
                                        name: 'Negotiating',
                                    },
                                    {
                                        id: 'pending',
                                        name: 'Pending',
                                    },
                                    {
                                        id: 'advance_paid',
                                        name: 'Advance Paid',
                                    },
                                    {
                                        id: 'awaiting_for_shipping',
                                        name: 'Awaiting for Shipping',
                                    },
                                    {
                                        id: 'cancelled',
                                        name: 'Cancelled',
                                    },
                                ]"
                                v-model="form.status"
                            />
                        </div>
                        <div class="mt-2">
                            <button
                                type="submit"
                                class="btn btn-main me-2"
                                :disabled="form.processing"
                            >
                                {{ lead ? "Update" : "Save" }}
                            </button>
                            <Link
                                class="btn btn-outline-danger"
                                :href="route('lead.index')"
                                >Cancel</Link
                            >
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { Link, useForm } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import FileInputComponent from "@/Components/FileInputComponent.vue";
import SelectInputComponentVue from "../../Components/SelectInputComponent.vue";
import Editor from "@tinymce/tinymce-vue";
import { ref } from "vue";

// import SuspendedUserAlert from "./Partials/SuspendedUserAlert.vue";

export default {
    components: {
        Link,
        AppLayout,
        FileInputComponent,
        // SuspendedUserAlert,
        SelectInputComponentVue,
        Editor,
    },
    // setup() {
    //   const apiKey = ref(import.meta.env.VITE_TINYMCE_API_KEY);
    //   return {
    //     apiKey
    //   }
    // },
    props: {
        errors: Object,
        lead: Object,
        customers: Array,
    },

    data() {
        return {
            apiKey: import.meta.env.VITE_TINYMCE_API_KEY,
            form: useForm({
                name: "",
                email: "",
                phone: "",
                purchase_time: "",
                address: "",
                message: "",
                payment_method: "",
                url: "",
                vehicles: [],
                vehicle: "",
                vehicle_id: "",
                customer_id: "",
                type: "",
                id: "",
                status: "pending",
            }),
            // password: useForm({
            //   id: "",
            //   confirm_password: "",
            //   password: "",
            //   password_confirmation: "",
            // }),
        };
    },
    watch: {
        "form.customer_id": function (newVal) {
            this.updateCusData(newVal);
        },
    },
    mounted() {
        let self = this;

        if (this.lead) {
            this.form.id = this.lead.id;
            this.form.name = this.lead.name;
            this.form.email = this.lead.email;
            this.form.phone = this.lead.phone;
            this.form.purchase_time = this.lead.purchase_time;
            this.form.address = this.lead.address;
            this.form.message = this.lead.message;
            this.form.payment_method = this.lead.payment_method;
            this.form.vehicles = JSON.parse(this.lead.vehicle_data);

            this.form.customer_id = this.lead.customer_id;

            this.form.status = this.lead.status;
        }

        // $(".card-body").on("change", "#role", function (evt) {
        //   self.form.role = $(evt.target).val();
        // });
        // $(".card-body").on("change", "#branch", function (evt) {
        //   self.form.branch = $(evt.target).val();
        // });
    },

    methods: {
        addVehicle() {
            if (!this.form.vehicle) return;

            this.form.vehicles.push({
                name: this.form.vehicle,
                type: this.form.type,
                url: this.form.url,
                vehicle_id: this.form.vehicle_id,
            });

            // Clear the input fields after adding
            this.form.url = "";
            this.form.vehicle = "";
            this.form.type = "";
        },

        removeVehicle(index) {
            this.form.vehicles.splice(index, 1);
        },

        updateCusData(cusId) {
            const customer = this.customers.find((c) => c.id == cusId);
            if (customer) {
                this.form.name = customer.name;
                this.form.email = customer.email;
                this.form.phone = customer.phone;
            }
        },

        validateAndExtract() {
            const url = this.form.url.trim();
            // Local Vehicle URL Pattern (Now pointing to nikoba.com)
            const localPattern =
                /^https:\/\/nikoba\.com\/view-details\/([^/]+)\/([^?]+)\?id=(\d+)$/;
            // Auction Vehicle URL Pattern (Remains unchanged)
            const auctionPattern =
                /^https:\/\/nikoba\.com\/auction\/([^/]+)\/([^?]+)\?id=([A-Za-z0-9]+)$/;

            let match;
            if ((match = url.match(localPattern))) {
                this.form.vehicle = match[2].replace(/-/g, " ");
                this.form.vehicle_id = match[3];
                this.form.type = "local";
                this.form.errors.url = "";
            } else if ((match = url.match(auctionPattern))) {
                // console.log(match[3])
                this.form.vehicle = match[2].replace(/-/g, " ");
                this.form.vehicle_id = match[3];
                this.form.type = "auction";
                this.form.errors.url = "";
            } else {
                this.form.errors.url = "Invalid URL format!";
                this.form.vehicle = "";
                this.form.type = "";
            }
        },

        submit() {
            this.form.post(
                this.lead ? route("lead.update") : route("lead.store"),
                {
                    onSuccess: () => {
                        this.form.reset();
                        this.$root.showMessage(
                            "success",
                            '<span class="text-success">Success</span><br/>',
                            "A Record Was Created Successfully! "
                        );
                    },
                    onError: () => {
                        this.form.reset("password", "password_confirmation");
                        this.$root.showMessage(
                            "error",
                            '<span class="text-danger">Error</span><br>',
                            "Something went wrong! "
                        );
                    },
                }
            );
        },
    },
};
</script>
<style scoped>
/* Flex container for checkboxes */
.features-container {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

/* Checkbox Container */
.checkbox-container {
    display: flex;
    align-items: center;
    width: calc(25% - 1rem);
    /* Adjust the width to divide the space into 4 columns */
    margin-bottom: 0.5rem;
}

/* Hide the default checkbox */
.checkbox-container input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

/* Custom Checkbox Design */
.checkbox-container label {
    position: relative;
    padding-left: 30px;
    cursor: pointer;
    font-weight: 500;
    user-select: none;
}

/* Create the custom checkbox */
.checkbox-container label::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    width: 20px;
    height: 20px;
    background-color: #f1f1f1;
    border: 2px solid #ccc;
    border-radius: 4px;
    transition: background-color 0.2s, border-color 0.2s;
}

/* Show the checkmark when checked */
.checkbox-container input[type="checkbox"]:checked + label::before {
    background-color: #3d3232;
    border-color: #000000;
}

.checkbox-container input[type="checkbox"]:checked + label::after {
    content: "";
    position: absolute;
    left: 6px;
    top: 2px;
    width: 6px;
    height: 12px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.border-none {
    border: 0px !important;
}

/* Hover Effect */
.checkbox-container label:hover::before {
    border-color: #007bff;
}
</style>
