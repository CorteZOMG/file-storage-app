<template>
  <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50 dark:bg-gray-950">
    <div class="mb-4">
      <NuxtLink to="/" class="text-2xl font-bold text-primary hover:opacity-80 transition">
        FileStorage
      </NuxtLink>
    </div>

    <UCard class="w-full sm:max-w-md">
      <slot />
    </UCard>
  </div>
</template>
