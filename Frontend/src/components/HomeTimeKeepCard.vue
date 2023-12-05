<template>
  <Calendar></Calendar>
  <div class="edit-timekeep">
    <div class="card-content">
      <div class="status">
        <div style="display: flex; flex-direction: column; align-items: center">
          <span class="unselectable">8:30 AM - 12:00 AM</span>
          <StatusButton @click="status.status_AM = status_AM" @change-status="n => status_AM = n"
                        :status_index="status_AM"></StatusButton>
        </div>
        <div style="display: flex; flex-direction: column; align-items: center">
          <span class="unselectable">1:00 PM - 5:45 PM</span>
          <StatusButton @click="status.status_PM = status_PM" @change-status="n => status_PM = n"
                        :status_index="status_PM"></StatusButton>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.edit-timekeep {
  width: 320px;
  position: sticky !important;
  top: 10vh;
}

.card-header {
  font-size: 18px;
  font-weight: bold;
}

.status {
  display: flex;
  flex-direction: column;
  justify-content: space-between
}

.card-content {
  margin-top: 20px;
}

.unselectable {
  -webkit-user-select: none;
  -webkit-touch-callout: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

@media screen and (max-width: 1280px) {
  .status {
    flex-direction: row
  }
}
</style>

<script setup>
import {onBeforeUnmount, ref, watch} from "vue";
import {useUserStore} from "../stores/user";
import {useAlertStore} from "../stores/alert";
import StatusButton from "./StatusButton.vue";
import Calendar from "./Calendar.vue";
import {useStatusStore} from "../stores/status";
import TimeKeepAPI from "../services/TimeKeepAPI";

const prop = defineProps({
  user_id: {
    type: Number,
    default: 0,
  },
  date: {
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

function updateTimeKeep() {
  TimeKeepAPI.updateStatus(user.token, prop.user_id, prop.date, status_AM.value, status_PM.value)
      .then(() => {
        emit('update')
      })
      .catch((e) => {
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
