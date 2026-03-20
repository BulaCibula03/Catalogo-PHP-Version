import { defineStore } from 'pinia';

import axios from 'axios';

axios.defaults.headers.common['Content-Type'] = 'application/json'

axios.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('apiToken')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => Promise.reject(error)
)

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: '' as string,
    user: null as null | {
      id: number
      full_name: string
      email: string
      password_hash: string
      phone?: string
      gender?: string
      bio?: string
      profile_image?: string
    },
    loading: false,
    error: null as string | null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
  },

  actions: {
    setToken(token: string) {
      this.token = token
      localStorage.setItem('apiToken', token)
    },

    loadToken() {
      const t = localStorage.getItem('apiToken')
      if (t) this.setToken(t)
    },

    async login(email: string, password: string) {
      this.loading = true
      this.error = null
      try {
        const { data } = await axios.post('/apicatalogo/login.php', { email, password })
        this.setToken(data.token)
        await this.fetchProfile()
        return true
      } catch (e: any) {
        this.error = e.response?.data?.error ?? e.message
        return false
      } finally {
        this.loading = false
      }
    },

    logout() {
      this.token = ''
      this.user = null
      localStorage.removeItem('apiToken')
    },

    async fetchProfile() {
      if (!this.isAuthenticated) return
      try {
        const { data } = await axios.get('/apicatalogo/user.php')
        this.user = data
      } catch (e: any) {
        this.error = e.response?.data?.error ?? e.message
      }
    },

    async register(payload: {
      full_name: string
      email: string
      password: string
      phone?: string
      gender?: string
      bio?: string
      profile_image?: string
    }) {
      this.loading = true
      this.error = null
      try {
        await axios.post('/apicatalogo/register.php', payload)
        return await this.login(payload.email, payload.password)
      } catch (e: any) {
        this.error = e.response?.data?.error ?? e.message
        return false
      } finally {
        this.loading = false
      }
    },

    async updateProfile(payload: Partial<{
      full_name: string
      email: string
      password: string
      phone: string
      gender: string
      bio: string
      profile_image: string
    }>) {
      if (!this.isAuthenticated) return
      try {
        await axios.put('/apicatalogo/user.php', payload)
        await this.fetchProfile()
      } catch (e: any) {
        this.error = e.response?.data?.error ?? e.message
      }
    },

    async deleteAccount() {
      if (!this.isAuthenticated) return
      try {
        await axios.delete('/apicatalogo/user.php')
        this.logout()
      } catch (e: any) {
        this.error = e.response?.data?.error ?? e.message
      }
    },
  },
})
