<template>
  <el-dialog v-model="display" title="Request" width="40%" style="border-radius: 12px">
    <div style="display: flex; flex-direction: column">
      <el-empty description="No request available" v-if="!status_change_ref && !time_change_ref" />
      <el-card class="box-card" style="margin-bottom: 12px" v-if="status_change_ref">
        <div class="card--header">
          <span style="font-weight: bold">Leave/remote work request</span>
          <div style="display: flex">
            <div style="height: 8px; width: 40px; margin-right: 2px" :class="bg_color[status_AM]"></div>
            <div style="height: 8px; width: 40px;" :class="bg_color[status_PM]"></div>
          </div>
          <div>
            <el-button class="button" v-if="!view_leave_remote_content" @click="view_leave_remote_content = true">
              <el-icon>
                <EditPen />
              </el-icon>
            </el-button>
            <el-button class="button" v-if="view_leave_remote_content" @click="view_leave_remote_content = false">
              <el-icon><Select /></el-icon>
            </el-button>
            <el-button class="button" @click="sendStatusRequest">
              <el-icon>
                <Promotion />
              </el-icon>
            </el-button>
          </div>
        </div>
        <div v-if="view_leave_remote_content" style="margin-top: 12px;">
          <el-input v-model="leave_remote_content" type="textarea" :autosize="{ minRows: 2 }"
            placeholder="Write a message..."></el-input>
        </div>
      </el-card>
      <el-card class="box-card" v-if="time_change_ref">
        <div class="card--header">
          <span style="font-weight: bold">Checkin/checkout request</span>
          <span>{{ checkin }} - {{ checkout }}</span>
          <div>
            <el-button class="button" v-if="!view_checkin_checkout_content" @click="view_checkin_checkout_content = true">
              <el-icon>
                <EditPen />
              </el-icon>
            </el-button>
            <el-button class="button" v-if="view_checkin_checkout_content" @click="view_checkin_checkout_content = false">
              <el-icon><Select /></el-icon>
            </el-button>
            <el-button class="button" @click="sendTimeRequest">
              <el-icon>
                <Promotion />
              </el-icon>
            </el-button>
          </div>
        </div>
        <div v-if="view_checkin_checkout_content" style="margin-top: 12px;">
          <el-input v-model="checkin_checkout_content" type="textarea" :autosize="{ minRows: 2 }"
            placeholder="Write a message..."></el-input>
        </div>
      </el-card>
    </div>
    <template #footer>
      <span class="dialog-footer">
        <el-button @click="display = false">Cancel</el-button>
      </span>
    </template>
  </el-dialog>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import moment from "moment";
import { useUserStore } from "../stores/user";
import { useAlertStore } from "../stores/alert";
import MessageAPI from "../services/MessageAPI";
import { STATUS_CHANGE_REQ, TIME_CHANGE_REQ } from "../constants";

const user = useUserStore().user
const prop = defineProps({
  visible: {
    type: Boolean,
    default: false,
  },
  time_keeping_id: {
    type: Number,
    default: 0,
  },
  today: {
    type: String,
    default: moment().format('YYYY-MM-DD')
  },
  status_AM: {
    type: Number,
    default: 0,
  },
  status_PM: {
    type: Number,
    default: 0,
  },
  checkin: String,
  checkout: String,
  checkin_system: String,
  checkout_system: String,
  status_change: {
    type: Boolean,
    default: false
  },
  time_change: {
    type: Boolean,
    default: false,
  }
})
const display = ref(false)
const view_leave_remote_content = ref(false)
const view_checkin_checkout_content = ref(false)
const status_change_ref = ref(prop.status_change)
const time_change_ref = ref(prop.time_change)
const bg_color = ref(['bg-green', 'bg-purple', 'bg-gray'])
const alertBox = useAlertStore()

const leave_remote_content = ref('')
const checkin_checkout_content = ref('')
const emits = defineEmits(['update'])

watch(() => prop.visible, () => {
  display.value = true
  view_leave_remote_content.value = false
  view_checkin_checkout_content.value = false
  status_change_ref.value = prop.status_change
  time_change_ref.value = prop.time_change
})


function sendStatusRequest() {
  MessageAPI.sendMessage(user.token, prop.today, STATUS_CHANGE_REQ, leave_remote_content.value)
    .then((response) => {
      status_change_ref.value = false
      if (time_change_ref.value === false) {
        emits('update')
      }

      alertBox.alert = true
      alertBox.type = 'success'
      alertBox.msg = response.data.message
    })
    .catch((e) => {
      console.log(e)
    })
}

function sendTimeRequest() {
  MessageAPI.sendMessage(user.token, prop.today, TIME_CHANGE_REQ, checkin_checkout_content.value)
    .then((response) => {
      time_change_ref.value = false
      if (status_change_ref.value === false) {
        emits('update')
      }

      alertBox.alert = true
      alertBox.type = 'success'
      alertBox.msg = response.data.message
    })
    .catch((e) => {
      console.log(e)
    })
}
</script>

<style scoped>
.card--header {
  display: flex;
  justify-content: space-between;
  align-items: center;

}

pre {
  white-space: pre-wrap;
  white-space: -moz-pre-wrap;
  white-space: -o-pre-wrap;
  word-wrap: break-word;
}

.bg-green {
  background-color: #04fc43;
}

.bg-purple {
  background-color: rgba(0, 120, 248, 0.57);
}

.bg-gray {
  background-color: #ccc;
}
</style>
