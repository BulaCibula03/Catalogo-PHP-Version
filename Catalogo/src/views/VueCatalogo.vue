<script setup lang="ts">
import VueNavbar from '@/components/VueNavbar.vue';
import { useCatalogStore } from '@/stores/useCatalogStore';
import { useUserListStore } from '@/stores/useUserListStore';
import { useAuthStore } from '@/stores/useAuthStore';
import AddModal from '@/components/AddModal.vue';
import { onMounted, ref, watch } from 'vue';

/* ---------- Store ---------- */
const catalogo = useCatalogStore();
const userList = useUserListStore();

/* ---------- Caricamento iniziale ---------- */
onMounted(() => {
  catalogo.fetchAll();
});

/* ---------- Stato ricerca ---------- */
const ricerca = ref<string>('');

/* ---------- Stato modal ---------- */
const modalOpen = ref(false);
const selectedTitle = ref<any>(null);

/* ---------- Apertura modal ---------- */
function openModal(t: any) {
  if (!useAuthStore().isAuthenticated) {
    alert('Devi effettuare il login');
    return;
  }
  selectedTitle.value = t;
  modalOpen.value = true;
}

/* ---------- Conferma aggiunta ---------- */
async function confirmAdd() {
  if (!selectedTitle.value) return;
  await userList.add(selectedTitle.value.id);
}

/* ---------- Ricerca ---------- */
function onSearch() {
  catalogo.filterByTitle(ricerca.value);
}

watch(ricerca, (newVal) => {
  catalogo.filterByTitle(newVal);
});
</script>

<template>
  <div class="bg-slate-950 min-h-screen overscroll-hidden overflow-auto flex flex-col items-center dark">
    <VueNavbar />
    <div class="flex justify-center items-center p-10 pt-20 w-full">
      <!-- Table Section -->
      <div class="max-w-350 px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto w-full">
        <div class="flex flex-col justify-center items-center">
          <div class="-m-1.5 w-full">
            <div class="p-1.5 min-w-full inline-block align-middle">
              <div class="bg-white dark:bg-card rounded-xl overflow-hidden shadow-blue-800 shadow-2xl">
                <!-- Header -->
                <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-neutral-700">
                  <!-- Input -->
                  <div class="sm:col-span-1 w-full md:w-64">
                    <label for="search" class="sr-only">Search</label>
                    <div class="relative">
                      <input
                        v-model="ricerca"
                        @keyup.enter="onSearch"
                        type="text"
                        id="search"
                        class="py-2 px-3 ps-11 block w-full bg-white dark:bg-neutral-800 border-gray-200 dark:border-neutral-700 rounded-lg text-sm text-gray-800 dark:text-neutral-200 placeholder:text-gray-500 dark:placeholder:text-neutral-400 focus:border-blue-700 dark:focus:border-blue-600 focus:ring-blue-700 dark:focus:ring-blue-600"
                        placeholder="Cerca"
                      />
                      <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-4">
                        <svg class="shrink-0 size-4 text-gray-400 dark:text-neutral-500"
                             xmlns="http://www.w3.org/2000/svg"
                             width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round">
                          <circle cx="11" cy="11" r="8"/>
                          <path d="m21 21-4.3-4.3"/>
                        </svg>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Table -->
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                  <thead class="bg-gray-50 dark:bg-card">
                    <tr>
                      <th class="px-6 py-3 text-start"><span class="text-xs font-semibold uppercase text-gray-800 dark:text-neutral-200">Film/Serie</span></th>
                      <th class="px-6 py-3 text-start"><span class="text-xs font-semibold uppercase text-gray-800 dark:text-neutral-200">Genere</span></th>
                      <th class="px-6 py-3 text-start"><span class="text-xs font-semibold uppercase text-gray-800 dark:text-neutral-200">Descrizione</span></th>
                      <th class="px-6 py-3 text-start"><span class="text-xs font-semibold uppercase text-gray-800 dark:text-neutral-200">Data inizio‑fine</span></th>
                      <th class="px-6 py-3 text-start"><span class="text-xs font-semibold uppercase text-gray-800 dark:text-neutral-200">Status</span></th>
                    </tr>
                  </thead>

                  <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                    <tr
                      v-for="titolo in catalogo.titles"
                      :key="titolo.id"
                      class="bg-white dark:bg-card hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer"
                      @click="openModal(titolo)"
                    >
                      <!-- Film/Serie -->
                      <td class="whitespace-nowrap align-top p-6">
                        <div class="flex items-center gap-x-4">
                          <img
                            v-if="titolo.image_url"
                            :src="titolo.image_url"
                            alt="Immagine Titolo"
                            class="shrink-0 size-9.5 rounded-lg"
                          />
                          <div>
                            <span class="block text-sm font-semibold text-gray-800 dark:text-neutral-200">
                              {{ titolo.primary_title }}
                            </span>
                          </div>
                        </div>
                      </td>

                      <!-- Genere -->
                      <td class="whitespace-nowrap align-top p-6">
                        <ul class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                          <li v-for="genere in titolo.genres" :key="genere">{{ genere }}</li>
                        </ul>
                      </td>

                      <!-- Descrizione -->
                      <td class="w-72 min-w-72 align-top p-6">
                        <span class="block text-sm text-gray-500 dark:text-neutral-400">
                          {{ titolo.plot }}
                        </span>
                      </td>

                      <!-- Data -->
                      <td class="whitespace-nowrap align-top p-6">
                        <span v-if="titolo.end_year" class="text-sm text-gray-600 dark:text-neutral-300">
                          {{ titolo.start_year }} - {{ titolo.end_year }}
                        </span>
                        <span v-else class="text-sm text-gray-600 dark:text-neutral-300">
                          {{ titolo.start_year }}
                        </span>
                      </td>

                      <!-- Status -->
                      <td class="whitespace-nowrap align-top p-6">
                        <span v-if="titolo.end_year && titolo.type==='tvSeries'"
                              class="py-1 px-1.5 inline-flex items-center gap-x-1 text-xs font-medium bg-teal-100 text-teal-800 rounded-full dark:bg-teal-500/10 dark:text-teal-500">
                          Finito
                        </span>
                        <span v-else-if="!titolo.end_year && titolo.type==='tvSeries'"
                              class="py-1 px-1.5 inline-flex items-center gap-x-1 text-xs font-medium bg-teal-100 text-teal-800 rounded-full dark:bg-teal-500/10 dark:text-teal-500">
                          Non finito
                        </span>
                        <span v-else class="py-1 px-1.5 inline-flex items-center gap-x-1 text-xs font-medium bg-teal-100 text-teal-800 rounded-full dark:bg-teal-500/10 dark:text-teal-500">
                          Uscito
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <!-- End Table -->

                <!-- Footer -->
                <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-neutral-700">
                  <div class="max-w-sm space-y-3">
                    <div class="py-2 px-3 block bg-white dark:bg-neutral-800 border-gray-200 dark:border-neutral-700 text-gray-800 dark:text-white rounded-lg text-sm">
                      Totale: {{ catalogo.titles.length }}
                    </div>
                  </div>
                </div>
                <!-- End Footer -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal di conferma -->
    <AddModal
      :open="modalOpen"
      @close="modalOpen = false"
      @confirm="confirmAdd"
    >
      <template #title>
        {{ selectedTitle?.primary_title ?? '' }}
      </template>
    </AddModal>
  </div>
</template>
