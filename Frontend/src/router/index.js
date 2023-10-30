import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import HomeView from '../views/HomeView.vue'
import ScheduleView from '../views/ScheduleView.vue'
import UpdateAvatar from '../views/User/UpdateAvatar.vue'
import RegisterView from "../views/RegisterView.vue";
import UpdateProfile from "../views/User/UpdateProfile.vue";
import {useUserStore} from "../stores/user";
import ForgotPassword from "../views/User/ForgotPassword.vue";
import ResetPassword from "../views/User/ResetPassword.vue";
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: LoginView
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView
    },
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/schedule',
      name: 'schedule',
      component: ScheduleView
    },
    {
      path: '/update-avatar',
      name: 'UpdateAvatar',
      component: UpdateAvatar
    },
    {
      path: '/update-profile',
      name: 'update-profile',
      component: UpdateProfile,
    },
    {
      path: '/forgot-password',
      name: 'forgot-password',
      component: ForgotPassword,
    },
    {
      path: '/reset-password',
      name: 'reset-password',
      component: ResetPassword,
    }
  ]
})

router.beforeEach(async () => {
  const user = await useUserStore()
  if(user.isExpired()){
    user.logout()
  }
})

export default router
