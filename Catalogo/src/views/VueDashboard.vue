<script setup lang="ts">
import VueRecommended from '@/components/VueRecommended.vue'
import VueNavbar from '@/components/VueNavbar.vue'
import { onMounted, ref, computed } from 'vue'
import { useAuthStore } from '@/stores/useAuthStore'
import { useCatalogStore } from '@/stores/useCatalogStore'
import { useUserListStore } from '@/stores/useUserListStore'

const auth = useAuthStore()
const catalog = useCatalogStore()
const userList = useUserListStore()

onMounted(async () => {
  auth.loadToken()
  if (auth.isAuthenticated) {
    await auth.fetchProfile()
    await userList.fetch()
  }
  await catalog.fetchAll()
})

const search = ref('')
const filtered = computed(() =>
  search.value ? catalog.filterByTitle(search.value) : catalog.titles
)

function toggle(titleId: string) {
  if (!auth.isAuthenticated) {
    alert('Devi effettuare il login')
    return
  }
  userList.has(titleId) ? userList.remove(titleId) : userList.add(titleId)
}

/* ---------- 10 titoli consigliati ---------- */
const recommended = computed(() => {
  const genreCount: Record<string, number> = {}
  userList.myTitles.forEach(t => {
    t.genres?.forEach(g => {
      genreCount[g] = (genreCount[g] ?? 0) + 1
    })
  })

  const topGenres = Object.entries(genreCount)
    .sort((a, b) => b[1] - a[1])
    .map(g => g[0])
    .slice(0, 3)

  const notInUser = catalog.titles.filter(
    t => !userList.has(t.id) && t.genres?.some(g => topGenres.includes(g))
  )

  return notInUser.slice(0, 10)
})
</script>

<template>
  <div class="bg-slate-950 min-h-screen overflow-auto overscroll-auto flex flex-col items-center dark">
    <VueNavbar />

    <!-- Container principale -->
    <div class="min-w-200 max-w-6xl space-y-20 py-30">

      <!-- ====================  Riquadro Utente  ==================== -->
      <section>
        <h2 class="text-2xl font-semibold text-gray-100 mb-4 text-center">
          I tuoi contenuti
        </h2>
        <div class="flex justify-center">
          <VueRecommended :titles="userList.myTitles" class="max-w-full" />
        </div>
      </section>

      <!-- ====================  Riquadro Raccomandati  ==================== -->
      <section>
        <h2 class="text-2xl font-semibold text-gray-100 mb-4 text-center">
          Consigli per te
        </h2>
        <div class="flex justify-center">
          <VueRecommended :titles="recommended" class="max-w-full" />
        </div>
      </section>

    </div>
  </div>
</template>
