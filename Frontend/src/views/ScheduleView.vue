<template>
  <main>
    <div class="container">
      <div class="list-timekeeping">
        <form @submit.prevent="getListTimeKeeping" method="get">
          <div class="form-row align-items-center">
            <div class="col-sm-2 my-1">
              <input
                type="date"
                class="form-control"
                v-model="dataSearch.startTime"
              />
            </div>
            Đến
            <div class="col-sm-2 my-1">
              <input
                type="date"
                class="form-control"
                v-model="dataSearch.endTime"
              />
            </div>
            <div class="col-sm-2 my-1">
              <select
                class="custom-select"
                v-model="dataSearch.month"
                placeholder="Tìm Theo tháng"
              >
                <option v-for="options in month" :value="options.value">
                  {{ options.text }}
                </option>
              </select>
            </div>
            <div class="col-sm-2 my-1">
              <select
                class="custom-select"
                v-model="dataSearch.year"
                placeholder="Tìm Theo Năm"
              >
                <option v-for="options in years" :value="options.value">
                  {{ options.text }}
                </option>
              </select>
            </div>
            <div class="col-auto my-1">
              <button
                type="submit"
                class="btn btn-primary"
                @click="findTimeKeeping"
              >
                Tìm kiếm
              </button>
            </div>
            <div class="col-auto my-1">
              <button
                type="submit"
                class="btn btn-primary"
                @click="getListTimeKeeping"
              >
                Xem tất cả
              </button>
            </div>
            <div class="col-auto my-1">
              <button
                type="button"
                class="btn btn-primary"
                @click="exportExcel"
              >
                Xuất Excel
              </button>
            </div>
            <div class="col-auto my-1">
              <button type="button" class="btn btn-primary" @click="exportCSV">
                Xuất CSV
              </button>
            </div>
          </div>
        </form>
        <div class="table-responsive-md">
          <table class="table">
            <thead class="thead-dark">
              <tr>
                <th scope="col">Ngày</th>
                <th scope="col">Giờ Check-In</th>
                <th scope="col">Giờ Check-Out</th>
                <th scope="col">Ca Làm</th>
                <th scope="col">Số công</th>
                <th scope="col">Yêu cầu</th>
              </tr>
            </thead>
            <tbody v-for="item in data" :key="item.id">
              <tr>
                <td scope="row">{{ item.date }}</td>
                <td>{{ item.timeCheckIn }}</td>
                <td>{{ item.timeCheckOut }}</td>
                <td>{{ item.shift.name }}</td>
                <td>{{ item.shift.amount }}</td>
                <th scope="col">
                  <button type="button" class="btn btn-primary">Gửi</button>
                </th>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped>
@import "https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css";
main {
  max-width: 100vw;
  min-height: 80vh;
  display: flex;
  justify-content: center;
  border: 0.1em solid black;
}
.container {
  display: flex;
  justify-content: center;
  width: 100%;
  max-width: 100%;
}
table {
  width: 80vw;
  border: 0.1em solid black;
}
.table td {
  border-color: black;
}
.table th {
  border-color: black;
}
</style>

<script setup>
import { saveAs } from "file-saver";
import { read, utils, write } from "xlsx";
import { ref, reactive, onMounted } from "vue";
import {useUserStore} from "../stores/user";
import axios from "axios";
const user = useUserStore().user
let dataSearch = reactive({
  startTime: null,
  endTime: null,
  month: null,
  year: null,
});
let data = ref();
const month = [
  { value: null, text: "Tìm theo tháng" },
  { value: 1, text: "Tháng 1" },
  { value: 2, text: "Tháng 2" },
  { value: 3, text: "Tháng 3" },
  { value: 4, text: "Tháng 4" },
  { value: 5, text: "Tháng 5" },
  { value: 6, text: "Tháng 6" },
  { value: 7, text: "Tháng 7" },
  { value: 8, text: "Tháng 8" },
  { value: 9, text: "Tháng 9" },
  { value: 10, text: "Tháng 10" },
  { value: 11, text: "Tháng 11" },
  { value: 12, text: "Tháng 12" },
];
const currentYear = new Date().getFullYear();
const years = [];

for (let year = currentYear; year >= 2020; year--) {
  years.push({ value: year, text: "Năm " + year });
}

onMounted(() => {
  getListTimeKeeping();
});
const getListTimeKeeping = async () => {
  await axios
    .get("http://127.0.0.1:8000/api/get-list-timekeeping", {
      headers: {
        Authorization: `Bearer ${user.token}`
      },
    })
    .then(function (response) {
      data.value = response.data.data;
      console.log(data.value);
    });
};
const findTimeKeeping = async () => {
  if (dataSearch.startTime && dataSearch.endTime) {
    try {
      await axios
        .post(
          "http://127.0.0.1:8000/api/search-by-around-time",
          {
            startTime: dataSearch.startTime,
            endTime: dataSearch.endTime,
          },
          {
            headers: {
              Authorization: `Bearer ${user.token}`
            },
          }
        )
        .then(function (response) {
          data.value = response.data.data;
          console.log(data.value);
        });
    } catch (e) {
      console.log(e);
    }
  } else if (dataSearch.year) {
    try {
      await axios
        .post(
          "http://127.0.0.1:8000/api/search-by-month-year",
          {
            month: dataSearch.month,
            year: dataSearch.year,
          },
          {
            headers: {
              Authorization: `Bearer ${user.token}`
            },
          }
        )
        .then(function (response) {
          data.value = response.data.data;
          console.log(data.value);
        });
    } catch (e) {
      console.log(e);
    }
  } else {
    alert("Vui lòng điền thông tin trước khi search");
  }
};
const exportExcel = () => {
  const excelData = data.value.map(item => {
    return {
      id: item.id,
      date: item.date,
      timeCheckIn: item.timeCheckIn,
      timeCheckOut: item.timeCheckOut,
      ShiftName: item.shift.name,
      ShiftAmount: item.shift.amount
    };
  });
  const worksheet = utils.json_to_sheet(excelData);
  const workbook = utils.book_new();
  utils.book_append_sheet(workbook, worksheet, "Timekeeping");
  const excelBuffer = write(workbook, {
    bookType: "xlsx",
    type: "array",
  });
  const dataBlob = new Blob([excelBuffer], {
    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
  });
  saveAs(dataBlob, "timekeeping.xlsx");
};

const exportCSV = () => {
  let csvContent = "data:text/csv;charset=utf-8,";
  const headers = ['id', 'date', 'timeCheckIn', 'timeCheckOut', 'Shiftname', 'Shiftamount'];
  csvContent += headers.join(",") + "\n";
  data.value.forEach((item) => {
    const row = headers.map((header) => {
      if (header.includes("Shift")) {
        const shiftField = header.replace('Shift', '');
        return item.shift[shiftField];
      } else {
        return item[header];
      }
    }).join(",");
    csvContent += row + "\n";
  });

  const encodedUri = encodeURI(csvContent);
  const link = document.createElement("a");
  link.setAttribute("href", encodedUri);
  link.setAttribute("download", "timekeeping.csv");
  link.click();
};
</script>