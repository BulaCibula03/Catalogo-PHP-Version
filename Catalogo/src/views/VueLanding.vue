<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/useAuthStore'
import { useCatalogStore } from '@/stores/useCatalogStore'
//import { gsap } from 'gsap'
const router = useRouter()
const user = useAuthStore()
const catalogo = useCatalogStore()
onMounted(async()=>{
  user.loadToken()
  await catalogo.fetchAll()
  if(user.isAuthenticated){
    user.fetchProfile()
    router.push('/dashboard')
  }
})
interface Movie {
  id: number
  title: string
  year: string
  genre: string[]
  image: string|null
  rating: number
}

const featuredMovies = computed<Movie[]>(() => {
  const raw = catalogo.titles ?? []

  if (!raw.length) return []

  return raw.slice(0, 8).map((item, idx) => {
    const v = (item as any)._custom?.value ?? item

    return {
      id: idx + 1,
      title: v.primary_title ?? '',
      year: v.start_year?.toString() ?? '',
      genre: v.genres ?? [],
      image: v.image_url ?? null,
      rating: 0,
    } as Movie
  })
})

const stats = ref([
  { value: catalogo.titles.length + "+", label: 'Film & Serie TV' },
  { value: '50K+', label: 'Utenti Attivi' },
  { value: '24/7', label: 'Disponibilità' }
])

const features = ref([
  {
    icon: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
    title: 'Ricerca Avanzata',
    description: 'Trova rapidamente i tuoi film e serie preferiti con filtri intelligenti'
  },
  {
    icon: 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
    title: 'Raccomandazioni',
    description: 'Suggerimenti personalizzati basati sui tuoi gusti cinematografici'
  },
  {
    icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    title: 'Liste Personali',
    description: 'Crea e gestisci le tue collezioni di film e serie da guardare'
  },
  {
    icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    title: 'Aggiornamenti Continui',
    description: 'Catalogo sempre aggiornato con le ultime uscite cinematografiche'
  }
])

const scrollToFeatures = () => {
  const element = document.getElementById('features')
  element?.scrollIntoView({ behavior: 'smooth' })
}

//gsap.registerPlugin(MotionPathPlugin)
</script>

<template>
  <div class="min-h-screen bg-slate-900 overflow-visible">
    <header class="fixed top-0 left-0 right-0 z-50 bg-slate-900/95 backdrop-blur-sm border-b border-slate-800">
      <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center text-4xl text-white">
            Catalogo
          </div>
          <div class="flex items-center gap-4">
            <a href="#features" class="text-sm text-slate-300 hover:text-white transition-colors">
              Funzionalità
            </a>
            <a href="#catalog" class="text-sm text-slate-300 hover:text-white transition-colors">
              Catalogo
            </a>
            <RouterLink to="/login" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
              Accedi
            </RouterLink>
          </div>
        </div>
      </nav>
    </header>

    <section id="path" class="relative pt-32 pb-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-blue-600/10 via-slate-900 to-slate-900"></div>
      <div class="absolute inset-0">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-600/20 rounded-full filter blur-3xl animate-pulse"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full filter blur-3xl animate-pulse delay-1000"></div>
      </div>

      <div class="relative max-w-7xl mx-auto text-center">
        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight">
          Il Tuo Catalogo<br/>
          <span class="bg-gradient-to-r from-blue-400 to-blue-600 bg-clip-text text-transparent">
            Film & Serie TV
          </span>
        </h1>
        <p class="text-xl sm:text-2xl text-slate-300 mb-8 max-w-3xl mx-auto leading-relaxed">
          Scopri, organizza e gestisci la tua collezione cinematografica.
          Migliaia di titoli a portata di mano.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
          <RouterLink to="/signup" class="group px-8 py-4 bg-blue-600 text-white rounded-lg font-semibold text-lg hover:bg-blue-700 transition-all hover:scale-105 shadow-lg hover:shadow-blue-600/50">
            Inizia Ora
            <svg class="inline-block ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
          </RouterLink>
          <button @click="scrollToFeatures" class="px-8 py-4 bg-slate-800 text-white rounded-lg font-semibold text-lg hover:bg-slate-700 transition-all border border-slate-700">
            Scopri di Più
          </button>
        </div>
      </div>

      <div class="relative max-w-7xl mx-auto mt-20">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 text-center">
          <div v-for="stat in stats" :key="stat.label" class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700/50 hover:border-blue-600/50 transition-all">
            <div class="text-4xl font-bold text-white mb-2">{{ stat.value }}</div>
            <div class="text-sm text-slate-400">{{ stat.label }}</div>
          </div>
        </div>
      </div>
    </section>

    <section id="features" class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-950">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl sm:text-5xl font-bold text-white mb-4">
            Funzionalità Potenti
          </h2>
          <p class="text-xl text-slate-400 max-w-2xl mx-auto">
            Tutto ciò che ti serve per gestire la tua collezione cinematografica
          </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div v-for="feature in features" :key="feature.title"
               class="group bg-slate-800/50 backdrop-blur-sm rounded-xl p-8 border border-slate-700/50 hover:border-blue-600/50 transition-all hover:transform hover:scale-105">
            <div class="w-12 h-12 bg-blue-600/10 rounded-lg flex items-center justify-center mb-6 group-hover:bg-blue-600/20 transition-colors">
              <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon"/>
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-white mb-3">{{ feature.title }}</h3>
            <p class="text-slate-400 leading-relaxed">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <section id="catalog" class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-900">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl sm:text-5xl font-bold text-white mb-4">
            In Evidenza
          </h2>
          <p class="text-xl text-slate-400 max-w-2xl mx-auto">
            Scopri i titoli più popolari del nostro catalogo
          </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
          <div v-for="movie in featuredMovies" :key="movie.id"
               class="group cursor-pointer">
            <div class="relative overflow-hidden rounded-xl mb-4 aspect-[2/3] bg-slate-800">
              <img :src="movie.image"
                   :alt="movie.title"
                   class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"/>
              <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                <div class="absolute bottom-0 left-0 right-0 p-6">
                  <div class="flex items-center gap-2 text-sm text-slate-300 mb-2">
                    <span>{{ movie.year }}</span>
                    <span>•</span>
                    <span v-for="genre in movie.genre">{{ genre }}</span>
                  </div>
                </div>
              </div>
              <div class="absolute top-4 right-4 bg-slate-900/90 backdrop-blur-sm px-3 py-1 rounded-full flex items-center gap-1">
                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
              </div>
            </div>
            <h3 class="text-lg font-semibold text-white group-hover:text-blue-400 transition-colors">
              {{ movie.title }}
            </h3>
          </div>
        </div>

        <div class="text-center mt-12">
          <RouterLink to="/catalogo" class="inline-flex items-center px-6 py-3 bg-slate-800 text-white rounded-lg font-medium hover:bg-slate-700 transition-colors border border-slate-700">
            Esplora l'Intero Catalogo
            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
          </RouterLink>
        </div>
      </div>
    </section>

    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-blue-600 to-blue-800">
      <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-4xl sm:text-5xl font-bold text-white mb-6">
          Pronto per Iniziare?
        </h2>
        <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
          Unisciti a migliaia di appassionati di cinema e inizia a gestire
          la tua collezione oggi stesso.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
          <RouterLink to="/signup" class="px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold text-lg hover:bg-blue-50 transition-all hover:scale-105 shadow-xl">
            Registrati Gratis
          </RouterLink>
          <RouterLink to="/login" class="px-8 py-4 bg-transparent text-white rounded-lg font-semibold text-lg border-2 border-white hover:bg-white/10 transition-all">
            Ho Già un Account
          </RouterLink>
        </div>
      </div>
    </section>

    <footer class="bg-slate-950 border-t border-slate-800 py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
          <div class="col-span-2 text-4xl text-semibold text-white">
            Catalogo
            <p class="pt-2 text-slate-400 text-sm leading-relaxed">
              La piattaforma definitiva per gestire e scoprire film e serie TV.
            </p>
          </div>
          <div>
            <h4 class="text-white font-semibold mb-4">Prodotto</h4>
            <ul class="space-y-2">
              <li><a href="#features" class="text-slate-400 hover:text-white text-sm transition-colors">Funzionalità</a></li>
              <li><a href="#catalog" class="text-slate-400 hover:text-white text-sm transition-colors">Catalogo</a></li>
            </ul>
          </div>
        </div>
        <div class="border-t border-slate-800 pt-8 text-center text-slate-400 text-sm">
          <p>&copy; 2026 Catalogo. Tutti i diritti riservati.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
@keyframes pulse {
  0%, 100% {
    opacity: 0.2;
  }
  50% {
    opacity: 0.4;
  }
}

.animate-pulse {
  animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.delay-1000 {
  animation-delay: 1s;
}
</style>
