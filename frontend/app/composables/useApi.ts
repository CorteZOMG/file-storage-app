import type { FetchOptions } from 'ofetch'

/**
 * Composable that provides a pre-configured $fetch wrapper for the Laravel API.
 *
 * - Reads the base URL from runtimeConfig.public.apiBase
 * - Automatically attaches the Authorization header when an auth token cookie exists
 * - Returns a typed `apiFetch` function that mirrors $fetch's signature
 */
export function useApi() {
  const config = useRuntimeConfig()
  const token = useCookie('auth_token')

  /**
   * Wrapper around Nuxt's $fetch configured for the Laravel API.
   */
  async function apiFetch<T>(endpoint: string, options: FetchOptions = {}): Promise<T> {
    const headers: Record<string, string> = {
      Accept: 'application/json',
      ...(options.headers as Record<string, string> || {}),
    }

    if (token.value) {
      headers.Authorization = `Bearer ${token.value}`
    }

    return $fetch<T>(endpoint, {
      baseURL: config.public.apiBase as string,
      ...options,
      headers,
    })
  }

  return { apiFetch }
}
