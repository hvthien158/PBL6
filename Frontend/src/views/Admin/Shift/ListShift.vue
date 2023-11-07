<template>
  <main>
    <SlideBar></SlideBar>
    <div class="shift">
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
          List Shift
        </div>
        <div class="card-content">
          <el-table :data="getCurrentPageData" border stripe>
            <el-table-column prop="id" label="ID"></el-table-column>
            <el-table-column prop="name" label="Shift name"></el-table-column>
            <el-table-column prop="TimeValidCheckIn" label="Time Valid Check In"></el-table-column>
            <el-table-column prop="TimeValidCheckOut" label="Time Valid Check Out"></el-table-column>
            <el-table-column prop="amount" label="Amount"></el-table-column>
            <el-table-column label="Edit shift">
              <template #default="scope">
                <el-button class="el-button--text"
                  @click="router.push({ path: `/admin/update-shift/${scope.row.id}` })">Edit</el-button>
              </template>
            </el-table-column>
            <el-table-column label="Delete Shift">
              <template #default="scope">
                <el-button class="el-button--text" @click="deleteShift(scope.row.id)">Delete</el-button>
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

.shift {
  width: 85vw;
  display: flex;
  justify-content: center;
  min-height: 85vh;
}

.el-card {
  min-width: 100%;
}

.el-form {
  display: flex;
  justify-content: space-between;
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
import { ref, onMounted, computed } from "vue";
import { useUserStore } from "../../../stores/user";
import axios from "axios";
import router from "../../../router";
import { useAlertStore } from "../../../stores/alert";
import { saveAs } from "file-saver";
import { utils, write } from "xlsx";
let data = ref();
let dataSearch = ref(null);
let currentPage = ref(1)
const pageSize = 10
const user = useUserStore().user;
const alertStore = useAlertStore();
onMounted(() => {
  if (user.role !== "admin") {
    router.push({ path: "/" });
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
const deleteShift = async (id) => {
  try {
    await axios.delete(`http://127.0.0.1:8000/api/delete-shift/${id}`, {
      headers: { Authorization: `Bearer ${user.token}` }
    }).then(function (response) {
      if (response.status == 200) {
        console.log(response)
        messages('success', response.data.message)
      }
    })
  } catch (e) {
    console.log(e)
    messages('error', e.data.message)
  }
  displayShift()
}
const filteredData = computed(() => {
  if (!dataSearch.value) {
    return data.value
  } else {
    let searchText = dataSearch.value.toLowerCase();
    currentPage.value = 1
    return data.value.filter((item) => {
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
const exportExcel = () => {
  const excelData = filteredData.value.map((item) => {
    return {
      id: item.id,
      name: item.name,
      TimeValidCheckIn: item.TimeValidCheckIn,
      TimeValidCheckOut: item.TimeValidCheckOut,
      amount: item.amount,
    };
  });
  const worksheet = utils.json_to_sheet(excelData);
  const workbook = utils.book_new();
  utils.book_append_sheet(workbook, worksheet, "ListShift");
  const excelBuffer = write(workbook, {
    bookType: "xlsx",
    type: "array",
  });
  const dataBlob = new Blob([excelBuffer], {
    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
  });
  saveAs(dataBlob, "list_shift.xlsx");
};

const exportCSV = () => {
  let csvContent = "data:text/csv;charset=utf-8,";
  const headers = [
    "id",
    "name",
    "TimeValidCheckIn",
    "TimeValidCheckOut",
    "amount"
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
  link.setAttribute("download", `list_shift.csv`);
  link.click();
};
const messages = (type, msg) => {
  alertStore.alert = true;
  alertStore.type = type;
  alertStore.msg = msg;
};
</script>
