<template>
  <AppLayout>
    <section class="py-8">
      <h2 class="text-2xl font-semibold mb-4">Shop by Category</h2>
      <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        <article v-for="c in categories" :key="c.id" class="border rounded-lg p-4 hover:shadow">
          <img v-if="c.image" :src="c.image" :alt="c.title" class="w-full h-36 object-cover rounded mb-3" />
          <h3 class="font-medium">{{ c.title }}</h3>
        </article>
      </div>
    </section>

    <section class="py-8 border-t">
      <h2 class="text-2xl font-semibold mb-4">Brands</h2>
      <div class="grid gap-4 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-6 items-center">
        <div v-for="b in brands" :key="b.id" class="flex items-center justify-center border rounded-lg p-3 bg-white">
          <img v-if="b.logo" :src="b.logo" :alt="b.title" class="max-h-12 object-contain" />
          <span v-else class="text-sm">{{ b.title }}</span>
        </div>
      </div>
    </section>

    <section class="py-8 border-t" v-if="attributes && attributes.length">
      <h2 class="text-2xl font-semibold mb-4">Popular Attributes</h2>
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="a in attributes" :key="a.id" class="rounded-lg border p-4">
          <h3 class="font-medium mb-2">
            {{ a.name }}
            <small class="text-gray-500">({{ a.code }})</small>
          </h3>
          <!-- Example render: list first 6 options if select -->
          <ul v-if="a.type === 'select' && a.options && a.options.length" class="text-sm text-gray-700 space-y-1">
            <li v-for="o in a.options.slice(0,6)" :key="o.id">
              <span v-if="o.hex" :style="{ background: o.hex }" class="inline-block w-3 h-3 mr-2 rounded"></span>
              {{ o.value }}
            </li>
          </ul>
          <p v-else class="text-sm text-gray-500">Type: {{ a.type }} <span v-if="a.unit">({{ a.unit }})</span></p>
        </div>
      </div>
    </section>
  </AppLayout>
</template>

<script>
import AppLayout from '@@/Layouts/AppLayout.vue';

export default {
  name: 'HomePageHardware',
  components: { AppLayout },
  props: {
    categories: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    attributes: { type: Array, default: () => [] },
    attributeSets: { type: Array, default: () => [] }, // available if you want a “By Use-Case” section later
  },
};
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
* { font-family: "Poppins", sans-serif; }
</style>
