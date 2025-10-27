<template>
    <div>
        <!-- Trigger Button -->
       <button @click="openModal" class="secondary-btn w-100">
         Inquiry
  
        </button>

        <teleport to="body">
        <!-- Modal Overlay -->
        <transition name="fade">
            <div v-if="showModal" class="modal-backdrop" @click.self="closeModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Inquiry</h5>
                            <button type="button" class="btn-close" @click="closeModal">×</button>
                        </div>
                        <div class="modal-body">
                            <section class="container mt-0">
                                <form class="row g-3" @submit.prevent="submitEnquiry">
                                    <!-- form fields -->
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="form-control rounded-0" v-model="form.vehicle_name"
                                            disabled />
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="form-control" placeholder="Your Name"
                                            v-model="form.name" />
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input type="email" class="form-control" placeholder="Your Email"
                                            v-model="form.email" />
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="form-control" placeholder="Your Mobile Number"
                                            v-model="form.mobile" />
                                    </div>
                                    <div class="col-12">
                                        <select class="form-select" v-model="form.purchase_time">
                                            <option value="" disabled selected>When Are You Hoping To Purchase?</option>
                                            <option value="Immediately">Immediately</option>
                                            <option value="Next Week">Next Week</option>
                                            <option value="Two Weeks">Two Weeks</option>
                                            <option value="One Month">One Month</option>
                                            <option value="Not Sure">Not Sure</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <select class="form-select" v-model="form.payment_method">
                                            <option value="" disabled selected>Payment Method</option>
                                            <option value="Full Lease">Full Lease</option>
                                            <option value="Half Lease">Half Lease</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Loan">Loan</option>
                                            <option value="Half Loan">Half Loan</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <textarea class="form-control" placeholder="Your Address"
                                            v-model="form.address"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <textarea class="form-control" placeholder="Your Message" rows="3"
                                            v-model="form.message"></textarea>
                                    </div>
                                    <div class="col-12 text-center">
                                        <Checkbox as="div" v-model="form.capchaResponse" />
                                        <div :class="capchaVerifyStyles" class="mt-2">
                                            {{ form.capchaResponse ? 'Verified' : 'Please click the captcha' }}
                                        </div>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="reset" class="btn btn-dark rounded-0 mx-1">RESET</button>
                                        <button type="submit" class="btn btn-primary rounded-0 mx-1">SUBMIT
                                            ENQUIRY</button>
                                    </div>
                                </form>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
        </teleport>
    </div>
</template>

<script>
import { useForm } from '@inertiajs/inertia-vue3';
import Swal from 'sweetalert2';
import { Checkbox } from 'vue-recaptcha';

export default {
    components: { Checkbox },
    data() {
        return {
            showModal: false,
            form: useForm({
                vehicle_name:
                    this.$page.props.vehicle.manufacture.title + ' ' +
                    this.$page.props.vehicle.vehicle_model.title + ' ',
                vehicle_url: route('product', {
                    model: this.$root.stringToSlug(this.$page.props.vehicle.manufacture.title),
                    slug:
                        this.$root.stringToSlug(this.$page.props.vehicle.manufacture.title) +
                        '-' +
                        this.$root.stringToSlug(this.$page.props.vehicle.vehicle_model.title),
                    id: this.$page.props.vehicle.id,
                }),
                vehicle_id: this.$page.props.vehicle.id,
                stock_type: 'local',
                name: '',
                email: '',
                mobile: '',
                purchase_time: '',
                payment_method: '',
                address: '',
                message: '',
                capchaResponse: null,
            }),
        };
    },
    watch: {
        showModal(val) {
            document.body.style.overflow = val ? 'hidden' : '';
        }
    },
    methods: {
        openModal() {
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
        },
        submitEnquiry() {
            if (this.form.capchaResponse) {
                this.form.post(route('submit-inquiry'), {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.form.reset(
                            'name',
                            'email',
                            'mobile',
                            'purchase_time',
                            'payment_method',
                            'address',
                            'message'
                        );
                        Swal.fire({
                            icon: 'success',
                            title: 'Inquiry Submitted Successfully',
                            showConfirmButton: false,
                            timer: 1500,
                        });
                        this.closeModal();
                    },
                    onError: () => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Something went wrong! Please try again later',
                            showConfirmButton: false,
                            timer: 1500,
                        });
                    }
                });
            }
        }
    },
    computed: {
        capchaVerifyStyles() {
            return this.form.capchaResponse ? 'text-success' : 'text-danger';
        }
    },
    beforeUnmount() {
        document.body.style.overflow = '';
    }
};
</script>

<style scoped>
/* Contact Button */
.secondary-btn{
    position: relative;
    bottom: auto;
    width: auto;
    margin: 1rem auto 0;  
    
   
    font-size: clamp(0.9rem, 2.5vw, 1rem);
    font-weight: 500;
    border: none;
    border-radius: 2rem;
    background: linear-gradient(90deg, #0D1B2A 0%, #232E3B 20%, #1E1E1E 40%, #273B51 60%, #222B35 80%, #26384D 100%);
    color: #fff;
    cursor: pointer;
    transition: box-shadow 0.3s ease, transform 0.3s ease;

    display: inline-flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    border-radius: 20px;
     padding: 0.6rem 1.2rem;     
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;

}

.contact-btn:hover {
    box-shadow: 0 0.5rem 1rem rgba(29, 140, 248, 0.5);
    transform: scale(1.05);
}

@media (max-width: 576px) {
    .contact-btn {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
}

.modal-backdrop {
    /* keep full-screen, dark overlay */
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.7);
    /* z-index: 9999; */

    /* center the dialog, but leave space up top */
    display: flex;
    align-items: center;
    /* vertical centering within padded area */
    justify-content: center;
    /* horizontal centering */
    padding: 5rem 1rem 1rem;
    /* top right/bottom/left */

    overflow-y: auto;
}


/* Modal Dialog */
.modal-dialog {
    width: 100%;
    max-width: 640px;
    margin: 0 auto;
    max-height: calc(100vh - 4rem);
    display: flex;
    align-items: center;
    justify-content: center;
}

@media (min-width: 768px) {
    .modal-dialog {
        max-width: 720px;
    }
}

@media (min-width: 1024px) {
    .modal-dialog {
        max-width: 800px;
    }
}

/* Modal Content */
.modal-content {
    background: #fff;
    border-radius: 0.5rem;
    overflow: hidden;
    animation: fadeInDown 0.3s ease-out;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - 4rem);
    width: 100%;
    z-index: 1050;
}

/* Modal Header */
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1rem;
    background: #f8f9fa;
}

.modal-title {
    margin: 0;
    font-size: clamp(1rem, 2.5vw, 1.25rem);
}

.btn-close {
    background: transparent;
    border: none;
    font-size: 1.25rem;
    line-height: 1;
    cursor: pointer;
    padding: 0.5rem;
}

/* Modal Body */
.modal-body {
    overflow-y: auto;
    flex: 1 1 auto;
    padding: 1rem;
    max-height: calc(100vh - 10rem);
}

@media (min-width: 768px) {
    .modal-body {
        padding: 1.5rem;
    }
}

/* Form Controls */
.form-control,
.form-select {
    box-shadow: none !important;
    border-radius: 0;
    font-size: clamp(0.85rem, 2vw, 0.95rem);
}

.form-control::placeholder,
.form-select:invalid {
    color: #6c757d;
    opacity: 0.7;
}

/* Buttons in Form */
.btn {
    padding: 0.5rem 1rem;
    font-size: clamp(0.85rem, 2vw, 0.95rem);
}

@media (max-width: 576px) {
    .btn {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
    }
}

/* Animation */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Transition wrapper */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Responsive Form Layout */
.row.g-3 {
    margin-right: -0.5rem;
    margin-left: -0.5rem;
}

.row.g-3>* {
    padding-right: 0.5rem;
    padding-left: 0.5rem;
}

/* small-screen tweaks */
@media (max-width: 576px) {
    .modal-backdrop {
        display: flex;
        align-items: flex-start;
        /* stack from the top, not true center */
        justify-content: center;
        /* still center horizontally */
        /* nav height (~4rem) + gap (2rem) = 6rem */
        padding: 6rem 0.5rem 1rem;
        /* top right&left bottom */
    }

    .modal-dialog {
        width: 100%;
        max-width: 95%;
        /* leave a little breathing room on sides */
        margin: 0 auto;
        /* center horizontally */
    }

    .modal-content {
        max-height: calc(100vh - 8rem);
        /* allow body to scroll if it’s tall */
        border-radius: 0.25rem;
        /* slightly tighter corners on mobile */
    }

    /* keep header (with the close button) “stuck” under the navbar */
    .modal-header {
        position: sticky;
        top: 0;
        background: #fff;
        /* match content background */
        z-index: 10;
        padding-top: 1rem;
        /* extra breathing room if you like */
    }
}

/* push the inquiry box down by nav-height + gap at all sizes */
.modal-backdrop .modal-dialog {
    margin: calc(4rem + 1rem) auto 1rem !important;
    /* ↑ 4rem navbar height + 1rem breathing room */
}

/* keep it from getting too wide on small phones */
@media (max-width: 576px) {
    .modal-backdrop .modal-dialog {
        max-width: 95%;
    }
}



@media (min-width: 992px) and (max-width: 1197px) {
  /* keep pill full-width, same padding/font as WhatsApp */
  .secondary-btn.w-100 {
    width: 100% !important;
    padding: 10px 10px !important;
    font-size: .6rem !important;
    font-weight: 600 !important;
  }
  /* remove its little arrow icon if you like */
  .secondary-btn.w-100 .arrow-icon_1 {
    display: none !important;
  }
}


@media (min-width: 1199px) and (max-width: 1400px) {
  .secondary-btn.w-100 {
    width: 100% !important;
    padding: 10px 10px !important;
    font-size: .8rem !important;
    font-weight: 600 !important;
  }
  /* remove its little arrow icon if you like */
  .secondary-btn.w-100 .arrow-icon_1 {
    display: none !important;
  }
}


</style>