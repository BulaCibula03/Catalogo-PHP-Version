<!-- src/components/AddModal.vue -->
<script setup lang="ts">
import { ref, defineEmits } from 'vue'

const emit = defineEmits<{
  (e: 'confirm'): void
  (e: 'close'): void
}>()

const isOpen = defineProps<{ open: boolean }>()

function confirm() {
  emit('confirm')
  emit('close')
}
function close() {
  emit('close')
}
</script>

<template>
  <transition name="fade">
    <div
      v-if="open"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
    >
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg max-w-sm w-full p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
          Aggiungi alla tua lista?
        </h3>
        <p class="text-gray-600 dark:text-gray-300 mb-6">
          Vuoi aggiungere <span class="font-medium">{{ $slots.title?.()[0]?.children }}</span> alla tua lista personale?
        </p>
        <div class="flex justify-end gap-3">
          <button
            @click="close"
            class="px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
          >
            Annulla
          </button>
          <button
            @click="confirm"
            class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition"
          >
            Aggiungi
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  @apply transition-opacity duration-200;
}
.fade-enter-from,
.fade-leave-to {
  @apply opacity-0;
}
</style>
