<script setup>
const props = defineProps({
  status: {
    type: Number,
    required: true
  },
  title: {
    type: String,
    required: true
  },
})
</script>

<template>
  <div class="container-fluid vh-100">
    <div class="row h-100 align-items-center justify-content-center">
      <div class="col-12 text-center">
        <h1 class="display-1 fw-bold text-muted mb-4">
          {{ status }}
        </h1>
        <h2 class="h3 text-muted mb-3">
          {{ title }}
        </h2>
        <a href="/" class="btn btn-primary mt-4">
          Back to Home
        </a>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>