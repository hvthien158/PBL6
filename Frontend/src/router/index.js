import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import HomeView from '../views/HomeView.vue'
import ScheduleView from '../views/ScheduleView.vue'
import MyProfile from "../views/User/MyProfile.vue";
import { useUserStore } from "../stores/user";
import { useAlertStore } from "../stores/alert";
import ListUser from "../views/Admin/User/ListUser.vue"
import ListTimeKeeping from '../views/Admin/TimeKeeping/ListTimeKeeping.vue'
import ListDepartment from '../views/Admin/Department/ListDepartment.vue'
import ForgotPassword from "../views/User/ForgotPassword.vue";
import ResetPassword from "../views/User/ResetPassword.vue";
import ChangePassword from "../views/User/ChangePassword.vue";
import ListShift from '../views/Admin/Shift/ListShift.vue';
import ListMeeting from "../views/Admin/Meeting/ListMeeting.vue";
import Recorder from "../views/Admin/Meeting/Recorder.vue";
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
      path: '/schedule',
      name: 'schedule',
      component: ScheduleView
    },
    {
      path: '/admin/schedule/:userID',
      name: 'admin-schedule',
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
      path: '/admin/record',
      name: 'record',
      component: Recorder,
    },
    {
      path: '/admin/list-timekeeping',
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
      path: '/admin/list-user/department/:id',
      name: 'userDepartment',
      component: ListUser
    },
    {
      path: '/admin/list-shift/',
      name: 'listShift',
      component: ListShift
    },
    {
      path: '/admin/meeting/schedule',
      name: 'scheduleMeeting',
      component: ListMeeting,
    }
  ]
})

router.beforeEach(async () => {
  const user = await useUserStore()
  if (user.isExpired()) {
    user.logout()
    await router.push({
      name: 'login',
    })
  }
})

router.beforeEach(async (to) => {
  const user = await useUserStore().user
  if(user.token == '' && !(to.path.includes('login') || to.path.includes('forgot-password') || to.path.includes('reset-password'))) {
    await router.push({
      name: 'login',
    })
  }
  else if (to.path.includes('admin')) {
    if (user.role !== 'admin') {
      await router.push({
        name: 'home',
      })
    }
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
