<template>
  <div class="container">
    <div class="sidebar">
      <ul class="sidebar-menu">
        <li @click="router.push({ path : '/admin/list-timekeeping'})"
            :class="{ blue: selectedOption === 'timekeeping' }">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-clock-history"
               viewBox="0 0 16 16">
            <path
                d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7.003 7.003 0 0 0-.985-.299l.219-.976c.383.086.76.2 1.126.342zm1.37.71a7.01 7.01 0 0 0-.439-.27l.493-.87a8.025 8.025 0 0 1 .979.654l-.615.789a6.996 6.996 0 0 0-.418-.302zm1.834 1.79a6.99 6.99 0 0 0-.653-.796l.724-.69c.27.285.52.59.747.91l-.818.576zm.744 1.352a7.08 7.08 0 0 0-.214-.468l.893-.45a7.976 7.976 0 0 1 .45 1.088l-.95.313a7.023 7.023 0 0 0-.179-.483m.53 2.507a6.991 6.991 0 0 0-.1-1.025l.985-.17c.067.386.106.778.116 1.17l-1 .025zm-.131 1.538c.033-.17.06-.339.081-.51l.993.123a7.957 7.957 0 0 1-.23 1.155l-.964-.267c.046-.165.086-.332.12-.501zm-.952 2.379c.184-.29.346-.594.486-.908l.914.405c-.16.36-.345.706-.555 1.038l-.845-.535m-.964 1.205c.122-.122.239-.248.35-.378l.758.653a8.073 8.073 0 0 1-.401.432l-.707-.707z"/>
            <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/>
            <path
                d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
          </svg>
          Timekeeping management
        </li>
        <li @click="router.push({ path : '/admin/list-user'})" :class="{ yellow: selectedOption === 'user' }">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-people"
               viewBox="0 0 16 16">
            <path
                d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
          </svg>
          User management
        </li>
        <li @click="router.push({ path : '/admin/list-department'})" :class="{ red: selectedOption === 'department' }">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-building"
               viewBox="0 0 16 16">
            <path
                d="M4 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zM4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM7.5 5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM4.5 8a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5z"/>
            <path
                d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1zm11 0H3v14h3v-2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V15h3z"/>
          </svg>
          Department management
        </li>
        <li @click="router.push({ path : '/admin/list-shift'})" :class="{ green: selectedOption === 'shift' }">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-mask"
               viewBox="0 0 16 16">
            <path
                d="M6.225 1.227A7.5 7.5 0 0 1 10.5 8a7.5 7.5 0 0 1-4.275 6.773 7 7 0 1 0 0-13.546zM4.187.966a8 8 0 1 1 7.627 14.069A8 8 0 0 1 4.186.964z"/>
          </svg>
          Shift management
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import {ref} from 'vue';
import router from '../router'

const selectedOption = ref('');

switch (router.currentRoute.value.fullPath) {
  case '/admin/list-timekeeping':
    selectedOption.value = 'timekeeping'
    break
  case '/admin/list-user':
    selectedOption.value = 'user'
    break
  case '/admin/list-department':
    selectedOption.value = 'department'
    break
  case '/admin/list-shift':
    selectedOption.value = 'shift'
    break
}
</script>

<style scoped>
.container {
  display: flex;
  min-height: 80vh;
  background-color: #f1f1f1;
  width: 15vw;
  margin: 0;
  padding: 0;
  font-size: small;
}

.sidebar {
  width: 100%;
  padding: 10px;
  background-color: white;
}

.sidebar-menu {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar-menu li {
  cursor: pointer;
  padding: 10px;
  margin-bottom: 5px;
}

.sub-menu {
  display: flex;
  flex-direction: column;
  padding-left: 20px;
}

.sub-menu button {
  border: none;
  padding: 5px 10px;
  margin-bottom: 5px;
  border-radius: 3px;
}

.sub-menu button:hover {
  cursor: pointer;
  background-color: #888;
}

.sub-menu button:last-child {
  margin-bottom: 0;
}

.sidebar-menu li.active {
  border-radius: 3px;
}

.content {
  flex: 1;
  padding: 20px;
}

h3 {
  color: #333;
  margin-bottom: 10px;
}

button {
  margin-right: 10px;
}

ul li {
  display: block;
  width: 100%;
  padding: 20px;
  border-left: 5px solid;
  position: relative;
  z-index: 2;
  text-decoration: none;
  color: #444;
  box-sizing: border-box;
}

ul li:hover {
  border-bottom: 0;
  color: #fff;
}

ul li:first-child {
  border-left: 10px solid #3498db;
}

ul li:nth-child(2) {
  border-left: 10px solid #ffd071;
}

ul li:nth-child(3) {
  border-left: 10px solid #f0776c;
}

ul li:last-child {
  border-left: 10px solid #1abc9c;
}

ul li:after {
  content: "";
  height: 100%;
  left: 0;
  top: 0;
  width: 0;
  position: absolute;
  transition: all 0.3s ease 0s;
  -webkit-transition: all 0.3s ease 0s;
  z-index: -1;
}

ul li:hover:after {
  width: 100%;
}

ul li:first-child:after,
.blue {
  background: #3498db;
}

ul li:nth-child(2):after,
.yellow {
  background: #ffd071;
}

ul li:nth-child(3):after,
.red {
  background: #f0776c;
}

ul li:last-child:after,
.green{
  background: #1abc9c;
}

.blue, .green, .yellow, .red {
  color: #fff;
}
</style>
