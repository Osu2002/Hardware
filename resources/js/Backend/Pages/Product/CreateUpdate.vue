<template>
    <AppLayout>
        <div class="row">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h5>Product</h5>
                    <p>Create / Update a product.</p>
                </div>
                <hr />

                <div class="card-body">
                    <form @submit.prevent="submit">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Name</label>
                                <input
                                    class="form-control"
                                    v-model="form.name"
                                />
                                <div class="text-danger">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <!-- SKU with Generate button -->
                            <div class="mb-3 col-md-3">
                                <label class="form-label">SKU</label>
                                <div class="input-group">
                                    <input
                                        class="form-control"
                                        v-model="form.sku"
                                    />
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary"
                                        @click="generateSku"
                                    >
                                        Generate
                                    </button>
                                </div>
                                <div class="text-danger">
                                    {{ form.errors.sku }}
                                </div>
                            </div>

                            <SelectInputComponent
                                class="mb-3 col-md-3"
                                id="status"
                                label="Status"
                                :options="[
                                    { id: '1', name: 'Active' },
                                    { id: '0', name: 'Inactive' },
                                ]"
                                :error="form.errors.status"
                                v-model="form.status"
                            />
                        </div>

                        <div class="row">
                            <SelectInputComponent
                                class="mb-3 col-md-4"
                                id="brand"
                                label="Brand"
                                :options="brands"
                                option-label="title"
                                option-value="id"
                                v-model="form.brand_id"
                            />
                            <SelectInputComponent
                                class="mb-3 col-md-4"
                                id="uom"
                                label="Unit of Measure"
                                :options="uoms"
                                option-label="name"
                                option-value="id"
                                v-model="form.uom_id"
                            />
                            <SelectInputComponent
                                class="mb-3 col-md-4"
                                id="attribute_set"
                                label="Attribute Set"
                                :options="attributeSets"
                                option-label="name"
                                option-value="id"
                                v-model="form.attribute_set_id"
                            />
                        </div>

                        <!-- Dynamic Attributes -->
                        <div
                            v-if="attributes && attributes.length"
                            class="row mt-2"
                        >
                            <div class="col-12">
                                <h6 class="mb-2">Attributes</h6>
                            </div>

                            <div
                                class="col-md-4 mb-3"
                                v-for="a in attributes"
                                :key="a.id"
                            >
                                <label class="form-label">
                                    {{ a.name }}
                                    <span v-if="a.unit" class="text-muted"
                                        >({{ a.unit }})</span
                                    >
                                    <span
                                        v-if="a.is_required"
                                        class="text-danger"
                                        >*</span
                                    >
                                </label>

                                <input
                                    v-if="a.type === 'text'"
                                    class="form-control"
                                    v-model="attrValue[a.code]"
                                    type="text"
                                />

                                <input
                                    v-else-if="a.type === 'number'"
                                    class="form-control"
                                    v-model="attrValue[a.code]"
                                    type="number"
                                    step="0.01"
                                />

                                <input
                                    v-else-if="a.type === 'color'"
                                    class="form-control form-control-color"
                                    v-model="attrValue[a.code]"
                                    type="color"
                                />

                                <select
                                    v-else-if="a.type === 'boolean'"
                                    class="form-select"
                                    v-model="attrValue[a.code]"
                                >
                                    <option :value="''">--</option>
                                    <option :value="1">Yes</option>
                                    <option :value="0">No</option>
                                </select>

                                <select
                                    v-else-if="a.type === 'select'"
                                    class="form-select"
                                    v-model="attrValue[a.code]"
                                >
                                    <option :value="''">-- Select --</option>
                                    <option
                                        v-for="o in a.options || []"
                                        :key="o.id"
                                        :value="o.value"
                                    >
                                        {{ o.value }}
                                    </option>
                                </select>

                                <input
                                    v-else
                                    class="form-control"
                                    v-model="attrValue[a.code]"
                                    type="text"
                                />

                                <div class="text-danger">
                                    {{ form.errors["attr_" + a.code] }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <SelectInputComponent
                                class="mb-3 col-md-6"
                                id="primary_category_id"
                                label="Primary Category (optional)"
                                :options="categories"
                                option-label="title"
                                option-value="id"
                                v-model="form.primary_category_id"
                            />
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Categories</label>
                                <select
                                    class="form-select"
                                    v-model="form.categories"
                                    multiple
                                    size="6"
                                >
                                    <option
                                        v-for="c in categories"
                                        :key="c.id"
                                        :value="c.id"
                                    >
                                        {{ c.title }}
                                    </option>
                                </select>
                                <div class="text-muted small">
                                    Hold Ctrl / Cmd to multi-select
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-3">
                                <label class="form-label">Price</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    class="form-control"
                                    v-model="form.price"
                                />
                                <div class="text-danger">
                                    {{ form.errors.price }}
                                </div>
                            </div>

                            <div class="mb-3 col-md-3">
                                <label class="form-label">Sale Price</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    class="form-control"
                                    v-model="form.sale_price"
                                />
                                <div class="text-danger">
                                    {{ form.errors.sale_price }}
                                </div>
                            </div>

                            <div class="mb-3 col-md-2">
                                <label class="form-label">Sort</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    v-model="form.sort_order"
                                />
                                <div class="text-danger">
                                    {{ form.errors.sort_order }}
                                </div>
                            </div>
                        </div>

                        <!-- Discount -->
                        <div class="mb-3 col-md-6">
                            <label class="form-label d-block"
                                >Apply Discount</label
                            >
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    id="discount_no"
                                    value="0"
                                    v-model="form.discount_status"
                                />
                                <label
                                    class="form-check-label"
                                    for="discount_no"
                                    >No</label
                                >
                            </div>
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    id="discount_yes"
                                    value="1"
                                    v-model="form.discount_status"
                                />
                                <label
                                    class="form-check-label"
                                    for="discount_yes"
                                    >Yes</label
                                >
                            </div>
                            <div class="text-danger">
                                {{ form.errors.discount_status }}
                            </div>

                            <div
                                v-if="form.discount_status === '1'"
                                class="mt-2"
                            >
                                <label class="form-label">Discount</label>
                                <div class="input-group">
                                    <select
                                        class="form-select"
                                        v-model="form.discount_type"
                                    >
                                        <option value="">
                                            -- Select Type --
                                        </option>
                                        <option value="percent">
                                            Percentage (%)
                                        </option>
                                        <option value="amount">
                                            Amount (LKR)
                                        </option>
                                    </select>
                                    <input
                                        type="number"
                                        step="0.01"
                                        class="form-control"
                                        v-model="form.discounted_amount"
                                    />
                                </div>
                                <div class="text-danger">
                                    {{
                                        form.errors.discount_type ||
                                        form.errors.discounted_amount
                                    }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label"
                                    >Short Description</label
                                >
                                <textarea
                                    class="form-control"
                                    rows="3"
                                    v-model="form.short_description"
                                ></textarea>
                                <div class="text-danger">
                                    {{ form.errors.short_description }}
                                </div>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Description</label>
                                <textarea
                                    class="form-control"
                                    rows="6"
                                    v-model="form.description"
                                ></textarea>
                                <div class="text-danger">
                                    {{ form.errors.description }}
                                </div>
                            </div>
                        </div>

                        <!-- Images -->
                        <div class="row mt-2">
                            <div class="mb-3 col-md-12">
                                <label class="form-label">Images</label>
                                <input
                                    type="file"
                                    class="form-control"
                                    multiple
                                    accept="image/*"
                                    @change="onFiles"
                                />

                                <!-- previews (no broken image icon) -->
                                <div class="mt-2 d-flex gap-2 flex-wrap">
                                    <div
                                        v-for="(p, i) in previewItems"
                                        :key="p.key"
                                        class="position-relative"
                                    >
                                        <img
                                            v-if="p.url"
                                            :src="p.url"
                                            @error="hideBroken(i)"
                                            style="
                                                height: 70px;
                                                width: 70px;
                                                object-fit: cover;
                                                border: 1px solid #eee;
                                                padding: 2px;
                                                border-radius: 6px;
                                            "
                                        />
                                        <button
                                            v-if="p.canRemove"
                                            type="button"
                                            class="btn btn-sm btn-danger position-absolute"
                                            style="
                                                top: -8px;
                                                right: -8px;
                                                border-radius: 999px;
                                                padding: 0 6px;
                                                line-height: 18px;
                                            "
                                            @click="removeSelectedImage(i)"
                                            title="Remove"
                                        >
                                            ×
                                        </button>
                                    </div>
                                </div>

                                <div class="text-danger">
                                    {{ form.errors.images }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button
                                type="submit"
                                class="btn btn-main me-2"
                                :disabled="form.processing"
                            >
                                {{ product ? "Update" : "Save" }}
                            </button>
                            <Link
                                class="btn btn-outline-danger"
                                :href="route('product.index')"
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
import SelectInputComponent from "@/Components/SelectInputComponent.vue";

export default {
    components: { Link, AppLayout, SelectInputComponent },
    props: {
        product: Object,
        brands: Array,
        uoms: Array,
        categories: Array,
        attributeSets: Array,
        attributes: Array,
        images: Array, // existing image urls (edit)
    },
    data() {
        return {
            form: useForm({
                id: "",
                name: "",
                sku: "",
                status: "1",
                sort_order: 0,
                brand_id: "",
                uom_id: "",
                attribute_set_id: "",
                primary_category_id: "",
                categories: [],
                price: "",
                sale_price: "",
                short_description: "",
                description: "",
                attributes_map: [],
                images: [],

                discount_status: "0",
                discount_type: "",
                discounted_amount: "",
            }),

            // { code: value }
            attrValue: {},

            // selected file list for upload
            selectedFiles: [],
            selectedPreviews: [],

            // existing images from server
            existingPreviews: (this.images || []).filter(Boolean),
            brokenIndexes: new Set(),
        };
    },

    computed: {
        previewItems() {
            // existing images (cannot remove here unless you implement delete on backend)
            const existing = (this.existingPreviews || []).map((url, idx) => ({
                key: "ex-" + idx,
                url,
                canRemove: false,
            }));

            // new selected files (removable)
            const selected = (this.selectedPreviews || []).map((url, idx) => ({
                key: "new-" + idx,
                url: this.brokenIndexes.has(idx) ? "" : url,
                canRemove: true,
            }));

            return [...existing, ...selected];
        },
    },

    mounted() {
        if (this.product) {
            const p = this.product;
            this.form.id = p.id;
            this.form.name = p.name ?? "";
            this.form.sku = p.sku ?? "";
            this.form.status = String(p.status ?? "1");
            this.form.sort_order = p.sort_order ?? 0;
            this.form.brand_id = p.brand_id ?? "";
            this.form.uom_id = p.uom_id ?? "";
            this.form.attribute_set_id = p.attribute_set_id ?? "";
            this.form.primary_category_id = p.primary_category_id ?? "";
            this.form.categories = (p.categories || []).map((c) => c.id);
            this.form.price = p.price ?? "";
            this.form.sale_price = p.sale_price ?? "";
            this.form.short_description = p.short_description ?? "";
            this.form.description = p.description ?? "";

            this.form.discount_status = String(p.discount_status ?? "0");
            this.form.discount_type = p.discount_type ?? "";
            this.form.discounted_amount = p.discounted_amount ?? "";

            // load saved JSON {code:value}
            const saved = p.attributes_json || {};
            Object.keys(saved).forEach(
                (code) => (this.attrValue[code] = saved[code])
            );
        } else {
            // ✅ auto generate SKU on create
            this.generateSku();
        }

        if (
            this.form.attribute_set_id &&
            (!this.attributes || !this.attributes.length)
        ) {
            this.reloadAttributes(this.form.attribute_set_id);
        }
    },

    watch: {
        "form.attribute_set_id"(newVal, oldVal) {
            if (newVal === oldVal) return;

            if (!newVal) {
                this.attrValue = {};
                return;
            }

            this.reloadAttributes(newVal);
        },

        attributes: {
            immediate: true,
            handler(newAttrs) {
                const next = {};
                (newAttrs || []).forEach((a) => {
                    next[a.code] = this.attrValue[a.code] ?? "";
                });
                this.attrValue = next;
            },
        },
    },

    methods: {
        generateSku() {
            // example: HW-9K3D7P2Q (frontend only; server will enforce uniqueness too)
            const part = Math.random().toString(36).slice(2, 10).toUpperCase();
            this.form.sku = "HW-" + part;
        },

        reloadAttributes(setId) {
            const url = this.product
                ? route("product.edit", this.product.id)
                : route("product.create");

            this.$inertia.get(
                url,
                { attribute_set_id: setId },
                {
                    preserveState: true,
                    preserveScroll: true,
                    replace: true,
                    only: ["attributes"],
                }
            );
        },

        onFiles(e) {
            // clear old object URLs
            (this.selectedPreviews || []).forEach((u) => {
                try {
                    URL.revokeObjectURL(u);
                } catch (_) {}
            });

            const files = Array.from(e.target.files || []);
            this.selectedFiles = files;
            this.form.images = files;

            this.brokenIndexes = new Set();
            this.selectedPreviews = files.map((f) => URL.createObjectURL(f));
        },

        removeSelectedImage(previewIndex) {
            // previewIndex is in combined list; remove only from new ones
            // existing previews first => count them
            const existingCount = (this.existingPreviews || []).length;
            const idx = previewIndex - existingCount;

            if (idx < 0) return;

            const newFiles = [...this.selectedFiles];
            const newPrevs = [...this.selectedPreviews];

            const removedPrev = newPrevs[idx];
            if (removedPrev) {
                try {
                    URL.revokeObjectURL(removedPrev);
                } catch (_) {}
            }

            newFiles.splice(idx, 1);
            newPrevs.splice(idx, 1);

            this.selectedFiles = newFiles;
            this.selectedPreviews = newPrevs;
            this.form.images = newFiles;
        },

        hideBroken(i) {
            this.brokenIndexes.add(i);
        },

        submit() {
            // pack attributes_map [{attribute_id,value}] so backend saves {code:value}
            this.form.attributes_map = (this.attributes || []).map((a) => ({
                attribute_id: a.id,
                value: this.attrValue[a.code] ?? null,
            }));

            const url = this.product
                ? route("product.update")
                : route("product.store");

            this.form.post(url, {
                forceFormData: true,
                onSuccess: () => {
                    this.form.reset();
                    this.attrValue = {};
                    this.selectedFiles = [];
                    (this.selectedPreviews || []).forEach((u) => {
                        try {
                            URL.revokeObjectURL(u);
                        } catch (_) {}
                    });
                    this.selectedPreviews = [];
                },
            });
        },
    },
};
</script>
