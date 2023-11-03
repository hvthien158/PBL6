<template>
  <header>
    <div class="header-img">
      <router-link to="/">
        <img class="logo" :src="logo" alt="Logo" />
      </router-link>
    </div>
    <span v-if="user.token" style="font-weight: bold; margin-right: 20px">
      <el-dropdown>
        <span class="el-dropdown-link" style="line-height: 44px; font-size: larger">
          {{user.name}}
        </span>
        <template #dropdown>
          <el-dropdown-menu>
            <el-dropdown-item @click="updateProfile">Thông tin cá nhân</el-dropdown-item>
            <el-dropdown-item @click="changePass">Đổi mật khẩu</el-dropdown-item>
            <el-dropdown-item @click="logout">Đăng xuất</el-dropdown-item>
          </el-dropdown-menu>
        </template>
      </el-dropdown>
    </span>
  </header>
</template>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

header {
  width: 100vw;
  height: 9vh;
  padding: 12px;
  background-color: #313335;
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
.el-dropdown-link{
  cursor: pointer;
  border: none;
}
.el-dropdown-link:hover{
  color: #f3952d;
}
</style>
<script setup>
import logo from "../assets/image/logo.png";
import schedule from "../assets/image/schedule.png";
import { reactive } from "vue";
import axios from "axios";
import router from "../router";
import {useUserStore} from "../stores/user";
import {useAlertStore} from "../stores/alert";

const alertStore = useAlertStore()
const user = useUserStore().user

function updateProfile(){
  router.push({
    name: 'update-profile',
  })
}

function changePass(){
  router.push({
    name: 'change-password',
  })
}

const logout = async () => {
  try {
    await axios
      .post("http://127.0.0.1:8000/api/logout", null, {
        headers: { Authorization: `Bearer ${user.token}` },
      })
      .then(function (response) {
        if (response.status === 200) {
          useUserStore().logout()

          //alert success
          alertStore.alert = true
          alertStore.type = 'success'
          alertStore.msg = 'Đã đăng xuất'

          router.push({ path: "/login" });
        }
      });
  } catch (e) {
    console.log(e);
  }
};
</script>