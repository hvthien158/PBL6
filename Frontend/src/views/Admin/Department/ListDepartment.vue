<template>
  <main>
    <SlideBar></SlideBar>
    <div class="department">
      <div class="list-department">
        <h1>Danh sách cơ quan làm việc</h1>
        <div class="table-responsive-md">
          <table class="table">
            <thead class="table-dark">
              <tr>
                <td scope="col">ID</td>
                <td>Tên cơ quan</td>
                <td>Địa chỉ</td>
                <td>Số điện thoại</td>
                <td>Email</td>
                <td>Quản lý nhân viên trong cơ quan</td>
                <td>Chỉnh sửa thông tin</td>
                <td>Xóa cơ quan</td>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in department">
                <td scope="row">{{ item.id }}</td>
                <td>{{ item.name }}</td>
                <td>{{ item.address }}</td>
                <td>{{ item.phone_number }}</td>
                <td>{{ item.email }}</td>
                <td>Xem danh sách</td>
                <td>Chỉnh sửa</td>
                <td>Xóa</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</template>
<style scoped>
main {
  min-height: 80vh;
  border-top: 0.1em solid black;
  box-sizing: border-box;
  display: flex;
}
.department{
    width: 85vw;
    display: flex;
    justify-content: center;
}
.table td{
    border: 1px solid #dee2e6;
}
.department h1 {
    text-align: center;
}
</style>
<script setup>
import SlideBar from "../../../components/SlideBar.vue";
import { ref, onMounted } from 'vue'
import axios from 'axios'
const department = ref()
onMounted(() => {
    displayDepartment()
})
const displayDepartment = async () => {
  try {
    await axios
      .get("http://127.0.0.1:8000/api/department")
      .then(function (response) {
        department.value = response.data.data;
      });
  } catch (e) {
    console.log(e);
  }
};
</script>
