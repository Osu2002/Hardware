<template>
  <section class="contact-page">
    <!-- HERO SECTION -->
    <header class="hero">
      <div class="container">
        <div class="hero-grid">
          <!-- Left Column -->
          <div class="hero-content">
            <div class="hero-badge">
              <span class="badge-dot"></span>
              <span>Hardware • Tools • Supplies</span>
            </div>
            <h1 class="hero-title">Let's Start a Conversation</h1>
            <p class="hero-description">
              Whether you need pricing, product availability, technical guidance, or delivery information—
              our team is here to help. Reach out and we'll respond within 24 hours.
            </p>
            
            <!-- Quick Topics -->
            <div class="quick-topics">
              <p class="topics-label">Popular inquiries:</p>
              <div class="topic-chips">
                <button 
                  v-for="topic in quickTopics" 
                  :key="topic"
                  class="topic-chip" 
                  type="button" 
                  @click="prefill(topic)"
                >
                  {{ topic }}
                </button>
              </div>
            </div>

            <!-- Trust Indicators -->
            <div class="trust-badges">
              <div class="trust-item">
                <IconCheck :size="20" />
                <span>Genuine parts & accessories</span>
              </div>
              <div class="trust-item">
                <IconCheck :size="20" />
                <span>Same-day dispatch available</span>
              </div>
              <div class="trust-item">
                <IconCheck :size="20" />
                <span>Comprehensive warranty support</span>
              </div>
            </div>
          </div>

          <!-- Right Column - Contact Card -->
          <div class="contact-card">
            <div class="card-header">
              <div class="status-badge">
                <span class="status-pulse"></span>
                <span>Available Now</span>
              </div>
              <h3>Quick Contact</h3>
              <p>Choose your preferred way to reach us</p>
            </div>

            <div class="contact-methods">
              <a :href="`tel:${contact.phonePrimary}`" class="contact-method">
                <div class="method-icon phone">
                  <IconPhone :size="22" />
                </div>
                <div class="method-content">
                  <span class="method-label">Phone</span>
                  <span class="method-value">{{ contact.phonePrimary }}</span>
                </div>
                <IconArrow :size="18" />
              </a>

              <a :href="whatsAppLink" target="_blank" rel="noopener" class="contact-method">
               <div class="method-icon whatsapp">
  <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" style="display:block" aria-hidden="true">
    <path d="M12 2a10 10 0 0 0-8.68 14.98L2 22l5.2-1.31A10 10 0 1 0 12 2Zm0 18.2a8.2 8.2 0 0 1-4.18-1.14l-.3-.18-3.07.77.82-2.98-.2-.31A8.2 8.2 0 1 1 12 20.2Z"/>
    <path d="M16.7 13.9c-.2-.1-1.2-.6-1.4-.7-.2-.1-.4-.1-.6.1-.2.2-.7.7-.8.9-.1.2-.3.2-.5.1a6.73 6.73 0 0 1-3-2.6c-.1-.2 0-.4.1-.5l.4-.4c.1-.1.2-.3.3-.4.1-.2 0-.3 0-.5 0-.1-.6-1.4-.8-1.9-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2 0 1.2.8 2.3.9 2.5.1.2 1.6 2.6 4 3.6.6.3 1 .4 1.4.5.6.2 1.2.2 1.6.1.5-.1 1.2-.5 1.4-1 .2-.5.2-1 .1-1.1-.1-.1-.3-.2-.5-.3Z"/>
  </svg>
</div>

                <div class="method-content">
                  <span class="method-label">WhatsApp</span>
                  <span class="method-value">{{ contact.whatsapp }}</span>
                </div>
                <IconArrow :size="18" />
              </a>

              <a :href="`mailto:${contact.email}`" class="contact-method">
                <div class="method-icon email">
                  <IconMail :size="22" />
                </div>
                <div class="method-content">
                  <span class="method-label">Email</span>
                  <span class="method-value">{{ contact.email }}</span>
                </div>
                <IconArrow :size="18" />
              </a>
            </div>

            <div class="card-footer">
              <div class="hours-info">
                <IconClock :size="20" />
                <div>
                  <div class="hours-label">Business Hours</div>
                  <div class="hours-value">{{ contact.hours }}</div>
                </div>
              </div>
              <div class="response-badge">
                <span class="response-time">~24h</span>
                <span class="response-label">Avg. response</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- FORM & INFO SECTION -->
    <div class="container">
      <div class="content-grid">
        <!-- Form Column -->
        <div class="form-wrapper">
          <div class="form-header">
            <h2>Send Us a Message</h2>
            <p>Fill out the form below with your inquiry details. The more specific you are, the faster we can help.</p>
          </div>

          <form class="contact-form" @submit.prevent="handleSubmit">
            <div class="form-row">
              <div class="form-field">
                <label class="field-label">Full Name <span class="required">*</span></label>
                <input 
                  v-model.trim="form.name" 
                  type="text" 
                  class="field-input" 
                  placeholder="John Doe"
                  required 
                />
              </div>

              <div class="form-field">
                <label class="field-label">Email Address <span class="required">*</span></label>
                <input 
                  v-model.trim="form.email" 
                  type="email" 
                  class="field-input" 
                  placeholder="john@example.com"
                  required 
                />
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="field-label">Phone Number <span class="required">*</span></label>
                <input 
                  v-model.trim="form.phone" 
                  type="tel" 
                  class="field-input" 
                  placeholder="+94 7X XXX XXXX"
                  required 
                />
              </div>

              <div class="form-field">
                <label class="field-label">Inquiry Type <span class="required">*</span></label>
                <div class="select-wrapper">
                  <select v-model="form.enquiry_type" class="field-select" required>
                    <option disabled value="">Select a category</option>
                    <option>Pricing / Quotation</option>
                    <option>Product Availability</option>
                    <option>Bulk / Wholesale</option>
                    <option>Technical Support</option>
                    <option>Delivery & Warranty</option>
                    <option>Other</option>
                  </select>
                  <IconChevron :size="16" />
                </div>
              </div>
            </div>

            <div class="form-field">
              <label class="field-label">Subject</label>
              <input 
                v-model.trim="form.subject" 
                type="text" 
                class="field-input" 
                placeholder="Brief description of your inquiry"
              />
            </div>

            <div class="form-field">
              <label class="field-label">Message <span class="required">*</span></label>
              <textarea
                v-model.trim="form.enquiry"
                class="field-textarea"
                rows="6"
                placeholder="Please include item names, quantities, preferred brands, delivery location, and any other relevant details..."
                required
              ></textarea>
            </div>

            <div class="form-footer">
              <label class="checkbox-label">
                <input v-model="form.consent" type="checkbox" class="checkbox-input" required />
                <span class="checkbox-text">I agree to be contacted regarding this inquiry</span>
              </label>

              <button type="submit" class="submit-btn" :disabled="loading">
                <span v-if="!loading">Send Message</span>
                <span v-else class="loading-state">
                  <span class="spinner"></span>
                  Sending...
                </span>
              </button>
            </div>

            <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
          </form>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar">
          <!-- Info Card -->
          <div class="info-card">
            <h3 class="info-title">Contact Details</h3>
            <p class="info-subtitle">Multiple ways to reach our team</p>

            <div class="info-items">
              <a :href="`tel:${contact.phonePrimary}`" class="info-item">
                <div class="info-icon">
                  <IconPhone :size="20" />
                </div>
                <div class="info-content">
                  <span class="info-label">Call Us</span>
                  <span class="info-value">{{ contact.phonePrimary }}</span>
                </div>
              </a>

              <a :href="whatsAppLink" target="_blank" rel="noopener" class="info-item">
                <div class="info-icon">
<IconWhatsApp :size="22" />
                </div>
                <div class="info-content">
                  <span class="info-label">WhatsApp</span>
                  <span class="info-value">{{ contact.whatsapp }}</span>
                </div>
              </a>

              <a :href="`mailto:${contact.email}`" class="info-item">
                <div class="info-icon">
                  <IconMail :size="20" />
                </div>
                <div class="info-content">
                  <span class="info-label">Email Us</span>
                  <span class="info-value">{{ contact.email }}</span>
                </div>
              </a>

              <div class="info-item no-hover">
                <div class="info-icon">
                  <IconPin :size="20" />
                </div>
                <div class="info-content">
                  <span class="info-label">Visit Us</span>
                  <span class="info-value">{{ contact.address }}</span>
                </div>
              </div>
            </div>

            <div class="info-divider"></div>

            <div class="info-features">
              <div class="feature-card">
                <IconShield :size="24" />
                <h4>Warranty Support</h4>
                <p>Full assistance for eligible products and replacements per policy.</p>
              </div>

              <div class="feature-card">
                <IconTruck :size="24" />
                <h4>Islandwide Delivery</h4>
                <p>Fast shipping available across Sri Lanka based on location.</p>
              </div>
            </div>
          </div>

          <!-- FAQ Card -->
          <div class="faq-card">
            <h3 class="faq-title">Frequently Asked</h3>
            
            <details class="faq-item" open>
              <summary>How do I request a quotation?</summary>
              <p>Select "Pricing / Quotation" as your inquiry type and list all items with quantities and your delivery location. We'll send a detailed quote within 24 hours.</p>
            </details>

            <details class="faq-item">
              <summary>Do you offer bulk discounts?</summary>
              <p>Yes! Choose "Bulk / Wholesale" and share your approximate volumes. We offer competitive pricing for large orders and regular business customers.</p>
            </details>

            <details class="faq-item">
              <summary>Can I get technical advice?</summary>
              <p>Absolutely. Select "Technical Support" and include model numbers and details about your use case. Our specialists will guide you to the right solution.</p>
            </details>

            <details class="faq-item">
              <summary>What's your delivery timeframe?</summary>
              <p>Delivery times vary by location and product availability. Same-day dispatch is available for in-stock items. We'll confirm exact timelines with your quote.</p>
            </details>
          </div>
        </aside>
      </div>

      <!-- MAP SECTION -->
      <div class="map-section">
        <div class="map-header">
          <div>
            <h2 class="map-title">Visit Our Store</h2>
            <p class="map-description">Stop by for hands-on product demonstrations and expert advice. Call ahead to confirm stock availability.</p>
          </div>
          <a :href="contact.mapUrl" target="_blank" rel="noopener" class="directions-btn">
            <IconPin :size="18" />
            Get Directions
          </a>
        </div>
        
        <div class="map-container">
          <iframe
            class="map-iframe"
            :src="contact.mapEmbed"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="Store location on map"
          ></iframe>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import axios from "axios";

/** -----------------------------
 *  Icon System (local components)
 *  ----------------------------- */
const iconProps = {
  size: { type: Number, default: 20 },
};

const IconBase = {
  props: iconProps,
  template: `
    <svg
      :width="size"
      :height="size"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
      aria-hidden="true"
      focusable="false"
    >
      <slot/>
    </svg>
  `,
};

const IconPhone = {
  props: iconProps,
  components: { IconBase },
  template: `
    <IconBase v-bind="$props">
      <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
    </IconBase>
  `,
};

const IconMail = {
  props: iconProps,
  components: { IconBase },
  template: `
    <IconBase v-bind="$props">
      <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
      <polyline points="22,6 12,13 2,6"/>
    </IconBase>
  `,
};

const IconPin = {
  props: iconProps,
  components: { IconBase },
  template: `
    <IconBase v-bind="$props">
      <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
      <circle cx="12" cy="10" r="3"/>
    </IconBase>
  `,
};

const IconWhatsApp = {
  props: { size: { type: Number, default: 20 } },
  template: `
    <svg
      :width="size"
      :height="size"
      viewBox="0 0 24 24"
      fill="currentColor"
      style="display:block"
      aria-hidden="true"
    >
      <path d="M12 2a10 10 0 0 0-8.68 14.98L2 22l5.2-1.31A10 10 0 1 0 12 2Zm0 18.2a8.2 8.2 0 0 1-4.18-1.14l-.3-.18-3.07.77.82-2.98-.2-.31A8.2 8.2 0 1 1 12 20.2Z"/>
      <path d="M16.7 13.9c-.2-.1-1.2-.6-1.4-.7-.2-.1-.4-.1-.6.1-.2.2-.7.7-.8.9-.1.2-.3.2-.5.1a6.73 6.73 0 0 1-3-2.6c-.1-.2 0-.4.1-.5l.4-.4c.1-.1.2-.3.3-.4.1-.2 0-.3 0-.5 0-.1-.6-1.4-.8-1.9-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2 0 1.2.8 2.3.9 2.5.1.2 1.6 2.6 4 3.6.6.3 1 .4 1.4.5.6.2 1.2.2 1.6.1.5-.1 1.2-.5 1.4-1 .2-.5.2-1 .1-1.1-.1-.1-.3-.2-.5-.3Z"/>
    </svg>
  `,
};

const IconClock = {
  props: iconProps,
  components: { IconBase },
  template: `
    <IconBase v-bind="$props">
      <circle cx="12" cy="12" r="10"/>
      <polyline points="12 6 12 12 16 14"/>
    </IconBase>
  `,
};

const IconTruck = {
  props: iconProps,
  components: { IconBase },
  template: `
    <IconBase v-bind="$props">
      <path d="M1 3h15v13H1z"/>
      <path d="M16 8h4l3 3v5h-7V8z"/>
      <circle cx="5.5" cy="18.5" r="2.5"/>
      <circle cx="18.5" cy="18.5" r="2.5"/>
    </IconBase>
  `,
};

const IconShield = {
  props: iconProps,
  components: { IconBase },
  template: `
    <IconBase v-bind="$props">
      <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
    </IconBase>
  `,
};

const IconCheck = {
  props: iconProps,
  components: { IconBase },
  template: `
    <IconBase v-bind="$props">
      <polyline points="20 6 9 17 4 12"/>
    </IconBase>
  `,
};

const IconArrow = {
  props: iconProps,
  components: { IconBase },
  template: `
    <IconBase v-bind="$props">
      <line x1="5" y1="12" x2="19" y2="12"/>
      <polyline points="12 5 19 12 12 19"/>
    </IconBase>
  `,
};

const IconChevron = {
  props: iconProps,
  components: { IconBase },
  template: `
    <IconBase v-bind="$props">
      <polyline points="6 9 12 15 18 9"/>
    </IconBase>
  `,
};

/** -----------------------------
 *  Component
 *  ----------------------------- */
export default {
  name: "ModernContactPage",
  components: {
    IconPhone,
    IconMail,
    IconPin,
    IconWhatsApp, // ✅ WhatsApp
    IconClock,
    IconTruck,
    IconShield,
    IconCheck,
    IconArrow,
    IconChevron,
  },
  data() {
    return {
      contact: {
        phonePrimary: "+94 71 552 6000",
        whatsapp: "+94 71 552 6000",
        email: "mahindahardware@gmail.com",
        address: "135/A, Rathnapura Rd, Hinguraara, Embilipitiya",
        hours: "Mon–Sat: 9:00 AM – 7:00 PM • Sun: 10:00 AM – 2:00 PM",
        mapEmbed: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4703.697073463813!2d80.84065749999999!3d6.347923499999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae401adf6ae3a4b%3A0x1595096ba73b5242!2sMahinda%20Hardware!5e1!3m2!1sen!2slk!4v1766386294385!5m2!1sen!2slk\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>",
        mapUrl: "https://maps.app.goo.gl/81BM4EkBTpUp3qY36",
      },
      quickTopics: [
        "Pricing / Quotation",
        "Bulk / Wholesale",
        "Technical Support",
        "Delivery & Warranty",
      ],
      form: {
        name: "",
        email: "",
        phone: "",
        enquiry_type: "",
        subject: "",
        enquiry: "",
        consent: false,
      },
      loading: false,
      errorMessage: "",
    };
  },
  computed: {
    whatsAppLink() {
      const digits = (this.contact.whatsapp || "").replace(/[^\d]/g, "");
      const text = encodeURIComponent("Hi! I have an inquiry about your hardware products.");
      return `https://wa.me/${digits}?text=${text}`;
    },
  },
  methods: {
    prefill(type) {
      this.form.enquiry_type = type;
      if (!this.form.subject) this.form.subject = `Inquiry: ${type}`;
      const form = document.querySelector(".contact-form");
      if (form) form.scrollIntoView({ behavior: "smooth", block: "start" });
    },
    async handleSubmit() {
      try {
        this.loading = true;
        this.errorMessage = "";
        const response = await axios.post("/submit-contact", this.form);

        if (response?.data?.success) {
          Swal.fire({
            icon: "success",
            title: "Message Sent!",
            text: "Thank you for reaching out. We'll get back to you within 24 hours.",
            confirmButtonColor: "#0f5132",
            confirmButtonText: "Great!",
          });

          this.form = {
            name: "",
            email: "",
            phone: "",
            enquiry_type: "",
            subject: "",
            enquiry: "",
            consent: false,
          };
        } else {
          throw new Error("Unexpected response");
        }
      } catch (error) {
        if (error.response?.data?.message) {
          this.errorMessage = error.response.data.message;
          if (error.response.data.errors) {
            this.errorMessage +=
              ": " + Object.values(error.response.data.errors).flat().join(", ");
          }
        } else {
          this.errorMessage =
            "Sorry, something went wrong. Please try again or contact us directly.";
        }

        Swal.fire({
          icon: "error",
          title: "Oops!",
          text: this.errorMessage,
          confirmButtonColor: "#0f5132",
        });
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
/* ========================================
   VARIABLES & BASE
======================================== */


:root{
  --app-font: "Abadi MT Condensed Light","Abadi MT Condensed","Abadi MT",Abadi,
    system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;
}

body{
  font-family: var(--app-font);
}

.contact-page {
  --primary: #160f51;
  --primary-light: #14643e;
  --primary-dark: #0a3d24;
  --accent: #d4a574;
  --text: #1a1a1a;
  --text-muted: #666666;
  --text-light: #999999;
  --bg: #fafaf9;
  --bg-card: #ffffff;
  --border: #e5e5e0;
  --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
  --shadow-lg: 0 12px 32px rgba(0, 0, 0, 0.12);
  
  background: var(--bg);
  color: var(--text);
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  line-height: 1.6;
  min-height: 100vh;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
}

/* ========================================
   HERO SECTION
======================================== */
.hero {
  padding: 80px 0 60px;
  background: linear-gradient(135deg, #fafaf9 0%, #f5f5f0 100%);
  border-bottom: 1px solid var(--border);
}

.hero-grid {
  display: grid;
  grid-template-columns: 1.1fr 0.9fr;
  gap: 48px;
  align-items: start;
}

.hero-content {
  padding-top: 20px;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 24px;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--text-muted);
  margin-bottom: 24px;
}

.badge-dot {
  width: 8px;
  height: 8px;
  background: var(--primary);
  border-radius: 50%;
  animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.hero-title {
  font-size: clamp(2.5rem, 5vw, 3.5rem);
  font-weight: 800;
  line-height: 1.1;
  margin: 0 0 20px;
  color: var(--text);
  letter-spacing: -0.02em;
}

.hero-description {
  font-size: 1.125rem;
  line-height: 1.7;
  color: var(--text-muted);
  margin: 0 0 32px;
  max-width: 600px;
}

/* Quick Topics */
.quick-topics {
  margin-bottom: 32px;
}

.topics-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-muted);
  margin: 0 0 12px;
}

.topic-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.topic-chip {
  padding: 10px 20px;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 24px;
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--text);
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.topic-chip:hover {
  background: var(--primary);
  color: white;
  border-color: var(--primary);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

/* Trust Badges */
.trust-badges {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 24px;
  background: rgba(15, 81, 50, 0.04);
  border-left: 3px solid var(--primary);
  border-radius: 8px;
}

.trust-item {
  display: flex;
  align-items: center;
  gap: 12px;
  color: var(--text);
  font-size: 0.95rem;
  font-weight: 500;
}

.trust-item svg {
  color: var(--primary);
  flex-shrink: 0;
}

/* Contact Card */
.contact-card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 32px;
  box-shadow: var(--shadow-lg);
  position: sticky;
  top: 24px;
}

.card-header {
  margin-bottom: 24px;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 14px;
  background: rgba(15, 81, 50, 0.08);
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: 16px;
}

.status-pulse {
  width: 8px;
  height: 8px;
  background: var(--primary);
  border-radius: 50%;
  animation: pulse 2s ease-in-out infinite;
}

.card-header h3 {
  font-size: 1.5rem;
  font-weight: 700;
  margin: 0 0 8px;
  color: var(--text);
}

.card-header p {
  font-size: 0.95rem;
  color: var(--text-muted);
  margin: 0;
}

/* Contact Methods */
.contact-methods {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 24px;
}

.contact-method {
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  gap: 14px;
  padding: 16px;
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 12px;
  text-decoration: none;
  color: var(--text);
  transition: all 0.2s ease;
}

.contact-method:hover {
  background: rgba(15, 81, 50, 0.04);
  border-color: var(--primary);
  transform: translateX(4px);
}

.method-icon {
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
  flex-shrink: 0;
}

.method-icon.phone {
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
}

.method-icon.whatsapp {
  background: rgba(34, 197, 94, 0.1);
  color: #22c55e;
}

.method-icon.email {
  background: rgba(249, 115, 22, 0.1);
  color: #f97316;
}

.method-content {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.method-label {
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.method-value {
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--text);
}

.contact-method svg:last-child {
  color: var(--text-light);
  transition: transform 0.2s ease;
}

.contact-method:hover svg:last-child {
  transform: translateX(4px);
}

/* Card Footer */
.card-footer {
  padding-top: 24px;
  border-top: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.hours-info {
  display: flex;
  align-items: start;
  gap: 12px;
  flex: 1;
}

.hours-info svg {
  color: var(--primary);
  margin-top: 2px;
}

.hours-label {
  font-size: 0.8rem;
  font-weight: 700;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 2px;
}

.hours-value {
  font-size: 0.85rem;
  color: var(--text);
  line-height: 1.5;
}

.response-badge {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 12px 16px;
  background: rgba(15, 81, 50, 0.08);
  border-radius: 10px;
}

.response-time {
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--primary);
  line-height: 1;
}

.response-label {
  font-size: 0.7rem;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-top: 4px;
}

/* ========================================
   CONTENT GRID
======================================== */
.content-grid {
  display: grid;
  grid-template-columns: 1.3fr 0.7fr;
  gap: 40px;
  margin: 60px 0;
}

/* ========================================
   FORM
======================================== */
.form-wrapper {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 40px;
  box-shadow: var(--shadow-sm);
}

.form-header {
  margin-bottom: 32px;
}

.form-header h2 {
  font-size: 1.875rem;
  font-weight: 700;
  margin: 0 0 12px;
  color: var(--text);
}

.form-header p {
  font-size: 1rem;
  color: var(--text-muted);
  margin: 0;
  line-height: 1.6;
}

.contact-form {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-field {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.field-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text);
  letter-spacing: 0.01em;
}

.required {
  color: #ef4444;
}

.field-input,
.field-select,
.field-textarea {
  width: 100%;
  padding: 14px 16px;
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 10px;
  font-size: 0.95rem;
  color: var(--text);
  transition: all 0.2s ease;
  font-family: inherit;
}

.field-input:focus,
.field-select:focus,
.field-textarea:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 4px rgba(15, 81, 50, 0.08);
  background: var(--bg-card);
}

.field-input::placeholder,
.field-textarea::placeholder {
  color: var(--text-light);
}

.field-textarea {
  resize: vertical;
  min-height: 140px;
  line-height: 1.6;
}

.select-wrapper {
  position: relative;
}

.select-wrapper svg {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
  color: var(--text-muted);
}

.field-select {
  appearance: none;
  cursor: pointer;
  padding-right: 40px;
}

/* Form Footer */
.form-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  margin-top: 8px;
  flex-wrap: wrap;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  flex: 1;
}

.checkbox-input {
  width: 20px;
  height: 20px;
  cursor: pointer;
  accent-color: var(--primary);
}

.checkbox-text {
  font-size: 0.9rem;
  color: var(--text-muted);
}

.submit-btn {
  padding: 14px 40px;
  background: var(--primary);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.submit-btn:hover {
  background: var(--primary-light);
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(15, 81, 50, 0.25);
}

.submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.loading-state {
  display: flex;
  align-items: center;
  gap: 10px;
}

.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.error-message {
  padding: 14px 18px;
  background: rgba(239, 68, 68, 0.08);
  border: 1px solid rgba(239, 68, 68, 0.2);
  border-radius: 10px;
  color: #dc2626;
  font-size: 0.9rem;
  font-weight: 500;
  margin: 0;
}

/* ========================================
   SIDEBAR
======================================== */
.sidebar {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

/* Info Card */
.info-card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 32px;
  box-shadow: var(--shadow-sm);
}

.info-title {
  font-size: 1.25rem;
  font-weight: 700;
  margin: 0 0 8px;
  color: var(--text);
}

.info-subtitle {
  font-size: 0.9rem;
  color: var(--text-muted);
  margin: 0 0 24px;
}

.info-items {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 24px;
}

.info-item {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px;
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 10px;
  text-decoration: none;
  color: var(--text);
  transition: all 0.2s ease;
}

.info-item:not(.no-hover):hover {
  background: rgba(15, 81, 50, 0.04);
  border-color: var(--primary);
}

.info-icon {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(15, 81, 50, 0.08);
  border-radius: 8px;
  color: var(--primary);
  flex-shrink: 0;
}

.info-content {
  display: flex;
  flex-direction: column;
  gap: 2px;
  flex: 1;
}

.info-label {
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.info-value {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--text);
  line-height: 1.4;
}

.info-divider {
  height: 1px;
  background: var(--border);
  margin: 24px 0;
}

.info-features {
  display: grid;
  gap: 16px;
}

.feature-card {
  padding: 20px;
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 12px;
}

.feature-card svg {
  color: var(--primary);
  margin-bottom: 12px;
}

.feature-card h4 {
  font-size: 1rem;
  font-weight: 700;
  margin: 0 0 6px;
  color: var(--text);
}

.feature-card p {
  font-size: 0.875rem;
  color: var(--text-muted);
  margin: 0;
  line-height: 1.5;
}

/* FAQ Card */
.faq-card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 32px;
  box-shadow: var(--shadow-sm);
}

.faq-title {
  font-size: 1.25rem;
  font-weight: 700;
  margin: 0 0 20px;
  color: var(--text);
}

.faq-item {
  padding: 16px;
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.faq-item + .faq-item {
  margin-top: 12px;
}

.faq-item:hover {
  background: rgba(15, 81, 50, 0.04);
}

.faq-item[open] {
  background: rgba(15, 81, 50, 0.04);
  border-color: var(--primary);
}

.faq-item summary {
  font-weight: 700;
  color: var(--text);
  cursor: pointer;
  user-select: none;
  list-style: none;
}

.faq-item summary::-webkit-details-marker {
  display: none;
}

.faq-item summary::after {
  content: '+';
  float: right;
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--primary);
  transition: transform 0.2s ease;
}

.faq-item[open] summary::after {
  content: '−';
}

.faq-item p {
  margin: 12px 0 0;
  padding-top: 12px;
  border-top: 1px solid var(--border);
  color: var(--text-muted);
  font-size: 0.9rem;
  line-height: 1.6;
}

/* ========================================
   MAP SECTION
======================================== */
.map-section {
  margin: 40px 0 80px;
}

.map-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  margin-bottom: 24px;
  flex-wrap: wrap;
}

.map-title {
  font-size: 1.875rem;
  font-weight: 700;
  margin: 0 0 8px;
  color: var(--text);
}

.map-description {
  font-size: 1rem;
  color: var(--text-muted);
  margin: 0;
  max-width: 600px;
}

.directions-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background: var(--primary);
  color: white;
  text-decoration: none;
  border-radius: 10px;
  font-weight: 600;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.directions-btn:hover {
  background: var(--primary-light);
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(15, 81, 50, 0.25);
}

.map-container {
  border-radius: 20px;
  overflow: hidden;
  border: 1px solid var(--border);
  box-shadow: var(--shadow-lg);
  background: var(--bg);
}

.map-iframe {
  width: 100%;
  height: 450px;
  border: 0;
  display: block;
}

/* ========================================
   RESPONSIVE
======================================== */
@media (max-width: 1024px) {
  .hero-grid {
    grid-template-columns: 1fr;
    gap: 40px;
  }

  .contact-card {
    position: static;
  }

  .content-grid {
    grid-template-columns: 1fr;
    gap: 32px;
  }
}

@media (max-width: 768px) {
  .hero {
    padding: 60px 0 40px;
  }

  .hero-title {
    font-size: 2rem;
  }

  .hero-description {
    font-size: 1rem;
  }

  .form-wrapper {
    padding: 28px;
  }

  .form-row {
    grid-template-columns: 1fr;
    gap: 16px;
  }

  .form-footer {
    flex-direction: column;
    align-items: stretch;
  }

  .submit-btn {
    width: 100%;
  }

  .info-card,
  .faq-card {
    padding: 24px;
  }

  .map-iframe {
    height: 350px;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 0 16px;
  }

  .hero {
    padding: 40px 0 32px;
  }

  .contact-card {
    padding: 24px;
  }

  .form-wrapper {
    padding: 20px;
  }

  .map-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .directions-btn {
    width: 100%;
    justify-content: center;
  }
}
</style>