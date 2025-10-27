<template>
  <footer class="site-footer">
    <div class="footer-top">
      <div class="container-fluid py-5 px-3 px-md-5">
        <div class="row align-items-stretch gy-4 gx-md-4">

          <!-- LEFT COLUMN: Header + description + Get in Touch card -->
          <!-- <div class="col-12 col-md-6 col-lg-5 d-flex px-3 px-md-0"> -->
          <div class="col-12 col-md-6 col-lg-5 d-flex ps-0 ps-md-3 pe-3">


            <!-- <div class="d-flex flex-column h-100 w-100"> -->
            <div class="d-flex flex-column h-100 w-100">
              <div class="mb-2 text-center text-md-start">
                <!-- <img 
                src="/images/logo1.png" 
                alt="World Auto" 
                class="footer-logo d-block mx-auto mx-md-0" /> -->
                <h2 class="footer-logo-text">World Auto Dealers</h2>
                <p class="footer-desc">
                  Have a question, feedback, or need assistance? Fill out the form below and our team will get back to
                  you
                  within 24 hours. We're here to help you every step of the way.
                </p>
              </div>

              <!-- <div class="contact-card mt-2">
              <h3 class="contact-card-title">Get in Touch</h3>
              <template v-if="$page.props.countries?.length">
                <div class="flags-grid">
                  <Link
                    v-for="country in $page.props.countries"
                    :key="country.id"
                    :href="route('available', {
                      countrySlug: country.name.toLowerCase().replace(/\s+/g, '-')
                    })"
                    class="flag-link"
                    :class="{ active: selectedCountry?.id === country.id }"
                    preserve-state
                    preserve-scroll
                    @click="selectCountry(country)"
                  >
                    <img
                      :src="country.media?.[0]?.original_url"
                      :alt="`${country.name} Flag`"
                      class="flag-image"
                    />
                  </Link>
                </div>
              </template>
</div> -->

            </div>
          </div>



          <!-- <div class="col-12 col-md-6 col-lg-7 d-flex align-items-start px-3"> -->
          <div class="col-12 col-md-6 col-lg-7 d-flex flex-column px-3">
            <!-- <div class="ratio ratio-16x9 rounded-5 overflow-hidden h-100 w-100"> -->
            <!-- <div class="ratio ratio-16x9 rounded-5 overflow-hidden h-100 w-100 mt-auto">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31688.423040114812!2d79.84701563476564!3d6.884269400000006!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae25b93c3fc71b3%3A0x3ffde51c4e6f6f77!2sNikoba%20Online%20Car%20Showroom!5e0!3m2!1sen!2slk!4v1719466160471!5m2!1sen!2slk"
              class="w-100 h-100 border-0"
              allowfullscreen
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
          </div> -->

            <div class="footer-marquee">
              <div class="marquee-wrapper">
                <div class="marquee-content" id="marqueeText">
                  WORLD AUTO DEALERS. WORLD AUTO DEALERS. WORLD AUTO DEALERS. WORLD AUTO DEALERS. WORLD AUTO DEALERS.
                  WORLD AUTO
                  DEALERS.
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>


    </div>

    <!-- Middle: link columns + Contact Us & Follow Us -->
    <div class="footer-links">
      <div class="container-fluid py-5 px-3 px-md-5">
        <div class="row 
                gx-0 gx-md-2 gy-4 mx-0 
                row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5">


          <!-- ▸ Dynamic “Company / Quick Links …” columns -->
          <div class="col text-start ps-0 ps-md-3" v-for="(col, i) in linkCols" :key="i">
            <h6 class="links-title">{{ col.title }}</h6>
            <ul class="list-unstyled mb-0">
              <li v-for="(link, j) in col.links" :key="j" class="mb-2">
                <Link :href="link.href" class="footer-link">
                {{ link.text }}
                </Link>
              </li>
            </ul>
          </div>



          <!-- ▸ Our Brands (dynamic) -->
          <div class="col text-start ps-0 ps-md-3">
            <h6 class="links-title">Our Brands</h6>
            <ul class="list-unstyled mb-0">
              <li v-for="manufacture in $page.props.category_manufactures.slice(0, 8)" :key="manufacture.id"
                class="mb-2">
                <Link method="post" :href="route('live.auction')"
                  :data="{ _method: 'GET', manufacturer: manufacture.name, search: true }" class="footer-link">
                {{ formatName(manufacture.name) }}
                </Link>
              </li>
            </ul>
          </div>

          <!-- ▸ Vehicles Type (dynamic) -->
          <div class="col text-start ps-0 ps-md-3">
            <h6 class="links-title">Vehicles Type</h6>
            <ul class="list-unstyled mb-0">
              <li v-for="type in $page.props.vehicle_types" :key="type.id" class="mb-2">
                <Link method="post" :href="route('available')" :data="{ _method: 'GET', type: type.id }"
                  class="footer-link">
                {{ formatName(type.title) }}
                </Link>
              </li>
            </ul>
          </div>


          <!-- Contact Us + Socials -->
          <div class="col text-start ps-0 ps-md-3">
            <h6 class="links-title">Contact Us</h6>


            <div v-for="country in $page.props.countries" :key="country.id"
              class="d-flex align-items-center rounded-pill border border-white px-3 py-1 mb-2"
              style="width: max-content;">
              <img :src="country.media?.[0]?.original_url" alt="flag" class="img-fluid" style="max-width: 1.25rem;" />
              <!-- plain anchor for phone -->
              <a :href="`tel:${country.phone1}`" class="ms-2 small text-white text-nowrap">
                {{ country.phone1 }}
              </a>
            </div>


            <div class="follow-section mt-4">
              <h6 class="links-title">Follow Us</h6>
              <div class="social-icons d-flex flex-row gap-3">
                <a href="https://facebook.com" target="_blank" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="https://twitter.com" target="_blank" class="social-icon"><i class="fab fa-twitter"></i></a>
                <a href="https://instagram.com" target="_blank" class="social-icon"><i class="fab fa-instagram"></i></a>
                <!-- <a href="https://linkedin.com" target="_blank" class="social-icon"><i class="fab fa-linkedin-in"></i></a> -->
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>


    <div class="footer-bottom">
      <!-- left: copyright -->
      <small>© 2025 World Auto Dealers PVT Ltd. All rights reserved</small>

      <!-- right: branded credit block -->
      <div class="developer-credit">
        <span>Web Design Services by</span>
        <a href="https://weblook.com/" target="_blank" rel="noopener" class="developer-logo-link">
          <img src="/images/web.png" alt="weblock.com" class="developer-logo" />
        </a>
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
      linkCols: [
        {
          title: "Company",
          links: [
            { text: "About Us", href: "/about" },
            { text: "Services", href: "/knowledge-center" },
            { text: "FAQs", href: "/knowledge-center" },
            { text: "Contact Us", href: "/contact" },
          ],
        },
        {
          title: "Quick Links",
          links: [
            { text: "Get in Touch", href: "/contact" },
            { text: "Knowledge Center", href: "/knowledge-center" },
            { text: "Live Auction In Japan", href: "/live-auction" },
            { text: "How To Order", href: "/knowledge-center" },
          ],
        },
      ],
    };
  },
  methods: {
    selectCountry(country) {
      this.selectedCountry = country;
    },
    formatName(name) {
      if (!name) return '';
      const lower = name.toLowerCase();
      return lower.charAt(0).toUpperCase() + lower.slice(1);
    },
    initMarquee() {
      const marquee = document.getElementById('marqueeText');
      if (marquee) {
        const txt = marquee.textContent;
        marquee.textContent = txt + ' ' + txt;
      }
    },
  },
  mounted() {
    this.initMarquee();
  },
};
</script>

<style scoped>
.footer-logo-text {
  font-size: 3.5rem;
  font-weight: 700;
  margin-bottom: 3rem;
  margin-top: 7rem;
  color: #fff;
}

.footer-logo {
  max-width: 300px;
  width: 100%;
  height: auto;
  margin-bottom: 10rem;
}

.site-footer {
  font-family: Poppins, sans-serif;
  color: #fff;
  background: #0a1f44;
}

.footer-top {
  background: linear-gradient(90deg, #415a77 0%, #0a3f79 0%, #163353 34%, #142334 67%, #0e1e30 80%, #0d1b2a 100%);
}

.footer-title {
  font-size: 2rem;
  font-weight: 700;
}

.footer-desc {
  max-width: 500px;
  /* ← same as .contact-card’s max-width */
  width: 100%;
  line-height: 1.7;
  text-align: left;
}

.py-5 {
  padding-top: 1% !important;
  padding-bottom: 0rem !important;
}

.contact-card {
  background: #fff;
  border-radius: 2rem;


  margin: 1rem 0;

  /* keep your snug sizing */
  padding: 1.5rem 2rem;
  width: auto;
  max-width: 350px;

  text-align: center;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  /* remove any align-self override */
}




.contact-card-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 2.5rem;
  letter-spacing: -0.02em;
}



.flags-grid {
  display: inline-grid;
  grid-auto-flow: column;
  grid-auto-columns: min-content;
  gap: 0.5rem;
  padding: 0.5rem;
  margin: 0 auto;
  /* center the inline-grid */
  /* drop width/max-width overrides */
  width: auto;
  max-width: none;
}

.flag-link {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.25rem;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  border-radius: 0.5rem;
}

.flag-link:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

.flag-image {
  height: 30px;
  width: 45px;
  object-fit: cover;
  border-radius: 4px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.footer-links {
  background: linear-gradient(90deg, #415a77 0%, #0a3f79 0%, #163353 34%, #142334 67%, #0e1e30 80%, #0d1b2a 100%);
}

.links-title {
  font-size: 1rem;
  font-weight: 700;
  margin-bottom: 2rem;
}

.footer-link {
  color: #fff;
  text-decoration: none;
}

.footer-link:hover {
  text-decoration: underline;
}

.footer-bottom {
  background: linear-gradient(90deg, #415a77 0%, #0a3f79 0%, #163353 34%, #142334 67%, #0e1e30 80%, #0d1b2a 100%);
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 3rem;
  font-size: 0.85rem;
}

.developer-link {
  color: #fff;
  text-decoration: none;
}

.developer-link:hover {
  text-decoration: underline;
}

.social-icons {
  display: flex;
  gap: 1.25rem;
  margin-top: 1rem;
}

.social-icon {
  color: #fff;
  font-size: 1.25rem;
  transition: color 0.2s;
}

.social-icon:hover {
  color: #00bfff;
}

@media (max-width: 767px) {
  .footer-top .row {
    flex-direction: column;
  }

  .footer-bottom {
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-start !important;
    padding: 1rem 1.5rem !important;
  }

  /* .footer-top .container-fluid,
  .footer-links .container-fluid,
  .footer-bottom {
    padding-left: 2rem;
    padding-right: 2rem;
  } */

  .contact-card {
    padding: 2.5rem 2rem;
    max-width: 100%;
    margin-top: 1.5rem;
  }

  .footer-links .text-start {
    padding-left: 1.5rem;
    /* Adjust padding for larger screens */
  }

  .contact-card-title {
    font-size: 1.75rem;
    margin-bottom: 1.5rem;
  }

  .flags-grid {
    grid-template-columns: repeat(auto-fit, minmax(40px, 1fr));
    gap: 0.35rem;
    padding: 0.25rem;
  }

  .flag-image {
    height: 25px;
    width: 35px;
  }
}

@media (max-width: 320px) {
  .flags-grid {
    grid-template-columns: repeat(auto-fit, minmax(35px, 1fr));
    gap: 0.25rem;
  }

  .flag-image {
    height: 20px;
    width: 30px;
  }
}

@media (min-width: 992px) {
  .footer-top .ratio {
    max-height: 390px;
  }
}


.footer-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 3rem;
  background: linear-gradient(90deg,
      #415a77 0%,
      #0a3f79 0%,
      #163353 34%,
      #142334 67%,
      #0e1e30 80%,
      #0d1b2a 100%);
}

/* new credit block */
.developer-credit {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.developer-credit span {
  color: #fff;
  font-size: 0.72rem;
}

.developer-logo-link {
  display: inline-block;
}

.developer-logo {
  height: 24px;
  width: auto;
  display: block;
}



.footer-top .row {
  display: flex;
  flex-wrap: wrap;
}


.footer-marquee {

  flex: 1 1 auto;
  /* drop the hard min-width so it can resize down on small screens */
  min-width: 0;
  width: 100%;
  padding-top: 2.5rem;
  margin-bottom: 10px;
  overflow: hidden;
}

.marquee-wrapper {
  position: relative;
  width: 100%;
  height: 40vh;
  overflow: hidden;

}


.marquee-content {
  position: absolute;
  white-space: nowrap;
  font-size: 12em;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
  -webkit-text-stroke: 3.2px rgba(255, 255, 255, 0.8);
  color: transparent;
  will-change: transform;
  animation: marquee 20s linear infinite;


}

@keyframes marquee {
  0% {
    transform: translateX(0);
  }

  100% {
    transform: translateX(-10%);
  }
}



@media (max-width: 768px) {
  .footer-marquee {
    padding-top: 1.5rem;
  }

  .marquee-wrapper {
    height: 25vh;
  }

  .marquee-content {
    font-size: 6em;
    letter-spacing: 1px;
    -webkit-text-stroke: 1px rgba(255, 255, 255, 0.8);
  }
}

@media (max-width: 480px) {
  .marquee-wrapper {
    height: 20vh;
  }

  .marquee-content {
    font-size: 4em;
  }
}


@media (max-width: 767px) {
  .footer-desc {
    text-align: center;
    margin-left: auto;
    margin-right: auto;
  }

  .contact-card {
    /* center the card itself */
    margin-left: auto;
    margin-right: auto;
  }
}
</style>
