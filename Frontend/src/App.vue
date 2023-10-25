<script setup>
import { RouterView } from "vue-router";
import Header from "./components/Header.vue";
import HeaderHome from "./components/HeaderHome.vue";
import Footer from "./components/Footer.vue";

</script>

<template>
    <!-- <Header v-if="localStorage.user"></Header>
    <HeaderHome v-else></HeaderHome> -->
    <HeaderHome></HeaderHome>
    <RouterView/>
    <Footer></Footer>
</template>

<style scoped>

</style>
