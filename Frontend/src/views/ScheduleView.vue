<template>
  <div class="timekeeping-management">
    <EditTimeKeep
        :user_id="user_id"
        :date="today.date"
        :day-of-week="today.dayOfWeek"
        :checkin="today.timeCheckIn"
        :checkout="today.timeCheckOut"
        :status_AM_prop="today.status_AM"
        :status_PM_prop="today.status_PM"
        :status_change="today.adminAcceptStatus === 0"
        :time_change="today.adminAcceptTime === 0"
        @update="getListTimeKeeping(filter_value[0], filter_value[1])"
        @update_no_reset="updateNoReset"
        @nextday="index += 1"
        @prevday="index -= 1"></EditTimeKeep>
    <el-card>
      <el-backtop :right="20" :bottom="100"/>
      <div slot="header" class="card-header">
        Time Keeping
        <span style="position: absolute; right: calc(4vw + 36px)" v-if="admin_view">User: {{ admin_view }}</span>
      </div>
      <div class="card-content">
        <el-form>
          <div class="responsive-menu">
            <el-date-picker style="max-width: 250px" v-model="filter_value" type="daterange"
              start-placeholder="Start date" end-placeholder="End date" format="YYYY-MM-DD" value-format="YYYY-MM-DD" />
            <div class="export">
              <el-form-item>
                <el-button type="warning" @click="handleExportExcel">Export Excel</el-button>
                <el-button type="warning" @click="handleExportCSV">Export CSV</el-button>
              </el-form-item>
            </div>
          </div>
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
          <el-checkbox v-model="only_show" label="Only show checkin days" size="large"/>
        </div>
        <el-table  :row-style="rowStyle" :cell-style="cellStyle" size="small" style="font-size: 14px"
                  :data="only_show ? dataDisplay : dataByMonth" border>
          <el-table-column prop="dayOfWeek" label="Day of week" width="160">
            <template #default="scope">
              <el-button
                  style="color: #606266;
                  font-weight: normal"
                  class="el-button--text"
                  @click="editTime(scope.$index)"
              >
                {{ scope.row.dayOfWeek }}&thinsp;
              </el-button>
              <span v-if="scope.row.statusRequest === 0"
                    style="color: red">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-exclamation-circle" viewBox="0 0 16 16">
                  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                  <path
                      d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                </svg>
              </span>
              <span v-else-if="scope.row.statusRequest === 1" style="color: #f3952d">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock"
                     viewBox="0 0 16 16">
                  <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                  <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                </svg>
              </span>
              <span v-else-if="scope.row.statusRequest === 2" style="color: #1cb966">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-check-circle" viewBox="0 0 16 16">
                  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                  <path
                      d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                </svg>
              </span>
            </template>
          </el-table-column>
          <el-table-column prop="date" label="Date" width="100"></el-table-column>
          <el-table-column label="Status" width="120"></el-table-column>
          <el-table-column prop="timeCheckIn" label="Checkin" min-width="110"></el-table-column>
          <el-table-column prop="timeCheckOut" label="Checkout" min-width="110"></el-table-column>
          <el-table-column prop="timeWork" label="Working time" min-width="110"></el-table-column>
          <el-table-column prop="shift" label="Shift" width="80"></el-table-column>
          <el-table-column label="Operations" width="100">
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
  display: flex;
  justify-content: space-around;
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

.responsive-menu {
  width: 100%;
  display: flex;
  justify-content: space-around;
  align-items: center;
}

@media screen and (max-width: 1440px) {
  .edit-timekeeping {
    display: none;
  }

  .el-card {
    width: 90vw;
  }
}

@media screen and (max-width: 608px) {
  .el-form {
    display: flex;
    justify-content: center;
  }

  .el-form .responsive-menu {
    width: auto;
    display: block;
  }

  .export {
    margin-top: 20px;
    display: flex;
    justify-content: center;
  }
}
</style>

<script setup>
import EditTimeKeep from "../components/EditTimeKeep.vue"
import ExportData from "../components/ExportData.vue"
import {saveAs} from "file-saver";
import {read, utils, write} from "xlsx";
import {ref, reactive, onMounted, defineProps, computed, watchEffect, watch} from "vue";
import {useUserStore} from "../stores/user";
import moment from "moment";
import router from "../router";
import TimeKeepAPI from "../services/TimeKeepAPI";
const props = defineProps({
  notification : Object
})
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
  adminAcceptStatus: null,
  adminAcceptTime: null,
})
const first_load = ref(true)
const admin_view = ref('')
const user_id = ref(user.id)
const visibleMode = ref(false)
const operationMode = ref('Excel')
const status_change = ref(false)
const time_change = ref(false)
const index = ref(0)
const check = ref('')
const getListTimeKeeping = async (from, to) => {
  if (router.currentRoute.value.fullPath === '/schedule') {
    try {
      await TimeKeepAPI.getListTimeKeep(from, to, user.token)
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
      await TimeKeepAPI.getListTimeKeepOfUser(from, to, user.token, userID)
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
watch(props, () => {
  if(props.notification.type == 0){
    getListTimeKeeping(filter_value.value[0], filter_value.value[1])
  }
})
watch(() => filter_value.value,  () => {
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
    if(start.format('YYYY-MM-DD') === moment().format('YYYY-MM-DD')){
      index.value = result.length
    }
    let check = data.value.find((element) => {
      return element.date === start.format('YYYY-MM-DD')
    })
    if (check) {
      check.statusRequest = checkStatusRequest(check)
      result.push(check)
    } else {
      if (start.format('dddd') === 'Sunday' || start.format('dddd') === "Saturday") {
        result.push({
          date: start.format('YYYY-MM-DD'),
          dayOfWeek: start.format('dddd'),
          status_AM: 2,
          status_PM: 2,
        })
      } else {
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
    } else {
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

function checkStatusRequest(check) {
  let status = check.adminAcceptStatus
  let time = check.adminAcceptTime

  if(status == null && time == null){
    return 'Nothing'
  } else if(status == null){
    return time
  } else if(time == null) {
    return status
  }
  return Math.min(status, time)
}

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

function rowStyle({row}) {
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
    'background': 'linear-gradient(to right, ' + color[status_AM] + ' 49%, rgba(0, 0, 0, 0) 49%), linear-gradient(to right, white 50%, ' + color[status_PM] + ' 50%)',
    'padding': '16px 30px',
    'background-clip': 'content-box',
  }
}

function cellStyle({rowIndex, columnIndex}) {
  if (!only_show.value && columnIndex === 2) {
    return custom_status_css(dataByMonth.value[rowIndex].status_AM, dataByMonth.value[rowIndex].status_PM)
  } else if (columnIndex === 2) {
    return custom_status_css(dataDisplay.value[rowIndex].status_AM, dataDisplay.value[rowIndex].status_PM)
  }

}

watch(() => index.value, () => {
  if (only_show.value === false) {
    if (dataByMonth.value[index.value].dayOfWeek === 'Saturday') {
      index.value += 2
    } else if (dataByMonth.value[index.value].dayOfWeek === 'Sunday'){
      index.value -= 2
    }
  }
  editTime(index.value)
})

function editTime(id) {
  index.value = id
  if (only_show.value === true) {
    if (!['Saturday', 'Sunday'].includes(dataDisplay.value[id].dayOfWeek)) {
      today.value = dataDisplay.value[id]
    }
  } else {
    if (!['Saturday', 'Sunday'].includes(dataByMonth.value[id].dayOfWeek)) {
      today.value = dataByMonth.value[id]
    }
  }
}

async function updateNoReset(time_keeping){
  await getListTimeKeeping(filter_value.value[0], filter_value.value[1]);
  let check = data.value.find((element) => {
    return element.date === time_keeping
  })
  today.value = check
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
