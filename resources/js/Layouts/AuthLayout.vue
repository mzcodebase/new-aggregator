<template>
  <div class="flex min-h-screen flex-col items-center justify-center gap-6 bg-slate-50 px-4 py-12">
    <div class="w-full max-w-sm">
      <div class="flex flex-col gap-8">
        <div class="flex flex-col items-center gap-4">
          <Link
            href="/"
            class="flex flex-col items-center gap-2 font-medium text-slate-800 hover:text-slate-600"
          >
            <span class="text-xl font-semibold">News Aggregator</span>
          </Link>
          <div class="space-y-2 text-center">
            <h1 class="text-xl font-medium text-slate-900">{{ title }}</h1>
            <p class="text-sm text-slate-500">{{ description }}</p>
          </div>
        </div>
        <slot />
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  title: { type: String, required: true },
  description: { type: String, required: true },
});
</script>
