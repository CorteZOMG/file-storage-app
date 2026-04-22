<template>
  <div class="max-w-xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Upload File</h1>

    <UAlert
      v-if="errors.general"
      color="error"
      variant="subtle"
      :title="errors.general[0]"
      class="mb-4"
    />

    <UAlert
      v-if="success"
      color="success"
      variant="subtle"
      title="File uploaded successfully! Redirecting…"
      class="mb-4"
    />

    <UCard>
      <form class="space-y-5" @submit.prevent="handleSubmit">
        <!-- File Picker -->
        <UFormField label="Image File" :error="errors.file?.[0] || clientErrors.file">
          <input
            id="file-input"
            ref="fileInput"
            type="file"
            accept="image/*"
            class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary/20 file:cursor-pointer cursor-pointer"
            @change="onFileSelected"
          >
        </UFormField>

        <!-- Comment -->
        <UFormField label="Comment (optional)" :error="errors.comment?.[0]">
          <UTextarea
            id="comment"
            v-model="form.comment"
            placeholder="Add a description…"
            :rows="3"
            class="w-full"
          />
        </UFormField>

        <!-- Expiry Date -->
        <UFormField label="Expires at (optional)" :error="errors.expires_at?.[0] || clientErrors.expires_at">
          <UInput
            id="expires_at"
            v-model="form.expires_at"
            type="datetime-local"
            class="w-full"
          />
        </UFormField>

        <div class="flex justify-end pt-2">
          <UButton type="submit" size="lg" :loading="loading" :disabled="loading">
            Upload
          </UButton>
        </div>
      </form>
    </UCard>
  </div>
</template>

<script setup lang="ts">
import type { ValidationErrors } from '~/composables/useApi'
import { parseValidationErrors } from '~/composables/useApi'
import type { FetchError } from 'ofetch'

definePageMeta({ middleware: 'auth' })

const { apiFetch } = useApi()
const router = useRouter()

const fileInput = ref<HTMLInputElement | null>(null)
const selectedFile = ref<File | null>(null)

const form = reactive({
  comment: '',
  expires_at: '',
})

const errors = ref<ValidationErrors>({})
const clientErrors = reactive({
  file: '',
  expires_at: '',
})
const loading = ref(false)
const success = ref(false)

const MAX_SIZE_MB = 5
const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml']

function onFileSelected(event: Event) {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0] ?? null
  selectedFile.value = file
  clientErrors.file = ''

  if (!file) return

  if (!ALLOWED_TYPES.includes(file.type)) {
    clientErrors.file = 'Only image files are allowed (JPEG, PNG, GIF, WebP, SVG).'
    selectedFile.value = null
    return
  }

  if (file.size > MAX_SIZE_MB * 1024 * 1024) {
    clientErrors.file = `File must be smaller than ${MAX_SIZE_MB}MB.`
    selectedFile.value = null
  }
}

function validateClient(): boolean {
  let valid = true
  clientErrors.file = ''
  clientErrors.expires_at = ''

  if (!selectedFile.value) {
    clientErrors.file = 'Please select a file.'
    valid = false
  }

  if (form.expires_at) {
    const expiryDate = new Date(form.expires_at)
    if (expiryDate <= new Date()) {
      clientErrors.expires_at = 'Expiry date must be in the future.'
      valid = false
    }
  }

  return valid
}

async function handleSubmit() {
  errors.value = {}
  if (!validateClient()) return

  loading.value = true

  const formData = new FormData()
  formData.append('file', selectedFile.value!)
  if (form.comment) formData.append('comment', form.comment)
  if (form.expires_at) {
    formData.append('expires_at', new Date(form.expires_at).toISOString())
  }

  try {
    await apiFetch('/files', {
      method: 'POST',
      body: formData,
    })
    success.value = true
    setTimeout(() => router.push('/'), 1000)
  } catch (e) {
    errors.value = parseValidationErrors(e as FetchError)
  } finally {
    loading.value = false
  }
}
</script>
