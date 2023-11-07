<template>
  <main>
    <SlideBar></SlideBar>
    <div class="department">
      <el-card>
        <el-form inline>
          <el-form-item label="Search by name">
            <el-input v-model="dataSearch"></el-input>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="exportExcel()">Export Excel</el-button>
            <el-button type="primary" @click="exportCSV()">Export CSV</el-button>
          </el-form-item>
        </el-form>
        <div slot="header" class="card-header">
          List Department
        </div>
        <div class="card-content">
          <el-table :data="getCurrentPageData" border stripe>
            <el-table-column prop="id" label="ID"></el-table-column>
            <el-table-column prop="name" label="Department name"></el-table-column>
            <el-table-column prop="address" label="Address"></el-table-column>
            <el-table-column prop="phoneNumber" label="Phone number"></el-table-column>
            <el-table-column prop="email" label="Email"></el-table-column>
            <el-table-column prop="quantityUser" label="Quantity staff"></el-table-column>
            <el-table-column label="Staff">
              <template #default="scope">
                <el-button class="el-button--text" v-if="scope.row.quantityUser === 0"
                  @click="messages('error','There are no employees at this department')">View
                  Staff</el-button>
                  <el-button class="el-button--text" v-else
                  @click="router.push({ path: `/admin/list-user/${scope.row.name}` })">View
                  Staff</el-button>
              </template>
            </el-table-column>
            <el-table-column label="Edit department">
              <template #default="scope">
                <el-button class="el-button--text"
                  @click="router.push({ path: `/admin/update-department/${scope.row.id}` })">Edit</el-button>
              </template>
            </el-table-column>
            <el-table-column label="Delete department">
              <template #default="scope">
                <el-button class="el-button--text"
                  @click="deleteDepartment(scope.row.id, scope.row.quantityUser)">Delete</el-button>
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
import { saveAs } from "file-saver";
import { utils, write } from "xlsx";
const user = useUserStore().user;
const alertStore = useAlertStore();
const department = ref();
const pageSize = 10
let currentPage = ref(1);
let dataSearch = ref(null)
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
const exportExcel = () => {
  const excelData = filteredData.value.map((item) => {
    return {
      id: item.id,
      name: item.name,
      address: item.address,
      phoneNumber: item.phoneNumber,
      email: item.email,
      quantityUser: item.quantityUser
    };
  });
  const worksheet = utils.json_to_sheet(excelData);
  const workbook = utils.book_new();
  utils.book_append_sheet(workbook, worksheet, "ListDepartment");
  const excelBuffer = write(workbook, {
    bookType: "xlsx",
    type: "array",
  });
  const dataBlob = new Blob([excelBuffer], {
    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
  });
  saveAs(dataBlob, "list_department.xlsx");
};

const exportCSV = () => {
  let csvContent = "data:text/csv;charset=utf-8,";
  const headers = [
    "id",
    "name",
    "address",
    "phoneNumber",
    "email",
    "quantityUser"
  ];
  csvContent += headers.join(",") + "\n";
  filteredData.value.forEach((item) => {
    const row = headers
      .map((header) => {
        return item[header];
      })
      .join(",");
    csvContent += row + "\n";
  });

  const encodedUri = encodeURI(csvContent);
  const link = document.createElement("a");
  link.setAttribute("href", encodedUri);
  link.setAttribute("download", `list_department.csv`);
  link.click();
};
</script>
