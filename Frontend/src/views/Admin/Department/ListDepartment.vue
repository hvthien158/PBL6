<template>
  <main>
    <SlideBar></SlideBar>
    <div class="department">
      <div class="list-department">
        <h1>List department</h1>
        <div class="table-responsive-md">
          <table class="table">
            <thead style="background-color: #ef9400">
              <tr>
                <td scope="col">ID</td>
                <td>Name department</td>
                <td>Address</td>
                <td>Phone number</td>
                <td>Email</td>
                <td>Quantity staff</td>
                <td>View list staff</td>
                <td>Edit info</td>
                <td>Delete department</td>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in department">
                <td scope="row">{{ item.id }}</td>
                <td>{{ item.name }}</td>
                <td>{{ item.address }}</td>
                <td>{{ item.phoneNumber }}</td>
                <td>{{ item.email }}</td>
                <td>{{ item.quantityUser }}</td>
                <td>
                  <a
                    @click="
                      router.push({
                        path: `/admin/list-user/${item.name}`,
                      })
                    "
                    >View list</a
                  >
                </td>
                <td>
                  <a
                    @click="
                      router.push({
                        path: `/admin/update-department/${item.id}`,
                      })
                    "
                    >Edit</a
                  >
                </td>
                <td>
                  <a @click="deleteDepartment(item.id, item.quantityUser)"
                    >Delete</a
                  >
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
main {
  min-height: 80vh;
  border-top: 0.1em solid black;
  box-sizing: border-box;
  display: flex;
}
.department {
  width: 85vw;
  display: flex;
  justify-content: center;
}
.table td {
  border: 1px solid #dee2e6;
}
.department h1 {
  text-align: center;
}

a:hover{
  cursor: pointer;
  color: #f3952d !important;
}
</style>
<script setup>
import SlideBar from "../../../components/SlideBar.vue";
import { ref, onMounted } from "vue";
import axios from "axios";
import router from "../../../router";
import { useUserStore } from "../../../stores/user";
import { useAlertStore } from "../../../stores/alert";
const user = useUserStore().user;
const alertStore = useAlertStore();
const department = ref();
onMounted(() => {
  displayDepartment();
});
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
const deleteDepartment = async (id, isHasStaff) => {
  if (isHasStaff === 0) {
    try {
      await axios
        .delete(`http://127.0.0.1:8000/api/delete-department/${id}`, {
          headers: { Authorization: `Bearer ${user.token}` },
        })
        .then(function (response) {
          console.log(response);
          if (response.status == 200) {
            messages("success", response.data.message);
            displayDepartment();
          }
        });
    } catch (e) {
      messages("error", e.data.message);
    }
  } else {
    messages("error", "Please move staff to another department");
  }
};
const messages = (type, msg) => {
  alertStore.alert = true;
  alertStore.type = type;
  alertStore.msg = msg;
};
</script>
