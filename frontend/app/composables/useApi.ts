import type { FetchError } from 'ofetch'
import type { NitroFetchOptions, NitroFetchRequest } from 'nitropack'

export interface ValidationErrors {
  [field: string]: string[]
}

export function parseValidationErrors(error: FetchError): ValidationErrors {
  if (error.response?.status === 422 && error.data?.errors) {
    return error.data.errors as ValidationErrors
  }

  return { general: [error.data?.message || 'An unexpected error occurred.'] }
}

export function useApi() {
  const config = useRuntimeConfig()
  const token = useCookie('auth_token')

  async function apiFetch<T>(
    endpoint: string,
    options: NitroFetchOptions<NitroFetchRequest> = {},
  ): Promise<T> {
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
