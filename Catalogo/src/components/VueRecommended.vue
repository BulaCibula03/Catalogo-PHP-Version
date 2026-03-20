<!-- src/components/VueRecommended.vue -->
<script setup lang="ts">
import { computed } from 'vue'
import Autoplay from 'embla-carousel-autoplay'
import { Card, CardContent } from '@/components/ui/card'
import {
  Carousel,
  CarouselContent,
  CarouselItem,
  CarouselPrevious,
  CarouselNext,
} from '@/components/ui/carousel'

const props = defineProps<{
  titles: Array<{
    id: string
    primary_title: string
    image_url?: string
    start_year?: number
    end_year?: number
    genres?: string[]
  }>
}>()

const plugin = Autoplay({
  delay: 3000,
  stopOnMouseEnter: true,
  stopOnInteraction: false,
})

const items = computed(() =>
  props.titles.length ? props.titles : [{ id: 'empty', primary_title: '' }]
)
</script>

<template>
<Carousel
  class="relative w-130 shadow-blue-900 shadow-2xl rounded-2xl"
  :plugins="[plugin]"
  @mouseenter="plugin.stop"
  @mouseleave="[plugin.reset(), plugin.play()]"
>
  <CarouselContent class="rounded-2xl justify-middle">
    <CarouselItem
      v-for="item in items"
      :key="item.id"
    >
      <Card class="h-full flex flex-col">
        <CardContent class="flex-1 flex flex-col">
          <!-- Immagine -->
          <div class="img-wrapper flex-1 overflow-hidden rounded-t-xl bg-gray-900/30">
            <img
              v-if="item.image_url"
              :src="item.image_url"
              alt="poster"
              class="w-full h-full object-cover"
            />
            <span
              v-else
              class="flex h-full items-center justify-center text-2xl font-semibold text-gray-500"
            >
              {{ item.primary_title || 'Nessun contenuto' }}
            </span>
          </div>

          <!-- Info -->
          <div class="p-5 info bg-slate-900 rounded-b-xl text-center">
            <h3 class="title text-sm font-medium text-gray-100 truncate">
              {{ item.primary_title }}
            </h3>

            <p v-if="item.start_year"
               class="text-xs text-gray-400 mt-1">
              {{ item.start_year }}
              <span v-if="item.end_year">‑{{ item.end_year }}</span>
            </p>

            <div v-if="item.genres?.length"
                 class="flex flex-wrap justify-center gap-1 mt-2">
              <span v-for="(g,i) in item.genres.slice(0,2)"
                    :key="g"
                    class="genre-badge px-2 py-0.5 text-xs bg-gray-700 text-gray-200 rounded-full">
                {{ g }}
              </span>
              <span v-if="item.genres.length > 2"
                    class="genre-badge px-2 py-0.5 text-xs bg-gray-700 text-gray-200 rounded-full">
                +{{ item.genres.length - 2 }}
              </span>
            </div>
          </div>
        </CardContent>
      </Card>
    </CarouselItem>
  </CarouselContent>

  <CarouselPrevious />
  <CarouselNext />
</Carousel>
</template>
