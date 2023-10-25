<script setup>
import logo from '../assets/image/logo.png'
</script>
<template>
  <header>
    <img class="logo" :src="logo" alt="">
  </header>
</template>
<style scoped>
*{
    margin: 0;
    padding: 0
  }
  header {
    max-width: 100vw;
    height: 10vh;
    background-color: #ffffff;
    display: flex;
    justify-content: center;
    padding-top: 10px;
    padding-bottom: 10px;
    box-sizing: border-box;
  }
  .logo {
    max-height: 100%;

  }
</style>
