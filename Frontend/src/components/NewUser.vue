<template>
  <el-dialog v-model="prop.visible" :close-on-press-escape="false" :close-on-click-modal="false" :show-close="false"
             @keyup.esc="$emit('invisible')" min-width="30%">
    <template #header>
      <div class="my-header">
        <h4 v-if="prop.mode === 'create'">Create User</h4>
        <h4 v-else>Update User</h4>
        <el-button type="danger" @click="$emit('invisible')">
          <el-icon class="el-icon--left">
            <CircleCloseFilled/>
          </el-icon>
          Close
        </el-button>
      </div>
    </template>
    <el-form :model="form">
      <el-form-item v-if="prop.mode === 'update'" label="ID" :label-width="formLabelWidth"
                    :rules="[{ required: true }]">
        <el-input v-model="prop.userId" autocomplete="off" disabled/>
      </el-form-item>
      <el-form-item label="Name" :label-width="formLabelWidth" :rules="[{ required: true }]">
        <el-input v-model="form.name" autocomplete="off"/>
        <small>{{ checkLanding.checkName }}</small>
      </el-form-item>
      <el-form-item label="Email" :label-width="formLabelWidth" :rules="[{ required: true }]">
        <el-input v-model="form.email" autocomplete="off"/>
        <small>{{ checkLanding.checkEmail }}</small>
      </el-form-item>
      <el-form-item v-if="prop.mode === 'create'" label="Password" :label-width="formLabelWidth"
                    :rules="[{ required: true }]">
        <el-input type="password" show-password v-model="form.password" autocomplete="off"/>
        <small>{{ checkLanding.checkPassword }}</small>
      </el-form-item>
      <el-form-item label="Date of Birth" :label-width="formLabelWidth">
        <el-date-picker placeholder="yyyy-mm-dd" v-model="form.DOB">
        </el-date-picker>
      </el-form-item>
      <el-form-item label="Address" :label-width="formLabelWidth">
        <el-input v-model="form.address" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="Phone Number" :label-width="formLabelWidth" :rules="[{ required: true }]">
        <el-input v-model="form.phoneNumber" autocomplete="off"/>
        <small>{{ checkLanding.checkPhoneNumber }}</small>
      </el-form-item>
      <el-form-item label="Salary" :label-width="formLabelWidth">
        <el-input :formatter="(value) => `$ ${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                  :parser="(value) => value.replace(/\$\s?|(,*)/g, '')" v-model="form.salary" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="Role" :label-width="formLabelWidth" :rules="[{ required: true }]">
        <el-select v-model="form.role" type="text">
          <el-option v-for="item in role" :label="item.name" :value="item.value"></el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="Position" :label-width="formLabelWidth">
        <el-select v-model="form.position" type="text">
          <el-option v-for="item in position" :label="item.name" :value="item.value"></el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="Department" :label-width="formLabelWidth" :rules="[{ required: true }]">
        <el-select v-model="form.department" type="text">
          <el-option label="Select Department" :value="0"></el-option>
          <el-option v-for="item in department" :label="item.name" :value="item.id"></el-option>
        </el-select>
        <small>{{ checkLanding.department }}</small>
      </el-form-item>

    </el-form>
    <template #footer>
      <span class="dialog-footer">
        <el-button @click="$emit('invisible')">Cancel</el-button>
        <el-button v-if="prop.mode === 'create'" type="primary" @click="createUser">
          Create
        </el-button>
        <el-button v-else type="primary" @click="updateUser">
          Update
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

const formLabelWidth = "150px";
const user = useUserStore().user
const alertStore = useAlertStore()
const prop = defineProps({
  mode: {
    type: String,
    default: 'create'
  },
  userId: {
    type: Number,
  },
  visible: {
    type: Boolean
  }
})

const form = reactive({
  name: '',
  email: '',
  password: '',
  address: '',
  DOB: '',
  phoneNumber: '',
  salary: '',
  position: '',
  role: 'user',
  department: '',
})
const role = [
  {
    name: "User",
    value: "user",
  },
  {
    name: "Admin",
    value: "admin",
  }
];
const position = [
  {
    name: "Select Position",
    value: "none",
  },
  {
    name: "Intern",
    value: "intern",
  },
  {
    name: "Fresher",
    value: "fresher",
  },
  {
    name: "Junior",
    value: "junior",
  },
  {
    name: "Senior",
    value: "senior",
  },
];
const checkLanding = reactive({
  checkName: '',
  checkEmail: '',
  checkDepartment: '',
  checkPhoneNumber: '',
  checkPassword: ''
})
let department = ref()

const emits = defineEmits(['invisible', 'update_data'])

DepartmentAPI.getAllDepartment()
    .then(function (response) {
      department.value = response.data.data;
    });

watch(() => prop.userId,
    () => {
      loadUser()
    })
watch(() => prop.mode,
    () => {
      if (prop.mode === 'create') {
        form.name = ''
        form.email = ''
        form.password = ''
        form.address = ''
        form.DOB = ''
        form.phoneNumber = ''
        form.salary = ''
        form.position = ''
        form.role = ''
        form.department = ''
      }
    })

function loadUser() {
  if (prop.mode === 'update') {
    UserAPI.get1User(user.token, prop.userId)
        .then((response) => {
          form.email = response.data.data.email === 'none' ? '' : response.data.data.email;
          form.name = response.data.data.name === 'none' ? '' : response.data.data.name;
          form.address = response.data.data.address === 'none' ? '' : response.data.data.address;
          form.phoneNumber = response.data.data.phone_number === 'none' ? '' : response.data.data.phone_number;
          form.DOB = response.data.data.DOB === 'none' ? '' : response.data.data.DOB;
          form.salary = response.data.data.salary === 'none' ? '' : response.data.data.salary.split('$')[1];
          form.position = response.data.data.position === 'none' ? '' : response.data.data.position;
          form.role = response.data.data.role;
          form.department = response.data.data.department.id;
        })
        .catch((e) => {
          console.log(e)
          alertStore.alert = true;
          alertStore.type = 'error';
          alertStore.msg = 'Something wrong'
        })
  }
}

loadUser()
watch(() => form.name, () => {
  checkLanding.checkName = (form.name.length <= 4) ? 'The name must be at least 4 characters.' : ''
})
watch(() => form.password, () => {
  checkLanding.checkPassword = (form.password.length <= 6 && prop.mode === 'create') ? 'The password must be at least 6 characters.' : ''
})
watch(() => form.email, () => {
  if (form.email == '') {
    checkLanding.checkEmail = 'The email field is required.'
  } else {
    checkLanding.checkEmail = !isEmail(form.email) ? 'The email must be a valid email address.' : ''
  }
})
watch(() => form.phoneNumber, () => {
  checkLanding.checkPhoneNumber = (form.phoneNumber != '' && !isPhoneNumber(form.phoneNumber)) ? 'The phone number must be a valid phone number.' : ''
})
watch(() => form.department, () => {
  checkLanding.department = form.department == 0 ? 'The department field is required.' : ''
})
const validate = () => {
  checkLanding.checkName = (form.name.length <= 4) ? 'The name must be at least 4 characters.' : ''
  checkLanding.checkPassword = (form.password.length <= 6 && prop.mode === 'create') ? 'The password must be at least 6 characters.' : ''
  if (form.email == '') {
    checkLanding.checkEmail = 'The email field is required.'
  } else {
    checkLanding.checkEmail = !isEmail(form.email) ? 'The email must be a valid email address.' : ''
  }
  checkLanding.checkPhoneNumber = (form.phoneNumber != '' && !isPhoneNumber(form.phoneNumber)) ? 'The phone number must be a valid phone number.' : ''
  checkLanding.department = form.department == 0 ? 'The department must be required.' : ''
  return checkLanding.checkName === '' && checkLanding.checkEmail === '' && checkLanding.checkPassword === '' && checkLanding.department === '';
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

function createUser() {
  if (validate()) {
    try {
      UserAPI.createUser(user.token, form, formatToPost(form.DOB))
          .then(function () {
            emits('update_data')
            alertStore.alert = true
            alertStore.type = 'success'
            alertStore.msg = 'Create success'
          })
    } catch (e) {
      alertStore.alert = true
      alertStore.type = 'error'
      alertStore.msg = 'Something went wrong'
      console.log(e)
    }
  }
}

function updateUser() {
  if (validate()) {
    try {
      UserAPI.updateUser(user.token, form, formatToPost(form.DOB), prop.userId)
          .then(function () {
            emits('update_data')
            alertStore.alert = true
            alertStore.type = 'success'
            alertStore.msg = 'Update success'
          })
    } catch (e) {
      alertStore.alert = true
      alertStore.type = 'error'
      alertStore.msg = 'Update success'
      console.log(e)
    }
  }
}

const formatToPost = (time) => {
  if (!time) {
    return ''
  }
  const date = new Date(time);
  const year = date.getFullYear();
  const month = (date.getMonth() + 1).toString().padStart(2, '0');
  const day = date.getDate().toString().padStart(2, '0');
  return `${year}-${month}-${day}`;
}
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
