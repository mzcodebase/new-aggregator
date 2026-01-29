<template>
  <AppLayout>
    <div class="max-w-2xl">
      <h1 class="text-2xl font-bold text-slate-900">Feed preferences</h1>
      <p class="mt-1 text-sm text-slate-500">
        Choose your preferred sources, categories, and authors to personalize your feed.
      </p>

      <form @submit.prevent="submit" class="mt-8 space-y-8">
        <div v-if="flash?.success" class="rounded-md bg-green-50 p-4 text-sm text-green-800">
          {{ flash.success }}
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
          <h2 class="text-lg font-semibold text-slate-900">Sources</h2>
          <p class="mt-1 text-sm text-slate-500">Select which news sources appear in your feed.</p>
          <div class="mt-4 flex flex-wrap gap-3">
            <label
              v-for="s in sources"
              :key="s"
              class="inline-flex cursor-pointer items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2 text-sm hover:bg-slate-50"
              :class="{ 'border-slate-800 bg-slate-100': form.preferred_sources.includes(s) }"
            >
              <input
                v-model="form.preferred_sources"
                type="checkbox"
                :value="s"
                class="h-4 w-4 rounded border-slate-300 text-slate-600 focus:ring-slate-500"
              />
              {{ s }}
            </label>
          </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
          <h2 class="text-lg font-semibold text-slate-900">Categories</h2>
          <p class="mt-1 text-sm text-slate-500">Select categories you want to see.</p>
          <div class="mt-4 flex flex-wrap gap-3">
            <label
              v-for="c in categories"
              :key="c.id"
              class="inline-flex cursor-pointer items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2 text-sm hover:bg-slate-50"
              :class="{ 'border-slate-800 bg-slate-100': form.preferred_category_ids.includes(c.id) }"
            >
              <input
                v-model="form.preferred_category_ids"
                type="checkbox"
                :value="c.id"
                class="h-4 w-4 rounded border-slate-300 text-slate-600 focus:ring-slate-500"
              />
              {{ c.name }}
            </label>
          </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
          <h2 class="text-lg font-semibold text-slate-900">Authors</h2>
          <p class="mt-1 text-sm text-slate-500">Select authors you want to follow.</p>
          <div class="mt-4 flex max-h-48 flex-wrap gap-3 overflow-y-auto">
            <label
              v-for="a in authors"
              :key="a.id"
              class="inline-flex cursor-pointer items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2 text-sm hover:bg-slate-50"
              :class="{ 'border-slate-800 bg-slate-100': form.preferred_author_ids.includes(a.id) }"
            >
              <input
                v-model="form.preferred_author_ids"
                type="checkbox"
                :value="a.id"
                class="h-4 w-4 rounded border-slate-300 text-slate-600 focus:ring-slate-500"
              />
              {{ a.name }}
            </label>
          </div>
        </div>

        <div class="flex gap-3">
          <button
            type="submit"
            :disabled="form.processing"
            class="rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
          >
            {{ form.processing ? 'Saving...' : 'Save preferences' }}
          </button>
          <Link
            href="/feed"
            class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
          >
            View my feed
          </Link>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();
const flash = page.props.flash ?? {};

const props = defineProps({
  preferences: { type: Object, required: true },
  sources: { type: Array, default: () => [] },
  categories: { type: Array, default: () => [] },
  authors: { type: Array, default: () => [] },
});

const form = useForm({
  preferred_sources: props.preferences.preferred_sources ?? [],
  preferred_category_ids: props.preferences.preferred_category_ids ?? [],
  preferred_author_ids: props.preferences.preferred_author_ids ?? [],
});

function submit() {
  form.put('/settings/preferences');
}
</script>
