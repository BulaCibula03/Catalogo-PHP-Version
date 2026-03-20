<script setup lang="ts">
import { useAuthStore } from "@/stores/useAuthStore";
const login = useAuthStore()
import { type HTMLAttributes, ref } from "vue"
import { useRouter } from "vue-router";
import { cn } from '@/lib/utils'
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

const props = defineProps<{

  class?: HTMLAttributes["class"]
}>()

const email = ref<string>("")
const password = ref<string>("")
const router = useRouter()
async function check(){
  await login.login(email.value,password.value)
  if(login.isAuthenticated) router.push('/dashboard')
}
</script>

<template>
  <div :class="cn('flex flex-col gap-6', props.class)" class="rounded-2xl">
    <Card>
      <CardHeader>
        <CardTitle>Login to your account</CardTitle>
        <CardDescription>
          Enter your email below to login to your account
        </CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="check()">
          <FieldGroup>
            <Field>
              <FieldLabel for="email">
                Email
              </FieldLabel>
              <Input
                id="email"
                type="email"
                placeholder="m@example.com"
                required
                v-model="email"
              />
            </Field>
            <Field>
              <div class="flex items-center">
                <FieldLabel for="password">
                  Password
                </FieldLabel>
              </div>
              <Input id="password" type="password" required v-model="password" />
            </Field>
            <Field>
              <Button type="submit">
                Login
              </Button>
              <FieldDescription class="text-center">
                Non hai un account?
                <RouterLink to="/signup">
                  Sign up
                </RouterLink>
              </FieldDescription>
            </Field>
          </FieldGroup>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
