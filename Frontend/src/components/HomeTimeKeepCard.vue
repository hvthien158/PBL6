<template>
  <Calendar></Calendar>
  <div class="edit-timekeep">
    <div class="card-content">
      <div style="display: flex; flex-direction: column">
        <div style="display: flex; flex-direction: column; align-items: center">
          <span class="unselectable">8:30AM - 12:00PM</span>
          <StatusButton @click="status.status_AM = status_AM" @change-status="n => status_AM = n" :status_index="status_AM"></StatusButton>
        </div>
        <div style="display: flex; flex-direction: column; align-items: center">
          <span class="unselectable">1:00PM - 5:45PM</span>
          <StatusButton @click="status.status_PM = status_PM" @change-status="n => status_PM = n" :status_index="status_PM"></StatusButton>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.edit-timekeep{
  width: 320px;
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
.unselectable {
  -webkit-user-select: none;
  -webkit-touch-callout: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
</style>

<script setup>
import ButtonLoading from "./ButtonLoading.vue";
import {computed, onBeforeUnmount, onUnmounted, ref, watch, watchEffect} from "vue";
import axios from "axios";
import {useUserStore} from "../stores/user";
import {useAlertStore} from "../stores/alert";
import Clock from "./Clock.vue"
import moment from "moment";
import StatusButton from "./StatusButton.vue";
import Calendar from "./Calendar.vue";
import {useStatusStore} from "../stores/status";

const prop = defineProps({
  user_id: {
    type: Number,
    default: 0,
  },
  date:{
    type: String,
    default: '2000-01-01'
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
const user = useUserStore().user
const alertStore = useAlertStore()
const emit = defineEmits(['update'])
const status_AM = ref(0)
const status_PM = ref(0)
const status = useStatusStore()

watch([() => prop.status_AM_prop, () => prop.status_PM_prop],
    () => {
      status_AM.value = prop.status_AM_prop
      status_PM.value = prop.status_PM_prop
    })

function updateTimeKeep(){
  axios.post('http://127.0.0.1:8000/api/timekeeping/update', {
    'user_id': prop.user_id,
    'date': prop.date,
    'status_am': status_AM.value,
    'status_pm': status_PM.value,
  }, {
    headers: {Authorization: `Bearer ${user.token}`},
  }).then(() => {
    emit('update')
  }).catch((e) => {
    alertStore.alert = true
    alertStore.type = 'error'
    alertStore.msg = 'Something went wrong'
    console.log(e)
  })
}

onBeforeUnmount(() => {
  updateTimeKeep()
})
</script>
