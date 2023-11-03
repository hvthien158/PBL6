<template>
  <main>
    <SlideBar></SlideBar>
    <ScheduleView ></ScheduleView>
  </main>
</template>
<style>
main {
  min-height: 100vh;
  border-top: 0.1em solid black;
  box-sizing: border-box;
  display: flex;
}
</style>
<script setup>
import SlideBar from "../../../components/SlideBar.vue";
import ScheduleView from "../../ScheduleView.vue";
</script>

