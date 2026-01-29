<template>
  <div class="min-h-screen bg-slate-50">
    <nav class="border-b border-slate-200 bg-white">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
          <div class="flex items-center">
            <Link
              href="/"
              class="text-xl font-semibold text-slate-800 hover:text-slate-600"
            >
              News Aggregator
            </Link>
            <div class="ml-10 flex gap-6">
              <Link
                href="/"
                class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900"
                :class="{ 'bg-slate-100 text-slate-900': page.url === '/' || page.url.startsWith('/?') }"
              >
                Home
              </Link>
              <Link
                v-if="auth.user"
                href="/feed"
                class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900"
                :class="{ 'bg-slate-100 text-slate-900': page.url.startsWith('/feed') }"
              >
                My Feed
              </Link>
              <Link
                v-if="auth.user"
                href="/settings/preferences"
                class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900"
                :class="{ 'bg-slate-100 text-slate-900': page.url.startsWith('/settings') }"
              >
                Settings
              </Link>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <template v-if="auth.user">
              <span class="text-sm text-slate-600">{{ auth.user.name }}</span>
              <button
                type="button"
                @click="logout"
                class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100"
              >
                Log out
              </button>
            </template>
            <template v-else>
              <Link
                href="/login"
                class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100"
              >
                Log in
              </Link>
              <Link
                href="/register"
                class="rounded-md bg-slate-800 px-3 py-2 text-sm font-medium text-white hover:bg-slate-700"
              >
                Register
              </Link>
            </template>
          </div>
        </div>
      </div>
    </nav>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const auth = page.props.auth;

function logout() {
  router.post('/logout');
}
</script>
