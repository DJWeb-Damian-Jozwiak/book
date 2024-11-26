<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  token: String,
})

const form = useForm({
  token: props.token,
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post('/auth/reset-password', {
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
            <h2 class="card-title text-center">Reset Password</h2>
            <p class="text-muted text-center mb-4">
              Enter your new password below.
            </p>

            <form @submit.prevent="submit">
              <input type="hidden" v-model="form.token" />

              <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input
                    id="password"
                    type="password"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.password }"
                    v-model="form.password"
                    required
                />
                <div v-if="form.errors.password" class="invalid-feedback">
                  {{ form.errors.password }}
                </div>
              </div>

              <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input
                    id="password_confirmation"
                    type="password"
                    class="form-control"
                    v-model="form.password_confirmation"
                    required
                />
              </div>

              <button
                  type="submit"
                  class="btn btn-primary w-100"
                  :disabled="form.processing"
              >
                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status"></span>
                Reset password
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>