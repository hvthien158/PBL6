<template>
  <div class="edit-timekeep">
    <el-card style="border-radius: 12px" class="box-card" :class="{ 'bg-orange' : today, 'bg-gray' : weekend}">
      <div class="card-content">
        <h1>{{ prop.dayOfWeek }}</h1>
        <p>{{ prop.date }}</p>
        <div style="display: flex">
          <div style="display: flex; flex-direction: column; align-items: center">
            <span style="color: #939393">Morning</span>
            <StatusButton @change-status="n => status_AM = n" :status_index="status_AM"></StatusButton>
          </div>
          <div style="display: flex; flex-direction: column; align-items: center">
            <span style="color: #939393">Afternoon</span>
            <StatusButton @change-status="n => status_PM = n" :status_index="status_PM"></StatusButton>
          </div>
        </div>
        <p>Check-in:</p>
        <div>
          <el-time-select
              v-model="check_in_format"
              start="08:30"
              step="00:15"
              end="18:30"
              placeholder="(none)"
          />
        </div>
        <p style="margin-top: 20px">Check-out:</p>
        <div>
          <el-time-select
              v-model="check_out_format"
              start="08:30"
              step="00:15"
              end="18:30"
              placeholder="(none)"
          />
        </div>
        <ButtonLoading @click="updateTimeKeep" style="font-size: 15px; margin-top: 20px" size="large" type="warning" round>Save</ButtonLoading>
      </div>
    </el-card>
    <Clock style="scale: 0.55; margin-top: 5vh"></Clock>
  </div>
</template>

<style scoped>
.edit-timekeep{
  display: flex;
  flex-direction: column;
  width: 30vw !important;
  margin-right: 30px;
  position: sticky !important;
  top: 10vh;
}
.card-header{
  font-size: 18px;
  font-weight: bold;
}
.card-content{
  margin-top: 20px;
}
.bg-orange{
  background-color: rgba(255, 153, 41, 0.36);
}
.bg-gray{
  background-color: rgba(204, 204, 204, 0.43);
}
</style>

<script setup>
import ButtonLoading from "./ButtonLoading.vue";
import {computed, ref, watch, watchEffect} from "vue";
import axios from "axios";
import {useUserStore} from "../stores/user";
import {useAlertStore} from "../stores/alert";
import Clock from "./Clock.vue"
import moment from "moment";
import StatusButton from "./StatusButton.vue";

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
    default: '01/01/2000'
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
})
const check_in_format = ref(prop.checkin.slice(0, 5))
const check_out_format = ref(prop.checkout.slice(0, 5))
const user = useUserStore().user
const alertStore = useAlertStore()
const emit = defineEmits(['update'])
const today = ref(false)
const weekend = ref(false)
const status_AM = ref(0)
const status_PM = ref(0)

watch(() => prop.date,
    () => {
        weekend.value = prop.dayOfWeek === 'Sunday' || prop.dayOfWeek === 'Saturday';
        today.value = prop.date === moment().format('YYYY-MM-DD')
        check_out_format.value = prop.checkout.slice(0, 5)
        check_in_format.value = prop.checkin.slice(0, 5)
        status_AM.value = prop.status_AM_prop
        status_PM.value = prop.status_PM_prop
    })

function updateTimeKeep(){
  axios.post('http://127.0.0.1:8000/api/timekeeping/update', {
    'user_id': prop.user_id,
    'date': prop.date,
    'time_check_in': check_in_format.value,
    'time_check_out': check_out_format.value,
    'status_am': status_AM.value,
    'status_pm': status_PM.value,
  }, {
    headers: {Authorization: `Bearer ${user.token}`},
  }).then(() => {
    emit('update')
    alertStore.alert = true
    alertStore.type = 'success'
    alertStore.msg = 'Update success'
  }).catch((e) => {
    alertStore.alert = true
    alertStore.type = 'error'
    alertStore.msg = 'Something went wrong'
    console.log(e)
  })
}
</script>
