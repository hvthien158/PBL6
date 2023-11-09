<template>
  <div class="container">
    <div class="sidebar">
      <ul class="sidebar-menu">
        <li @click="router.push({ path : '/admin/list-user'})" :class="{ active: selectedOption === 'user' }">User management</li>
        <li @click="router.push({ path : '/admin/list-department'})" :class="{ active: selectedOption === 'organization' }">Department management</li>
        <li @click="router.push({ path : '/admin/list-timekeeping'})" :class="{ active: selectedOption === 'attendance' }">Timekeeping management</li>
        <li @click="router.push({ path : '/admin/list-shift'})" :class="{ active: selectedOption === 'shift' }">Shift management</li>
      </ul>
    </div>
  </div>
</template>
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
.sidebar-menu li:hover {
  cursor: pointer;
  border-left: #9ca3af 4px solid;
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
</style>
<script setup>
import { ref } from 'vue';
import router from '../router'
const selectedOption = ref(null);
</script>