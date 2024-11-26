<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card shadow text-center">
          <div class="card-body p-4">
            <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-block mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-check-lg text-success" viewBox="0 0 16 16">
                <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
              </svg>
            </div>

            <h2 class="card-title">Password Reset Successfully</h2>
            <p class="text-muted mb-4">
              Your password has been reset successfully. You can now use your new password to log in.
            </p>

            <Link
                href="/auth/login"
                class="btn btn-primary"
            >
              Go to login
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<style scoped>

</style>