<template>
  <div class="max-w-4xl mx-auto py-8">
    <div v-if="loading" class="text-center py-20 text-gray-500">
      <UIcon name="i-heroicons-arrow-path" class="w-8 h-8 animate-spin mx-auto mb-4 text-primary" />
      <p>Loading shared file…</p>
    </div>

    <div v-else-if="error" class="text-center py-20">
      <UAlert
        color="error"
        variant="subtle"
        title="Link Expired or Invalid"
        :description="error"
        class="max-w-md mx-auto"
      />
      <UButton to="/" class="mt-6" variant="soft">Go to My Files</UButton>
    </div>

    <div v-else-if="file" class="space-y-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white truncate" :title="file.name">
          {{ file.name }}
        </h1>
        <a
          :href="imageUrl"
          download
          class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-[var(--ui-primary)] rounded-md hover:opacity-90 transition shadow-sm"
        >
          <UIcon name="i-heroicons-arrow-down-tray" class="w-4 h-4" />
          Download
        </a>
      </div>

      <UCard>
        <div class="flex flex-col md:flex-row gap-8">
          <!-- Image Display (Using signed URL) -->
          <div class="md:w-3/5 flex items-center justify-center bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 min-h-[300px] p-2">
            <img
              :src="imageUrl"
              :alt="file.name"
              class="max-h-[500px] w-auto object-contain rounded"
            >
          </div>

          <!-- Public Details -->
          <div class="md:w-2/5 space-y-6">
            <div>
              <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">File Info</h3>
              <div class="space-y-3">
                <div>
                  <p class="text-xs text-gray-400">Name</p>
                  <p class="text-sm font-medium break-all">{{ file.name }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-400">Uploaded On</p>
                  <p class="text-sm font-medium">{{ formatDate(file.created_at) }}</p>
                </div>
              </div>
            </div>

            <UDivider v-if="file.comment" />

            <div v-if="file.comment">
              <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Description</h3>
              <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-md text-sm border border-gray-100 dark:border-gray-800 text-gray-700 dark:text-gray-300">
                {{ file.comment }}
              </div>
            </div>
          </div>
        </div>
      </UCard>
    </div>
  </div>
</template>

<script setup lang="ts">
// NO auth middleware — this page is strictly public.
definePageMeta({ layout: 'default' })

const route = useRoute()
const { apiFetch } = useApi()
const token = route.params.token as string

interface SharedFile {
  name: string
  comment: string | null
  created_at: string
}

interface ShareResponse {
  message: string
  data: SharedFile
  image_url: string
}

const file = ref<SharedFile | null>(null)
const imageUrl = ref<string>('')
const loading = ref(true)
const error = ref<string | null>(null)

onMounted(async () => {
  try {
    const response = await apiFetch<ShareResponse>(`/share/${token}`)
    file.value = response.data
    imageUrl.value = response.image_url
  } catch (err: any) {
    // Determine context string for generic 404s
    error.value = err.response?.status === 404
      ? 'This shared link is no longer active. It may have expired or been deleted.'
      : 'Failed to load the shared file.'
  } finally {
    loading.value = false
  }
})

function formatDate(dateStr: string): string {
  return new Date(dateStr).toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}
</script>
