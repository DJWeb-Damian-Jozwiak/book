<script setup>
import { ref } from 'vue'
import {Link} from "@inertiajs/vue3";
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  login: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post('/auth/login', {
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
            <h2 class="card-title text-center mb-4">Sign in</h2>

            <form @submit.prevent="submit">
              <div class="mb-3">
                <label for="login" class="form-label">Email or Username</label>
                <input
                    id="login"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.login }"
                    v-model="form.login"
                    required
                />
                <div v-if="form.errors.login" class="invalid-feedback">
                  {{ form.errors.login }}
                </div>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
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

              <div class="mb-3 d-flex justify-content-between align-items-center">
                <div class="form-check">
                  <input
                      id="remember"
                      type="checkbox"
                      class="form-check-input"
                      v-model="form.remember"
                  />
                  <label class="form-check-label" for="remember">
                    Remember me
                  </label>
                </div>

                <Link href="/auth/forgot-password" class="text-decoration-none">
                  Forgot your password?
                </Link>
              </div>

              <button
                  type="submit"
                  class="btn btn-primary w-100 mb-3"
                  :disabled="form.processing"
              >
                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status"></span>
                Sign in
              </button>

              <div class="text-center">
                <Link href="/auth/register" class="text-decoration-none">
                  Don't have an account? Sign up
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