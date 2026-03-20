<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/useAuthStore'
import { Button } from '@/components/ui/button'
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import {
  Field,
  FieldDescription,
  FieldGroup,
  FieldLabel,
} from '@/components/ui/field'
import { Input } from '@/components/ui/input'

const signup = useAuthStore()
const router = useRouter()

const fullname = ref<string>('')
const email = ref<string>('')
const password = ref<string>('')
const confermapassword = ref<string>('')

async function check() {
  // valida password
  if (password.value.length < 8 || password.value !== confermapassword.value) {
    // qui potresti impostare un messaggio di errore visibile all'utente
    return
  }

  const payload = {
    full_name: fullname.value,
    email:     email.value,
    password:  password.value,
  }

  const success = await signup.register(payload)

  if (success && signup.isAuthenticated) {
    router.push('/dashboard')
  } else {
    // rimani sulla pagina di signup; eventuale messaggio di errore è in signup.error
  }
}
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>Crea account</CardTitle>
      <CardDescription>
        Inserisci le tue informazione qui sotto.
      </CardDescription>
    </CardHeader>
    <CardContent>
      <form @submit.prevent="check">
        <FieldGroup>
          <Field>
            <FieldLabel for="name">Full Name</FieldLabel>
            <Input id="name" type="text" placeholder="John Doe" required v-model="fullname" />
          </Field>

          <Field>
            <FieldLabel for="email">Email</FieldLabel>
            <Input id="email" type="email" placeholder="m@example.com" required v-model="email" />
            <FieldDescription>Non verrà condivisa con nessuno</FieldDescription>
          </Field>

          <Field>
            <FieldLabel for="password">Password</FieldLabel>
            <Input id="password" type="password" required v-model="password" />
            <FieldDescription>Dev'essere lunga minimo 8 caratteri.</FieldDescription>
          </Field>

          <Field>
            <FieldLabel for="confirm-password">Confirm Password</FieldLabel>
            <Input id="confirm-password" type="password" required v-model="confermapassword" />
            <FieldDescription>Per favore conferma la tua password.</FieldDescription>
          </Field>

          <FieldGroup>
            <Field>
              <Button type="submit" :disabled="signup.loading">
                {{ signup.loading ? 'Creating...' : 'Create Account' }}
              </Button>
              <FieldDescription class="px-6 text-center">
                Hai già un account? <RouterLink to="/login">Sign in</RouterLink>
              </FieldDescription>
            </Field>
          </FieldGroup>
        </FieldGroup>
      </form>

      <p v-if="signup.error" class="text-red-600 mt-2">{{ signup.error }}</p>
    </CardContent>
  </Card>
</template>
