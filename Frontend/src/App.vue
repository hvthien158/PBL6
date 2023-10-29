<template>
  <Header v-if="user.token === ''"></Header>
  <HeaderHome v-else></HeaderHome>
  <router-view :key="$route.fullPath"></router-view>
  <Footer></Footer>
</template>
<script setup>
import { RouterView } from "vue-router";
import Header from "./components/Header.vue";
import HeaderHome from "./components/HeaderHome.vue";
import Footer from "./components/Footer.vue";
import {useUserStore} from "./stores/user";

const user = useUserStore().user
</script>
