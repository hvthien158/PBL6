<template>
  <div>
    <div class="edit-timekeep">
      <el-card style="border-radius: 12px;" class="box-card" :class="{ 'bg-orange': today, 'bg-gray': weekend }">
        <div class="card-content">
          <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <el-button text @click="$emit('prevday')">&lt;&lt;Previous day</el-button>
            <el-button text @click="$emit('nextday')">Next day>></el-button>
          </div>
          <h1>{{ prop.dayOfWeek }}</h1>
          <p>{{ prop.date }}</p>
          <div style="display: flex">
            <div class="info-status">
              <span style="color: #939393">Morning</span>
              <StatusButton @change-status="n => status_AM = n" :status_index="status_AM"></StatusButton>
            </div>
            <div class="info-status">
              <span style="color: #939393">Afternoon</span>
              <StatusButton @change-status="n => status_PM = n" :status_index="status_PM"></StatusButton>
            </div>
          </div>
          <p>Check-in: <span style="color: #888">{{ check_in_system }}</span></p>
          <div>
            <el-time-select v-model="check_in_format" start="08:30" step="00:15" end="18:30" placeholder="(none)"/>
          </div>
          <p style="margin-top: 20px">Check-out: <span style="color: #888">{{ check_out_system }}</span></p>
          <div>
            <el-time-select v-model="check_out_format" start="08:30" step="00:15" end="18:30" placeholder="(none)"/>
          </div>
          <ButtonLoading @click="updateTimeKeep" style="font-size: 15px; margin-top: 20px" size="large" type="warning"
                         round>Save
          </ButtonLoading>
          <ButtonLoading :warning="status_change_ref || time_change_ref"
                         @click="dialog_visible = !dialog_visible"
                         style="font-size: 15px; margin-top: 20px" size="large" type="warning"
                         round>Request
          </ButtonLoading>
        </div>
      </el-card>
      <div style="margin-top: -10vh; margin-left: -7vw; margin-bottom: -17vh">
        <Clock style="scale: 0.5"></Clock>
      </div>
    </div>
  </div>
  <UserRequest
      :visible="dialog_visible"
      :status_AM="status_AM"
      :status_PM="status_PM"
      :checkin="check_in_format"
      :checkout="check_out_format"
      :checkin_system="check_in_system"
      :checkout_system="check_out_system"
      :time_change="time_change_ref"
      :status_change="status_change_ref"
      :today="prop.date"
      @update="emitUpdate"></UserRequest>
</template>

<script setup>
import ButtonLoading from "./ButtonLoading.vue";
import {computed, ref, watch, watchEffect} from "vue";
import {useUserStore} from "../stores/user";
import {useAlertStore} from "../stores/alert";
import Clock from "./Clock.vue"
import moment from "moment";
import StatusButton from "./StatusButton.vue";
import UserRequest from "./UserRequest.vue";
import TimeKeepAPI from "../services/TimeKeepAPI";

const prop = defineProps({
  user_id: {
    type: Number,
    default: 0,
  },
  dayOfWeek: {
    type: String,
    default: 'Sunday'
  },
  date: {
    type: String,
    default: '2000-01-01'
  },
  checkin: {
    type: String,
    default: ''
  },
  checkout: {
    type: String,
    default: ''
  },
  status_AM_prop: {
    type: Number,
    default: 0,
  },
  status_PM_prop: {
    type: Number,
    default: 0,
  },
  status_change: {
    type: Boolean,
    default: false,
  },
  time_change: {
    type: Boolean,
    default: false,
  }
})
const check_in_format = ref(prop.checkin.slice(0, 5))
const check_in_system = ref(prop.checkin.slice(7, 12))
const check_out_format = ref(prop.checkout.slice(0, 5))
const check_out_system = ref(prop.checkout.slice(7, 12))
const user = useUserStore().user
const alertStore = useAlertStore()
const emit = defineEmits(['update', 'nextday', 'prevday', 'update_no_reset'])
const today = ref(false)
const weekend = ref(false)
const status_AM = ref(0)
const status_PM = ref(0)
const status_change_ref = ref(false)
const time_change_ref = ref(false)
const dialog_visible = ref(false)

watch([() => prop.checkin, () => prop.checkout, () => prop.status_AM_prop, () => prop.status_PM_prop, () => prop.status_change, () => prop.time_change],
    () => {
      weekend.value = prop.dayOfWeek === 'Sunday' || prop.dayOfWeek === 'Saturday'
      today.value = prop.date === moment().format('YYYY-MM-DD')
      check_out_format.value = prop.checkout.slice(0, 5)
      check_out_system.value = prop.checkout.slice(7, 12)
      check_in_format.value = prop.checkin.slice(0, 5)
      check_in_system.value = prop.checkin.slice(7, 12)
      status_AM.value = prop.status_AM_prop
      status_PM.value = prop.status_PM_prop
      status_change_ref.value = prop.status_change
      time_change_ref.value = prop.time_change
    })

function updateTimeKeep() {
  TimeKeepAPI.updateTimeKeep(
      user.token,
      prop.user_id,
      prop.date,
      check_in_format.value,
      check_out_format.value,
      status_AM.value,
      status_PM.value,
  )
      .then(() => {
        emit('update_no_reset', prop.date)
        alertStore.alert = true
        alertStore.type = 'success'
        alertStore.msg = 'Update success'
      })
      .catch((e) => {
        alertStore.alert = true
        alertStore.type = 'error'
        alertStore.msg = 'Something went wrong'
        console.log(e)
      })
}

function emitUpdate() {
  status_change_ref.value = false
  time_change_ref.value = false
  emit('update')
}
</script>

<style scoped>
.edit-timekeep {
  display: flex;
  flex-direction: column;
  width: 30vw !important;
  margin-right: 30px;
  position: sticky !important;
  top: 10vh;
}

.info-status {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 50%;
}

.card-header {
  font-size: 18px;
  font-weight: bold;
}

.card-content {
  margin-top: 10px;
}

.bg-orange {
  background-color: rgba(255, 153, 41, 0.36);
}

.bg-gray {
  background-color: rgba(204, 204, 204, 0.43);
}
</style>
