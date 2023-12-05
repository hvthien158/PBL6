<template>
  <el-dialog v-model="prop.visible" :close-on-press-escape="false" :close-on-click-modal="false" :show-close="false"
             @keyup.esc="$emit('invisible')" width="30%">
    <template #header>
      <div class="my-header">
        <h4 v-if="prop.mode === 'create'">Create Department</h4>
        <h4 v-else>Update Department</h4>
        <el-button type="danger" @click="$emit('invisible')">
          <el-icon class="el-icon--left">
            <CircleCloseFilled/>
          </el-icon>
          Close
        </el-button>
      </div>
    </template>
    <el-form :model="form">
      <el-form-item v-if="prop.mode === 'update'" :rules="[{ required : true }]" label="ID"
                    :label-width="formLabelWidth">
        <el-input v-model="prop.departmentId" autocomplete="off" disabled/>
      </el-form-item>
      <el-form-item label="Department name" :label-width="formLabelWidth" :rules="[{ required : true }]">
        <el-input v-model="form.name" autocomplete="off"/>
        <small>{{ checkLanding.checkName }}</small>
      </el-form-item>
      <el-form-item label="Address" :label-width="formLabelWidth" :rules="[{ required : true }]">
        <el-input v-model="form.address" autocomplete="off"/>
        <small>{{ checkLanding.checkAddress }}</small>
      </el-form-item>

      <el-form-item label="Email" :label-width="formLabelWidth">
        <el-input v-model="form.email" autocomplete="off"/>
        <small>{{ checkLanding.checkEmail }}</small>
      </el-form-item>

      <el-form-item label="Phone Number" :label-width="formLabelWidth">
        <el-input v-model="form.phoneNumber" autocomplete="off"/>
        <small>{{ checkLanding.checkPhoneNumber }}</small>
      </el-form-item>
      <el-form-item label="Department Manager" :label-width="formLabelWidth">
        <el-select v-model="form.manager" type="text">
          <el-option label="Select Manager" :value="0"></el-option>
          <el-option v-for="item in dataUser" :label="item.id + ' - ' + item.name" :value="item.id"></el-option>
        </el-select>
      </el-form-item>
    </el-form>
    <template #footer>
            <span class="dialog-footer">
                <el-button @click="$emit('invisible')">Cancel</el-button>
                <el-button v-if="prop.mode === 'create'" type="primary" @click="createDepartment">
                    Create
                </el-button>
                <el-button v-else type="primary" @click="updateDepartment">
                    Update
                </el-button>
            </span>
    </template>
  </el-dialog>
</template>

<style scoped>
.department-container {
  width: 100%;
  background-color: #ccc;
  padding: 10px 0;
  display: block;
  align-items: center;
  overflow-x: auto;
  border-radius: 4px;
}

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
  margin-right: 8px;
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

.department-container::-webkit-scrollbar {
  height: 6px;
  background-color: #f0f0f0;
}

.department-container::-webkit-scrollbar-thumb {
  height: 6px;
  background-color: #e0e0e0;
}

.el-select {
  width: 50%;
}
</style>

<script setup>
import {reactive, ref, watch, defineProps} from "vue";
import {useUserStore} from "../stores/user";
import {useAlertStore} from "../stores/alert";
import DepartmentAPI from "../services/DepartmentAPI";
import UserAPI from "../services/UserAPI";

const prop = defineProps({
  mode: {
    type: String,
    default: 'create'
  },
  departmentId: {
    type: Number
  },
  visible: {
    type: Boolean
  }
})

const formLabelWidth = "160px";
const user = useUserStore().user
const alertStore = useAlertStore()
const dataUser = ref()
const form = reactive({
  name: '',
  email: '',
  address: '',
  phoneNumber: '',
  manager: 0
})
const checkLanding = reactive({
  checkName: '',
  checkEmail: '',
  checkAddress: '',
  checkPhoneNumber: '',
})
const emits = defineEmits(['invisible', 'updateData'])
watch(() => prop.departmentId,
    () => {
      loadDepartment()
    })
watch(() => prop.mode,
    () => {
      if (prop.mode === 'create') {
        form.name = ''
        form.email = ''
        form.address = ''
        form.phoneNumber = ''
        form.manager = ''
      }
    })
const loadDepartment = async () => {
  if (prop.mode === 'update') {
    try {
      await DepartmentAPI.get1Department(user.token, prop.departmentId)
          .then((response) => {
            form.email = response.data.data.email
            form.name = response.data.data.name
            form.address = response.data.data.address
            form.phoneNumber = response.data.data.phoneNumber
            form.manager = response.data.data.manager.id
          })
    } catch (e) {
      console.log(e)
      messages('error', e.data.message)
    }
  }
}
const displayUser = async () => {
  try {
    await UserAPI.getAllUser(user.token)
        .then(function (response) {
          dataUser.value = response.data.data;
        });
  } catch (e) {
    console.log(e);
  }
};
if (prop.mode === 'update') {
  loadDepartment()
}
displayUser()

watch(() => form.name, () => {
  checkLanding.checkName = (form.name === '') ? 'The name field is required.' : ''
})
watch(() => form.address, () => {
  checkLanding.checkAddress = (form.address === '') ? 'The address field is required.' : ''
})
watch(() => form.email, () => {
  checkLanding.checkEmail = (!isEmail(form.email) && form.email !== '') ? 'The email must be a valid email address.' : ''
})
watch(() => form.phoneNumber, () => {
  checkLanding.checkPhoneNumber = (form.phoneNumber !== '' && !isPhoneNumber(form.phoneNumber)) ? 'The phone number must be a valid phone number.' : ''
})

function validate() {
  checkLanding.checkName = (form.name === '') ? 'The name field is required.' : ''
  checkLanding.checkAddress = (form.address === '') ? 'The address field is required.' : ''
  checkLanding.checkEmail = (!isEmail(form.email) && form.email !== '') ? 'The email must be a valid email address.' : ''
  checkLanding.checkPhoneNumber = (form.phoneNumber !== '' && !isPhoneNumber(form.phoneNumber)) ? 'The phone number must be a valid phone number.' : ''
  return (checkLanding.checkName === '' && checkLanding.checkAddress === '' && checkLanding.checkPhoneNumber === '' && checkLanding.checkEmail === '')
}

const isEmail = (email) => {
  let filter =
      /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return filter.test(email);
};
const isPhoneNumber = (phoneNumber) => {
  let filter = /^([0-9]{10})+$/;
  return filter.test(phoneNumber);
};

function createDepartment() {
  if (validate()) {
    let manager = form.manager === 0 ? null : form.manager
    DepartmentAPI.createDepartment(user.token, form, manager)
        .then(function () {
          emits('updateData')
          form.email = ''
          form.name = ''
          form.address = ''
          form.phoneNumber = ''
          form.manager = 0
          messages('success', 'Create success')
        })
        .catch((e) => {
          messages('error', e.response.data.message)
          console.log(e)
        })
  }
}

function updateDepartment() {
  if (validate()) {
    let manager = form.manager === 0 ? null : form.manager
    DepartmentAPI.updateDepartment(user.token, form, manager, prop.departmentId)
        .then(function (response) {
          emits('updateData')
          console.log(response)
          messages('success', 'Update successfully')
        })
        .catch((e) => {
          messages('success', 'Something went wrong')
          console.log(e)
        })
  }
}

const messages = (type, msg) => {
  alertStore.alert = true;
  alertStore.type = type;
  alertStore.msg = msg
}
</script>
  