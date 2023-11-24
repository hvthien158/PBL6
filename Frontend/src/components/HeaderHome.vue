<template>
  <header>
    <div class="header-img">
      <router-link to="/">
        <img class="logo" :src="logo" alt="Logo"/>
      </router-link>
    </div>
    <span v-if="user.token" style="font-weight: bold; margin-right: 20px; display: flex; align-items: center">
      <AdminMessage v-if="user.role === 'admin'"></AdminMessage>
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
                              @click="router.push({name: 'listUser'})">Admin Page</el-dropdown-item>
            <el-dropdown-item @click="changePass">Change password</el-dropdown-item>
            <el-dropdown-item @click="logout">Logout</el-dropdown-item>
          </el-dropdown-menu>
        </template>
      </el-dropdown>
    </span>
  </header>
</template>

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
  margin-left: 50px;
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
</style>
<script setup>
import logo from "../assets/image/logo.png";
import axios from "axios";
import router from "../router";
import {useUserStore} from "../stores/user";
import {useAlertStore} from "../stores/alert";
import AdminMessage from "./AdminMessage.vue";

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
    await axios
        .post("http://127.0.0.1:8000/api/logout", null, {
          headers: {Authorization: `Bearer ${user.token}`},
        })
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