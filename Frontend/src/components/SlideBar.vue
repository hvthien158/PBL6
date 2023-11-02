<template>
  <div class="container">
    <div class="sidebar">
      <ul class="sidebar-menu">
        <li @click="selectedOption = (selectedOption == 'user' ? null : 'user') " :class="{ active: selectedOption === 'user' }">Quản lý người dùng</li>
        <div v-if="selectedOption === 'user'" class="sub-menu">
          <button @click="router.push({ path : '/admin/list-user'})">Danh sách người dùng</button>
          <button @click="router.push({ path : '/admin/create-user'})">Thêm người dùng</button>
        </div>
        <li @click="selectedOption = (selectedOption == 'organization' ?  null : 'organization') " :class="{ active: selectedOption === 'organization' }">Quản lý cơ quan</li>
        <div v-if="selectedOption === 'organization'" class="sub-menu">
          <button @click="router.push({ path : '/admin/list-department'})">Danh sách cơ quan</button>
          <button @click="router.push({ path : '/admin/add-department'})">Thêm cơ quan</button>
        </div>
        <li @click="selectedOption = (selectedOption == 'attendance' ? null: 'attendance')" :class="{ active: selectedOption === 'attendance' }">Quản lý chấm công</li>
        <div v-if="selectedOption === 'attendance'" class="sub-menu">
          <button>Thêm chấm công</button>
          <button>Sửa chấm công</button>
          <button>Xóa chấm công</button>
        </div>
        <li @click="selectedOption =(selectedOption == 'shift' ? null : 'shift') " :class="{ active: selectedOption === 'shift' }">Quản lý ca làm</li>
        <div v-if="selectedOption === 'shift'" class="sub-menu">
          <button>Thêm ca làm</button>
          <button>Sửa ca làm</button>
          <button>Xóa ca làm</button>
        </div>
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
  padding: 0
}

.sidebar {
  width: 100%;
  background-color: #333;
  color: #fff;
  padding: 10px;
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
  background-color: #666;
  color: #fff;
  border: none;
  padding: 5px 10px;
  margin-bottom: 5px;
  border-radius: 3px;
}

.sub-menu button:hover {
  background-color: #888;
}

.sub-menu button:last-child {
  margin-bottom: 0;
}

.sidebar-menu li.active {
  background-color: #555;
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