<template>
  <div class="timekeeping-management">
    <EditTimeKeep class="edit-timekeeping" style="height: 100%" :user_id="user_id" :date="today.date"
      :day-of-week="today.dayOfWeek" :checkin="today.timeCheckIn" :checkout="today.timeCheckOut"
      :status_AM_prop="today.status_AM" :status_PM_prop="today.status_PM"
      @update="getListTimeKeeping(filter_value[0], filter_value[1])"></EditTimeKeep>
    <el-card>
      <el-backtop :right="20" :bottom="100" />
      <div slot="header" class="card-header">
        Time Keeping
        <span style="position: absolute; right: calc(4vw + 36px)" v-if="admin_view">User: {{ admin_view }}</span>
      </div>
      <div class="card-content">
        <el-form inline>
          <el-date-picker style="margin: 40px 30px 0 15%" v-model="filter_value" type="daterange"
            start-placeholder="Start date" end-placeholder="End date" format="YYYY-MM-DD" value-format="YYYY-MM-DD" />
          <el-form-item>
            <el-button type="warning" @click="handleExportExcel">Export Excel</el-button>
            <el-button type="warning" @click="handleExportCSV">Export CSV</el-button>
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
        <el-table :row-style="rowStyle" :cell-style="cellStyle" size="small" style="font-size: 14px"
          :data="only_show ? dataDisplay : dataByMonth" border>
          <el-table-column prop="dayOfWeek" label="Day of week" width="160"></el-table-column>
          <el-table-column prop="date" label="Date" width="100"></el-table-column>
          <el-table-column label="Status" width="120"></el-table-column>
          <el-table-column prop="timeCheckIn" label="Checkin" min-width="110"></el-table-column>
          <el-table-column prop="timeCheckOut" label="Checkout" min-width="110"></el-table-column>
          <el-table-column prop="timeWork" label="Working time" min-width="110"></el-table-column>
          <el-table-column prop="shift" label="Shift" width="80"></el-table-column>
          <el-table-column label="Request" width="100">
            <template #default="scope">
              <el-button class="el-button--text" @click="editTime(scope.$index)">Edit</el-button>
            </template>
          </el-table-column>
        </el-table>
        <div class="form-export">
          <ExportData @invisible="visibleMode = false" :mode="operationMode" :userId="user_id" v-if="visibleMode">
          </ExportData>
        </div>
        <div class="pagination">
          <el-button type="info" @click="previousMonth">
            Prev
          </el-button>
          <span>{{ monthDisplay + 1 }}/{{ yearDisplay }}</span>
          <el-button type="info" @click="nextMonth">
            Next
          </el-button>
        </div>
      </div>
    </el-card>
  </div>
</template>

<style scoped>
.timekeeping-management {
  padding: 20px 4vw;
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

.form-export {
  position: absolute;
  bottom: 7%;
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

.el-table {
  --el-table-row-hover-bg-color: none;
}

.pagination span {
  margin: 0 10px;
}

@media screen and (max-width: 1440px) {
  .edit-timekeeping {
    display: none;
  }

  .el-card {
    width: 90vw;
  }
}
</style>

<script setup>
import EditTimeKeep from "../components/EditTimeKeep.vue"
import ExportData from "../components/ExportData.vue"
import { saveAs } from "file-saver";
import { read, utils, write } from "xlsx";
import { ref, reactive, onMounted, defineProps, computed, watchEffect, watch } from "vue";
import { useUserStore } from "../stores/user";
import axios from "axios";
import moment from "moment";
import router from "../router";

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
const admin_view = ref('')
const user_id = ref(user.id)
const visibleMode = ref(false)
const operationMode = ref('Excel')
const getListTimeKeeping = async (from, to) => {
  if (router.currentRoute.value.fullPath === '/schedule') {
    try {
      await axios
        .get("http://127.0.0.1:8000/api/get-list-timekeeping/" + from + '/' + to, {
          headers: {
            Authorization: `Bearer ${user.token}`,
          },
        })
        .then(function (response) {
          data.value = response.data.data;
          if (first_load.value) {
            loadToday()
            first_load.value = false
          }
        });
      dataSearch.startDate = null
      dataSearch.endDate = null
    } catch (e) {
      console.log(e);
    }
  } else {
    let userID = router.currentRoute.value.params.userID
    user_id.value = Number(userID)
    try {
      await axios
        .get("http://127.0.0.1:8000/api/get-list-timekeeping/" + from + '/' + to + '/' + userID, {
          headers: {
            Authorization: `Bearer ${user.token}`,
          },
        })
        .then(function (response) {
          data.value = response.data.data;
          admin_view.value = data.value[0].user
          if (first_load.value) {
            loadToday()
            first_load.value = false
          }
        });
      dataSearch.startDate = null
      dataSearch.endDate = null
    } catch (e) {
      console.log(e);
    }
  }
};

watch(() => filter_value.value, () => {
  getListTimeKeeping(filter_value.value[0], filter_value.value[1])
})

filterByMonth()
const dataByMonth = computed(() => {
  let result = []

  let from = {
    year: filter_value.value[0].slice(0, 4),
    month: filter_value.value[0].slice(5, 7),
    day: filter_value.value[0].slice(8, 10),
  }
  let to = {
    year: filter_value.value[1].slice(0, 4),
    month: filter_value.value[1].slice(5, 7),
    day: filter_value.value[1].slice(8, 10),
  }
  let start = moment('' + from.year + from.month + from.day)
  let end = moment('' + to.year + to.month + to.day)
  let limit = 100

  do {
    let check = data.value.find((element) => {
      return element.date === start.format('YYYY-MM-DD')
    })
    if (check) {
      result.push(check)
    } else {
      if (start.format('dddd') === 'Sunday' || start.format('dddd') === "Saturday") {
        result.push({
          date: start.format('YYYY-MM-DD'),
          dayOfWeek: start.format('dddd'),
          status_AM: 2,
          status_PM: 2,
        })
      }
      else {
        result.push({
          date: start.format('YYYY-MM-DD'),
          dayOfWeek: start.format('dddd'),
          status_AM: 0,
          status_PM: 0,
        })
      }
    }
    start = start.add(1, 'days')
    limit -= 1
  } while (start.format('L') !== end.format('L') && limit !== 0)
  //add last day
  let check = data.value.find((element) => {
    return element.date === start.format('YYYY-MM-DD')
  })
  if (check) {
    result.push(check)
  } else {
    if (start.format('dddd') === 'Sunday' || start.format('dddd') === "Saturday") {
      result.push({
        date: start.format('YYYY-MM-DD'),
        dayOfWeek: start.format('dddd'),
        status_AM: 2,
        status_PM: 2,
      })
    }
    else {
      result.push({
        date: start.format('YYYY-MM-DD'),
        dayOfWeek: start.format('dddd'),
        status_AM: 0,
        status_PM: 0,
      })
    }
  }

  return result
})

function filterByMonth() {
  filter_value.value = [
    yearDisplay.value + '-' + (monthDisplay.value + 1).toString().padStart(2, '0') + '-' + '01',
    yearDisplay.value + '-' + (monthDisplay.value + 1).toString().padStart(2, '0') + '-' + getDaysInMonth(monthDisplay.value + 1, yearDisplay.value)
  ]
}

function getDaysInMonth(month, year) {
  return moment(year + '-' + month.toString().padStart(2, '0')).daysInMonth()
}

watchEffect(() => {
  if (only_show.value === true) {
    dataDisplay.value = dataByMonth.value.filter((data) => {
      return data.timeCheckIn
    })
  } else {
    dataDisplay.value = []
  }
})

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

function loadToday() {
  let check = data.value.find((element) => {
    return element.date === moment().format('YYYY-MM-DD')
  })
  if (check) {
    today.value = check
    today.value.dayOfWeek = moment().format('dddd') + ' (Today)'
  } else {
    today.value.date = moment().format('YYYY-MM-DD')
    today.value.dayOfWeek = moment().format('dddd') + ' (Today)'
  }
}

function rowStyle({ row }) {
  if (row.date === moment().format('YYYY-MM-DD')) {
    return {
      'background-color': 'rgba(255,153,41,0.37)'
    }
  }
  if (row.dayOfWeek === 'Sunday' || row.dayOfWeek === 'Saturday') {
    return {
      'background-color': 'rgba(204,204,204,0.42)',
    }
  }
  if (row.date === today.value.date) {
    return {
      'background-color': 'rgba(41,255,248,0.37)'
    }
  }
}
const custom_status_css = function (status_AM, status_PM) {
  const color = ['#04fc43', 'rgba(0,120,248,0.57)', '#ccc']
  return {
    'background': 'linear-gradient(to right, ' + color[status_AM] + ' 49%, rgba(0, 0, 0, 0) 49%), linear-gradient(to right, white 50%, ' + color[status_PM] + ' 1%)',
    'padding': '16px 30px',
    'background-clip': 'content-box',
  }
}
function cellStyle({ rowIndex, columnIndex }) {
  if (!only_show.value && columnIndex === 2) {
    return custom_status_css(dataByMonth.value[rowIndex].status_AM, dataByMonth.value[rowIndex].status_PM)
  } else if (columnIndex === 2) {
    return custom_status_css(dataDisplay.value[rowIndex].status_AM, dataDisplay.value[rowIndex].status_PM)
  }

}

function editTime(index) {
  if (only_show.value === true) {
    today.value = dataDisplay.value[index]
  } else {
    today.value = dataByMonth.value[index]
  }
}
const handleExportExcel = () => {
  visibleMode.value = true
  operationMode.value = 'Excel'
  user_id.value = Number(router.currentRoute.value.params.userID)
}
const handleExportCSV = () => {
  visibleMode.value = true
  operationMode.value = 'CSV'
  user_id.value = Number(router.currentRoute.value.params.userID)
}
</script>
