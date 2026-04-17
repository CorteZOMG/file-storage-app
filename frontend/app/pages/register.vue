<template>
  <div>
    <UAlert
      v-if="errors.general"
      color="error"
      variant="subtle"
      :title="errors.general[0]"
      class="mb-4"
    />

    <form class="space-y-4" @submit.prevent="handleSubmit">
      <UFormField label="Name" :error="errors.name?.[0]">
        <UInput
          id="name"
          v-model="form.name"
          type="text"
          placeholder="John Doe"
          size="lg"
          class="w-full"
        />
      </UFormField>

      <UFormField label="Email" :error="errors.email?.[0]">
        <UInput
          id="email"
          v-model="form.email"
          type="email"
          placeholder="you@example.com"
          size="lg"
          class="w-full"
        />
      </UFormField>

      <UFormField label="Password" :error="errors.password?.[0]">
        <UInput
          id="password"
          v-model="form.password"
          type="password"
          placeholder="••••••••"
          size="lg"
          class="w-full"
        />
      </UFormField>

      <UFormField label="Confirm Password" :error="errors.password_confirmation?.[0]">
        <UInput
          id="password_confirmation"
          v-model="form.password_confirmation"
          type="password"
          placeholder="••••••••"
          size="lg"
          class="w-full"
        />
      </UFormField>

      <div class="flex items-center justify-between pt-2">
        <NuxtLink
          to="/login"
          class="text-sm text-primary hover:underline"
        >
          Already registered?
        </NuxtLink>

        <UButton type="submit" size="lg" :loading="loading">
          Register
        </UButton>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import type { ValidationErrors } from '~/composables/useApi'

definePageMeta({ layout: 'guest' })

const authStore = useAuthStore()
const router = useRouter()

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const errors = ref<ValidationErrors>({})
const loading = ref(false)

async function handleSubmit() {
  errors.value = {}
  loading.value = true

  try {
    await authStore.register(form)
    await router.push('/')
  } catch (e) {
    errors.value = e as ValidationErrors
  } finally {
    loading.value = false
  }
}
</script>
