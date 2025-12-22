<template>
  <footer class="site-footer mh-footer">
    <!-- Top marquee strip -->
    <div class="mh-footer-marquee-strip">
      <div class="mh-footer-marquee-inner">
        <span class="mh-footer-marquee-text">
          {{ marqueeText.repeat(4) }}
        </span>
      </div>
    </div>

    <!-- Main footer area with background image -->
    <div class="mh-footer-main">
      <div class="mh-footer-bg"></div>
      <div class="mh-footer-overlay"></div>

      <div class="mh-footer-inner">
        <div class="mh-footer-grid">
          <!-- Logo + description -->
          <div class="mh-footer-col mh-footer-brand">
            <Link href="/" class="mh-footer-logo-link">
              <!-- swap the src to your actual logo path -->
              <img
                src="/images/mahindalogo.png"
                alt="Mahinda Hardware & Electrical"
                class="mh-footer-logo"
              />
            </Link>
            <p class="mh-footer-desc">
              Your one-stop shop for all electrical and hardware needs.
             
            </p>
          </div>

          <!-- Quick Links -->
          <div class="mh-footer-col">
            <h3 class="mh-footer-heading">Quick Links</h3>
            <ul class="mh-footer-links-list">
              <li v-for="link in quickLinks" :key="link.text">
                <Link :href="link.href" class="mh-footer-link">
                  {{ link.text }}
                </Link>
              </li>
            </ul>
          </div>

          <!-- Contact Us -->
          <div class="mh-footer-col">
            <h3 class="mh-footer-heading">Contact Us</h3>

            <ul class="mh-footer-contact-list">
              <li>
                <div class="mh-footer-contact-icon">
                  <i class="fas fa-phone-alt"></i>
                </div>
                <div class="mh-footer-contact-text">
                  <a href="tel:+94715526000">
                    +94 71 552 6000
                  </a>
                </div>
              </li>

              <li>
                <div class="mh-footer-contact-icon">
                  <i class="fab fa-whatsapp"></i>
                </div>
                <div class="mh-footer-contact-text">
                  <a
                    href="https://wa.me/94771234567"
                    target="_blank"
                    rel="noopener"
                  >
                    WhatsApp
                  </a>
                </div>
              </li>

              <li>
                <div class="mh-footer-contact-icon">
                  <i class="far fa-envelope"></i>
                </div>
                <div class="mh-footer-contact-text">
                  <a href="mailto:mahindahardware@gmail.com">
                    mahindahardware@gmail.com
                  </a>
                </div>
              </li>

              <li>
                <div class="mh-footer-contact-icon">
                  <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="mh-footer-contact-text">
                  <span>
                    135/A, Rathnapura Rd, Hinguraara, Embilipitiya
                  </span>
                </div>
              </li>
            </ul>

            <!-- Social icons -->
            <div class="mh-footer-socials">
              <a
                href="https://facebook.com"
                target="_blank"
                rel="noopener"
                class="mh-footer-social-link"
              >
                <i class="fab fa-facebook-f"></i>
              </a>
              <a
                href="https://instagram.com"
                target="_blank"
                rel="noopener"
                class="mh-footer-social-link"
              >
                <i class="fab fa-instagram"></i>
              </a>
            </div>
          </div>

          <!-- Map -->
          <div class="mh-footer-col mh-footer-map-col">
            <h3 class="mh-footer-heading">Find Us</h3>
            <div class="mh-footer-map">
            <iframe
  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4703.697073463813!2d80.84065749999999!3d6.347923499999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae401adf6ae3a4b%3A0x1595096ba73b5242!2sMahinda%20Hardware!5e1!3m2!1sen!2slk!4v1766386294385!5m2!1sen!2slk"
  loading="lazy"
  referrerpolicy="no-referrer-when-downgrade"
  allowfullscreen
  title="Store Location"
></iframe>


            </div>
          </div>
        </div>

        <!-- bottom line + text -->
     <div class="mh-footer-bottom">
  <div class="mh-footer-bottom-inner">
    <div class="mh-footer-bottom-left">
      © 2025 Mahinda Hardware &amp; Electrical. All rights reserved.
    </div>

    <div class="mh-footer-bottom-right mh-footer-credit">
      Web design &amp; developed by
      <a
        href="https://scalar7solutions.com"
        target="_blank"
        rel="noopener"
        class="mh-footer-credit-link"
      >
        Scalar7 Solutions
      </a>
    </div>
  </div>
</div>

      </div>
    </div>
  </footer>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";

export default {
  name: "Footer",
  components: { Link },
  data() {
    return {
      marqueeText:
        "MAHINDA HARDWARE & ELECTRICAL • YOUR TRUSTED ELECTRICAL PARTNER • QUALITY PRODUCTS • EXPERT SERVICE • ",
      quickLinks: [
        { text: "Home", href: "/" },
        { text: "Products", href: "/products" },
        { text: "Services", href: "/services" },
        { text: "Contact", href: "/contact" },
      ],
    };
  },
};
</script>

<style scoped>
.mh-footer {
  position: relative;
  width: 100%;
  color: #f9fafb;
  font-family: Poppins, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI",
    sans-serif;
  background-color: #020617;
}

/* ==== Top marquee strip ==== */

.mh-footer-marquee-strip {
  background: linear-gradient(90deg, #0b1220 0%, #1f2937 40%, #020617 100%);
  padding: 0.45rem 0;
  overflow: hidden;
}

.mh-footer-marquee-inner {
  display: flex;
  white-space: nowrap;
}

.mh-footer-marquee-text {
  font-size: 0.65rem;
  letter-spacing: 0.35em;
  text-transform: uppercase;
  opacity: 0.7;
  animation: mh-marquee 40s linear infinite;
}

@keyframes mh-marquee {
  0% {
    transform: translateX(0);
  }

  100% {
    transform: translateX(-50%);
  }
}

/* ==== Main area (background + overlay) ==== */

.mh-footer-main {
  position: relative;
  overflow: hidden;
}

.mh-footer-bg {
  position: absolute;
  inset: 0;
  background-image: url("/images/footer-bg.jpg");
  /* update path if needed */
  background-size: cover;
  background-position: center;
  filter: brightness(0.4);
}

.mh-footer-overlay {
  position: absolute;
  inset: 0;
  background: radial-gradient(
      circle at top left,
      rgba(15, 23, 42, 0.7),
      transparent 50%
    ),
    radial-gradient(
      circle at bottom right,
      rgba(15, 23, 42, 0.9),
      rgba(15, 23, 42, 0.98)
    );
}

.mh-footer-inner {
  position: relative;
  z-index: 1;
  padding: 2.5rem 1.5rem 1.75rem;
}

@media (min-width: 768px) {
  .mh-footer-inner {
    padding: 3rem 2.5rem 2rem;
  }
}

@media (min-width: 1024px) {
  .mh-footer-inner {
    padding: 3.5rem 3rem 2.25rem;
  }
}

/* ==== Grid layout ==== */

.mh-footer-grid {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr) minmax(0, 1.2fr) minmax(
      0,
      1.4fr
    );
  gap: 2.25rem;
}

@media (max-width: 991.98px) {
  .mh-footer-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    row-gap: 2.5rem;
  }
}

@media (max-width: 575.98px) {
  .mh-footer-grid {
    grid-template-columns: minmax(0, 1fr);
  }
}

/* ==== Columns ==== */

.mh-footer-col {
  min-width: 0;
}

.mh-footer-brand {
  max-width: 340px;
}

.mh-footer-logo-link {
  display: inline-block;
  margin-bottom: 1.25rem;
}

.mh-footer-logo {
  height: 11.5rem;
  width: auto;
  filter: brightness(0) invert(1);
}

.mh-footer-desc {
  font-size: 0.9rem;
  line-height: 1.7;
  color: rgba(248, 250, 252, 0.8);
}

/* Headings */

.mh-footer-heading {
  font-size: 0.8rem;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  font-weight: 600;
  margin-bottom: 1.25rem;
}

/* Quick links */

.mh-footer-links-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.mh-footer-links-list li + li {
  margin-top: 0.55rem;
}

.mh-footer-link {
  font-size: 0.9rem;
  color: rgba(248, 250, 252, 0.72);
  text-decoration: none;
  transition: color 0.2s ease;
}

.mh-footer-link:hover {
  color: #ffffff;
}

/* Contact list */

.mh-footer-contact-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.mh-footer-contact-list li {
  display: flex;
  align-items: flex-start;
  gap: 0.55rem;
  margin-bottom: 0.7rem;
}

.mh-footer-contact-icon {
  width: 1.25rem;
  height: 1.25rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 999px;
  background: rgba(56, 189, 248, 0.15);
  color: #38bdf8;
  flex-shrink: 0;
  margin-top: 0.1rem;
  font-size: 0.7rem;
}

.mh-footer-contact-text a,
.mh-footer-contact-text span {
  font-size: 0.9rem;
  color: rgba(248, 250, 252, 0.78);
  text-decoration: none;
  word-break: break-word;
}

.mh-footer-contact-text a:hover {
  color: #ffffff;
}

/* Social */

.mh-footer-socials {
  display: flex;
  gap: 0.75rem;
  margin-top: 1rem;
}

.mh-footer-social-link {
  width: 2rem;
  height: 2rem;
  border-radius: 999px;
  background: rgba(15, 23, 42, 0.7);
  border: 1px solid rgba(148, 163, 184, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  color: rgba(248, 250, 252, 0.75);
  font-size: 0.9rem;
  transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease,
    transform 0.2s ease;
}

.mh-footer-social-link:hover {
  background: #38bdf8;
  border-color: #38bdf8;
  color: #0f172a;
  transform: translateY(-1px);
}

/* Map */

.mh-footer-map-col {
  display: flex;
  flex-direction: column;
}

.mh-footer-map {
  flex: 1;
  border-radius: 0.75rem;
  overflow: hidden;
  border: 1px solid rgba(148, 163, 184, 0.45);
  background: rgba(15, 23, 42, 0.9);
  aspect-ratio: 4 / 3;
}

.mh-footer-map iframe {
  width: 100%;
  height: 100%;
  border: 0;
}

/* Bottom line */

.mh-footer-bottom {
  margin-top: 1.5rem;
  border-top: 1px solid rgba(148, 163, 184, 0.35);
  padding-top: 0.8rem;
  padding-bottom: 1rem;
}

.mh-footer-bottom-inner {
  max-width: 1200px;
  margin: 0 auto;

  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;

  gap: 0.45rem;
  text-align: center;

  font-size: 0.78rem;
  color: rgba(248, 250, 252, 0.65);
}

.mh-footer-credit {
  margin-top: 0; /* important (remove the top margin for flex layout) */
  font-size: 0.75rem;
  color: rgba(248, 250, 252, 0.55);
}

@media (min-width: 640px) {
  .mh-footer-bottom-inner {
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    text-align: left;
  }

  .mh-footer-bottom-right {
    text-align: right;
  }
}

.mh-footer-credit-link {
  color: rgba(248, 250, 252, 0.75);
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s ease;
}


.mh-footer-credit-link:hover {
  color: #ffffff;
  text-decoration: underline;
}

@media (min-width: 640px) {
  .mh-footer-bottom-inner {
    text-align: left;
  }
}
</style>
