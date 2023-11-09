<template>
  <div class="timekeeping-management">
    <EditTimeKeep
        style="height: 100%"
        :date="today.date"
        :day-of-week="today.dayOfWeek"
        :checkin="today.timeCheckIn"
        :checkout="today.timeCheckOut"
        @update="getListTimeKeeping"
    ></EditTimeKeep>
    <el-card>
      <div slot="header" class="card-header">
        Time Keeping
      </div>
      <div class="card-content">
        <el-form inline>
          <el-date-picker
              style="margin: 40px 30px 0 25%"
              v-model="filter_value"
              type="daterange"
              start-placeholder="Start date"
              end-placeholder="End date"
              format="DD/MM/YYYY"
              value-format="DD/MM/YYYY"
          />
          <el-form-item>
            <el-button type="warning" @click="exportExcel">Export Excel</el-button>
            <el-button type="warning" @click="exportCSV">Export CSV</el-button>
          </el-form-item>
        </el-form>
        <div>
          <div class="pagination">
            <el-button type="info" @click="previousMonth">
              Prev
            </el-button>
            <span>{{ monthDisplay + 1 }}/{{ yearDisplay }}</span>
            <el-button type="info" @click="nextMonth">
              Next
            </el-button>
          </div>
          <el-checkbox v-model="only_show" label="Only show checkin days" size="large" />
        </div>
        <el-table :data="only_show ? dataDisplay : dataByMonth" border stripe>
          <el-table-column prop="dayOfWeek" label="Day of week"></el-table-column>
          <el-table-column prop="date" label="Date"></el-table-column>
          <el-table-column prop="timeCheckIn" label="Checkin"></el-table-column>
          <el-table-column prop="timeCheckOut" label="Checkout"></el-table-column>
          <el-table-column prop="timeWork" label="Working time"></el-table-column>
          <el-table-column prop="shift.name" label="Shift"></el-table-column>
          <el-table-column label="Request">
            <template #default="scope">
              <el-button class="el-button--text" @click="editTime(scope.$index)">Edit</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </el-card>
  </div>
</template>

<style scoped>
.timekeeping-management {
  padding: 20px;
  display: flex;
  justify-content: center;
}

.el-form {
  margin-bottom: 20px;
}

.card-header {
  font-size: 18px;
  font-weight: bold;
}

.el-card {
  width: 70vw;
}

.card-content {
  margin-top: 20px;
}

.export {
  margin-top: 20px;
  margin-bottom: 20px;
  display: flex;
  justify-content: flex-end;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 10px 0;
}

.el-form-item {
  margin-bottom: 0;
}

.pagination span {
  margin: 0 10px;
}
</style>

<script setup>
import EditTimeKeep from "../components/EditTimeKeep.vue"
import { saveAs } from "file-saver";
import { read, utils, write } from "xlsx";
import {ref, reactive, onMounted, defineProps, computed, watchEffect} from "vue";
import { useUserStore } from "../stores/user";
import axios from "axios";
import moment from "moment";

const user = useUserStore().user;
let dataSearch = reactive({
  startDate: null,
  endDate: null,
});
let data = ref([])
const dataDisplay = ref([])
const monthDisplay = ref(moment().month())
const yearDisplay = ref(moment().year())
const only_show = ref(false)
const filter_value = ref([])
const today = ref({
  dayOfWeek: '',
  date: '',
  timeCheckIn: '',
  timeCheckOut: '',
})
const first_load = ref(true)

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
        if(first_load.value){
          loadToday()
          first_load.value = false
        }
      });
    dataSearch.startDate = null
    dataSearch.endDate = null
  } catch (e) {
    console.log(e);
  }
};

filterByMonth()
const dataByMonth = computed(() => {
  let result = []

  let from = {
    day: filter_value.value[0].slice(0, 2),
    month: filter_value.value[0].slice(3, 5),
    year: filter_value.value[0].slice(6, 10),
  }
  let to = {
    day: filter_value.value[1].slice(0, 2),
    month: filter_value.value[1].slice(3, 5),
    year: filter_value.value[1].slice(6, 10),
  }
  let start = moment('' + from.year + from.month + from.day)
  let end = moment('' + to.year + to.month + to.day)
  let limit = 100

  do {
    let check = data.value.find((element) => {
      return element.date === start.format('DD') + '/' + (start.month() + 1).toString().padStart(2, '0') + '/' + start.year()
    })
    if(check){
      result.push(check)
    } else {
      result.push({
        date: start.format('DD') + '/' + (start.month() + 1).toString().padStart(2, '0') + '/' + start.year(),
        dayOfWeek: start.format('dddd')
      })
    }
    start = start.add(1, 'days')
    limit -= 1
  } while (start.format('L') !== end.format('L') && limit !== 0)
  //add last day
  let check = data.value.find((element) => {
    return element.date === start.format('DD') + '/' + (start.month() + 1).toString().padStart(2, '0') + '/' + start.year()
  })
  if(check){
    result.push(check)
  } else {
    result.push({
      date: start.format('DD') + '/' + (start.month() + 1).toString().padStart(2, '0') + '/' + start.year(),
      dayOfWeek: start.format('dddd')
    })
  }

  return result
})

function filterByMonth(){
  filter_value.value = [
    '01/' + (monthDisplay.value + 1).toString().padStart(2, '0') + '/' + yearDisplay.value,
    getDaysInMonth(monthDisplay.value + 1, yearDisplay.value) + '/' + (monthDisplay.value + 1).toString().padStart(2, '0') + '/' + yearDisplay.value
  ]
}

function getDaysInMonth(month, year){
  return moment(year + '-' + month.toString().padStart(2, '0')).daysInMonth()
}

watchEffect(() => {
  if(only_show.value === true){
    dataDisplay.value = dataByMonth.value.filter((data) => {
      return data.timeCheckIn
    })
  } else {
    dataDisplay.value = []
  }
})

function nextMonth(){
  if(monthDisplay.value === 11){
    monthDisplay.value = 0
    yearDisplay.value += 1
  } else {
    monthDisplay.value += 1
  }
  filterByMonth()
}
function previousMonth(){
  if(monthDisplay.value === 0){
    monthDisplay.value = 11
    yearDisplay.value -= 1
  } else {
    monthDisplay.value -= 1
  }
  filterByMonth()
}

function loadToday(){
  let check = data.value.find((element) => {
    return element.date === moment().format('DD') + '/' + (moment().month() + 1).toString().padStart(2, '0') + '/' + moment().year()
  })
  if(check){
    today.value = check
  } else {
    today.value.date = moment().format('DD') + '/' + (moment().month() + 1).toString().padStart(2, '0') + '/' + moment().year()
    today.value.dayOfWeek = moment().format('dddd')
  }
}

function editTime(index){
  if(only_show.value === true){
    today.value = dataDisplay.value[index]
  } else {
    today.value = dataByMonth.value[index]
  }
}

const formatDate = (date) => {
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  return `${year}-${month}-${day}`;
};

const filteredData = computed(() => {
  if (dataSearch.startDate && dataSearch.endDate) {
    const startDate = formatDate(dataSearch.startDate)
    const endDate = formatDate(dataSearch.endDate)
    return data.value.filter((item) => {
      const datePart = item.date.split("-");
      const itemDay = datePart[0]
      const itemMonth = datePart[1]
      const itemYear = datePart[2]
      const itemDate = `${itemYear}-${itemMonth}-${itemDay}`;
      return (itemDate >= startDate && itemDate <= endDate)
    })
  }
  else {
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
      timeWork: item.timeWork,
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
    "timeWork",
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
  if (!(dataSearch.startDate || dataSearch.endDate)) {
    link.setAttribute("download", `${user.name}.csv`);
  } else {
    link.setAttribute("download", `${user.name}_${dataSearch.startDate}_${dataSearch.endDate}.csv`);
  }
  link.click();
};
</script>
