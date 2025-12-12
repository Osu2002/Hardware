<template>
  <section class="categories-section" v-if="categories && categories.length">
    <h2 class="categories-heading">Shop by Category</h2>

    <div class="categories-grid">
      <div
        v-for="category in categories"
        :key="category.id"
        class="category-card"
         @click="goToCategory(category)" 
      >
        <div class="category-image-wrapper">
          <img
            v-if="category.image"
            :src="category.image"
            :alt="category.title"
            class="category-image"
          />
          <div v-else class="category-image placeholder">
            {{ category.title.charAt(0) }}
          </div>
        </div>

        <div class="category-name">
          {{ category.title }}
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'CategoriesBox',
  props: {
    categories: {
      type: Array,
      default: () => [],
    },
  },
   methods: {
    goToCategory(category) {
    this.$inertia.visit(route('category.list', category.id));
  },
  },
};
</script>

<style scoped>
.categories-section {
  margin: 32px 0 40px;
}

.categories-heading {
  font-size: 1.4rem;
  font-weight: 600;
  margin-bottom: 18px;
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr)); /* 4 per row on desktop */
  gap: 18px;
}

/* Card */
.category-card {
  background: #ffffff;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
  cursor: pointer;
  transition: transform 0.15s ease, box-shadow 0.15s ease,
    border-color 0.15s ease;
  display: flex;
  flex-direction: column;
}

.category-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 18px rgba(0, 0, 0, 0.06);
  border-color: #d1d5db;
}

/* Image area */
.category-image-wrapper {
  width: 100%;
  padding-top: 70%; /* 7:10 ratio */
  position: relative;
  overflow: hidden;
  background: #f3f4f6;
}

.category-image {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.category-image.placeholder {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.4rem;
  color: #6b7280;
  background: linear-gradient(135deg, #e5e7eb, #f9fafb);
}

/* Name */
.category-name {
  padding: 10px 12px 12px;
  text-align: center;
  font-size: 0.95rem;
  font-weight: 500;
  color: #111827;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}

/* Responsive tweaks */
@media (max-width: 1024px) {
  .categories-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 768px) {
  .categories-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 480px) {
  .categories-grid {
    grid-template-columns: 1fr;
  }
}
</style>
