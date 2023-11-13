<template>
  <main>
    <SlideBar></SlideBar>
    <div class="user">
      <div class="title-table">
        <div>
          <el-button type="warning" @click="handleCreate">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
            </svg>
            New
          </el-button>
        </div>
        <span style="font-size: 32px; font-weight: 700; padding-bottom: 50px">User management</span>
        <div>
          <el-input v-model="search" placeholder="Type to search" />
        </div>
      </div>
      <el-table :data="filterTableData" height="48vh" style="width: 100%">
        <el-table-column prop="id" label="ID" width="50" />
        <el-table-column prop="name" label="Name" width="180" />
        <el-table-column prop="avatar" label="avatar" width="220">
          <template #default="scope">
            <el-avatar :src="scope.row.avatar" :size="40"/>
          </template>
        </el-table-column>
        <el-table-column prop="email" label="Email" width="220" />
        <el-table-column prop="address" label="Address" width="300"/>
        <el-table-column prop="DOB" label="Date of birth" width="120" />
        <el-table-column prop="phone_number" label="Phone number" width="120" />
        <el-table-column prop="position" label="Position" width="120" />
        <el-table-column prop="salary" label="Salary" width="120" />
        <el-table-column prop="department.name" label="Department" width="120" />
        <el-table-column prop="role" label="Role" width="100" />
        <el-table-column label="History" width="100">
          <template #default="scope">
            <el-button link type="primary" @click="handleViewHistory(scope.row.id)">View</el-button>
          </template>
        </el-table-column>
        <el-table-column fixed="right" label="Operations" width="120">
          <template #default="scope">
            <el-button link type="primary" @click="handleEdit(scope.row.id)">Edit</el-button>
            <el-button link type="danger" @click="handleDelete(scope.row.id)">Delete</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div class="form-user">
        <NewUser @update_data="displayUser" @invisible="visible_mode = false" :mode="operation_mode" :user_id="userID_update" v-if="visible_mode"></NewUser>
      </div>
    </div>
    <ConfirmBox
        v-if="confirm_box"
        title="Are you sure?"
        msg="Delete this user?"
        @confirm="deleteUser"
        @cancel="confirm_box = false"
    >
    </ConfirmBox>
  </main>
</template>
<style scoped>
@import "https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css";
main {
  box-sizing: border-box;
  display: flex;
}
.user {
  width: 80vw;
  margin-top: -40px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin-left: 20px;
}
.user h1 {
  text-align: center;
  color: black !important;
}
.header-row{
  background-color: #f3952d;
}
.title-table{
  width: 75vw;
  display: flex;
  justify-content: space-between;
}
.title-table div{
  margin-top: 30px;
  display: flex;
  align-items: end;
  justify-content: center;
  padding-bottom: 4px;
}
.form-user{
  position: absolute;
  bottom: 7%;
}
</style>
<script setup>
import SlideBar from "../../../components/SlideBar.vue";
import NewUser from "../../../components/NewUser.vue"
import {ref, onMounted, computed} from "vue";
import { useUserStore } from "../../../stores/user";
import axios from "axios";
import router from "../../../router";
import { useRoute } from "vue-router";
import { useAlertStore } from "../../../stores/alert";
import ConfirmBox from "../../../components/ConfirmBox.vue";

const data = ref([]);
const user = useUserStore().user;
const alertStore = useAlertStore();
const route = useRoute();
const visible_mode = ref(false)
const operation_mode = ref('create')
const userID_update = ref(0)
const confirm_box = ref(false)
const search = ref('')

const filterTableData = computed(() =>
    data.value.filter(
        (data) =>
            !search.value ||
            data.name.toLowerCase().includes(search.value.toLowerCase())
    )
)

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
  visible_mode.value = false
  try {
    await axios
      .get("http://127.0.0.1:8000/api/user/", {
        headers: { Authorization: `Bearer ${user.token}` },
      })
      .then(function (response) {
        data.value = response.data.data;
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
        data.value = response.data.data;
      });
  } catch (e) {
    console.log(e);
  }
};

const delete_id = ref(0)
function handleDelete(id){
  confirm_box.value = true
  delete_id.value = id
}

const deleteUser = async () => {
  confirm_box.value = false
  try {
    await axios
      .delete(`http://127.0.0.1:8000/api/delete-user/${delete_id.value}`, {
        headers: { Authorization: `Bearer ${user.token}` },
      })
      .then(function (response) {
        messages("success", response.data.message);
      });
  } catch (e) {
    messages("error", e.data.message);
  }
  delete_id.value = 0
  displayUser();
};
const messages = (type, msg) => {
  alertStore.alert = true;
  alertStore.type = type;
  alertStore.msg = msg;
};

function handleCreate(){
  operation_mode.value = 'create'
  if(!visible_mode.value){
    visible_mode.value = true
  }
}

function handleEdit(id){
  visible_mode.value = true;
  operation_mode.value = 'update'
  userID_update.value = id
}

function handleViewHistory(id){
  router.push({ path: `/admin/list-timekeeping/${id}`})
}
</script>
