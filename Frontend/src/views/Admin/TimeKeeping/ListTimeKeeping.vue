<template>
  <main>
    <SlideBar></SlideBar>
    <div class="timekeeping">
      <span style="font-size: 32px; font-weight: 700; text-align: center;">TimeKeeping Management</span>
      <div class="title-table">
        <div>
          <el-date-picker
              v-model="filter_value"
              type="daterange"
              start-placeholder="Start date"
              end-placeholder="End date"
              format="YYYY-MM-DD"
              value-format="YYYY-MM-DD"
          />
        </div>
        <div class="pagination-month">
          <el-button type="info" @click="previousMonth">
            Prev
          </el-button>
          <span>{{ monthDisplay + 1 }}/{{ yearDisplay }}</span>
          <el-button type="info" @click="nextMonth">
            Next
          </el-button>
        </div>
        <div>
          <el-select v-model="dataSearch.department" placeholder="Select department">
            <el-option label="Select Department" :value="0"></el-option>
            <el-option v-for="item in department" :key="item.id" :label="item.name" :value="item.id"
                       :disabled="item.disabled"/>
          </el-select>
          <el-input v-model="dataSearch.name" placeholder="Search by username"/>
        </div>

      </div>
      <el-table :data="timekeeping" height="48vh" style="width: 100%;" border stripe>
        <el-table-column prop="id" label="ID" min-width="50"></el-table-column>
        <el-table-column prop="name" label="Name" min-width="200">
          <template #default="scope">
            <el-button link type="success" @click="router.push({ path: `/admin/schedule/${scope.row.id}`})">
              {{ scope.row.name }}
            </el-button>
          </template>
        </el-table-column>
        <el-table-column prop="department" label="Department name" min-width="110">
          <template #default="scope">
            <el-button link type="warning" @click="goToDepartment(scope.row.department)">{{
                scope.row.department
              }}
            </el-button>
          </template>
        </el-table-column>
        <el-table-column prop="regularWorkingDays" label="The number of days specified"
                         min-width="120"></el-table-column>
        <el-table-column label="Working days">
          <el-table-column prop="sumWorkingDays" label="Total" min-width="120"></el-table-column>
          <el-table-column prop="remoteDays" label="Remote" min-width="120"></el-table-column>
          <el-table-column prop="leaveDays" label="Not work" min-width="120"></el-table-column>
          <el-table-column prop="lateDays" label="Late arrival" min-width="120"></el-table-column>
        </el-table-column>
        <el-table-column prop="regularWorkingTime" label="Predetermined time" min-width="120"></el-table-column>
        <el-table-column label="Working time">
          <el-table-column prop="sumWorkingTime" label="Total" min-width="120"></el-table-column>
          <el-table-column prop="scheduledWorkingTime" label="Scheduled" min-width="120"></el-table-column>
          <el-table-column prop="overtimeWorkingTime" label="Overtime" min-width="120"></el-table-column>
          <el-table-column prop="averageWorkingHours" label="Average" min-width="120"></el-table-column>
        </el-table-column>
      </el-table>
      <div style="text-align: right; width: 100%">
        <span style="padding-right: 12px; color: #8c8c8c">{{ table_description }}</span>
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
  position: absolute;
  bottom: 0%;
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
  margin-bottom: 10px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.pagination-month {
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 10px 0;
}

.pagination-month span {
  margin: 0 12px 4px 12px;
}

.timekeeping {
  width: 80vw;
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
import {ref, onMounted, computed, reactive, watch} from "vue";
import router from "../../../router";
import {useUserStore} from "../../../stores/user";
import {useAlertStore} from "../../../stores/alert";
import {saveAs} from "file-saver";
import {utils, write} from "xlsx";
import moment from "moment"
import Pagination from "../../../components/Pagination.vue";
import DepartmentAPI from "../../../services/DepartmentAPI";
import TimeKeepAPI from "../../../services/TimeKeepAPI";

const user = useUserStore().user;
const alertStore = useAlertStore();
const timekeeping = ref();
const department = ref()
const timekeepingId = ref(0)
const deleteId = ref(0)
const visibleMode = ref(false)
const debounceSearch = ref(null);
const user_quantity = ref(0);
let currentPage = ref(1);
let totalPage = ref(1);
let dataSearch = reactive({
  name: '',
  department: 0
})
const filter_value = ref([])
const monthDisplay = ref(moment().month())
const yearDisplay = ref(moment().year())
const table_description = ref('')

function filterByMonth() {
  filter_value.value = [
    yearDisplay.value + '-' + (monthDisplay.value + 1).toString().padStart(2, '0') + '-' + '01',
    yearDisplay.value + '-' + (monthDisplay.value + 1).toString().padStart(2, '0') + '-' + getDaysInMonth(monthDisplay.value + 1, yearDisplay.value)
  ]
}

filterByMonth()

function getDaysInMonth(month, year) {
  return moment(year + '-' + month.toString().padStart(2, '0')).daysInMonth()
}

function nextMonth() {
  if (monthDisplay.value === 11) {
    monthDisplay.value = 0
    yearDisplay.value += 1
  } else {
    monthDisplay.value += 1
  }
  filterByMonth()
}

function previousMonth() {
  if (monthDisplay.value === 0) {
    monthDisplay.value = 11
    yearDisplay.value -= 1
  } else {
    monthDisplay.value -= 1
  }
  filterByMonth()
}

function updateTableDescription() {
  let tail = (user_quantity.value < currentPage.value * 10) ? user_quantity.value : currentPage.value * 10
  table_description.value = ((currentPage.value - 1) * 10 + 1) + '..' + tail + ' of ' + user_quantity.value + ' users'
}

onMounted(() => {
  displayTimeKeeping();
  displayDepartment();
});

const displayDepartment = async () => {
  try {
    await DepartmentAPI.getAllDepartment()
        .then(function (response) {
          department.value = response.data.data;
        });
  } catch (e) {
    console.log(e)
  }
}

watch(() => filter_value.value, () => {
  displayTimeKeeping()
})

const displayTimeKeeping = async () => {
  try {
    await TimeKeepAPI.statisticTimeKeep(
        user.token,
        currentPage.value - 1,
        filter_value.value,
        dataSearch
    )
        .then((response) => {
          timekeeping.value = response.data.data
          user_quantity.value = response.data.quantity
          totalPage.value = Math.ceil(user_quantity.value / 10)
          if (totalPage.value === 0) {
            totalPage.value = 1
          }
          updateTableDescription()
        });
  } catch (e) {
    console.log(e);
  }
};

watch(
    [() => dataSearch.name, () => dataSearch.department],
    () => {
      if (debounceSearch.value) {
        clearTimeout(debounceSearch.value);
      }
      debounceSearch.value = setTimeout(() => {
        currentPage.value = 1;
        displayTimeKeeping();
      }, 500);
    },
    {deep: true}
);

const formatDate = (date) => {
  if (date !== '') {
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${year}-${month}-${day}`;
  } else {
    return date;
  }
};

watch(() => currentPage.value, () => {
  displayTimeKeeping()
  updateTableDescription()
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

function handleEdit(id) {
  visibleMode.value = true;
  timekeepingId.value = id
}

function goToDepartment(department_name) {
  department.value.forEach((item) => {
    if (item.name === department_name) {
      router.push({
        path: `/admin/list-user/department/${item.id}`
      })
    }
  })
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
