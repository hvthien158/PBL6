<template>
  <div class="edit-timekeep">
    <el-card style="border-radius: 12px" class="box-card">
      <div class="card-content">
        <h1>{{ prop.dayOfWeek }}</h1>
        <p>{{ prop.date }}</p>
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
    <Clock style="scale: 0.55; margin-top: 10vh"></Clock>
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
</style>

<script setup>
import ButtonLoading from "./ButtonLoading.vue";
import {computed, ref, watch, watchEffect} from "vue";
import axios from "axios";
import {useUserStore} from "../stores/user";
import {useAlertStore} from "../stores/alert";
import Clock from "./Clock.vue"

const prop = defineProps({
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
  }
})
const check_in_format = ref(prop.checkin.slice(0, 5))
const check_out_format = ref(prop.checkout.slice(0, 5))
const user = useUserStore().user
const alertStore = useAlertStore()
const emit = defineEmits(['update'])

watch(() => prop.date,
    () => {
          check_out_format.value = prop.checkout.slice(0, 5)
          check_in_format.value = prop.checkin.slice(0, 5)
    })

function updateTimeKeep(){
  console.log(check_in_format.value)
  console.log(check_out_format.value)
  axios.post('http://127.0.0.1:8000/api/timekeeping/update', {
    'date': prop.date,
    'time_check_in': check_in_format.value,
    'time_check_out': check_out_format.value,
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
