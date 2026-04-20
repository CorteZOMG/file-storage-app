<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Reports & Statistics</h1>

    <div v-if="loading" class="text-center py-12 text-gray-500">
      Loading reports…
    </div>

    <div v-else-if="error" class="text-center py-12">
      <UAlert color="error" variant="subtle" :title="error" />
    </div>

    <div v-else-if="reports" class="space-y-8">
      <!-- High Level Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <UCard>
          <p class="text-sm text-gray-500 font-medium">Total Files</p>
          <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ reports.total_files }}</p>
        </UCard>
        <UCard>
          <p class="text-sm text-gray-500 font-medium">Deleted Files (Trash)</p>
          <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ reports.deleted_files }}</p>
        </UCard>
        <UCard>
          <p class="text-sm text-gray-500 font-medium">Public Links (Active)</p>
          <p class="text-2xl font-bold text-green-600 mt-1">{{ reports.links_stats.public.count }}</p>
          <p class="text-xs text-gray-400 mt-1">{{ reports.links_stats.public.views }} total views</p>
        </UCard>
        <UCard>
          <p class="text-sm text-gray-500 font-medium">One-Time Links</p>
          <p class="text-2xl font-bold text-purple-600 mt-1">{{ reports.links_stats.one_time.count }}</p>
          <p class="text-xs text-gray-400 mt-1">{{ reports.links_stats.one_time.views }} total views</p>
        </UCard>
      </div>

      <!-- Top Links Table -->
      <UCard>
        <template #header>
          <h2 class="text-lg font-bold text-gray-900 dark:text-white">Top 5 Most Viewed Links</h2>
        </template>

        <div v-if="reports.top_links.length === 0" class="text-gray-500 text-sm text-center py-4">
          No share links generated yet.
        </div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead>
              <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">File</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Views</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Token</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              <tr v-for="link in reports.top_links" :key="link.id" class="hover:bg-gray-50 dark:hover:bg-gray-900">
                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white truncate max-w-[200px]">
                  {{ link.file ? link.file.name : 'Unknown File' }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm">
                  <UBadge
                    :color="link.type === 'public' ? 'success' : 'primary'"
                    variant="subtle"
                    size="xs"
                  >
                    {{ link.type }}
                  </UBadge>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium">
                  {{ link.views }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm font-mono text-gray-500">
                  {{ link.token }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </UCard>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: 'auth' })

interface LinkStat {
  count: number
  views: number
}

interface TopLink {
  id: number
  token: string
  type: string
  views: number
  file?: {
    name: string
  }
}

interface ReportData {
  total_files: number
  deleted_files: number
  links_stats: {
    public: LinkStat
    one_time: LinkStat
  }
  top_links: TopLink[]
}

const { apiFetch } = useApi()

const reports = ref<ReportData | null>(null)
const loading = ref(true)
const error = ref<string | null>(null)

onMounted(async () => {
  try {
    // ReportApiController response directly returns data object, not wrapped in {data: ...}
    const response = await apiFetch<ReportData>('/reports')
    reports.value = response
  } catch {
    error.value = 'Failed to load report statistics.'
  } finally {
    loading.value = false
  }
})
</script>
