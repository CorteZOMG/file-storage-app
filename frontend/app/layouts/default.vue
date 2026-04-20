<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
    <!-- Top Navigation -->
    <nav class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
      <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-14">
          <!-- Left: Logo + Links -->
          <div class="flex items-center gap-6">
            <NuxtLink to="/" class="text-lg font-semibold text-gray-900 dark:text-white">
              FileStorage
            </NuxtLink>

            <div class="hidden sm:flex items-center gap-1">
              <UButton
                to="/"
                variant="ghost"
                color="neutral"
                size="sm"
              >
                Files
              </UButton>
              <UButton
                to="/files/upload"
                variant="ghost"
                color="neutral"
                size="sm"
              >
                Upload
              </UButton>
              <UButton
                to="/reports"
                variant="ghost"
                color="neutral"
                size="sm"
              >
                Reports
              </UButton>
            </div>
          </div>

          <!-- Right: User + Logout -->
          <div class="flex items-center gap-3">
            <span v-if="authStore.user" class="text-sm text-gray-600 dark:text-gray-400 hidden sm:block">
              {{ authStore.user.name }}
            </span>
            <UButton
              variant="soft"
              color="neutral"
              size="sm"
              @click="handleLogout"
            >
              Log out
            </UButton>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
      <slot />
    </main>
  </div>
</template>

<script setup lang="ts">
const authStore = useAuthStore()
const router = useRouter()

onMounted(() => {
  if (authStore.isAuthenticated && !authStore.user) {
    authStore.fetchUser()
  }
})

async function handleLogout() {
  await authStore.logout()
  await router.push('/login')
}
</script>
