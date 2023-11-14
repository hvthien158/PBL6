<template>
  <main>
    <SlideBar></SlideBar>
    <div class="user">
      <span style="font-size: 32px; font-weight: 700;">User management</span>
      <div class="title-table">
        <div>
          <el-button type="warning" @click="handleCreate">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg"
              viewBox="0 0 16 16">
              <path fill-rule="evenodd"
                d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z" />
            </svg>
            New
          </el-button>
        </div>
        <div>
          <el-input v-model="dataSearch.name" placeholder="Search by name" />
          <el-input v-model="dataSearch.address" placeholder="Search by address" />
          <el-input v-model="dataSearch.email" placeholder="Search by email" />
          <el-input v-model="dataSearch.phoneNumber" placeholder="Search by phone number" />
          <el-form-item>
            <el-select v-model="dataSearch.department" type="text">
              <el-option label="Select Department" :value="0"></el-option>
              <el-option v-for="item in department" :label="item.name" :value="item.id"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-select v-model="dataSearch.position" type="text">
              <el-option label="Select Position" value="none"></el-option>
              <el-option v-for="item in position" :label="item.name" :value="item.value"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-select v-model="dataSearch.role" type="text">
              <el-option label="Select role" value="none"></el-option>
              <el-option v-for="item in role" :label="item.name" :value="item.value"></el-option>
            </el-select>
          </el-form-item>
        </div>
      </div>
      <el-table :data="data" height="59vh" style="width: 100%">
        <el-table-column prop="id" label="ID" width="50" />
        <el-table-column prop="name" label="Name" width="180" />
        <el-table-column prop="avatar" label="avatar" width="100">
          <template #default="scope">
            <el-avatar :src="scope.row.avatar" :size="40" />
          </template>
        </el-table-column>
        <el-table-column prop="email" label="Email" width="250" />
        <el-table-column prop="address" label="Address" width="300" />
        <el-table-column prop="DOB" label="Date of birth" width="120" />
        <el-table-column prop="phone_number" label="Phone number" width="120" />
        <el-table-column prop="position" label="Position" width="120" />
        <el-table-column prop="salary" label="Salary" width="120" />
        <el-table-column prop="department.name" label="Department" width="200" />
        <el-table-column prop="role" label="Role" width="100" />
        <el-table-column label="History" width="100">
          <template #default="scope">
            <el-button link type="primary" @click="handleViewHistory(scope.row.id)">View</el-button>
          </template>
        </el-table-column>
        <el-table-column fixed="right" label="Operations" width="120">
          <template #default="scope">
            <el-button link type="primary" @click="handleEdit(scope.row.id)">Edit</el-button>
            <el-button v-if="user.id != scope.row.id" link type="danger"
              @click="handleDelete(scope.row.id)">Delete</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div class="pagination">
        <el-button @click="previousPage" :disabled="currentPage === 1">
          Previous
        </el-button>
        <span>{{ currentPage }} / {{ totalPage }}</span>
        <el-button @click="nextPage" :disabled="currentPage === totalPage">
          Next
        </el-button>
      </div>
      <div class="form-user">
        <NewUser @update_data="displayUser" @invisible="visible_mode = false" :mode="operation_mode"
          :userId="userID_update" :visible=visible_mode v-if="visible_mode"></NewUser>
      </div>
    </div>
    <ConfirmBox v-if="confirm_box" title="Are you sure?" msg="Delete this user?" @confirm="deleteUser"
      @cancel="confirm_box = false">
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

.header-row {
  background-color: #f3952d;
}

.title-table {
  width: 80vw;
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
  margin-bottom: 10px;
}

.title-table .el-input {
  margin-left: 10px;
  width: 20%;
}

.title-table div {
  display: flex;
  align-items: end;
  justify-content: center;
}

.el-form-item {
  min-width: 170px;
  margin-left: 10px;
  margin-bottom: 0px;
}

.form-user {
  position: absolute;
  bottom: 7%;
}

.pagination {
  margin-top: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.pagination span {
  margin: 0 10px;
}
</style>
<script setup>
import SlideBar from "../../../components/SlideBar.vue";
import NewUser from "../../../components/NewUser.vue"
import { ref, onMounted, reactive, watch } from "vue";
import { useUserStore } from "../../../stores/user";
import axios from "axios";
import router from "../../../router";
import { useRoute } from "vue-router";
import { useAlertStore } from "../../../stores/alert";
import ConfirmBox from "../../../components/ConfirmBox.vue";

const data = ref();
const user = useUserStore().user;
const alertStore = useAlertStore();
const route = useRoute();
const visible_mode = ref(false)
const operation_mode = ref('create')
const userID_update = ref(0)
const department = ref()
const confirm_box = ref(false)
<<<<<<< Frontend/src/views/Admin/User/ListUser.vue
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
    if (route.params.id) {
      userDepartment(route.params.id);
    } else {
      displayUser();
    }
=======
const totalPage = ref(0)
const debounceSearch = ref(null);
let currentPage = ref(1);
let dataSearch = reactive({
  name: '',
  email: '',
  address: '',
  phoneNumber: '',
  position: 'none',
  role: 'none',
  department: 0
})
const role = [
  {
    name: "User",
    value: "user",
  },
  {
    name: "Admin",
    value: "admin",
>>>>>>> Frontend/src/views/Admin/User/ListUser.vue
  }
];
const position = [
  {
    name: "Intern",
    value: "intern",
  },
  {
    name: "Fresher",
    value: "fresher",
  },
  {
    name: "Junior",
    value: "junior",
  },
  {
    name: "Senior",
    value: "senior",
  },
];
axios.get("http://127.0.0.1:8000/api/department")
  .then(function (response) {
    department.value = response.data.data;
  });
const displayUser = async () => {
  visible_mode.value = false
  try {
    await axios
      .post(`http://127.0.0.1:8000/api/list-user/${currentPage.value - 1}`, {
        name: dataSearch.name.toLowerCase(),
        address: dataSearch.address.toLowerCase(),
        phoneNumber: dataSearch.phoneNumber,
        email: dataSearch.email.toLowerCase(),
        position: dataSearch.position === 'none' ? '' : dataSearch.position,
        role: dataSearch.role === 'none' ? '' : dataSearch.role,
        department: dataSearch.department === 0 ? '' : dataSearch.department
      }, {
        headers: { Authorization: `Bearer ${user.token}` },
      })
      .then(function (response) {
        console.log(response)
        data.value = response.data.user;
        totalPage.value = response.data.totalPage
      });
  } catch (e) {
    console.log(e);
  }
};
watch(dataSearch, () => {
  if (debounceSearch.value) {
    clearTimeout(debounceSearch.value);
  }
  debounceSearch.value = setTimeout(() => {
    currentPage.value = 1;
    displayUser();
  }, 500);
});
onMounted(() => {
  if (user.role !== "admin") {
    router.push({ path: "/" });
  } else {
    console.log(route)
    if (route.query.department) {
      dataSearch.department = Number(route.query.department)
    }
    displayUser();
  }
});
const delete_id = ref(0)
function handleDelete(id) {
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

function handleCreate() {
  operation_mode.value = 'create'
  if (!visible_mode.value) {
    visible_mode.value = true
  }
}

function handleEdit(id) {
  visible_mode.value = true;
  operation_mode.value = 'update'
  userID_update.value = id
}
const nextPage = () => {
  if (currentPage.value < totalPage.value) {
    currentPage.value++;
    displayUser()
  }
};
const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    displayUser()
  }
};
function handleViewHistory(id) {
  router.push({ path: `/admin/list-timekeeping/${id}` })
}
</script>
