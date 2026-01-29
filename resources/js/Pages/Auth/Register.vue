<template>
  <AuthLayout
    title="Create an account"
    description="Enter your details below to create your account"
  >
    <Head title="Register" />

    <form @submit.prevent="submit" class="flex flex-col gap-6">
      <div class="grid gap-6">
        <div class="grid gap-2">
          <label for="name" class="text-sm font-medium text-slate-700">Name</label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            required
            autofocus
            autocomplete="name"
            placeholder="Full name"
            class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
          />
          <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
        </div>

        <div class="grid gap-2">
          <label for="email" class="text-sm font-medium text-slate-700">Email address</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
            class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
          />
          <p v-if="form.errors.email" class="text-sm text-red-600">{{ form.errors.email }}</p>
        </div>

        <div class="grid gap-2">
          <label for="password" class="text-sm font-medium text-slate-700">Password</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            autocomplete="new-password"
            placeholder="Password (min 8 characters)"
            class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
          />
          <p v-if="form.errors.password" class="text-sm text-red-600">{{ form.errors.password }}</p>
        </div>

        <div class="grid gap-2">
          <label for="password_confirmation" class="text-sm font-medium text-slate-700">Confirm password</label>
          <input
            id="password_confirmation"
            v-model="form.password_confirmation"
            type="password"
            required
            autocomplete="new-password"
            placeholder="Confirm password"
            class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
          />
          <p v-if="form.errors.password_confirmation" class="text-sm text-red-600">
            {{ form.errors.password_confirmation }}
          </p>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
        >
          <span v-if="form.processing">Creating account...</span>
          <span v-else>Create account</span>
        </button>
      </div>

      <p class="text-center text-sm text-slate-500">
        Already have an account?
        <Link href="/login" class="font-medium text-slate-700 hover:text-slate-900">Log in</Link>
      </p>
    </form>
  </AuthLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

function submit() {
  form.post('/register', {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
}
</script>
