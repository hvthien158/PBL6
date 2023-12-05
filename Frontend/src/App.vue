<template>
  <div class="content">
    <HeaderHome :notification="notification"></HeaderHome>
      <router-view :notification="notification" class="main-view" :key="$route.fullPath"></router-view>
    <Footer></Footer>
    <transition name="slide-fade">
      <AlertBox v-if="isAlert" :type="typeAlert" :msg="msgAlert"></AlertBox>
    </transition>
  </div>
</template>

<style>
*.table tbody td {
  color: black;
}
*.table thead td {
  color: rgb(255, 255, 255);
}

.main-view {
  margin-top: 60px;
  min-height: calc(100vh - 120px);
  display: flex;
  background-image: url("assets/background.jpg");
  background-size: 100%;
}
input {
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
import { useAlertStore } from "./stores/alert";
import AlertBox from "./components/AlertBox.vue";
import { ref, watch, reactive } from "vue";
import { onMessage } from "firebase/messaging";
import { messaging } from './firebase';
import { ElNotification } from 'element-plus'
const isAlert = ref(false);
const typeAlert = ref("");
const msgAlert = ref("");
const alertStore = useAlertStore();

watch(
  () => alertStore.alert,
  () => {
    if (alertStore.alert === true) {
      typeAlert.value = alertStore.type;
      msgAlert.value = alertStore.msg;
      isAlert.value = true;
      setTimeout(() => {
        alertStore.clear();
        isAlert.value = false;
      }, 3000);
    }
  }
);
let notification = reactive({
  title: '',
  message: '',
  type: ''
})
console.log(typeof notification)
onMessage(messaging, (payload) => {
    notification.title = payload.data.title
    notification.message = payload.data.message
    notification.type = payload.data.type
});
watch(notification, () => {
    openNotification()
})
const openNotification = () => {
    ElNotification({
        title: notification.title,
        message: notification.message,
        position: 'bottom-right',
    })
}
</script>
