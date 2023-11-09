<template>
  <main>
    <SlideBar></SlideBar>
    <div class="timekeeping">
        <span style="font-size: 32px; font-weight: 700; text-align: center;">TimeKeeping Management</span>
        <div class="title-table">
          <div>
            <el-date-picker style="margin-left: 10px" v-model="dataSearch.startDate" type="date"
              placeholder="For date"></el-date-picker>
            <el-date-picker style="margin-left: 10px" v-model="dataSearch.endDate" type="date"
              placeholder="To date"></el-date-picker>
          </div>

          <div>
            <el-select v-model="dataSearch.department" placeholder="Select">
              <el-option key="0" label="Selected department" value="" />
              <el-option v-for="item in department" :key="item.id" :label="item.name" :value="item.name"
                :disabled="item.disabled" />
            </el-select>
            <el-input v-model="dataSearch.name" placeholder="Search by username" />
          </div>
            
        </div>
          <el-table :data="getCurrentPageData" height="48vh" style="width: 100%;" border stripe>
            <el-table-column prop="id" label="ID" min-width="50"></el-table-column>
            <el-table-column prop="user.name" label="User name" min-width="200"
              @click="router.push({ path: `/admin/list-user/${scope.row.user.id}` })"></el-table-column>
            <el-table-column prop="user.department.name" label="Department name" min-width="300"></el-table-column>
            <el-table-column prop="date" label="Date" min-width="150"></el-table-column>
            <el-table-column prop="timeCheckIn" label="Time Check In" min-width="150"></el-table-column>
            <el-table-column prop="timeCheckOut" label="Time Check Out" min-width="180"></el-table-column>
            <el-table-column prop="shift.name" label="Shift name" min-width="150"></el-table-column>
            <el-table-column fixed="right" label="Operations" width="140">
              <template #default="scope">
                <el-button link type="primary" @click="handleEdit(scope.row.id)">Edit</el-button>
                <el-button link type="danger" @click="handleDelete(scope.row.id)">Delete</el-button>
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
        <ConfirmBox v-if="confirmBox" title="Are you sure?" msg="Delete this timekeeping?" @confirm="deleteTimekeeping()"
          @cancel="confirmBox = false">
        </ConfirmBox>
        <div class="form-timekeeping">
          <NewTimeKeeping @updateData="displayTimeKeeping" @invisible="visibleMode = false" 
            :timekeepingId="timekeepingId" v-if="visibleMode"></NewTimeKeeping>
        </div>
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
}
.el-select {
  margin-right: 5px;
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

.el-date-picker {
  margin-left: 10px;
}

.pagination {
  margin-top: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.timekeeping {
  width: 80vw;
  margin-top: -40px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin-left: 20px;
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
  max-width: 50%;
  margin-right: 5px;
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
import { ref, onMounted, computed, reactive } from "vue";
import axios from "axios";
import router from "../../../router";
import { useUserStore } from "../../../stores/user";
import { useAlertStore } from "../../../stores/alert";
import ConfirmBox from "../../../components/ConfirmBox.vue";
import { saveAs } from "file-saver";
import { utils, write } from "xlsx";
import NewTimeKeeping from "../../../components/NewTimeKeeping.vue";
const user = useUserStore().user;
const alertStore = useAlertStore();
const timekeeping = ref();
const department = ref()
const timekeepingId= ref(0)
const deleteId = ref(0)
const pageSize = 9
const visibleMode = ref(false)
const confirmBox = ref(false)
let currentPage = ref(1);
let dataSearch = reactive({
  name: '',
  startDate: '',
  endDate: '',
  department: ''
})
onMounted(() => {
  displayTimeKeeping();
  displayDepartment();
});
const displayDepartment = async () => {
  try {
    await axios.get("http://127.0.0.1:8000/api/department")
      .then(function (response) {
        department.value = response.data.data;
      });
  } catch (e) {
    console.log(e)
  }
}
const displayTimeKeeping = async () => {
  visibleMode.value = false
  try {
    await axios
      .get("http://127.0.0.1:8000/api/manage-timekeeping", {
        headers: { Authorization: `Bearer ${user.token}` }
      })
      .then(function (response) {
        timekeeping.value = response.data.data;
        console.log(timekeeping)
      });
  } catch (e) {
    console.log(e);
  }
};
const deleteTimekeeping = async () => {
  confirmBox.value = false
  try {
    await axios
      .delete(`http://127.0.0.1:8000/api/delete-timekeeping/${deleteId.value}`, {
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
  displayTimeKeeping();
};
const filteredData = computed(() => {
  let search = timekeeping.value
  if ((dataSearch.startDate != '' && dataSearch.endDate != '')) {
    const startDate = formatDate(dataSearch.startDate)
    const endDate = formatDate(dataSearch.endDate)
    search = search.filter((item) => {
      const datePart = item.date.split("/");
      const itemDay = datePart[0]
      const itemMonth = datePart[1]
      const itemYear = datePart[2]
      const itemDate = `${itemYear}-${itemMonth}-${itemDay}`;
      return (itemDate >= startDate && itemDate <= endDate)
    })
  }
  if (dataSearch.name) {
    let searchText = dataSearch.name.toLowerCase();
    currentPage.value = 1
    search = search.filter((item) => {
      return item.user.name.toLowerCase().includes(searchText);
    })
  }
  if (dataSearch.department) {
    let searchText = dataSearch.department.toLowerCase();
    console.log(dataSearch.department)
    currentPage.value = 1
    console.log(searchText)
    search = search.filter((item) => {
      return item.user.department.name.toLowerCase() === searchText;
    })
  }
  if (dataSearch.name == '' && (dataSearch.startDate == '' || dataSearch.endDate == '') && dataSearch.department == '') {
    search = timekeeping.value
  }
  return search
})
const formatDate = (date) => {
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  return `${year}-${month}-${day}`;
};
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

function handleEdit(id) {
  visibleMode.value = true;
  timekeepingId.value = id
}
function handleDelete(id) {
  confirmBox.value = true
  deleteId.value = id
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
