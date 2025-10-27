<template>
  <section class="faq-page container-fluid">
    <div class="row align-items-center section">
      <div class="col-lg-6 col-md-12">
        <h2 class="section-title">How to Select Your Dream Vehicle?</h2>
        <div class="accordion">
          <details class="faq-item">
            <summary>How to select my Dream vehicle?</summary>
            <ul>
              <li>Go to “LIVE AUCTION”</li>
              <li>Select your vehicle through “SEARCH” options</li>
              <li>Use “ADVANCED SEARCH” options to refine your result</li>
              <li>
                Read the “Auction Sheet” by clicking on “How to read the auction sheet”
              </li>
              <li>
                Before placing your bid, see the Previous Sold Prices of same-conditioned
                vehicles by clicking on “Statistics by Model”
              </li>
              <li>
                If you place a higher bid than average, you have a better chance to
                win—and we guarantee 100% benefit if the sold price is less than your bid
              </li>
              <li>
                Place your bid by making an initial deposit through our Online Payment
                system
              </li>
              <li>Contact our Sales Staff for further assistance</li>
            </ul>
          </details>

          <details class="faq-item">
            <summary>How do I find a vehicle in World Auto Dealers?</summary>
            <p>
              If you have a specific vehicle in mind, use our Search or Advanced Search
              features to locate it. Otherwise, browse Our Stocks in Japan or Our Stock in
              Sri Lanka, or filter by brand or vehicle type.
            </p>
          </details>

          <details class="faq-item">
            <summary>What do I do if the vehicle I want is not listed on your site?</summary>
            <p>
              If you can't find what you’re looking for, email us and we'll help you locate
              it.
            </p>
          </details>

          <details class="faq-item">
            <summary>What is a “Fast Delivery”?</summary>
            <p>
              This service is provided only by World Auto Dealers. Select vehicles can be delivered
              within three weeks of ordering. You can search for Fast Delivery vehicles via
              our Search/Advanced Search forms or by clicking on our LATEST ARRIVALS link.
            </p>
          </details>

          <details class="faq-item">
            <summary>What is the World Auto Dealers Best Price Calculator Guarantee?</summary>
            <p>
             World Auto Dealers’s best price guarantee applies for transport from JAPAN to your
              selected border. If you find a cheaper provider, we’ll reimburse the
              difference! T&Cs apply.
            </p>
          </details>

          <details class="faq-item">
            <summary>How long does it take for me to get my vehicle?</summary>
            <p>
              If the vehicle is in LATEST ARRIVALS, you’ll receive it three weeks after
              payment. Other vehicles take four to six weeks (including clearance).
            </p>
          </details>
        </div>
      </div>

      <div class="col-lg-6 col-md-12">
        <img
          src="/images/sec1.png"
          alt="Dream vehicle on the road"
          class="section-img"
        />
      </div>
    </div>

    <!-- Section 2: Tabs + Swapped Layout -->
    <div class="row align-items-center section section-2">
      <!-- Tab Buttons -->
      <div class="col-12 tab-buttons d-flex justify-content-end">
        <button
          class="tab-btn"
          :class="{ active: activeTab === 'order' }"
          @click="activeTab = 'order'"
        >
          Ordering / Shipment
        </button>
        <button
          class="tab-btn"
          :class="{ active: activeTab === 'pricing' }"
          @click="activeTab = 'pricing'"
        >
          Pricing / Payments
        </button>
      </div>

      <!-- Left: Image -->
      <div class="col-lg-6 col-md-12 order-md-1 order-2">
        <img
          src="/images/sec2.png"
          alt="Vehicle shipment"
          class="section-img"
        />
      </div>

      <!-- Right: Conditional FAQs -->
      <div class="col-lg-6 col-md-12 order-md-2 order-1">
        <div v-if="activeTab === 'order'" class="accordion">
          <details class="faq-item">
            <summary>Can you deliver the vehicle?</summary>
            <p>
              Yes—our global shipping partners can deliver to any port you request.
            </p>
          </details>
          <details class="faq-item">
            <summary>Which borders do you deliver to?</summary>
            <p>
              We can deliver to any border or port you specify at checkout.
            </p>
          </details>
          <details class="faq-item">
            <summary>How do I coordinate transport?</summary>
            <p>
              When ordering, select which border you’d like delivery to. We’ll coordinate
              with our shipping partners to arrange everything.
            </p>
          </details>
          <details class="faq-item">
            <summary>How do I place an order for a vehicle?</summary>
            <p>
              Please refer to our How To Buy section for step-by-step ordering
              instructions, or send us an inquiry and a Sales Advisor will contact you
              ASAP.
            </p>
          </details>
          <details class="faq-item">
            <summary>How do I track the status of my order/shipment?</summary>
            <p>
              Our customer service team will track your shipment from pickup to delivery—just
              email us your reference number.
            </p>
          </details>
          <details class="faq-item">
            <summary>Can I clear the shipment myself?</summary>
            <p>
              Yes—you may clear the vehicle with your own customs agent if you prefer.
            </p>
          </details>
        </div>

        <div v-else class="accordion">
          <details class="faq-item">
            <summary>What are FOB, CIF, ExDurban, and To Your Border prices?</summary>
            <p>
              <strong>FOB:</strong> Free on Board—price before shipping.
            </p>
            <p>
              <strong>CIF:</strong> Cost + Insurance + Freight (FOB plus insurance and
              shipping).
            </p>
            <p>
              <strong>To Your Border Price:</strong> Includes transport, clearing, road bond,
              etc.—the total out-the-door cost.
            </p>
          </details>
          <details class="faq-item">
            <summary>What payment methods do you accept?</summary>
            <p>
              For Sri Lankans: Letter of Credit (LC). For other countries: Telegraphic
              Transfers (TT) and Credit Cards.
            </p>
          </details>
          <details class="faq-item">
            <summary>How do I get your Bank Details?</summary>
            <p>
              After ordering, a World Auto Dealers representative will email you our bank details. You
              can also find them on our website under Bank Details.
              <span class="warning">Warning:</span> Only trust details sent by an official
              World Auto Dealers representative. Always double-check against our site.
            </p>
          </details>
          <details class="faq-item">
            <summary>Can I make my payment to any other bank besides Japan?</summary>
            <p>
              No—payments must go to the account listed on our website.
            </p>
          </details>
          <details class="faq-item">
            <summary>Can I cancel a vehicle after I've made my payment?</summary>
            <p>
              Yes—you may cancel, but a 20% cancellation fee applies.
            </p>
          </details>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'FaqPage',
  data() {
    return {
      activeTab: 'order', // 'order' or 'pricing'
    };
  },
};
</script>

<style scoped>
.faq-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: clamp(1.5rem, 3vw, 2rem) clamp(1rem, 2vw, 1.5rem);
}

.section {
  margin-bottom: clamp(2rem, 5vw, 3rem);
}

.section-img {
  width: 100%;
  max-height: 400px;
  border-radius: 0.75rem;
  object-fit: cover;
  aspect-ratio: 4 / 3;
}

/* Tab buttons */
.tab-buttons {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.tab-btn {
  background: #f7f4f4;
  border: none;
  padding: clamp(0.5rem, 1.5vw, 0.75rem) clamp(1rem, 2vw, 1.5rem);
  font-weight: 500;
  cursor: pointer;
  border-radius: 0.5rem;
  transition: all 0.3s ease;
  font-size: clamp(0.875rem, 2vw, 1rem);
}

.tab-btn:hover {
  background: #e0e0e0;
}

.tab-btn.active {
  background: #171b4c;
  color: white;
}

/* Section titles */
.section-title {
  font-size: clamp(1.5rem, 3vw, 1.75rem);
  margin-bottom: 1.5rem;
  color: #171b4c;
  font-weight: 600;
}

/* Accordion */
.accordion {
  margin-bottom: 2rem;
}

.faq-item {
  background-color: #f7f4f4;
  border-radius: 0.75rem;
  margin-bottom: 1rem;
  overflow: hidden;
}

.faq-item summary::-webkit-details-marker {
  display: none;
}

.faq-item summary {
  padding: clamp(0.75rem, 2vw, 1rem) clamp(1rem, 2vw, 1.5rem);
  font-weight: 500;
  cursor: pointer;
  position: relative;
  color: #171b4c;
  transition: background 0.2s;
  font-size: clamp(0.875rem, 2vw, 1rem);
}

.faq-item summary:hover {
  background-color: rgba(23, 27, 76, 0.05);
}

.faq-item summary::after {
  content: '+';
  position: absolute;
  right: 1.5rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 1.25rem;
  color: #171b4c;
}

.faq-item[open] summary::after {
  content: '–';
}

.faq-item ul,
.faq-item p {
  padding: 0 clamp(1rem, 2vw, 1.5rem) clamp(0.75rem, 2vw, 1rem);
  margin: 0;
  border-top: 1px solid #ececec;
  font-size: clamp(0.875rem, 2vw, 0.9375rem);
}

.faq-item ul {
  list-style: none;
  padding-top: 0.5rem;
}

.faq-item ul li {
  margin-bottom: 0.5rem;
  position: relative;
  padding-left: 1.5rem;
}

.faq-item ul li::before {
  content: '✔';
  position: absolute;
  left: 0;
  color: #405ef2;
}

.warning {
  display: block;
  color: #d32f2f;
  font-weight: bold;
  margin-top: 0.5rem;
}

/* Responsive Design */
@media (max-width: 992px) {
  .section {
    margin-bottom: 2rem;
  }

  .section-img {
    max-height: 300px;
    margin-top: 1.5rem;
  }

  .section-title {
    font-size: clamp(1.25rem, 2.5vw, 1.5rem);
  }

  .tab-buttons {
    justify-content: center;
  }
}

@media (max-width: 768px) {
  .faq-page {
    padding: 1rem;
  }

  .section-img {
    max-height: 250px;
  }

  .faq-item summary {
    padding: 0.75rem 1rem;
  }

  .faq-item ul,
  .faq-item p {
    padding: 0 1rem 0.75rem;
  }

  .tab-btn {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
  }
}

@media (max-width: 576px) {
  .section-img {
    max-height: 200px;
  }

  .section-title {
    font-size: 1.25rem;
  }

  .faq-item summary::after {
    font-size: 1rem;
  }

  .tab-buttons {
    gap: 0.5rem;
  }

  .tab-btn {
    flex: 1;
    text-align: center;
  }
}
</style>