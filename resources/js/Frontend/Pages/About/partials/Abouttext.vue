<template>
  <!-- Full-bleed “Our Story” with padded columns -->
  <section class="our-story-section py-5">
    <div class="container-fluid p-0">
      <div class="row g-0 align-items-stretch">
        <!-- Left: image grid with loaders -->
        <div class="col-12 col-md-6 px-4 px-md-5 d-flex flex-column">
          <div class="images-grid-wrapper flex-fill">
            <div class="images-grid">
              
              <!-- top-left small -->
              <div class="image-wrapper small small-top">
                <div v-if="!img1Loaded" class="skeleton-placeholder"></div>
                <img
                  src="/images/111.png"
                  alt="Close-up car headlight"
                  class="small small-top"
                  :class="{ 'is-loaded': img1Loaded }"
                  @load="onImg1Load"
                />
              </div>

              <!-- right-large -->
              <div class="image-wrapper large">
                <div v-if="!img2Loaded" class="skeleton-placeholder"></div>
                <img
                  src="/images/upper.png"
                  alt="Dream car driving"
                  class="large"
                  :class="{ 'is-loaded': img2Loaded }"
                  @load="onImg2Load"
                />
              </div>

              <!-- bottom-left small -->
             <div class="image-wrapper small small-bottom">
                <div v-if="!img3Loaded" class="skeleton-placeholder"></div>
                <img
                  src="/images/333.png"
                  alt="Car dashboard speedometer"
                  class="small small-bottom"
                  :class="{ 'is-loaded': img3Loaded }"
                  @load="onImg3Load"
                />
              </div>

            </div>
          </div>
        </div>

        <!-- Right: Text column -->
        <div class="col-12 col-md-6 px-4 px-md-5">
          <h1 class="mb-4">
            <span class="title-highlight">Our Story</span>
          </h1>

          <p class="story-text mb-3">
            <!-- We are transparent. Dealing with our precious clients and tending
            their sensitive needs to earn a better quality in life we are
            driven by morals which have become the foundation for our specialty
            in selling low-mileage, good quality cars, presenting the "Auction
            Sheet" which shows the real condition of the vehicles in our backyard. -->

            The roots of Hirucars trace back to 2005 - 2006, 
            when our founder launched his first engineering
             and education consulting company in Matara, 
             Sri Lanka. Since then, his journey has taken 
             him across more than 15 countries, earning permanent 
             residency in Singapore and dual citizenship in both 
             Australia and Sri Lanka. This global exposure broadened 
             his vision and led to the founding of Hirucars.
          </p>

          <p class="story-text mb-3">
            <!-- We are proud of being the largest online auto-exporting carport
            functioning in Sri Lanka that brings the automotive technology
            from the world-renowned Japanese manufacturers. We want our clients
            to reach complete satisfaction; that’s why we focus on Japanese
            used cars—they offer numerous advantages: -->
            Since 2018, we have been exporting vehicles from Australia to Sri Lanka, 
            and now we've expanded our importing capabilities to include Japan, the UK, and Singapore.  

          </p>

          <!-- <ul class="story-list">
            <li>Japanese used cars are impeccably maintained and of excellent quality.</li>
            <li>They come with an established grading system which comprehensively scrutinizes each vehicle’s condition.</li>
            <li>Cost-effectiveness merged with highly reliable technology.</li>
          </ul> -->
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "OurStory",
  data() {
    return {
      img1Loaded: false,
      img2Loaded: false,
      img3Loaded: false,
    };
  },
  methods: {
    onImg1Load() { this.img1Loaded = true; },
    onImg2Load() { this.img2Loaded = true; },
    onImg3Load() { this.img3Loaded = true; },
  },
};
</script>

<style scoped>
/* break out full viewport width */
.our-story-section {
  position: relative;
  left: 50%;
  margin-left: -50vw;
  width: 100vw;
  overflow-x: hidden;
}

.title-highlight {
  font-size: 2.5rem;
  font-weight: 600;
  color: #171b4c;
}

.story-text {
  color: #333;
  line-height: 1.6;
    font-size: 1.2rem;
}

.story-list {
  list-style: none;
  padding-left: 0;
  margin-top: 1rem;
}
.story-list li {
  position: relative;
  padding-left: 1.5rem;
  margin-bottom: 0.75rem;
}
.story-list li::before {
  content: "✔︎";
  position: absolute;
  left: 0;
  color: #405ef2;
}

/* grid setup */
.images-grid-wrapper {
  height: 100%;
}
.images-grid {
  display: grid;
  grid-template-columns: 1fr 2fr;
  grid-template-rows: 1fr 1fr;
  gap: 0.5rem;
  width: 100%;
  height: 100%;
  overflow: hidden;
}

/* wrap each for loader */
.image-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
}

/* shimmer placeholder */
.skeleton-placeholder {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: linear-gradient(
    to right,
    #e0e0e0 0%,
    #f8f8f8 50%,
    #e0e0e0 100%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 15px !important;
}

@keyframes shimmer {
  0%   { background-position: -200% 0; }
  100% { background-position:  200% 0; }
}

/* fade-in images */
.images-grid img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius:15px !important;
  opacity: 0;
  transition: opacity 0.5s ease;
}
.images-grid img.is-loaded {
  opacity: 1;
}

/* grid areas */
.images-grid .small-top    { grid-area: 1 / 1; }
.images-grid .small-bottom { grid-area: 2 / 1; }
.images-grid .large        { grid-area: 1 / 2 / span 2; }

@media (max-width: 768px) {
 .title-highlight {
    font-size: 2rem;
  }
  .story-text {
    font-size: 1rem;
    /* padding: 0 1rem; */
  }
}

@media (max-width: 768px) {
  .images-grid-wrapper {
    margin-bottom: 1.5rem;
  }

  .image-loader-container {
    margin-bottom: 1.5rem;
  }
}

</style>
