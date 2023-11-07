<template>
  <main>
    <SlideBar></SlideBar>
    <div class="shift">
      <div class="shift-container">
        <h1>Quản lý ca làm</h1>
        <div class="table-responsive-md">
          <table class="table">
            <thead class="table-dark">
            <tr>
              <td scope="col">ID</td>
              <td>Tên</td>
              <td>Thời gian check in</td>
              <td>Thời gian check out</td>
              <td>Số công</td>
              <td>Sửa ca làm</td>
              <td>Xóa ca làm</td>
            </tr>
            </thead>
            <tbody>
            <tr v-for="item in data" :key="item.id">
              <td scope="row">{{ item.id }}</td>
              <td>{{ item.name }}</td>
              <td>{{ item.TimeValidCheckIn }}</td>
              <td>{{ item.TimeValidCheckOut }}</td>
              <td>{{ item.amount }}</td>
              <td>
                <a
                    @click="
                      router.push({ path: `/admin/update-shift/${item.id}` })
                    "
                >Sửa
                </a>
              </td>
              <td>
                <a @click="deleteUser(item.id)">Xóa</a>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</template>
<style scoped>
@import "https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css";

main {
  min-height: 82vh;
  border-top: 0.1em solid black;
  box-sizing: border-box;
  display: flex;
}

.shift {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 200px;
}

.shift-container {
  display: block;
}

.shift h1 {
  margin-bottom: 20px;
  text-align: center;
}

.table td {
  border: 1px solid #dee2e6;
}

.table td a {
  cursor: pointer;
}
</style>
<script setup>
import SlideBar from "../../../components/SlideBar.vue";
import {ref, onMounted} from "vue";
import {useUserStore} from "../../../stores/user";
import axios from "axios";
import router from "../../../router";

let data = ref();
const user = useUserStore().user;
onMounted(() => {
  if (user.role !== "admin") {
    router.push({path: "/"});
  }
  displayShift();
});
const displayShift = async () => {
  try {
    await axios
        .get("http://127.0.0.1:8000/api/shift")
        .then(function (response) {
          data.value = response.data.data;
        });
  } catch (e) {
    console.log(e);
  }
}
const deleteUser = async (id) => {
  try {
    await axios.delete(`http://127.0.0.1:8000/api/delete-user/${id}`, {
      headers: {Authorization: `Bearer ${user.token}`}
    })
  } catch (e) {
    console.log(e)
  }
  displayUser()
}
</script>
