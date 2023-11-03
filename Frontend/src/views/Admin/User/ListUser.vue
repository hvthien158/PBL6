<template>
  <main>
    <SlideBar></SlideBar>
    <div class="user">
      <h1>Manager user</h1>
      <div class="table-responsive-md">
        <table class="table">
          <thead class="table-dark">
            <tr>
              <td scope="col">ID</td>
              <td>Name</td>
              <td>Email</td>
              <td>Address</td>
              <td>Date of birth</td>
              <td>Phone number</td>
              <td>Avatar</td>
              <td>Salary</td>
              <td>Posion</td>
              <td>Role</td>
              <td>Department name</td>
              <td>History timekeeping</td>
              <td>Edit info</td>
              <td>Delete user</td>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in data" :key="item.id">
              <td scope="row">{{ item.id }}</td>
              <td>{{ item.name }}</td>
              <td>{{ item.email }}</td>
              <td>{{ item.address }}</td>
              <td>{{ item.DOB }}</td>
              <td>{{ item.phoneNumber }}</td>
              <td>{{ item.avatar }}</td>
              <td>{{ item.salary }}</td>
              <td>{{ item.position }}</td>
              <td>{{ item.role }}</td>
              <td>{{ item.department.name }}</td>
              <td>
                <a
                  @click="
                    router.push({ path: `/admin/list-timekeeping/${item.id}` })
                  "
                  >View</a
                >
              </td>
              <td>
                <a
                  @click="
                    router.push({ path: `/admin/update-user/${item.id}` })
                  "
                  >Edit</a
                >
              </td>
              <td>
                <a @click="deleteUser(item.id)" v-if="item.id != user.id"
                  >Delete</a
                >
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</template>
<style scoped>
@import "https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css";
main {
  min-height: 100vh;
  border-top: 0.1em solid black;
  box-sizing: border-box;
  display: flex;
}
.user {
  width: 85vw;
  display: block;
  margin-top: 20px;
}
.user h1 {
  text-align: center;
  color: black !important;
  text-align: center;
}
.table td {
  border: 1px solid #dee2e6;
}
.table td a {
  cursor: pointer;
}
.table-responsive-md {
  margin-top: 20px;
}
</style>
<script setup>
import SlideBar from "../../../components/SlideBar.vue";
import { ref, onMounted } from "vue";
import { useUserStore } from "../../../stores/user";
import axios from "axios";
import router from "../../../router";
import { useRoute } from "vue-router";
import { useAlertStore } from "../../../stores/alert";
let data = ref();
const user = useUserStore().user;
const alertStore = useAlertStore();
const route = useRoute();
onMounted(() => {
  if (user.role !== "admin") {
    router.push({ path: "/" });
  } else {
    if (route.params.departmentName) {
      userDepartment(route.params.departmentName);
    } else {
      displayUser();
    }
  }
});
const displayUser = async () => {
  try {
    await axios
      .get("http://127.0.0.1:8000/api/user/", {
        headers: { Authorization: `Bearer ${user.token}` },
      })
      .then(function (response) {
        data.value = response.data.data;
        console.log(data.value);
      });
  } catch (e) {
    console.log(e);
  }
};
const userDepartment = async (departmentName) => {
  try {
    await axios
      .get(`http://127.0.0.1:8000/api/user-department/${departmentName}`, {
        headers: { Authorization: `Bearer ${user.token}` },
      })
      .then(function (response) {
        console.log(response)
        data.value = response.data.data;
        console.log(data.value);
      });
  } catch (e) {
    console.log(e);
  }
};
const deleteUser = async (id) => {
  try {
    await axios
      .delete(`http://127.0.0.1:8000/api/delete-user/${id}`, {
        headers: { Authorization: `Bearer ${user.token}` },
      })
      .then(function (response) {
        messages("success", response.data.message);
      });
  } catch (e) {
    messages("error", e.data.message);
  }
  displayUser();
};
const messages = (type, msg) => {
  alertStore.alert = true;
  alertStore.type = type;
  alertStore.msg = msg;
};
</script>
