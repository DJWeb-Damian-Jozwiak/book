<template>
  <div>
    <h1>{{ title }}</h1>
    <p>Welcome to your Inertia.js app!</p>
  </div>
</template>

<script setup>
defineProps({
  title: String
})
</script>