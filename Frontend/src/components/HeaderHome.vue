<template>
  <header>
    <div class="header-img">
      <router-link to="/">
        <img class="logo" :src="logo" alt="Logo"/>
      </router-link>
    </div>
    <span v-if="user.token" style="font-weight: bold; margin-right: 20px; display: flex; align-items: center">
      <div v-if="user.manager" class="new-meeting" title="Schedule a meeting" @click="new_meeting = true">
        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor" class="bi bi-calendar-plus" viewBox="0 0 16 16">
          <path d="M8 7a.5.5 0 0 1 .5.5V9H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V10H6a.5.5 0 0 1 0-1h1.5V7.5A.5.5 0 0 1 8 7"/>
          <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
        </svg>
      </div>
      <AdminMessage v-if="user.manager" :notification = props.notification></AdminMessage>
      <el-dropdown>
        <span class="el-dropdown-link">
          <span style="line-height: 44px; font-size: larger">{{ user.name }}</span>
        </span>
        <template #dropdown>
          <el-dropdown-menu>
            <div class="header-profile">
              <el-avatar :src="user.avatar" style="margin-right: 12px"></el-avatar>
              <div style="display: flex; flex-direction: column">
                <span id="dropdown-name" @click="myProfile">{{ user.name }}</span>
                <span style="color: #b2b2b2">{{ user.email }}</span>
              </div>
            </div>
            <el-dropdown-item v-if="user.role === 'admin'"
                              @click="router.push({name: 'list-timekeeping'})">Admin Page</el-dropdown-item>
            <el-dropdown-item @click="changePass">Change password</el-dropdown-item>
            <el-dropdown-item @click="logout">Logout</el-dropdown-item>
          </el-dropdown-menu>
        </template>
      </el-dropdown>
    </span>
  </header>
  <div class="new-meeting-form">
    <NewMeeting @invisible="new_meeting = false" :visible="new_meeting"></NewMeeting>
  </div>
</template>

<script setup>
import logo from "../assets/logo.png";
import router from "../router";
import {useUserStore} from "../stores/user";
import {useAlertStore} from "../stores/alert";
import AdminMessage from "./AdminMessage.vue";
import AuthAPI from "../services/AuthAPI";
import NewMeeting from "./NewMeeting.vue";
import {ref} from "vue";

const new_meeting = ref(false)
const props = defineProps({
  notification: {
    type: Object
  }
})
const alertStore = useAlertStore()
const user = useUserStore().user

function myProfile() {
  router.push({
    name: 'my-profile',
  })
}

function changePass() {
  router.push({
    name: 'change-password',
  })
}

const logout = async () => {
  try {
    await AuthAPI.logout(user.token, user.deviceToken)
        .then(function (response) {
          if (response.status === 200) {
            useUserStore().logout()

            //alert success
            alertStore.alert = true
            alertStore.type = 'success'
            alertStore.msg = 'Logged out'

            router.push({path: "/login"});
          }
        });
  } catch (e) {
    console.log(e);
  }
};
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

header {
  width: 100vw;
  height: 60px;
  padding: 12px;
  background-color: black;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 4px 4px 0 rgb(0 0 0 / 30%);
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1000;
}

.header-img {
  display: flex;
  align-items: center;
  width: 20%;
}

.header-img img {
  max-height: 8vh;
  margin-left: 2vw;
}

.el-dropdown-link {
  cursor: pointer;
  border: none;
  color: white;
  outline: none;
  margin-right: 2vw;
}

.el-dropdown-link:hover {
  color: #f3952d;
  outline: none;
}

.header-profile {
  display: flex;
  align-items: center;
  padding: 20px;
  width: 240px;
  border-bottom: 1px solid #ccc;
}

#dropdown-name {
  font-size: 15px;
  color: #6A679E;
  font-weight: 600;
}

#dropdown-name:hover {
  text-decoration: underline;
  color: #f3952d;
  cursor: pointer;
}

.new-meeting{
  margin-right: 20px;
  margin-bottom: 4px;
  color: white;
}

.new-meeting:hover{
  cursor: pointer;
  color: #f3952d;
}
</style>
