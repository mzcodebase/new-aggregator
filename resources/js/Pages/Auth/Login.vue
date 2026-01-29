<template>
  <AuthLayout
    title="Log in to your account"
    description="Enter your email and password below to log in"
  >
    <Head title="Log in" />

    <form @submit.prevent="submit" class="flex flex-col gap-6">
      <div class="grid gap-6">
        <div class="grid gap-2">
          <label for="email" class="text-sm font-medium text-slate-700">Email address</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            autofocus
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
            autocomplete="current-password"
            placeholder="Password"
            class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
          />
          <p v-if="form.errors.password" class="text-sm text-red-600">{{ form.errors.password }}</p>
        </div>

        <div class="flex items-center gap-2">
          <input
            id="remember"
            v-model="form.remember"
            type="checkbox"
            class="h-4 w-4 rounded border-slate-300 text-slate-600 focus:ring-slate-500"
          />
          <label for="remember" class="text-sm text-slate-700">Remember me</label>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
        >
          <span v-if="form.processing">Logging in...</span>
          <span v-else>Log in</span>
        </button>
      </div>

      <p class="text-center text-sm text-slate-500">
        Don't have an account?
        <Link href="/register" class="font-medium text-slate-700 hover:text-slate-900">Sign up</Link>
      </p>
    </form>
  </AuthLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

function submit() {
  form.post('/login', {
    onFinish: () => form.reset('password'),
  });
}
</script>
