<template>
  <div v-if="loading" class="text-center py-12 text-gray-500">
    Loading file details…
  </div>

  <div v-else-if="error" class="text-center py-12">
    <UAlert color="error" variant="subtle" :title="error" />
    <UButton to="/" class="mt-4" variant="soft">Back to Files</UButton>
  </div>

  <div v-else-if="file" class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <UButton to="/" icon="i-heroicons-arrow-left" color="neutral" variant="ghost" />
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white truncate" :title="file.name">
          {{ file.name }}
        </h1>
      </div>
      <div class="flex items-center gap-2">
        <UButton
          color="error"
          variant="soft"
          icon="i-heroicons-trash"
          @click="showDeleteModal = true"
        >
          Delete
        </UButton>
        <a
          :href="downloadUrl"
          download
          class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-[var(--ui-primary)] rounded-md hover:opacity-90 transition"
        >
          Download
        </a>
      </div>
    </div>

    <!-- Image Preview & Details Card -->
    <UCard>
      <div class="flex flex-col md:flex-row gap-8">
        <!-- Image Preview -->
        <div class="md:w-1/2 flex items-center justify-center bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 min-h-[300px]">
          <img
            :src="previewUrl"
            :alt="file.name"
            class="max-h-[400px] w-auto object-contain"
          >
        </div>

        <!-- Details -->
        <div class="md:w-1/2 space-y-6">
          <div>
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Details</h3>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-xs text-gray-400">Uploaded</p>
                <p class="text-sm font-medium">{{ formatDate(file.created_at) }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-400">Total Views</p>
                <p class="text-sm font-medium flex items-center gap-1">
                  <UIcon name="i-heroicons-eye" class="w-4 h-4 text-gray-400" />
                  {{ file.views }}
                </p>
              </div>
              <div class="col-span-2">
                <p class="text-xs text-gray-400">Expiration</p>
                <div v-if="file.expires_at" class="flex items-center gap-2 mt-1">
                  <UIcon
                    name="i-heroicons-clock"
                    class="w-4 h-4"
                    :class="isExpired(file.expires_at) ? 'text-red-500' : 'text-orange-500'"
                  />
                  <span class="text-sm font-medium" :class="isExpired(file.expires_at) ? 'text-red-500' : 'text-orange-500'">
                    {{ formatDate(file.expires_at) }}
                    ({{ isExpired(file.expires_at) ? 'Expired' : 'Expires soon' }})
                  </span>
                </div>
                <div v-else class="flex items-center gap-2 mt-1 text-green-600">
                  <UIcon name="i-heroicons-check-circle" class="w-4 h-4" />
                  <span class="text-sm font-medium">Never expires</span>
                </div>
              </div>
            </div>
          </div>

          <UDivider />

          <div>
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Comment</h3>
            <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-md text-sm border border-gray-100 dark:border-gray-800">
              {{ file.comment || 'No comment provided.' }}
            </div>
          </div>
        </div>
      </div>
    </UCard>

    <!-- Share Links Manager -->
    <UCard>
      <template #header>
        <div class="flex items-center justify-between">
          <h3 class="font-semibold text-lg flex items-center gap-2">
            <UIcon name="i-heroicons-link" class="w-5 h-5 text-primary" />
            Share Links
          </h3>
          <div class="flex gap-2">
            <UButton
              color="neutral"
              variant="outline"
              size="sm"
              :loading="generatingLink === 'public'"
              @click="generateLink('public')"
            >
              Public Link
            </UButton>
            <UButton
              color="primary"
              variant="soft"
              size="sm"
              :loading="generatingLink === 'one-time'"
              @click="generateLink('one-time')"
            >
              One-Time Link
            </UButton>
          </div>
        </div>
      </template>

      <div v-if="!file.share_links || file.share_links.length === 0" class="text-center py-6 text-gray-500 text-sm">
        No links generated yet. Use the buttons above to create one.
      </div>
      <div v-else>
        <ul class="divide-y divide-gray-100 dark:divide-gray-800">
          <li
            v-for="link in sortedLinks"
            :key="link.id"
            class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
          >
            <div class="flex items-center gap-3 flex-wrap">
              <UBadge
                :color="link.type === 'public' ? 'success' : 'primary'"
                variant="subtle"
                size="sm"
              >
                {{ link.type === 'public' ? 'Public' : 'One-Time' }}
              </UBadge>

              <UBadge
                v-if="!link.is_valid"
                color="error"
                variant="subtle"
                size="sm"
              >
                Exhausted
              </UBadge>

              <a
                v-if="link.is_valid"
                :href="sharePageUrl(link.token)"
                target="_blank"
                class="font-mono text-sm max-w-[200px] sm:max-w-xs truncate text-primary hover:underline"
              >
                {{ sharePageUrl(link.token) }}
              </a>
              <span
                v-else
                class="font-mono text-sm max-w-[200px] sm:max-w-xs truncate line-through opacity-50"
              >
                {{ sharePageUrl(link.token) }}
              </span>

              <UButton
                v-if="link.is_valid"
                icon="i-heroicons-clipboard-document"
                color="neutral"
                variant="ghost"
                size="xs"
                title="Copy Link"
                @click="copyToClipboard(sharePageUrl(link.token))"
              />
            </div>

            <div class="text-sm text-gray-500 flex items-center gap-1 whitespace-nowrap">
              <UIcon name="i-heroicons-eye" class="w-4 h-4 opacity-75" />
              {{ link.views }} views
            </div>
          </li>
        </ul>
      </div>
    </UCard>

    <!-- Delete Confirmation Modal -->
    <UModal v-model:open="showDeleteModal" title="Delete File?" description="This action cannot be undone.">
      <template #body>
        <p class="text-sm text-gray-600 dark:text-gray-300">
          Are you sure you want to delete <strong>{{ file.name }}</strong>?
        </p>
      </template>
      <template #footer>
        <div class="flex justify-end gap-2">
          <UButton color="neutral" variant="ghost" @click="showDeleteModal = false">Cancel</UButton>
          <UButton color="error" :loading="deleting" @click="deleteFile">Yes, delete it</UButton>
        </div>
      </template>
    </UModal>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: 'auth' })

const route = useRoute()
const router = useRouter()
const config = useRuntimeConfig()
const { apiFetch } = useApi()
const token = useCookie('auth_token')
const fileId = route.params.id

interface ShareLink {
  id: number
  token: string
  type: 'public' | 'one-time'
  views: number
  is_valid: boolean
  url: string
  created_at: string
}

interface FileDetail {
  id: number
  name: string
  comment: string | null
  views: number
  download_url: string
  preview_url: string
  expires_at: string | null
  created_at: string
  share_links?: ShareLink[]
}

const file = ref<FileDetail | null>(null)
const loading = ref(true)
const error = ref<string | null>(null)

const showDeleteModal = ref(false)
const deleting = ref(false)
const generatingLink = ref<string | null>(null)

// Build authenticated URLs for <img src> and <a href download>
const apiBase = config.public.apiBase as string

const previewUrl = computed(() => {
  if (!file.value) return ''
  return `${apiBase}/files/${file.value.id}/preview?token=${token.value}`
})

const downloadUrl = computed(() => {
  if (!file.value) return ''
  return `${apiBase}/files/${file.value.id}/download?token=${token.value}`
})

// Share links point to the Blade shared page, not the JSON API
function sharePageUrl(linkToken: string): string {
  // Base URL without /api — the shared page is a web route at /shared/{token}
  const base = apiBase.replace('/api', '')
  return `${base}/shared/${linkToken}`
}

const sortedLinks = computed(() => {
  if (!file.value?.share_links) return []
  return [...file.value.share_links].sort((a, b) =>
    new Date(b.created_at).getTime() - new Date(a.created_at).getTime()
  )
})

onMounted(async () => {
  try {
    const response = await apiFetch<{ data: FileDetail }>(`/files/${fileId}`)
    file.value = response.data
  } catch {
    error.value = 'File not found or you do not have permission to view it.'
  } finally {
    loading.value = false
  }
})

function formatDate(dateStr: string): string {
  return new Date(dateStr).toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
  })
}

function isExpired(dateStr: string): boolean {
  return new Date(dateStr) < new Date()
}

async function deleteFile() {
  deleting.value = true
  try {
    await apiFetch(`/files/${fileId}`, { method: 'DELETE' })
    showDeleteModal.value = false
    router.push('/')
  } catch {
    alert('Failed to delete file. Please try again.')
  } finally {
    deleting.value = false
  }
}

async function generateLink(type: 'public' | 'one-time') {
  generatingLink.value = type
  try {
    await apiFetch(`/files/${fileId}/links`, {
      method: 'POST',
      body: { type },
    })
    // Reload file details to get the new link list
    const response = await apiFetch<{ data: FileDetail }>(`/files/${fileId}`)
    file.value = response.data
  } catch {
    alert('Failed to generate link.')
  } finally {
    generatingLink.value = null
  }
}

function copyToClipboard(text: string) {
  navigator.clipboard.writeText(text)
  alert('Link copied to clipboard!')
}
</script>
