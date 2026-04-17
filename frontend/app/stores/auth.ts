import { defineStore } from 'pinia'
import type { FetchError } from 'ofetch'
import { parseValidationErrors } from '~/composables/useApi'

export interface AuthUser {
  id: number
  name: string
  email: string
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterPayload {
  name: string
  email: string
  password: string
  password_confirmation: string
}

interface AuthResponse {
  access_token: string
  token_type: string
  user: AuthUser
}

export const useAuthStore = defineStore('auth', () => {
  const token = useCookie('auth_token', { maxAge: 60 * 60 * 24 * 7 }) // 7 days
  const user = ref<AuthUser | null>(null)

  const isAuthenticated = computed(() => !!token.value)

  function setAuth(response: AuthResponse): void {
    token.value = response.access_token
    user.value = response.user
  }

  function clearAuth(): void {
    token.value = null
    user.value = null
  }

  async function authenticate<T extends object>(endpoint: string, payload: T): Promise<void> {
    const { apiFetch } = useApi()

    try {
      const response = await apiFetch<AuthResponse>(endpoint, {
        method: 'POST',
        body: payload,
      })

      setAuth(response)
    } catch (error) {
      throw parseValidationErrors(error as FetchError)
    }
  }

  /**
   * Log in with email & password.
   * @throws ValidationErrors on 422 responses.
   */
  async function login(credentials: LoginCredentials): Promise<void> {
    await authenticate('/login', credentials)
  }

  /**
   * Register a new user account.
   * @throws ValidationErrors on 422 responses.
   */
  async function register(payload: RegisterPayload): Promise<void> {
    await authenticate('/register', payload)
  }

  async function logout(): Promise<void> {
    const { apiFetch } = useApi()

    try {
      await apiFetch('/logout', { method: 'POST' })
    } finally {
      clearAuth()
    }
  }

  async function fetchUser(): Promise<void> {
    if (!token.value) return

    const { apiFetch } = useApi()

    try {
      user.value = await apiFetch<AuthUser>('/user')
    } catch {
      clearAuth()
    }
  }

  return {
    token,
    user,
    isAuthenticated,
    login,
    register,
    logout,
    fetchUser,
  }
})
