import { createRouter, createWebHistory } from 'vue-router'
import VueLanding from '@/views/VueLanding.vue'
import VueLogin from '@/views/VueLogin.vue'
import VueSignup from '@/views/VueSignup.vue'
import VueCatalogo from '@/views/VueCatalogo.vue'
import VueDashboard from '@/views/VueDashboard.vue'
import VueManage from '@/views/VueManage.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      name: "Landing",
      path: "/",
      component: VueLanding
    },
    {
      name: "Login",
      path: "/login",
      component: VueLogin
    },
    {
      name: "Signup",
      path: "/signup",
      component: VueSignup
    },
    {
      name: "Catalogo",
      path: "/catalogo",
      component: VueCatalogo
    },
    {
      name: "Dashboard",
      path: "/dashboard",
      component: VueDashboard
    },
    {
      name: "Manage",
      path: "/manage",
      component: VueManage
    }
  ],
})

export default router
