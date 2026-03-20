import { defineStore } from 'pinia';
import axios from 'axios';
import { useCatalogStore } from './useCatalogStore';

export const useUserListStore = defineStore('userList', {
  state: () => ({
    myTitles: [] as Array<{
      id: string
      primary_title: string
      original_title: string
      type: string
      start_year?: number
      image_url?: string
      genres?: string[]
    }>,
    loading: false,
    error: null as string | null,
  }),

  actions: {
    async fetch() {
        this.loading = true
        this.error = null
        try {
          const { data } = await axios.get('/apicatalogo/my_titles.php')
          this.myTitles = data
          const catalog = useCatalogStore()
          if (!catalog.titles.length) await catalog.fetchAll()
  
          const catalogMap = new Map(
            catalog.titles.map(t => [t.id, t])
          )

          this.myTitles = this.myTitles.map(userTitle => {
            const full = catalogMap.get(userTitle.id)
            return full
              ? { ...userTitle, genres: full.genres }
              : userTitle
          })
        } catch (e: any) {
          this.error = e.response?.data?.error ?? e.message
        } finally {
          this.loading = false
        }
      },

    async add(titleId: string) {
      try {
        await axios.post('/apicatalogo/add_title.php', { title_id: titleId })
        await this.fetch()
      } catch (e: any) {
        this.error = e.response?.data?.error ?? e.message
      }
    },

    async remove(titleId: string) {
      try {
        await axios.delete(`/apicatalogo/remove_title.php?title_id=${titleId}`)
        await this.fetch()
      } catch (e: any) {
        this.error = e.response?.data?.error ?? e.message
      }
    },

    has(titleId: string) {
      return this.myTitles.some((t) => t.id === titleId)
    },

    async exportAsJson() {
        if (!this.myTitles.length) await this.fetch()
  
        const json = JSON.stringify(this.myTitles, null, 2)
        const blob = new Blob([json], { type: 'application/json' })
        const url = URL.createObjectURL(blob)

        const a = document.createElement('a')
        a.href = url
        a.download = 'my_catalog.json'
        document.body.appendChild(a)
        a.click()
        document.body.removeChild(a)

        URL.revokeObjectURL(url)
      },
  },
})
