<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { useAuthStore } from '@/stores/useAuthStore'
import { storeToRefs } from 'pinia'
import router from '@/router'

const auth = useAuthStore()
const { user } = storeToRefs(auth)

/* ---------- Helper ---------- */
function normalize(v: string | undefined): string {
  return (v ?? '').trim().toUpperCase()
}

/* ---------- Campi del form ---------- */
const nome = ref<string>('')
const cognome = ref<string>('')
const email = ref<string>('')
const phone = ref<string>('')
const gender = ref<string>('')
const bio = ref<string>('')

const password = ref<string>('')
const passwordCurrent = ref<string>('')

const errorMsg = ref<string>('')
const showDeleteConfirm = ref<boolean>(false)

/* ---------- Snapshot per il confronto ---------- */
const originalSnapshot = ref<{
  full_name: string
  email: string
  phone: string
  gender: string
  bio: string
}>({
  full_name: '',
  email: '',
  phone: '',
  gender: '',
  bio: '',
})

function populateFromUser(u: typeof user.value) {
  if (!u) return
  const parts = (u.full_name ?? '').split(' ')
  nome.value = parts[0] ?? ''
  cognome.value = parts.slice(1).join(' ') ?? ''
  email.value = u.email ?? ''
  phone.value = u.phone ?? ''
  gender.value = (u.gender ?? '').toUpperCase()
  bio.value = u.bio ?? ''
  originalSnapshot.value = {
    full_name: `${nome.value} ${cognome.value}`.trim(),
    email: email.value,
    phone: phone.value,
    gender: gender.value,
    bio: bio.value,
  }
}

watch(user, (newUser) => {
  if (newUser) populateFromUser(newUser)
}, { immediate: true })

/* ---------- Costruzione payload di base ---------- */
function buildBasePayload() {
  const payload: Record<string, any> = {}
  const curFullName = `${nome.value} ${cognome.value}`.trim()

  if (normalize(originalSnapshot.value.full_name) !== normalize(curFullName))
    payload.full_name = curFullName
  if (normalize(originalSnapshot.value.email) !== normalize(email.value))
    payload.email = email.value.trim()
  if (normalize(originalSnapshot.value.phone) !== normalize(phone.value))
    payload.phone = phone.value.trim()
  if (normalize(originalSnapshot.value.gender) !== normalize(gender.value))
    payload.gender = gender.value.toUpperCase()
  if (normalize(originalSnapshot.value.bio) !== normalize(bio.value))
    payload.bio = bio.value.trim()

  return payload
}

/* ---------- Upload foto (solo percorso) ---------- */
const fileInput = ref<HTMLInputElement | null>(null)
const selectedFile = ref<File | null>(null)

function onFileSelected(event: Event) {
  const target = event.target as HTMLInputElement
  if (!target.files?.length) return

  selectedFile.value = target.files[0]

  const previewUrl = URL.createObjectURL(selectedFile.value)
  if (user.value) {
    user.value.profile_image = previewUrl
  }
}

/* ---------- Salvataggio profilo ---------- */
async function save() {
  errorMsg.value = ''
  const basePayload = buildBasePayload()

  if (password.value) {
    if (!passwordCurrent.value) {
      errorMsg.value = 'Devi inserire la password corrente per cambiarla.'
      return
    }
    if (password.value.length < 8) {
      errorMsg.value = 'La nuova password deve contenere almeno 8 caratteri.'
      return
    }
    basePayload.password_current = passwordCurrent.value
    basePayload.password = password.value
  }

  if (selectedFile.value) {
    const localUrl = URL.createObjectURL(selectedFile.value)
    basePayload.profile_image = localUrl
  }

  console.log('Payload inviato:', basePayload)

  if (!password.value && Object.keys(basePayload).length === 0) return

  await auth.updateProfile(basePayload)
  router.push('/dashboard')
}

/* ---------- Eliminazione account ---------- */
async function confirmDelete() {
  showDeleteConfirm.value = false
  await auth.deleteAccount()
  router.push('/')
}

/* ---------- Controlli iniziali ---------- */
onMounted(() => {
  if (!auth.isAuthenticated) {
    router.push('/login')
  } else if (!user.value) {
    auth.fetchProfile()
  }
})
</script>

<template>
  <div class="bg-slate-950 h-screen overflow-auto overscroll-auto items-center dark">
    <div class="max-w-4xl px-4 py-10 sm:px-6 lg:px-8 mx-auto min-w-90">
      <div class="bg-white dark:bg-card rounded-xl p-4 sm:p-7 shadow-2xl shadow-blue-800">
        <div class="mb-8">
          <h2 class="text-xl font-bold text-gray-800 dark:text-neutral-200">Profilo</h2>
          <p class="text-sm text-gray-600 dark:text-neutral-300">Gestisci il tuo account.</p>
        </div>

        <div v-if="errorMsg" class="mb-4 p-2 bg-red-100 text-red-800 rounded">
          {{ errorMsg }}
        </div>

        <form @submit.prevent>
          <div class="grid sm:grid-cols-12 gap-2 sm:gap-6">
            <!-- Foto profilo -->
            <div class="sm:col-span-3">
              <label class="inline-block text-sm text-gray-800 dark:text-neutral-200 mt-2.5">
                Foto profilo
              </label>
            </div>
            <div class="sm:col-span-9">
              <div class="flex items-center gap-5">
                <img class="inline-block size-16 rounded-full ring-2 ring-white dark:ring-neutral-800"
                     :src="user?.profile_image" alt="Avatar">
                <button type="button"
                        @click="fileInput?.click()"
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 text-gray-800 dark:text-white shadow-2xs hover:bg-gray-50 dark:hover:bg-neutral-700">
                  <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                       viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                       stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                  Upload photo
                </button>
                <!-- Input file nascosto -->
                <input type="file"
                       ref="fileInput"
                       accept="image/*"
                       class="hidden"
                       @change="onFileSelected">
              </div>
            </div>

            <!-- Nome completo -->
            <div class="sm:col-span-3">
              <label class="inline-block text-sm text-gray-800 dark:text-neutral-200 mt-2.5">
                Nome completo
              </label>
            </div>
            <div class="sm:col-span-9">
              <div class="sm:flex gap-2">
                <input id="af-account-first-name" type="text" v-model="nome"
                       class="py-1.5 sm:py-2 px-3 block w-full bg-white dark:bg-neutral-800 border-gray-200 dark:border-neutral-700 text-gray-800 dark:text-neutral-200 placeholder:text-gray-500 dark:placeholder:text-neutral-400 shadow-2xs"
                       placeholder="Nome">
                <input id="af-account-last-name" type="text" v-model="cognome"
                       class="py-1.5 sm:py-2 px-3 block w-full bg-white dark:bg-neutral-800 border-gray-200 dark:border-neutral-700 text-gray-800 dark:text-neutral-200 placeholder:text-gray-500 dark:placeholder:text-neutral-400 shadow-2xs"
                       placeholder="Cognome">
              </div>
            </div>

            <!-- Email -->
            <div class="sm:col-span-3">
              <label class="inline-block text-sm text-gray-800 dark:text-neutral-200 mt-2.5">
                Email
              </label>
            </div>
            <div class="sm:col-span-9">
              <input id="af-account-email" type="email" v-model="email"
                     class="py-1.5 sm:py-2 px-3 block w-full bg-white dark:bg-neutral-800 border-gray-200 dark:border-neutral-700 shadow-2xs sm:text-sm rounded-lg text-gray-800 dark:text-neutral-200 placeholder:text-gray-500 dark:placeholder:text-neutral-400 focus:border-gray-900 dark:focus:border-neutral-300 focus:ring-gray-900 dark:focus:ring-neutral-300"
                     placeholder="Email">
            </div>

            <!-- Password -->
            <div class="sm:col-span-3">
              <label class="inline-block text-sm text-gray-800 dark:text-neutral-200 mt-2.5">
                Password
              </label>
            </div>
            <div class="sm:col-span-9">
              <div class="space-y-2">
                <input type="password" v-model="passwordCurrent"
                       class="py-1.5 sm:py-2 px-3 block w-full bg-white dark:bg-neutral-800 border-gray-200 dark:border-neutral-700 shadow-2xs sm:text-sm rounded-lg text-gray-800 dark:text-neutral-200 placeholder:text-gray-500 dark:placeholder:text-neutral-400"
                       placeholder="Password corrente">
                <input type="password" v-model="password"
                       class="py-1.5 sm:py-2 px-3 block w-full bg-white dark:bg-neutral-800 border-gray-200 dark:border-neutral-700 shadow-2xs sm:text-sm rounded-lg text-gray-800 dark:text-neutral-200 placeholder:text-gray-500 dark:placeholder:text-neutral-400"
                       placeholder="Nuova password (opzionale)">
              </div>
            </div>

            <!-- Telefono -->
            <div class="sm:col-span-3">
              <label class="inline-block text-sm text-gray-800 dark:text-neutral-200 mt-2.5">
                Telefono
              </label>
              <span class="text-sm text-gray-400 dark:text-neutral-500">(Opzionale)</span>
            </div>
            <div class="sm:col-span-9">
              <input id="af-account-phone" type="text" v-model="phone"
                     class="py-1.5 sm:py-2 px-3 block w-full bg-white dark:bg-neutral-800 border-gray-200 dark:border-neutral-700 text-gray-800 dark:text-neutral-200 placeholder:text-gray-500 dark:placeholder:text-neutral-400 shadow-2xs"
                     placeholder="+xx xxx-xxxx-xxx">
            </div>

            <!-- Sesso -->
            <div class="sm:col-span-3">
              <label class="inline-block text-sm text-gray-800 dark:text-neutral-200 mt-2.5">
                Sesso
              </label>
            </div>
            <div class="sm:col-span-9 flex gap-4">
              <label class="flex items-center">
                <input type="radio" value="M" v-model="gender"
                       class="shrink-0 size-4 bg-transparent border-gray-300 dark:border-neutral-600 rounded-full shadow-2xs text-gray-800 dark:text-white focus:ring-0">
                <span class="ms-3 text-sm text-gray-500 dark:text-neutral-400">M</span>
              </label>
              <label class="flex items-center">
                <input type="radio" value="F" v-model="gender"
                       class="shrink-0 size-4 bg-transparent border-gray-300 dark:border-neutral-600 rounded-full shadow-2xs text-gray-800 dark:text-white focus:ring-0">
                <span class="ms-3 text-sm text-gray-500 dark:text-neutral-400">F</span>
              </label>
            </div>

            <!-- BIO -->
            <div class="sm:col-span-3">
              <label class="inline-block text-sm text-gray-800 dark:text-neutral-200 mt-2.5">
                BIO
              </label>
            </div>
            <div class="sm:col-span-9">
              <textarea v-model="bio" rows="6"
                        class="py-1.5 sm:py-2 px-3 block w-full bg-white dark:bg-neutral-800 border-gray-200 dark:border-neutral-700 shadow-2xs sm:text-sm rounded-lg text-gray-800 dark:text-neutral-200 placeholder:text-gray-500 dark:placeholder:text-neutral-400 focus:border-gray-900 dark:focus:border-neutral-300 focus:ring-gray-900 dark:focus:ring-neutral-300"></textarea>
            </div>
          </div>

          <!-- Bottoni azione -->
          <div class="mt-5 flex justify-end gap-x-2">
            <RouterLink to="/dashboard"
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 text-gray-800 dark:text-white shadow-2xs hover:bg-gray-50 dark:hover:bg-neutral-700">
              Annulla
            </RouterLink>

            <button type="button" @click="save()"
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-gray-800 dark:bg-white border border-transparent text-white dark:text-neutral-800 hover:bg-gray-900 dark:hover:bg-neutral-300">
              Salva modifiche
            </button>

            <button type="button"
                    @click="showDeleteConfirm = true"
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-red-600 hover:bg-red-700 text-white">
              Elimina account
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Conferma eliminazione -->
    <div v-if="showDeleteConfirm"
         class="fixed inset-0 flex items-center justify-center bg-black/50">
      <div class="bg-white dark:bg-neutral-800 p-6 rounded-lg max-w-sm w-full">
        <h3 class="text-lg font-semibold mb-4 text-red-600">Conferma eliminazione</h3>
        <p class="mb-4">Sei sicuro di voler eliminare definitivamente il tuo account? Questa azione è irreversibile.</p>
        <div class="flex justify-end gap-2">
          <button @click="showDeleteConfirm = false"
                  class="px-3 py-1 rounded bg-gray-200 dark:bg-neutral-700 text-gray-800 dark:text-neutral-200">
            Annulla
          </button>
          <button @click="confirmDelete"
                  class="px-3 py-1 rounded bg-red-600 hover:bg-red-700 text-white">
            Elimina definitivamente
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
