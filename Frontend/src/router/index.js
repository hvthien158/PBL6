import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import HomeView from '../views/HomeView.vue'
import ScheduleView from '../views/ScheduleView.vue'
import UpdateAvatar from '../views/User/UpdateAvatar.vue'
import RegisterView from "../views/RegisterView.vue";
import UpdateProfile from "../views/User/UpdateProfile.vue";
import { useUserStore } from "../stores/user";
import ListUser from "../views/Admin/User/ListUser.vue"
import ListTimeKeeping from '../views/Admin/TimeKeeping/ListTimeKeeping.vue'
import EditUser from '../views/Admin/User/EditUser.vue'
import ListDepartment from '../views/Admin/Department/ListDepartment.vue'
import EditDepartment from '../views/Admin/Department/EditDepartment.vue'
import {useUserStore} from "../stores/user";
import ForgotPassword from "../views/User/ForgotPassword.vue";
import ResetPassword from "../views/User/ResetPassword.vue";
import ChangePassword from "../views/User/ChangePassword.vue";
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
      path: '/schedule/',
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
      path: '/admin',
      name: 'admin',
      component: ListUser,
    },
    {
      path: '/admin/list-user',
      name: 'listUser',
      component: ListUser,
    },
    {
      path: '/admin/create-user',
      name: 'createUser',
      component: EditUser,
    },
    {
      path: '/admin/update-user/:id',
      name: 'updateUser',
      component: EditUser,
    },
    {
      path: '/admin/list-timekeeping/:id',
      name: 'list-timekeeping',
      component: ListTimeKeeping  
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
    },
    {
      path: '/change-password',
      name: 'change-password',
      component: ChangePassword,
    }
    ,
    {
      path: '/admin/list-department/',
      name: 'listDepartment',
      component: ListDepartment
    },
    {
      path: '/admin/add-department/',
      name: 'addDepartment',
      component: EditDepartment
    },
    {
      path: '/admin/update-department/:id',
      name: 'updateDepartment',
      component: EditDepartment
    }
    
  ]
})

router.beforeEach(async () => {
  const user = await useUserStore()
  if (user.isExpired()) {
    user.logout()
  }
})

export default router
