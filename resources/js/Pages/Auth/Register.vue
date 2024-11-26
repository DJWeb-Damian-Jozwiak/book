<script setup>
import { ref } from 'vue'
import {Link} from "@inertiajs/vue3";
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  username: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post('/auth/register', {
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
            <h2 class="card-title text-center mb-4">Create account</h2>

            <form @submit.prevent="submit">
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input
                    id="username"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.username }"
                    v-model="form.username"
                    required
                />
                <div v-if="form.errors.username" class="invalid-feedback">
                  {{ form.errors.username }}
                </div>
              </div>

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
                  class="btn btn-primary w-100 mb-3"
                  :disabled="form.processing"
              >
                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status"></span>
                Create account
              </button>

              <div class="text-center">
                <Link href="/auth/login" class="text-decoration-none">
                  Already have an account? Sign in
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