<template>
  <section class="hero">
    <div class="overlay"></div>
    <div class="container-fluid hero-content">
      <div class="hero-text">
        <h1>Vehicle Leasing Facilities</h1>
        <form @submit.prevent="calculate">
          <!-- Top row -->
          <div class="form-row">
            <div class="form-group">
              <label for="brand">Make of your current car</label>
              <select id="brand" v-model="brand" required @change="updateModels">
                <option disabled value="">Select Vehicle Brand</option>
                <option v-for="b in brands" :key="b" :value="b">{{ b }}</option>
              </select>
            </div>
            <div class="form-group">
              <label for="model">Model of your current car</label>
              <select id="model" v-model="model" required>
                <option disabled value="">Select Vehicle Model</option>
                <option v-for="m in filteredModels" :key="m" :value="m">{{ m }}</option>
              </select>
            </div>
          </div>

          <!-- Bottom row -->
          <div class="form-row">
            <div class="form-group">
              <label for="type">Vehicle you are interested in?</label>
              <select id="type" v-model="type" required>
                <option disabled value="">Select Vehicle Type</option>
                <option v-for="t in types" :key="t" :value="t">{{ t }}</option>
              </select>
            </div>
            <div class="form-group">
              <label class="hidden-label" for="calculate">Calculate</label>
              <button type="submit" class="calculate-btn">Calculate</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>
</template>

<script>



export default {
  name: 'LeasingHero',
 
  data() {
    return {
      brand: '',
      model: '',
      type: '',
      brands: ['Toyota', 'BMW', 'Audi', 'Mercedes'],
      // Simulated model mapping for demo purposes
      modelMap: {
        Toyota: ['Corolla', 'Camry', 'RAV4'],
        BMW: ['X5', '3 Series', '5 Series'],
        Audi: ['A4', 'Q5', 'A6'],
        Mercedes: ['C-Class', 'E-Class', 'GLC'],
      },
      types: ['Sedan', 'SUV', 'Hatchback', 'Coupe'],
    };
  },
  computed: {
    filteredModels() {
      return this.brand ? this.modelMap[this.brand] || [] : [];
    },
  },
  methods: {
    updateModels() {
      this.model = ''; // Reset model when brand changes
    },
    calculate() {
      if (!this.brand || !this.model || !this.type) {
        alert('Please fill in all fields.');
        return;
      }
      alert(`Leasing calculation:\nBrand: ${this.brand}\nModel: ${this.model}\nType: ${this.type}`);
    },
  },
};
</script>

<style scoped>
/* Load Poppins */

.hero {
  position: relative;
  width: 100%;
  min-height: clamp(600px, 80vh, 900px);
  background: url('../../../../../../public/images/Imageback2.png') center/cover no-repeat;
  display: flex;
  align-items: center;   
  font-family: 'Poppins', sans-serif;
  color: #fff;
  margin-bottom: 4rem; 
  margin-top: var(--title-spacing);
  margin-bottom: var(--bottom-spacing);
}

/* Gradient overlay */
.overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
}

/* Hero content */
.hero-content {
  position: relative;
  max-width: 1200px;
  margin: 0 auto;
  padding: clamp(1rem, 2vw, 1.5rem);
}

/* Hero text */
.hero-text {
   
  width: 100%;
  max-width: 600px;
 
  
}

.hero-text h1 {
  font-size: clamp(1.75rem, 4vw, 2.75rem);
  font-weight: 700;
  margin-bottom: clamp(1rem, 2vw, 1.5rem);
  line-height: 1.2;
   margin-bottom: 2rem; 
}


/* Form rows */
.form-row {
  display: flex;
 gap: clamp(0.75rem, 2vw, 1rem);
  margin-bottom: clamp(0.75rem, 2vw, 1rem);
}

.form-row:first-of-type {
  margin-top: clamp(1.5rem, 4vw, 2.5rem);
}

.form-row .form-group {
  flex: 1;
  position: relative;   /* for our floating label */
}

/* Form group */
.form-group {
  
  position: relative;
  margin-bottom: 1.5rem; 
}

.form-group label {
  position: absolute;
  top: 0.5rem;                 /* lifts it into the white box */
  left: 1rem;                  /* matches select’s left padding */
  background: #fff;            /* same as select bg */
  padding: 0 0.25rem;
  font-size: 0.75rem;
  color: #9CA3AF;              /* light gray */
  pointer-events: none;
  z-index: 1;
}

.form-group select {
  min-height: 4px;
  font-size: clamp(0.875rem, 2vw, 0.9375rem);
  font-family: inherit;
  background: #fff;
  color: #333;
  border: none;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M6 8L0 0h12L6 8z' fill='%23050B20'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 1rem center;
  background-position-x: calc(100% - 1rem);
  background-position-y: calc(100% - 1rem);
  background-size: 10px 8px;
  transition: box-shadow 0.3s ease;
  width: 100%;
  padding: 2rem 3rem 0.5rem 1.25rem; 
  box-sizing: border-box;
}

.form-group select {
  padding-top: 1.5rem; /* enough room for your floating label */
  
}

.form-group select,
.form-group select option {
  color: #050B20 !important;
  font-weight: 600;
}

.form-group select:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
}

.form-group select:disabled {
  background: #f5f5f5;
  cursor: not-allowed;
}

/* Calculate button */
.calculate-btn {
  width: 100%;
  min-height: 56px;
  font-family: inherit;
  font-size: clamp(0.875rem, 2vw, 0.9375rem);
  font-weight: 600;
  color: #fff;
  border: 1px solid #fff;
  border-radius: 15px;
  background: linear-gradient(
    90deg,
    #0d1b2a 0%,
    #232e3b 67%,
    #1e1e1e 89%,
    #273b51 92%,
    #222b35 95%,
    #26384d 100%
  );
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.calculate-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
}

.hidden-label {
  visibility: hidden;
}


/* keep your desktop positioning only for wide screens */
@media (min-width: 1200px) {
  .hero {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding-right: 43rem;
  }
}


/* Responsive Design */
@media (max-width: 992px) {
  .hero {
    min-height: 600px;
  }

  .hero-text {
    max-width: 100%;
  }

  .form-row {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .hero {
    min-height: 500px;
    padding: 2rem 0;
  }

  .hero-text h1 {
    font-size: 1.75rem;
  }

  
}



@media (max-width: 576px) {
  .hero {
    overflow-x: visible !important;
    padding: var(--title-spacing) 1rem var(--bottom-spacing) !important;
  }
  .hero-content {
    padding: 0 1rem !important;
  }
  .form-row {
    display: flex !important;
    flex-direction: column !important;
    gap: 1rem !important;
    margin-bottom: 1.5rem !important;
  }
  .form-row .form-group {
    width: 100% !important;
    max-width: none !important;
    margin-bottom: 1rem !important;
  }
  /* ← Shrink the select so its dropdown will fit */
  .form-group select {
    /* flip the native dropdown’s opening direction */
    direction: rtl !important;
    /* put the text back to normal LTR */
    text-align: left !important;
  }
  .form-group select option {
    direction: ltr !important;
  }
  .hero-text h1 {
    margin-bottom: 1.25rem !important;
  }
}


@media (max-width: 375px) {
  
  .hero {
    padding-top: 4.5rem;  
  }

  
  .hero-content {
    padding: 0 1rem !important;
  }

  
  .form-row .form-group {
    width: 100% !important;
    max-width: none !important;
    margin-bottom: 1rem;   
  }
}


</style>

<style scoped>
/* Add to existing styles */
.hero {
  margin-top: var(--title-spacing);
  margin-bottom: var(--bottom-spacing);
}

.hero-text h1 {
  margin-bottom: var(--title-spacing);
}
</style>