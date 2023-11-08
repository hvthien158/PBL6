<template>
  <main>
    <SlideBar></SlideBar>
    <div class="shift">
      <el-card>
        <h2 style="font-size: 32px; font-weight: 700; text-align: center; ">Shift management</h2>
        <div class="title-table">
        <div>
          <el-button type="warning" @click="handleCreate">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
            </svg>
            New
          </el-button>
        </div>
        <div>
          <el-input v-model="dataSearch" placeholder="Type to search" />
        </div>
      </div>
        <div class="card-content">
          <el-table :data="getCurrentPageData" height="50vh" border stripe>
            <el-table-column prop="id" label="ID" min-width="50"></el-table-column>
            <el-table-column prop="name" label="Shift name" min-width="180"></el-table-column>
            <el-table-column prop="TimeValidCheckIn" label="Time Valid Check In" min-width="300"></el-table-column>
            <el-table-column prop="TimeValidCheckOut" label="Time Valid Check Out" min-width="300"></el-table-column>
            <el-table-column prop="amount" min-width="100" label="Amount"></el-table-column>
            <el-table-column fixed="right" label="Operations" width="140">
              <template #default="scope">
                <el-button link type="primary" @click="router.push({ path: `/admin/update-shift/${scope.row.id}` })">Edit</el-button>
                <el-button link type="danger" @click="deleteShift(scope.row.id)">Delete</el-button>
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
.title-table{
  width: 100%;
  display: flex;
  justify-content: space-between;
  margin: 10px 0 10px 0
}
.title-table div{
  display: flex;
  align-items: end;
  justify-content: center;
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
