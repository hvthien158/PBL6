<template>
  <el-dropdown :hide-on-click="false" max-height="70vh">
    <span class="el-dropdown-link">
      <el-icon size="30">
        <Calendar />
      </el-icon>
    </span>
    <template #dropdown>
      <div style="width: 25vw; padding: 12px 20px 0 20px; display: flex; justify-content: space-between">
        <h4>Meeting Schedule</h4>
      </div>
      <el-dropdown-menu style="padding: 12px">
        <el-empty v-if="request_data.length === 0" description="No Data" />
        <el-dropdown-item
            :divided="true"
            v-for="item in request_data"
            style="position: relative"
            @click="item.hide = !item.hide;">
          <div>
            <div style="display: flex;">
              <div style="display: flex; flex-direction: column">
                <div style="display: flex; justify-content: space-between; width: 20vw">
                  <div style="display: flex">
                    <span>Meeting at: {{ item.start }}</span>
                  </div>
                </div>
                <span style="color: #b2b2b2">{{ item.title }}</span>
              </div>
            </div>
            <div style="color: #777; border-top: 1px solid #bbb; margin-top: 12px;" v-if="!item.hide">
              <span style="font-weight: bold">Detail:</span>
              <div>
                <span>Content: {{ item.detail }}</span> <br>
                <span>Start: {{ item.start }}</span> <br>
                <span>End: {{ item.end }}</span>
              </div>
            </div>
          </div>
        </el-dropdown-item>
      </el-dropdown-menu>
    </template>
  </el-dropdown>
</template>

<script setup>
import {ref, watch, reactive, onMounted} from "vue";
import {useUserStore} from "../stores/user";
import moment from "moment";
import {useAlertStore} from "../stores/alert";
import MessageAPI from "../services/MessageAPI";
import MeetingAPI from "../services/MeetingAPI";

const request_data = ref('')
const user = useUserStore().user
const alertStore = useAlertStore()
function loadRequest() {
  request_data.value = ''
  MeetingAPI.getMyScheduleMeeting(user.token)
      .then((response) => {
        request_data.value = response.data
        request_data.value.forEach((data) => {
          data.hide = true
        })
      }).catch((e) => {
    console.log(e)
  })
}

onMounted(() => {
  loadRequest()
})
</script>

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
