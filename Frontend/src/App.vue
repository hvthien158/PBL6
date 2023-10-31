<template>
  <HeaderHome></HeaderHome>
  <router-view style="margin-top: 9vh; background-color: #2b2b2b; min-height: 82vh" :key="$route.fullPath"></router-view>
  <Footer></Footer>
  <transition name="slide-fade">
    <AlertBox v-if="isAlert" :type="typeAlert" :msg="msgAlert"></AlertBox>
  </transition>
</template>

<style>
* {
  color: #a9a9a9;
}
input{
  color: black;
}
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.8s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(20px);
  opacity: 0;
}
</style>

<script setup>
import { RouterView } from "vue-router";
import HeaderHome from "./components/HeaderHome.vue";
import Footer from "./components/Footer.vue";
import {useAlertStore} from "./stores/alert";
import AlertBox from "./components/AlertBox.vue";
import {ref, watch} from "vue";

const isAlert = ref(false)
const typeAlert = ref('')
const msgAlert = ref('')
const alertStore = useAlertStore()

watch(() => alertStore.alert, () => {
  if(alertStore.alert === true){
    typeAlert.value = alertStore.type
    msgAlert.value = alertStore.msg
    isAlert.value = true
    setTimeout(() => {
      alertStore.clear()
      isAlert.value = false
    }, 3000)
  }
})
</script>
