<script setup>
import {Link} from "@inertiajs/vue3";
defineProps({
  category: Object,
})
</script>

<template>
<div>
  <h1>{{ category.name }}</h1>
  <Link class="btn btn-primary" href="/inertia/categories">Back</Link>
</div>
</template>

<style scoped>

</style>