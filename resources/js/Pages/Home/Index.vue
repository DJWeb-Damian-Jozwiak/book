<template>
  <div>
    <h1>{{ title }}</h1>
    <p>Welcome to your Inertia.js app!</p>
    <div class="card" style="width: 18rem;">
      <ul class="list-group list-group-flush">
        <li class="list-group-item" v-for="(category, index) in categories" :key="index">
          <Link class="btn-primary btn" :href="`/inertia/categories/${category.id}`">{{ category.name }}</Link>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import {Link} from "@inertiajs/vue3";

defineProps({
  title: String,
  categories: Array,
})
</script>