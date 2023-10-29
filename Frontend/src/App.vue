<template>
  <Header v-if="!checkLanding.isUser"></Header>
  <HeaderHome v-else :user="checkLanding.user"></HeaderHome>
  <router-view :key="$route.fullPath"></router-view>
  <Footer></Footer>
</template>
<script setup>
import { RouterView } from "vue-router";
import { reactive, onUpdated} from 'vue';
import Header from "./components/Header.vue";
import HeaderHome from "./components/HeaderHome.vue";
import Footer from "./components/Footer.vue";

let checkLanding = reactive({
  isUser: false,
  user: null
})
onUpdated(() => {
  if (localStorage.user) {
    checkLanding.user = JSON.parse(localStorage.user);
    checkLanding.isUser = true
  } else {
    checkLanding.user = null
    checkLanding.isUser = false
  }
});
</script>
