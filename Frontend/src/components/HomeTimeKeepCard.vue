<template>
  <div class="edit-timekeep">
    <el-card style="border-radius: 12px" class="box-card">
      <div class="card-content">
        <div style="display: flex; flex-direction: column">
          <div style="display: flex; flex-direction: column; align-items: center">
            <span style="color: #939393">Morning</span>
            <StatusButton @change-status="n => status_AM = n" :status_index="status_AM"></StatusButton>
          </div>
          <div style="display: flex; flex-direction: column; align-items: center">
            <span style="color: #939393">Afternoon</span>
            <StatusButton @change-status="n => status_PM = n" :status_index="status_PM"></StatusButton>
          </div>
        </div>
        <div style="display: flex; justify-content: center">
          <ButtonLoading @click="updateTimeKeep" style="font-size: 15px; margin-top: 20px" size="large" type="warning" round>Save</ButtonLoading>
        </div>
      </div>
    </el-card>
  </div>
</template>

<style scoped>
.edit-timekeep{
  display: flex;
  width: 20vw;
  flex-direction: column;
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
import moment from "moment";
import StatusButton from "./StatusButton.vue";

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
