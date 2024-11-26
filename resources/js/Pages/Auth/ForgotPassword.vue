<script setup>
import { ref } from 'vue'
import {Link} from "@inertiajs/vue3";
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  email: '',
})

const submit = () => {
  form.post('/auth/forgot-password', {
    preserveScroll: true,
  })
}
</script>

<template>
  <div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card shadow">
          <div class="card-body p-4">
            <h2 class="card-title text-center">Forgot Password</h2>
            <p class="text-muted text-center mb-4">
              Enter your email address and we'll send you a link to reset your password.
            </p>

            <form @submit.prevent="submit">
              <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input
                    id="email"
                    type="email"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.email }"
                    v-model="form.email"
                    required
                />
                <div v-if="form.errors.email" class="invalid-feedback">
                  {{ form.errors.email }}
                </div>
              </div>

              <button
                  type="submit"
                  class="btn btn-primary w-100 mb-3"
                  :disabled="form.processing"
              >
                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status"></span>
                Send reset link
              </button>

              <div class="text-center">
                <Link href="/auth/login" class="text-decoration-none">
                  Back to login
                </Link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>