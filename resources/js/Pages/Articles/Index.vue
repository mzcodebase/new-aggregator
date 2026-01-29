<template>
  <AppLayout>
    <div>
      <h1 class="text-2xl font-bold text-slate-900">{{ isFeed ? 'My Feed' : 'Articles' }}</h1>
      <p class="mt-1 text-sm text-slate-500">
        {{ isFeed ? 'Articles from your preferred sources, categories, and authors.' : '' }}
        {{ total }} article(s)
      </p>

      <ArticlesFilter
        v-if="!isFeed"
        :filters="filters"
        :sort="sort"
        :sources="sources"
        :categories="categories"
        :authors="authors"
        :is-feed="isFeed"
      />

      <div v-if="!articles.data?.length" class="mt-6 rounded-lg border border-slate-200 bg-white p-12 text-center text-slate-500">
        {{ isFeed ? 'No articles match your preferences. Update your preferences in Settings.' : 'No articles found.' }}
      </div>

      <div v-else class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <article
          v-for="article in articles.data"
          :key="article.id"
          class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm transition hover:shadow-md"
        >
          <a
            v-if="article.image"
            :href="article.url"
            target="_blank"
            rel="noopener noreferrer"
            class="block aspect-video w-full overflow-hidden bg-slate-100"
          >
            <img
              :src="article.image"
              :alt="article.title"
              class="h-full w-full object-cover"
            />
          </a>
          <div class="p-4">
            <p class="text-xs font-medium text-slate-500">{{ article.source }}</p>
            <h2 class="mt-1 text-lg font-semibold text-slate-900 line-clamp-2">
              <a
                :href="article.url"
                target="_blank"
                rel="noopener noreferrer"
                class="hover:text-slate-600"
              >
                {{ article.title }}
              </a>
            </h2>
            <p
              v-if="article.description"
              class="mt-2 text-sm text-slate-600 line-clamp-2"
            >
              {{ article.description }}
            </p>
            <p class="mt-2 text-xs text-slate-400">
              {{ formatDate(article.published_at) }}
            </p>
            <div v-if="article.categories?.length" class="mt-2 flex flex-wrap gap-1">
              <span
                v-for="cat in article.categories"
                :key="cat.id"
                class="rounded bg-slate-100 px-2 py-0.5 text-xs text-slate-600"
              >
                {{ cat.name }}
              </span>
            </div>
          </div>
        </article>
      </div>

      <div
        v-if="prevPageUrl || nextPageUrl"
        class="mt-8 flex items-center justify-center gap-4"
      >
        <Link
          v-if="prevPageUrl"
          :href="prevPageUrl"
          preserve-state
          class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
        >
          Previous
        </Link>
        <span class="text-sm text-slate-500">
          Page {{ currentPage }} of {{ lastPage }}
        </span>
        <Link
          v-if="nextPageUrl"
          :href="nextPageUrl"
          preserve-state
          class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
        >
          Next
        </Link>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ArticlesFilter from '@/Components/ArticlesFilter.vue';

const props = defineProps({
  articles: { type: Object, required: true },
  categories: { type: Array, default: () => [] },
  sources: { type: Array, default: () => [] },
  authors: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({}) },
  sort: { type: String, default: '-published_at' },
  isFeed: { type: Boolean, default: false },
});

// Spatie Laravel Data paginated structure: { data, links, meta }
const total = computed(() => props.articles?.meta?.total ?? props.articles?.total ?? 0);
const currentPage = computed(() => props.articles?.meta?.current_page ?? props.articles?.current_page ?? 1);
const lastPage = computed(() => props.articles?.meta?.last_page ?? props.articles?.last_page ?? 1);
const prevPageUrl = computed(() => props.articles?.meta?.prev_page_url ?? props.articles?.prev_page_url ?? null);
const nextPageUrl = computed(() => props.articles?.meta?.next_page_url ?? props.articles?.next_page_url ?? null);

function formatDate(value) {
  if (!value) return '';
  const d = new Date(value);
  return d.toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
}
</script>
