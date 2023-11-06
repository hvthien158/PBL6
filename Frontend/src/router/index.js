import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import NProgress from 'nprogress';
import HomeView from '../views/HomeView.vue'
import ScheduleView from '../views/ScheduleView.vue'
import MyProfile from "../views/User/MyProfile.vue";
import { useUserStore } from "../stores/user";
import { useAlertStore } from "../stores/alert";
import ListUser from "../views/Admin/User/ListUser.vue"
import ListTimeKeeping from '../views/Admin/TimeKeeping/ListTimeKeeping.vue'
import EditUser from '../views/Admin/User/EditUser.vue'
import ListDepartment from '../views/Admin/Department/ListDepartment.vue'
import EditDepartment from '../views/Admin/Department/EditDepartment.vue'
import ForgotPassword from "../views/User/ForgotPassword.vue";
import ResetPassword from "../views/User/ResetPassword.vue";
import ChangePassword from "../views/User/ChangePassword.vue";
import ListShift from '../views/Admin/Shift/ListShift.vue';
import EditShift from '../views/Admin/Shift/EditShift.vue';
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: LoginView
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
      path: '/my-profile',
      name: 'my-profile',
      component: MyProfile,
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
      path: '/reset-password/:token',
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
      path: '/admin/list-user/:departmentName',
      name: 'userDepartment',
      component: ListUser
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
    },
    {
      path: '/admin/list-shift/',
      name: 'listShift',
      component: ListShift
    },
    {
      path: '/admin/add-shift/',
      name: 'addShift',
      component: EditShift
    },
    {
      path: '/admin/update-shift/:id',
      name: 'updateShift',
      component: EditShift
    },
  ]
})

router.beforeEach(async () => {
  const user = await useUserStore()
  if (user.isExpired()) {
    user.logout()

    useAlertStore().alert = true
    useAlertStore().type = 'warning'
    useAlertStore().msg = '(Token expired) Please login'

    await router.push({
      name: 'login',
    })
  }
})

router.beforeResolve((to, from, next) => {
  if (to.name) {
    NProgress.start()
  }
  next()
})

router.afterEach((to, from) => {
  NProgress.done()
})

export default router
