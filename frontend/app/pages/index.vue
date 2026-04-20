<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Files</h1>
        <p class="text-sm text-gray-500 mt-1">{{ files.length }} {{ files.length === 1 ? 'file' : 'files' }}</p>
      </div>
      <UButton to="/files/upload" size="md">
        Upload File
      </UButton>
    </div>

    <div v-if="loading" class="text-center py-12 text-gray-500">
      Loading files…
    </div>

    <div v-else-if="error" class="text-center py-12">
      <UAlert color="error" variant="subtle" :title="error" />
    </div>

    <div v-else-if="files.length === 0" class="text-center py-16">
      <p class="text-gray-400 text-lg mb-4">No files uploaded yet.</p>
      <UButton to="/files/upload" variant="soft">Upload your first file</UButton>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <NuxtLink
        v-for="file in files"
        :key="file.id"
        :to="`/files/${file.id}`"
        class="block group"
      >
        <UCard class="h-full transition-shadow group-hover:shadow-md">
          <!-- Image thumbnail -->
          <div class="aspect-4/3 -mx-4 -mt-4 mb-4 overflow-hidden rounded-t-lg bg-gray-100 dark:bg-gray-800">
            <img
              :src="previewUrl(file)"
              :alt="file.name"
              class="w-full h-full object-cover transition-transform group-hover:scale-105"
              loading="lazy"
            >
          </div>

          <h3 class="font-semibold text-gray-900 dark:text-white truncate" :title="file.name">
            {{ file.name }}
          </h3>

          <p v-if="file.comment" class="text-sm text-gray-500 mt-1 line-clamp-2">
            {{ file.comment }}
          </p>
          <p v-else class="text-sm text-gray-400 italic mt-1">No comment</p>

          <div class="flex items-center justify-between mt-3 text-xs text-gray-400">
            <span>{{ formatDate(file.created_at) }}</span>
            <UBadge
              v-if="file.expires_at"
              :color="isExpired(file.expires_at) ? 'error' : 'warning'"
              variant="subtle"
              size="xs"
            >
              {{ isExpired(file.expires_at) ? 'Expired' : 'Expires soon' }}
            </UBadge>
          </div>
        </UCard>
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: 'auth' })

interface FileItem {
  id: number
  name: string
  comment: string | null
  views: number
  download_url: string
  preview_url: string
  expires_at: string | null
  created_at: string
}

const { apiFetch } = useApi()
const authStore = useAuthStore()

const files = ref<FileItem[]>([])
const loading = ref(true)
const error = ref<string | null>(null)

onMounted(async () => {
  if (!authStore.user) {
    await authStore.fetchUser()
  }

  try {
    const response = await apiFetch<{ data: FileItem[] }>('/files')
    files.value = response.data
  } catch {
    error.value = 'Failed to load files.'
  } finally {
    loading.value = false
  }
})

function previewUrl(file: FileItem): string {
  const config = useRuntimeConfig()
  const token = useCookie('auth_token')
  return `${config.public.apiBase}/files/${file.id}/preview?token=${token.value}`
}

function formatDate(dateStr: string): string {
  return new Date(dateStr).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  })
}

function isExpired(dateStr: string): boolean {
  return new Date(dateStr) < new Date()
}
</script>
