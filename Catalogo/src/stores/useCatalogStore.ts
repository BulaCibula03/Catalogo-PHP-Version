import { defineStore } from 'pinia';


import axios from 'axios';

export const useCatalogStore = defineStore('catalog', {
  state: () => ({
    titles: [] as Array<{
      id: string;
      type: string;
      primary_title: string;
      original_title: string;
      start_year?: number;
      end_year?: number;
      runtime_seconds?: number;
      plot?: string;
      image_url?: string;
      genres?: string[];
    }>,

    allTitles: [] as Array<{
      id: string;
      type: string;
      primary_title: string;
      original_title: string;
      start_year?: number;
      end_year?: number;
      runtime_seconds?: number;
      plot?: string;
      image_url?: string;
      genres?: string[];
    }>,
    loading: false,
    error: null as string | null,
  }),

  actions: {
    async fetchAll() {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await axios.get('/apicatalogo/catalog.php');
        this.titles = data;
        this.allTitles = data;         
      } catch (e: any) {
        this.error = e.response?.data?.error ?? e.message;
      } finally {
        this.loading = false;
      }
    },

    filterByTitle(query: string) {
      const q = query.toLowerCase().trim();

      if (!q) {
        this.titles = this.allTitles;
        return;
      }

      this.titles = this.allTitles.filter(
        (t) =>
          t.primary_title.toLowerCase().includes(q) ||
          t.original_title.toLowerCase().includes(q)
      );
    },

    async exportAsJson() {
      if (!this.titles.length) await this.fetchAll();

      const json = JSON.stringify(this.titles, null, 2);
      const blob = new Blob([json], { type: 'application/json' });
      const url = URL.createObjectURL(blob);

      const a = document.createElement('a');
      a.href = url;
      a.download = 'catalog.json';
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);

      URL.revokeObjectURL(url);
    },
  },
});
