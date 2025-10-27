<template>
  <div v-if="visible" class="gallery-grid-modal">
    <button class="close-btn" @click="$emit('close')">&times;</button>

    <!-- filter pills -->
    <div class="filter-buttons mb-3">
      <button
        v-for="cat in categories"
        :key="cat"
        :class="{ active: cat === selected }"
        @click="selected = cat"
      >
        {{ cat }}
      </button>
    </div>

    <!-- grid of images -->
    <div class="images-grid">
      <div
        v-for="(img, idx) in filteredImages"
        :key="idx"
        class="grid-item"
        @click="onSelect(img)"
      >
        <img :src="img.original_url" alt="" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    visible: Boolean,
    images: Array,
    categories: Array
  },
  data() {
    return {
      selected: 'All'
    }
  },
  computed: {
    filteredImages() {
      if (this.selected === 'All') return this.images
      return this.images.filter(
        img => img.custom_properties?.category === this.selected
      )
    }
  },
  methods: {
    onSelect(i) {
      this.$emit('select', i)
    },
    onSelect(img) {
    // find where this img lives in the full array:
     const realIndex = this.images.indexOf(img)
     this.$emit('select', realIndex)
   }
  }
}
</script>

<style scoped>
.gallery-grid-modal {
  position: fixed;
  inset: 0;
  background: #fff;
  padding: 1rem;
  overflow-y: auto;
  z-index: 2000;
}

.close-btn {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: none;
  border: none;
  font-size: 2rem;
  cursor: pointer;
}

.filter-buttons {
  display: flex;
  gap: .5rem;
}
.filter-buttons button {
  padding: .4rem .8rem;
  border: 1px solid #ccc;
  background: #f9f9f9;
  border-radius: 20px;
  cursor: pointer;
}
.filter-buttons button.active {
   background: linear-gradient(90deg, #0d1b2a 0%, #273b51 50%, #222b35 100%);
  color: #fff;
  border-color: #10447b;
}

.images-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: .75rem;
}

@media (max-width: 768px) {
  .images-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media (max-width: 576px) {
  .images-grid {
    grid-template-columns: 1fr;
  }
}
.grid-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 8px;
  cursor: pointer;
}
</style>
