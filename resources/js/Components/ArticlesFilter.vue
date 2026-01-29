<template>
  <div class="mb-6 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
    <h2 class="mb-4 text-lg font-semibold text-slate-900">Filter Articles</h2>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
      <div>
        <label for="filter-title" class="mb-1 block text-sm font-medium text-slate-700">Search</label>
        <input
          id="filter-title"
          v-model="localFilters.title"
          type="text"
          placeholder="Keyword..."
          class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
          @keyup.enter="apply"
        />
      </div>
      <div>
        <label for="filter-source" class="mb-1 block text-sm font-medium text-slate-700">Source</label>
        <select
          id="filter-source"
          v-model="localFilters.source"
          class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
        >
          <option value="">All sources</option>
          <option v-for="s in sources" :key="s" :value="s">{{ s }}</option>
        </select>
      </div>
      <div>
        <label for="filter-category" class="mb-1 block text-sm font-medium text-slate-700">Category</label>
        <select
          id="filter-category"
          v-model="localFilters['categories.name']"
          class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
        >
          <option value="">All categories</option>
          <option v-for="c in categories" :key="c.id" :value="c.name">{{ c.name }}</option>
        </select>
      </div>
      <div>
        <label for="filter-author" class="mb-1 block text-sm font-medium text-slate-700">Author</label>
        <select
          id="filter-author"
          v-model="localFilters['authors.name']"
          class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
        >
          <option value="">All authors</option>
          <option v-for="a in authors" :key="a.id" :value="a.name">{{ a.name }}</option>
        </select>
      </div>
      <div>
        <label for="filter-sort" class="mb-1 block text-sm font-medium text-slate-700">Sort</label>
        <select
          id="filter-sort"
          v-model="localSort"
          class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
        >
          <option value="-published_at">Newest first</option>
          <option value="published_at">Oldest first</option>
          <option value="title">Title A–Z</option>
          <option value="-title">Title Z–A</option>
          <option value="-source">Source</option>
        </select>
      </div>
    </div>
    <div class="mt-4 flex gap-2">
      <button
        type="button"
        @click="apply"
        class="rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700"
      >
        Apply filters
      </button>
      <button
        type="button"
        @click="reset"
        class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
      >
        Reset
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  filters: { type: Object, required: true },
  sort: { type: String, default: '-published_at' },
  sources: { type: Array, default: () => [] },
  categories: { type: Array, default: () => [] },
  authors: { type: Array, default: () => [] },
  isFeed: { type: Boolean, default: false },
});

const localFilters = ref({ ...props.filters });
const localSort = ref(props.sort);

watch(
  () => [props.filters, props.sort],
  () => {
    localFilters.value = { ...props.filters };
    localSort.value = props.sort;
  }
);

function buildQuery() {
  const q = {};
  if (localFilters.value.title) q['filter[title]'] = localFilters.value.title;
  if (localFilters.value.source) q['filter[source]'] = localFilters.value.source;
  if (localFilters.value['categories.name']) q['filter[categories.name]'] = localFilters.value['categories.name'];
  if (localFilters.value['authors.name']) q['filter[authors.name]'] = localFilters.value['authors.name'];
  if (localFilters.value['published_at']) q['filter[published_at]'] = localFilters.value['published_at'];
  if (localSort.value) q.sort = localSort.value;
  return q;
}

function apply() {
  router.get('/', buildQuery(), { preserveState: true });
}

function reset() {
  localFilters.value = {
    title: '',
    source: '',
    'categories.name': '',
    'authors.name': '',
    'published_at': '',
  };
  localSort.value = '-published_at';
  router.get('/', {}, { preserveState: true });
}
</script>
