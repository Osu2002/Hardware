<template>
    <section class="contact-section py-5">
        <div class="container">
            <div class="row gx-5">
                <!-- 1) Get in Touch Form -->
                <div class="col-12 col-md-6 mb-4 order-md-1">
                    <h2 class="section-title">Get in Touch</h2>
                    <p class="section-subtitle">
                        Have a question, feedback, or need assistance? Fill out the form below and our team will get
                        back to you within 24 hours. We’re here to help you every step of the way.
                    </p>

                    <form @submit.prevent="handleSubmit" class="form-grid">
                        <input v-model="form.name" type="text" class="form-control" placeholder="Name *" required />
                        <input v-model="form.email" type="email" class="form-control" placeholder="Email *" required />
                        <input v-model="form.phone" type="tel" class="form-control" placeholder="Phone number *"
                            required />
                        <select v-model="form.enquiry_type" class="form-select" required>
                            <option disabled value="">Inquiry Type?</option>
                            <option>General Question</option>
                            <option>Support</option>
                            <option>Partnership</option>
                        </select>
                        <textarea v-model="form.enquiry" class="form-control" placeholder="Message *" rows="5"
                            required></textarea>

                        <button type="submit" class="btn-send w-100" :disabled="loading">
                            {{ loading ? 'Sending...' : 'SEND' }}
                        </button>
                    </form>
                </div>

                <!--2) Contact Information Card -->
                <div class="col-12 col-md-6 mb-4 order-md-2">
                    <div class="contact-info p-4 w-100">
                        <h3 class="text-white mb-2">Contact Information</h3>
                        <p class="say mb-4">
                            Say something to start a chat!
                        </p>

                        <ul class="content-list mb-0">
                            <div v-if="$page.props.countries && $page.props.countries.length" class="countries-grid">
                                <div v-for="country in $page.props.countries" :key="country.id" class="contact-item">
                                    <div class="contacts">
                                        <!-- 1) Country header -->
                                        <div class="country-header">
                                            <img :src="country?.media?.length > 0 ? country.media[0]?.original_url : ''"
                                                height="20" width="30" :alt="`${country.name} Flag`" />
                                            <span class="country-name"><strong>{{ country.name }}</strong></span>
                                        </div>

                                        <!-- 2) Primary phone -->
                                        <span class="contact-detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2
           19.79 19.79 0 0 1-8.63-3.07
           19.5 19.5 0 0 1-6-6
           19.79 19.79 0 0 1-3.07-8.67
           A2 2 0 0 1 4.11 2h3
           a2 2 0 0 1 2 1.72
           12.84 12.84 0 0 0 .7 2.81
           2 2 0 0 1-.45 2.11L8.09 9.91
           a16 16 0 0 0 6 6l1.27-1.27
           a2 2 0 0 1 2.11-.45
           12.84 12.84 0 0 0 2.81.7
           A2 2 0 0 1 22 16.92z"></path>
                                            </svg>
                                            <a :href="`tel:${country.phone1}`" class="contact-link text-white">
                                                {{ country.phone1 }}
                                            </a>
                                        </span>

                                        <!-- 3) Secondary phone (if any) -->
                                        <span v-if="country.phone2" class="contact-detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2
           19.79 19.79 0 0 1-8.63-3.07
           19.5 19.5 0 0 1-6-6
           19.79 19.79 0 0 1-3.07-8.67
           A2 2 0 0 1 4.11 2h3
           a2 2 0 0 1 2 1.72
           12.84 12.84 0 0 0 .7 2.81
           2 2 0 0 1-.45 2.11L8.09 9.91
           a16 16 0 0 0 6 6l1.27-1.27
           a2 2 0 0 1 2.11-.45
           12.84 12.84 0 0 0 2.81.7
           A2 2 0 0 1 22 16.92z"></path>
                                            </svg>
                                            <a :href="`tel:${country.phone2}`" class="contact-link text-white">
                                                {{ country.phone2 }}
                                            </a>
                                        </span>

                                        <!-- 4) Email -->
                                        <span class="contact-detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 4h16c1.1 0 2 .9 2 2v12
               c0 1.1-.9 2-2 2H4
               c-1.1 0-2-.9-2-2V6
               c0-1.1.9-2 2-2z"></path>
                                                <polyline points="22,6 12,13 2,6"></polyline>
                                            </svg>
                                            <a :href="`mailto:${country.email}`" class="contact-link text-white">
                                                {{ country.email }}
                                            </a>
                                        </span>

                                        <!-- 5) Location -->
                                        <span class="contact-detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13
               a9 9 0 0 1 18 0z"></path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                            <span>{{ country.location }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <p class="text-grey-600">No countries available.</p>
                            </div>
                        </ul>
                    </div>
                </div>

                <!-- 3) Full-width Map -->
                <div class="col-12 order-md-3">
                    <div class="map-container rounded overflow-hidden">
                        <!-- iframe map as before -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import Swal from 'sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

export default {
    name: "ContactSection",
    props: {
        countries: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            form: {
                name: "",
                email: "",
                phone: "",
                enquiry_type: "",
                enquiry: "",
            },
            loading: false,
            errorMessage: "",
        };
    },
    methods: {
        async handleSubmit() {
            try {
                this.loading = true
                this.errorMessage = ''

                const response = await axios.post('/submit-contact2', this.form)

                if (response.data.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Thank you! Your message has been sent.',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    })

                    this.form = {
                        name: '',
                        email: '',
                        phone: '',
                        enquiry_type: '',
                        enquiry: ''
                    }
                }
            } catch (error) {
                console.error('Submission error:', error)

                // build a message
                if (error.response?.data?.message) {
                    this.errorMessage = error.response.data.message
                    if (error.response.data.errors) {
                        this.errorMessage += ': ' + Object
                            .values(error.response.data.errors)
                            .flat()
                            .join(', ')
                    }
                } else {
                    this.errorMessage = 'Sorry, there was an error submitting your form.'
                }

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: this.errorMessage,
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true
                })
            } finally {
                this.loading = false
            }
        }
    },
};
</script>

<style scoped>
.countries-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

/* SECTION LAYOUT */
.contact-section {
    background: #fff;
}

.section-title {
    font-size: 1.75rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.say {
    color: #d8dfe9c0;
}

.section-subtitle {
    color: #666;
    margin-bottom: 1.5rem;
}

/* FORM */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-grid .form-control,
.form-grid .form-select {
    grid-column: span 2;
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
    border: 1px solid #ccc;
    box-shadow: none;
}

.form-grid textarea.form-control {
    grid-column: span 2;
    resize: vertical;
    min-height: 100px;
}

.form-grid .form-control:focus,
.form-grid .form-select:focus {
    outline: none;
    border-color: #050b20;
}

.form-grid select.form-select {
    appearance: none;
}

.btn-send {
    grid-column: span 2;
    background-color: #050b20;
    color: #fff;
    border: none;
    padding: 0.75rem;
    border-radius: 0.375rem;
    font-weight: 500;
    transition: background 0.2s;
}

.btn-send:hover {
    background-color: #03101a;
}

/* CONTACT INFO CARD */
.contact-info {
    background-color: #050b20;
    border-radius: 1rem;
    padding: 2.2rem;
    max-width: 100%;
    margin: 0 auto;
    display: grid;
}

/* two-column grid for countries */
.countries-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.country-contact {
    width: 100%;
}

.contact-item {
    color: #fff;
}

.country-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.country-name {
    font-size: 1.1rem;
}

.contact-detail {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.95rem;
    margin-bottom: 5px;
}

.contact-link {
    color: #8fa5c1;
    text-decoration: none;
}

.contact-link:hover {
    text-decoration: underline;
}

/* MAP */
.map-container {
    margin-top: 1.5rem;
}
</style>