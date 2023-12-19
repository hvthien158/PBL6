<template>
  <el-dialog v-model="prop.visible" :close-on-press-escape="false" :close-on-click-modal="false" :show-close="false"
             @keyup.esc="$emit('invisible')" min-width="30%">
    <template #header>
      <div class="my-header">
        <h4>Schedule a meeting</h4>
        <el-button type="danger" @click="$emit('invisible')">
          <el-icon class="el-icon--left">
            <CircleCloseFilled/>
          </el-icon>
          Close
        </el-button>
      </div>
    </template>
    <el-form :model="form">
      <el-form-item label="Title" :label-width="formLabelWidth" :rules="[{ required: true }]">
        <el-input v-model="form.title" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="Attendees" :label-width="formLabelWidth" :rules="[{ required: true }]">
        <el-select
            v-model="form.attendees"
            multiple
            placeholder="Select"
            style="width: 240px"
        >
          <el-option
              v-for="item in list_user_available"
              :key="item.id"
              :label="item.name"
              :value="item.id"
          />
        </el-select>
      </el-form-item>
      <el-form-item label="Start" :label-width="formLabelWidth">
        <el-date-picker placeholder="YYYY-MM-DD" v-model="form.startDate">
        </el-date-picker>
        <el-time-picker placeholder="HH:mm" v-model="form.startTime">
        </el-time-picker>
      </el-form-item>
      <el-form-item label="End" :label-width="formLabelWidth">
        <el-date-picker placeholder="YYYY-MM-DD" v-model="form.endDate">
        </el-date-picker>
        <el-time-picker placeholder="HH:mm" v-model="form.endTime">
        </el-time-picker>
      </el-form-item>
      <el-form-item label="Detail" :label-width="formLabelWidth">
        <el-input type="textarea" v-model="form.address" autocomplete="off"/>
      </el-form-item>
    </el-form>
    <template #footer>
      <span class="dialog-footer">
        <el-button @click="$emit('invisible')">Cancel</el-button>
        <el-button type="primary" @click="createSchedule">
          Send
        </el-button>
      </span>
    </template>
  </el-dialog>
</template>

<script setup>
import {reactive, ref, watch} from "vue";
import {useUserStore} from "../stores/user";
import {useAlertStore} from "../stores/alert";
import DepartmentAPI from "../services/DepartmentAPI";
import UserAPI from "../services/UserAPI";

const formLabelWidth = "100px";
const user = useUserStore().user
const alertStore = useAlertStore()

const prop = defineProps({
  visible: {
    type: Boolean,
    default: false,
  }
})

const form = reactive({
  title: '',
  attendees: [],
  startDate: '',
  startTime: '',
  endDate: '',
  endTime: '',
  detail: '',
})
let department = ref()
const list_user_available = ref([
  {
    id: '',
    name: '',
  }
])

const emits = defineEmits(['invisible'])

watch(() => prop.visible, () => {
  getListUser()
})

function createSchedule() {
  try {
    //Call API
  } catch (e) {
    alertStore.alert = true
    alertStore.type = 'error'
    alertStore.msg = 'Something went wrong'
    console.log(e)
  }
}

const getListUser = async () => {
  try {
    await DepartmentAPI.getAllUser(user.token, user.owner_department_id)
        .then(function (response) {
          console.log(response.data)
          list_user_available.value = response.data
        });
  } catch (e) {
    console.log(e);
  }
};
</script>

<style scoped>
.my-header {
  display: flex;
  justify-content: space-between;
}

small {
  color: red;
  font-size: 14px;
  margin-left: 0;
}

.form-item {
  display: flex;
  align-items: center;
  margin-right: 16px;
}

.form-label {
  margin-right: 8px;
}

.el-button--text {
  margin-right: 15px;
}

.el-input {
  width: 100%;
}

.el-select {
  width: 100%;
}

.dialog-footer {
  margin-top: 16px;
}

.dialog-footer button:first-child {
  margin-right: 10px;
}

.dialog-footer button {
  margin-right: 8px;
}

span {
  display: block;
  color: red;
  margin-top: 0.5rem;
  margin-left: 16px;
}
</style>
