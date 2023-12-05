<template>
  <main>
    <SlideBar></SlideBar>
    <div class="department">
      <span style="font-size: 32px; font-weight: 700; text-align: center; ">Department management</span>
      <div class="title-table">
        <div>
          <el-button type="warning" @click="handleCreate">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg"
                 viewBox="0 0 16 16">
              <path fill-rule="evenodd"
                    d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
            </svg>
            New
          </el-button>
        </div>
        <el-input v-model="dataSearch.name" placeholder="Search by name"/>
        <el-input v-model="dataSearch.manager" placeholder="Search by manager"/>
        <el-input v-model="dataSearch.address" placeholder="Search by address"/>
        <el-input v-model="dataSearch.email" placeholder="Search by email"/>
        <el-input v-model="dataSearch.phoneNumber" placeholder="Search by phone number"/>
        <div class="input-number">
          <el-text>Min Staff</el-text>
          <el-input-number v-model="dataSearch.minStaff" controls-position="right" :min="0"></el-input-number>
        </div>
        <div class="input-number">
          <el-text>Max Staff</el-text>
          <el-input-number v-model="dataSearch.maxStaff" controls-position="right"
                           :min="dataSearch.minStaff"></el-input-number>
        </div>
      </div>
      <el-table :data="department" height="58vh" style="width: 100%;" border stripe>
        <el-table-column prop="id" label="ID" min-width="50"></el-table-column>
        <el-table-column prop="name" label="Department name" min-width="180"></el-table-column>
        <el-table-column prop="manager.name" label="Department Manager" min-width="200">
          <template #default="scope">
            <el-button class="el-button--text" v-if="scope.row.manager.name !== 'none'"
                       @click="handleViewManager(scope.row.manager.id)">
              {{ scope.row.manager.name }}
            </el-button>
            <el-button class="el-button--text" style="color:#F56C6C" v-else @click="handleEdit(scope.row.id)">
              Set Department Manager
            </el-button>
          </template>
        </el-table-column>
        <el-table-column prop="address" label="Address" min-width="300"></el-table-column>
        <el-table-column prop="phoneNumber" label="Phone number" min-width="130"></el-table-column>
        <el-table-column prop="email" label="Email" min-width="250"></el-table-column>
        <el-table-column prop="quantityUser" label="Quantity staff" width="115"></el-table-column>
        <el-table-column label="Staff" min-width="100">
          <template #default="scope">
            <el-button class="el-button--text" v-if="scope.row.quantityUser === 0"
                       @click="messages('error', 'There are no employees at this department')">View
              Staff
            </el-button>
            <el-button class="el-button--text" v-else
                       @click="router.push({ path: `/admin/list-user/`, query: { department: scope.row.id } })">View
              Staff
            </el-button>
          </template>
        </el-table-column>
        <el-table-column fixed="right" label="Operations" width="140">
          <template #default="scope">
            <el-button link type="primary" @click="handleEdit(scope.row.id)">Edit</el-button>
            <el-button link type="danger" @click="handleDelete(scope.row.id, scope.row.quantityUser)">Delete</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div style="text-align: right; width: 100%">
        <span style="padding-right: 12px; color: #8c8c8c">{{ tableDescription }}</span>
      </div>
      <div class="pagination">
        <el-button @click="previousPage" :disabled="currentPage === 1">
          Prev
        </el-button>
        <!--        <span>{{ currentPage }} / {{ totalPage }}</span>-->
        <Pagination :current_page_prop="currentPage" :total_page_prop="totalPage"
                    @change-page="(page) => currentPage = page"></Pagination>
        <el-button @click="nextPage" :disabled="currentPage === totalPage">
          Next
        </el-button>
      </div>
      <div class="form-department">
        <NewDepartment @updateData="displayDepartment" @invisible="visibleMode = false" :mode="operationMode"
                       :departmentId="departmentId" :visible="visibleMode" v-if="visibleMode"></NewDepartment>
      </div>
      <div class="form-department">
        <User @updateData="displayDepartment" @invisible="managerMode = false" :userId="userId"
              v-if="managerMode"></User>
      </div>
    </div>
    <ConfirmBox v-if="confirmBox" title="Are you sure?" msg="Delete this department?" @confirm="deleteDepartment()"
                @cancel="confirmBox = false">
    </ConfirmBox>

  </main>
</template>
<style scoped>
main {
  box-sizing: border-box;
  display: flex;
}

.form-department {
  margin-top: 50px;
  max-width: 1138px;
  position: absolute;
  bottom: 0%;
}

.title-table .input-number {
  display: flex;
  justify-content: flex-end;
}

.input-number {
  width: 12%;
  min-width: 170px;
  border-right: 0;
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
}

.input-number .el-input-number {
  width: 40%;
  background-color: #262727;
}

.input-number .el-text {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  padding: 0 10px;
  white-space: nowrap;
  border-radius: 4px 0 0 4px;
  background-color: #262727;
  color: #909399;
  box-sizing: border-box;
}

.el-form {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 10px;
}

label {
  margin-bottom: 0px;
}

.el-card {
  min-width: 100%;
}

.title-table .el-input {
  margin-left: 10px;
  width: 15%;
}

.pagination {
  margin-top: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.department {
  width: 80vw;
  margin-top: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin-left: 20px;
  position: relative;
}

.title-table {
  width: 100%;
  display: flex;
  justify-content: space-between;
  margin: 10px 0 10px 0;
}

.title-table div {
  display: flex;
  align-items: end;
  justify-content: center;
}

.table td {
  border: 1px solid #dee2e6;
}

.department h1 {
  text-align: center;
}

a:hover {
  cursor: pointer;
  color: #f3952d !important;
}

.pagination span {
  margin: 0 10px;
}
</style>
<script setup>
import SlideBar from "../../../components/SlideBar.vue";
import {ref, onMounted, watch, reactive} from "vue";
import router from "../../../router";
import {useUserStore} from "../../../stores/user";
import {useAlertStore} from "../../../stores/alert";
import ConfirmBox from "../../../components/ConfirmBox.vue";
import User from '../../../components/User.vue'
import NewDepartment from "../../../components/NewDepartment.vue";
import Pagination from "../../../components/Pagination.vue";
import DepartmentAPI from "../../../services/DepartmentAPI";

const user = useUserStore().user;
const alertStore = useAlertStore();
const department = ref();
const departmentId = ref(0)
const deleteId = ref(0)
const totalPage = ref(0)
const visibleMode = ref(false)
const operationMode = ref('create')
const tableDescription = ref()
const confirmBox = ref(false)
const debounceSearch = ref(null);
const userId = ref(null)
const managerMode = ref(false)
let currentPage = ref(1);
let dataSearch = reactive({
  name: '',
  manager: '',
  address: '',
  email: '',
  phoneNumber: '',
  minStaff: 0,
  maxStaff: 0
})
onMounted(() => {
  displayDepartment();
});
const displayDepartment = async () => {
  visibleMode.value = false
  try {
    await DepartmentAPI.getListDepartment(user.token, currentPage.value - 1, dataSearch)
        .then(function (response) {
          department.value = response.data.department;
          totalPage.value = Math.ceil(response.data.totalDepartment / 10) == 0 ? 1 : Math.ceil(response.data.totalDepartment / 10)
          let tail = (response.data.totalDepartment < currentPage.value * 10) ? response.data.totalDepartment : currentPage.value * 10
          tableDescription.value = ((currentPage.value - 1) * 10 + 1) + '..' + tail + ' of ' + response.data.totalDepartment + ' departments'
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
    displayDepartment();
  }, 500);
});
const deleteDepartment = async () => {
  confirmBox.value = false
  try {
    await DepartmentAPI.deleteDepartment(deleteId.value, user.token)
        .then(function (response) {
          console.log(response);
          if (response.status === 200) {
            messages("success", response.data.message);
          }
        });
  } catch (e) {
    messages("error", e.data.message);
  }
  displayDepartment();
};

watch(() => currentPage.value, () => {
  displayDepartment()
})
const nextPage = () => {
  if (currentPage.value < totalPage.value) {
    currentPage.value++;
  }
};
const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};
const messages = (type, msg) => {
  alertStore.alert = true;
  alertStore.type = type;
  alertStore.msg = msg;
};

function handleCreate() {
  operationMode.value = 'create'
  if (!visibleMode.value) {
    visibleMode.value = true
  }
}

function handleEdit(id) {
  visibleMode.value = true;
  operationMode.value = 'update'
  departmentId.value = id
}

function handleDelete(id, isHasStaff) {
  if (isHasStaff === 0) {
    confirmBox.value = true
    deleteId.value = id
  } else {
    messages("error", "Please move staff to another department");
  }
}

const handleViewManager = (id) => {
  managerMode.value = true;
  userId.value = id
}
</script>
