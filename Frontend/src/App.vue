<template>
  <Header v-if="!user"></Header>
  <HeaderHome v-else :user="user"></HeaderHome>
  <router-view :key="$route.fullPath"></router-view>
  <Footer></Footer>
</template>
<script setup>
import { RouterView } from "vue-router";
import Header from "./components/Header.vue";
import HeaderHome from "./components/HeaderHome.vue";
import Footer from "./components/Footer.vue";

let user = null;

if (localStorage.user) {
  user = JSON.parse(localStorage.user);
}
</script>