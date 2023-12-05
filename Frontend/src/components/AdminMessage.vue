<template>
  <el-dropdown :hide-on-click="false" max-height="70vh">
    <span class="el-dropdown-link">
      <el-icon size="30">
        <Message />
      </el-icon>
      <span v-if="new_message > 0" style="color: red; position: absolute; right: 40%">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-circle-fill"
          viewBox="0 0 16 16">
          <circle cx="6" cy="6" r="6" />
        </svg>
      </span>
    </span>
    <template #dropdown>
      <div style="width: 25vw; padding: 12px 20px 0 20px; display: flex; justify-content: space-between">
        <h4>Message</h4>
        <el-checkbox style="text-align: right" v-model="only_unread" label="Only view unread message" size="default" />
      </div>
      <el-dropdown-menu style="padding: 12px">
        <el-empty v-if="request_data.length === 0" description="No Data" />
        <el-dropdown-item :divided="true" v-for="item in request_data" style="position: relative"
          @click="item.hide = !item.hide; checkRead(item.id, item.is_read); item.is_read = 1">
          <div>
            <div style="display: flex;">
              <div style="margin-right: 12px;">
                <el-avatar :src="item.user.avatar"></el-avatar>
              </div>
              <span v-if="item.is_read === 0" class="icon-warn">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor"
                  class="bi bi-circle-fill" viewBox="0 0 16 16">
                  <circle cx="6" cy="6" r="6" />
                </svg>
              </span>
              <div style="display: flex; flex-direction: column">
                <div style="display: flex; justify-content: space-between; width: 20vw">
                  <div style="display: flex">
                    <span>{{ item.user.name }}</span>
                    <div style="height: 6px; width: 6px; border-radius: 50%; background-color: #1cb966"
                      v-if="item.is_check === 0"></div>
                  </div>
                  <span>{{ moment(item.created_at).fromNow() }}</span>
                </div>
                <span style="color: #b2b2b2">{{ item.title }}</span>
              </div>
            </div>
            <div style="color: #777; border-top: 1px solid #bbb; margin-top: 12px;" v-if="!item.hide">
              <span style="font-weight: bold">Detail:</span>
              <div v-if="item.title === 'Leave/remote work request'">
                <span>Morning: {{ status_request[item.time_keeping.status_am] }}</span> <br>
                <span>Afternoon: {{ status_request[item.time_keeping.status_pm] }}</span>
              </div>
              <div v-else>
                <span>Checkin: {{ item.time_keeping.time_check_in }}</span> <br>
                <span>Checkout: {{ item.time_keeping.time_check_out }}</span>
              </div>
              <div style="border-top: 1px solid #bbb; margin-top: 12px;">
                <span style="font-weight: bold">Message:</span>
                <pre v-if="item.content">{{ item.content }}</pre>
                <span v-else style="color: #ccc"><br>No message</span>
              </div>
              <div style="margin-top: 4px;">
                <el-button v-if="item.is_check === 0" @click="checkPass(item.id)" type="success"
                  style="padding: 12px">Confirm</el-button>
                <el-button v-else disabled @click="checkPass(item.id)" type="info"
                  style="padding: 12px">Confirmed</el-button>
              </div>
            </div>
          </div>
        </el-dropdown-item>
      </el-dropdown-menu>
    </template>
  </el-dropdown>
</template>

<style scoped>
.el-dropdown-link {
  cursor: pointer;
  border: none;
  color: white;
  outline: none;
  margin-right: 2vw;
}

.el-dropdown-link:hover {
  color: #f3952d;
  outline: none;
}

.header-profile {
  display: flex;
  align-items: center;
  padding: 20px;
  width: 240px;
  border-bottom: 1px solid #ccc;
}

#dropdown-name {
  font-size: 15px;
  color: #6A679E;
  font-weight: 600;
}

#dropdown-name:hover {
  text-decoration: underline;
  color: #f3952d;
  cursor: pointer;
}

.icon-warn {
  position: absolute;
  color: #ff3c00;
  left: 0;
  top: 1em;
}

pre {
  white-space: pre-wrap;
  /* css-3 */
  white-space: -moz-pre-wrap;
  /* Mozilla, since 1999 */
  white-space: -o-pre-wrap;
  /* Opera 7 */
  word-wrap: break-word;
  /* Internet Explorer 5.5+ */
}
</style>

<script setup>
import { ref, watch, reactive, onMounted } from "vue";
import axios from "axios";
import { useUserStore } from "../stores/user";
import { useAlertStore } from "../stores/alert";
import moment from "moment";
const status_request = ref(['Work', 'Remote', 'Not work'])
const only_unread = ref(false)
const request_data = ref('')
const user = useUserStore().user
const new_message = ref(0)
const alertStore = useAlertStore()
const props = defineProps({
  notification: {
    type: Object
  }
})
watch(props, () => {
  if(props.notification.type == 1){
    only_unread.value = true
    loadRequest()
  }
})
watch(() => only_unread.value, loadRequest)
function loadRequest() {
  if (only_unread.value) {
    axios.get('http://127.0.0.1:8000/api/message/limit-unread', {
      headers: {
        Authorization: `Bearer ${user.token}`
      },
    }).then((response) => {
      request_data.value = response.data.data
      new_message.value = 0
      request_data.value.forEach((data) => {
        data.hide = true
        if (data.is_read === 0) {
          new_message.value += 1
        }
      })
    }).catch((e) => {
      console.log(e)
    })
  } else {
    axios.get('http://127.0.0.1:8000/api/message/limit', {
      headers: {
        Authorization: `Bearer ${user.token}`
      },
    }).then((response) => {
      request_data.value = response.data.data
      new_message.value = 0
      request_data.value.forEach((data) => {
        data.hide = true
        if (data.is_read === 0) {
          new_message.value += 1
        }
      })
    }).catch((e) => {
      console.log(e)
    })
  }
}

onMounted(() => {
  loadRequest()
})

function checkRead(id, read) {
  if (read === 0) {
    new_message.value -= 1
    axios.post('http://127.0.0.1:8000/api/message/read', {
      id: id
    }, {
      headers: {
        Authorization: `Bearer ${user.token}`
      },
    }).catch((e) => {
      console.log(e)
    })
  }
}

async function checkPass(id) {
  try {
    await axios.post('http://127.0.0.1:8000/api/message/pass', {
      id: id
    }, {
      headers: {
        Authorization: `Bearer ${user.token}`
      },
    }).then((response) => {
      loadRequest()
      alertStore.alert = true
      alertStore.type = 'success'
      alertStore.msg = response.data.message
    })
  } catch (e) {
    console.log(e)
  }
}
</script>
