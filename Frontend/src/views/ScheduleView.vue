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
            To
            <div class="col-sm-2 my-1">
              <input
                type="date"
                class="form-control"
                v-model="dataSearch.endTime"
              />
            </div>
            <div class="col-sm-2 my-1">
              <select class="custom-select" v-model="dataSearch.month">
                <option v-for="options in month" :value="options.value">
                  {{ options.text }}
                </option>
              </select>
            </div>
            <div class="col-sm-2 my-1">
              <select class="custom-select" v-model="dataSearch.year">
                <option v-for="options in years" :value="options.value">
                  {{ options.text }}
                </option>
              </select>
            </div>
            <div class="col-auto my-1">
              <button
                type="submit"
                class="btn btn-primary"
                @click="getListTimeKeeping"
              >
                View all
              </button>
            </div>
            <div class="col-auto my-1">
              <button
                type="button"
                class="btn btn-primary"
                @click="exportExcel"
              >
                Export Excel
              </button>
            </div>
            <div class="col-auto my-1">
              <button type="button" class="btn btn-primary" @click="exportCSV">
                Export CSV
              </button>
            </div>
          </div>
        </form>
        <div class="table-responsive-md">
          <table class="table">
            <thead style="background-color: #ef9400">
              <tr>
                <th scope="col">Date</th>
                <th scope="col">Check-In</th>
                <th scope="col">Check-Out</th>
                <th scope="col">Shift</th>
                <th scope="col">Request</th>
              </tr>
            </thead>
            <tbody v-for="item in filteredData" :key="item.id">
              <tr>
                <td scope="row">{{ item.date }}</td>
                <td>{{ item.timeCheckIn }}</td>
                <td>{{ item.timeCheckOut }}</td>
                <td>{{ item.shift.name }}</td>
                <th scope="col">
                  <button type="button" class="btn btn-primary">Send</button>
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
  padding-top: 12px;
}
.container {
  display: flex;
  justify-content: center;
  width: 100%;
  max-width: 100%;
}
table {
  background-color: white;
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
import { ref, reactive, onMounted, defineProps, computed } from "vue";
import { useUserStore } from "../stores/user";
import axios from "axios";

const user = useUserStore().user;
let dataSearch = reactive({
  startTime: null,
  endTime: null,
  month: null,
  year: null,
});
let data = ref();
const month = [
  { value: null, text: "Search by month" },
  { value: 1, text: "January" },
  { value: 2, text: "February" },
  { value: 3, text: "March" },
  { value: 4, text: "April" },
  { value: 5, text: "May" },
  { value: 6, text: "June" },
  { value: 7, text: "July" },
  { value: 8, text: "August" },
  { value: 9, text: "September" },
  { value: 10, text: "October" },
  { value: 11, text: "November" },
  { value: 12, text: "December" },
];
const currentYear = new Date().getFullYear();
const years = [{ value: null, text: "Search by year" }];

for (let year = currentYear; year >= 2020; year--) {
  years.push({ value: year, text: year });
}
onMounted(() => {
  getListTimeKeeping();
});
const getListTimeKeeping = async () => {
  try {
    await axios
      .get("http://127.0.0.1:8000/api/get-list-timekeeping", {
        headers: {
          Authorization: `Bearer ${user.token}`,
        },
      })
      .then(function (response) {
        data.value = response.data.data;
        console.log(data.value);
      });
      dataSearch.startTime = null
      dataSearch.endTime = null
      dataSearch.month = null
      dataSearch.year = null
  } catch (e) {
    console.log(e);
  }
};
const filteredData = computed(() => {
  if (dataSearch.startTime && dataSearch.endTime) {
    return data.value.filter((item) => {
      const dateParts = item.date.split("-");

      const day = dateParts[0];
      const month = dateParts[1];
      const year = dateParts[2];

      const outputDateString = `${year}-${month}-${day}`;
      console.log(outputDateString >= dataSearch.startTime)
      console.log(outputDateString <= dataSearch.endTime)
      return (
        outputDateString >= dataSearch.startTime && outputDateString <= dataSearch.endTime
      );
    });
  } else if (dataSearch.year && dataSearch.month) {
    return data.value.filter((item) => {
      const dateParts = item.date.split("-");
      const month = parseInt(dateParts[1], 10);
      const year = parseInt(dateParts[2], 10);
      return year === dataSearch.year && month === dataSearch.month;
    });
  } else if (dataSearch.year) {
    return data.value.filter((item) => {
      const dateParts = item.date.split("-");
      const year = parseInt(dateParts[2], 10);
      return year === dataSearch.year;
    });
  } else {
    return data.value;
  }
});
const exportExcel = () => {
  const excelData = filteredData.value.map((item) => {
    return {
      id: item.id,
      date: item.date,
      timeCheckIn: item.timeCheckIn,
      timeCheckOut: item.timeCheckOut,
      ShiftName: item.shift.name,
      ShiftAmount: item.shift.amount,
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
  const headers = [
    "id",
    "date",
    "timeCheckIn",
    "timeCheckOut",
    "Shiftname",
    "Shiftamount",
  ];
  csvContent += headers.join(",") + "\n";
  filteredData.value.forEach((item) => {
    const row = headers
      .map((header) => {
        if (header.includes("Shift")) {
          const shiftField = header.replace("Shift", "");
          return item.shift[shiftField];
        } else {
          return item[header];
        }
      })
      .join(",");
    csvContent += row + "\n";
  });

  const encodedUri = encodeURI(csvContent);
  const link = document.createElement("a");
  link.setAttribute("href", encodedUri);
  link.setAttribute("download", "timekeeping.csv");
  link.click();
};
</script>
