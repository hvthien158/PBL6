<script setup>
import logo from "../assets/image/logo.png";
import schedule from "../assets/image/schedule.png";
import { reactive } from "vue";
const props = defineProps({
  user: {
    type: Object,
    required: true
  }
})
let checkLanding = reactive({
  isMenuOpen: false
});
</script>

<template>
  <header>
    <div class="header-img">
      <router-link to="/">
        <img class="logo" :src="logo" alt="Logo" />
      </router-link>
      <router-link to="/schedule">
        <img :src="schedule" alt="Schedule" />
      </router-link>
    </div>
    <div class="user-info">
      <div class="user" @click="checkLanding.isMenuOpen = !checkLanding.isMenuOpen">
        <p class="username">{{ props.user.name }}</p>
      </div>
      <div v-if="checkLanding.isMenuOpen" class="dropdown">
        <div class="dropdown-item">Cài đặt tài khoản</div>
        <div class="dropdown-item logout">Đăng xuất</div>
      </div>
    </div>
  </header>
</template>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

header {
  max-width: 100vw;
  height: 10vh;
  background-color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.header-img {
  display: flex;
  align-items: center;
  width: 20%;
}

.header-img img {
  max-height: 100%;
  width: 80px;
  margin-left: 50px;
}

.user-info {
  display: block;
  margin-left: 10px;
  height: 100%;
}

.user {
  cursor: pointer;
  height: 100%;
  display: flex;
  align-items: center;
}

.username {
  margin-right: 20px;
  cursor: pointer;
}

.dropdown {
  position: relative;
}

.dropdown .dropdown-item {
  color: #fff;
  padding: 10px;
  white-space: nowrap;
  cursor: pointer;
  background-color: #000;
}

.dropdown .dropdown-item.logout {
  background-color: #000;
  color: #ffffff;
}


.dropdown-content {
  display: none;
  position: absolute;
  background-color: #ffffff;
  box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
  z-index: 1;
  min-width: 160px;
}

.dropdown.open .dropdown-content {
  display: block;
}
</style>