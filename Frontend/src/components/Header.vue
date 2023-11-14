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
    width: 100vw;
    height: 10vh;
    background-color: black;
    display: flex;
    justify-content: center;
    padding-top: 10px;
    padding-bottom: 10px;
    box-sizing: border-box;
    box-shadow: 0 4px 4px 0 rgb(0 0 0 / 30%);
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
  }
  .logo {
    max-height: 100%;

  }
</style>

<script setup>
import logo from '../assets/image/logo.png'
</script>
