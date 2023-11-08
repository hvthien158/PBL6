<template>
  <main>
    <SlideBar></SlideBar>
    <div class="department">
      <el-card>
        <h2 style="font-size: 32px; font-weight: 700; text-align: center; ">Department management</h2>
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
            <el-input v-model="dataSearch" placeholder="Type to search" />
          </div>
        </div>
        <div class="card-content">
          <el-table :data="getCurrentPageData" height="48vh" border stripe>
            <el-table-column prop="id" label="ID" min-width="50"></el-table-column>
            <el-table-column prop="name" label="Department name" min-width="200"></el-table-column>
            <el-table-column prop="address" label="Address" min-width="300"></el-table-column>
            <el-table-column prop="phoneNumber" label="Phone number" min-width="150"></el-table-column>
            <el-table-column prop="email" label="Email" min-width="200"></el-table-column>
            <el-table-column prop="quantityUser" label="Quantity staff" min-width="150"></el-table-column>
            <el-table-column label="Staff" min-width="100">
              <template #default="scope">
                <el-button class="el-button--text" v-if="scope.row.quantityUser === 0"
                  @click="messages('error', 'There are no employees at this department')">View
                  Staff</el-button>
                <el-button class="el-button--text" v-else
                  @click="router.push({ path: `/admin/list-user/${scope.row.name}` })">View
                  Staff</el-button>
              </template>
            </el-table-column>
            <el-table-column fixed="right" label="Operations" width="140">
              <template #default="scope">
                <el-button link type="primary" @click="handleEdit(scope.row.id)">Edit</el-button>
                <el-button link type="danger"
                  @click="handleDelete(scope.row.id, scope.row.quantityUser)">Delete</el-button>
              </template>
            </el-table-column>
          </el-table>
          <div class="pagination">
            <el-button @click="previousPage" :disabled="currentPage === 1">
              Previous
            </el-button>
            <span>{{ currentPage }} / {{ getTotalPage }}</span>
            <el-button @click="nextPage" :disabled="currentPage === getTotalPage">
              Next
            </el-button>
          </div>
        </div>
        <ConfirmBox v-if="confirmBox" title="Are you sure?" msg="Delete this department?" @confirm="deleteDepartment()"
          @cancel="confirmBox = false">
        </ConfirmBox>
        <div class="form-department">
          <NewDepartment @updateData="displayDepartment" @invisible="visibleMode = false" :mode="operationMode"
            :departmentId="departmentId" v-if="visibleMode"></NewDepartment>
        </div>
      </el-card>
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

.card-header {
  text-align: center;
  background-color: #f3952d;
  font-size: 20px;
  font-weight: 700;
}

.form-department {
  margin-top: 50px;
  max-width: 1138px;
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

.pagination {
  margin-top: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.department {
  width: 85vw;
  display: flex;
  justify-content: center;
  min-height: 85vh;
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
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import router from "../../../router";
import { useUserStore } from "../../../stores/user";
import { useAlertStore } from "../../../stores/alert";
import ConfirmBox from "../../../components/ConfirmBox.vue";
import { saveAs } from "file-saver";
import { utils, write } from "xlsx";
import NewDepartment from "../../../components/NewDepartment.vue";
const user = useUserStore().user;
const alertStore = useAlertStore();
const department = ref();
const departmentId = ref(0)
const deleteId = ref(0)
const pageSize = 8
const visibleMode = ref(false)
const operationMode = ref('create')
const confirmBox = ref(false)
let currentPage = ref(1);
let dataSearch = ref(null)
onMounted(() => {
  displayDepartment();
});
const displayDepartment = async () => {
  visibleMode.value = false
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
const deleteDepartment = async () => {
  confirmBox.value = false
  try {
    await axios
      .delete(`http://127.0.0.1:8000/api/delete-department/${deleteId.value}`, {
        headers: { Authorization: `Bearer ${user.token}` },
      })
      .then(function (response) {
        console.log(response);
        if (response.status == 200) {
          messages("success", response.data.message);
        }
      });
  } catch (e) {
    messages("error", e.data.message);
  }
  displayDepartment();
};
const filteredData = computed(() => {
  if (!dataSearch.value) {
    return department.value
  } else {
    let searchText = dataSearch.value.toLowerCase();
    currentPage.value = 1
    return department.value.filter((item) => {
      console.log(searchText)
      return item.name.toLowerCase().includes(searchText);
    })
  }
})
const getTotalPage = computed(() => {
  if (filteredData.value) {
    const filteredDataLength = filteredData.value.length
    return Math.ceil(filteredDataLength / pageSize);
  }
})
const getCurrentPageData = computed(() => {
  if (filteredData.value) {
    const startIndex = (currentPage.value - 1) * pageSize
    const endIndex = startIndex + pageSize;
    return filteredData.value.slice(startIndex, endIndex);
  }
});
const nextPage = () => {
  if (currentPage.value < getTotalPage.value) {
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

// const exportExcel = () => {
//   const excelData = filteredData.value.map((item) => {
//     return {
//       id: item.id,
//       name: item.name,
//       address: item.address,
//       phoneNumber: item.phoneNumber,
//       email: item.email,
//       quantityUser: item.quantityUser
//     };
//   });
//   const worksheet = utils.json_to_sheet(excelData);
//   const workbook = utils.book_new();
//   utils.book_append_sheet(workbook, worksheet, "ListDepartment");
//   const excelBuffer = write(workbook, {
//     bookType: "xlsx",
//     type: "array",
//   });
//   const dataBlob = new Blob([excelBuffer], {
//     type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
//   });
//   saveAs(dataBlob, "list_department.xlsx");
// };

// const exportCSV = () => {
//   let csvContent = "data:text/csv;charset=utf-8,";
//   const headers = [
//     "id",
//     "name",
//     "address",
//     "phoneNumber",
//     "email",
//     "quantityUser"
//   ];
//   csvContent += headers.join(",") + "\n";
//   filteredData.value.forEach((item) => {
//     const row = headers
//       .map((header) => {
//         return item[header];
//       })
//       .join(",");
//     csvContent += row + "\n";
//   });

//   const encodedUri = encodeURI(csvContent);
//   const link = document.createElement("a");
//   link.setAttribute("href", encodedUri);
//   link.setAttribute("download", `list_department.csv`);
//   link.click();
// };
</script>
