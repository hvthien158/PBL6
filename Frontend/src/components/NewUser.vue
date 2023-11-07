<template>
  <div class="new-user-container">
    <div>
      <el-input disabled style="width: 50px" type="text" placeholder="ID"></el-input>
    </div>
    <div>
      <el-input v-model="form.name" style="width: 180px;" type="text" placeholder="Name"></el-input>
    </div>
    <div>
      <el-input v-model="form.email" style="width: 220px" type="text" placeholder="Email"></el-input>
    </div>
    <div v-if="prop.mode === 'create'">
      <el-input v-model="form.password" style="width: 220px" type="text"
                placeholder="Password"></el-input>
    </div>
    <div>
      <el-input v-model="form.address" style="width: 330px" type="text" placeholder="Address"></el-input>
    </div>
    <div>
      <el-date-picker v-model="form.DOB" style="width: 130px" type="date" value-format="YYYY-MM-DD" placeholder="Date of birth"></el-date-picker>
    </div>
    <div>
      <el-input v-model="form.phone_number" style="width: 120px" type="text" placeholder="Phone Number"></el-input>
    </div>
    <div>
      <el-input v-model="form.salary" style="width: 120px" type="text" placeholder="Salary"></el-input>
    </div>
    <div>
      <el-select v-model="form.position" style="width: 120px" type="text" placeholder="Position">
        <el-option v-for="item in position" :label="item.name" :value="item.value"></el-option>
      </el-select>
    </div>
    <div>
      <el-select v-model="form.role" style="width: 100px" type="text" placeholder="Role">
        <el-option v-for="item in role" :label="item.name" :value="item.value"></el-option>
      </el-select>
    </div>
    <div>
      <el-select v-model="form.department" style="width: 120px" type="text" placeholder="Department">
        <el-option v-for="item in department" :label="item.name" :value="item.id"></el-option>
      </el-select>
    </div>
  </div>
  <div class="operation">
    <el-button v-if="prop.mode === 'create'" type="primary" plain @click="createUser">Add</el-button>
    <el-button v-else type="primary" plain @click="updateUser">Change</el-button>
    <el-button @click="$emit('invisible')">Cancel</el-button>
    <span style="color: red; margin-top: 0.1rem; margin-left: 16px">{{ fail_validation }}</span>
  </div>
</template>

<style scoped>
.new-user-container {
  background-color: #ccc;
  height: 56px;
  max-width: 80vw;
  display: flex;
  align-items: center;
  overflow-x: scroll;
  padding-left: 8px;
  border-radius: 4px;
}

.new-user-container::-webkit-scrollbar {
  height: 6px;
  background-color: #f0f0f0;
}

.new-user-container::-webkit-scrollbar-thumb {
  height: 6px;
  background-color: #e0e0e0;
}

.new-user-container div input {
  padding: 0 8px;
}

.new-user-container div {
  padding-right: 4px;
}

.operation {
  display: flex;
  width: 80vw;
  padding: 12px;
}
</style>

<script setup>
import {reactive, ref, watch} from "vue";
import axios from "axios";
import {useUserStore} from "../stores/user";
import {useAlertStore} from "../stores/alert";

const user = useUserStore().user
const alertStore = useAlertStore()
const fail_validation = ref('')
const prop = defineProps({
  mode: {
    type: String,
    default: 'create'
  },
  user_id: {
    type: Number,
  }
})

const form = reactive({
  name: '',
  email: '',
  password: '',
  address: '',
  DOB: '',
  phone_number: '',
  salary: '',
  position: '',
  role: '',
  department: '',
})
const role = [
  {
    name: "Admin",
    value: "admin",
  },
  {
    name: "User",
    value: "user",
  },
];
const position = [
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
let department = ref()

const emits = defineEmits(['invisible', 'update_data'])

axios.get("http://127.0.0.1:8000/api/department")
    .then(function (response) {
      department.value = response.data.data;
    });

watch(() => prop.user_id,
    () => {
      loadUser()
    })
watch(() => prop.mode,
    () => {
      if(prop.mode === 'create'){
        form.name = ''
        form.email = ''
        form.password = ''
        form.address = ''
        form.DOB = ''
        form.phone_number = ''
        form.salary = ''
        form.position = ''
        form.role = ''
        form.department = ''
      }
    })
function loadUser(){
  if (prop.mode === 'update') {
    axios.get(`http://127.0.0.1:8000/api/user/${prop.user_id}`, {
      headers: {
        Authorization: `Bearer ${user.token}`
      },
    }).then((response) => {
      form.email = response.data.data[0].email === 'none' ? '' : response.data.data[0].email;
      form.name = response.data.data[0].name === 'none' ? '' : response.data.data[0].name;
      form.address = response.data.data[0].address === 'none' ? '' : response.data.data[0].address;
      form.phone_number = response.data.data[0].phone_number === 'none' ? '' : response.data.data[0].phone_number;
      form.DOB = response.data.data[0].DOB === 'none' ? '' : response.data.data[0].DOB;
      form.salary = response.data.data[0].salary === 'none' ? '' : response.data.data[0].salary;
      form.position = response.data.data[0].position === 'none' ? '' : response.data.data[0].position;
      form.role =
          response.data.data[0].role === "admin" ? "admin" : "user";
      form.department = response.data.data[0].department.id;
    }).catch(() => {
      alertStore.alert = true;
      alertStore.type = 'error';
      alertStore.msg = 'Something wrong'
    })
  }
}
loadUser()

const isEmail = (email) => {
  let filter =
      /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return filter.test(email);
};

function validate() {
  if(form.name === ''){
    fail_validation.value = 'Please enter name'
    return false
  } else if (form.email === '') {
    fail_validation.value = 'Please enter email'
    return false
  } else if (!isEmail(form.email)) {
    fail_validation.value = 'Invalid email'
    return false
  } else if (form.password === '' && prop.mode === 'create') {
    fail_validation.value = 'Please enter password'
    return false
  } else if (form.department === ''){
    fail_validation.value = 'Please choice a department'
    return false
  }
  return true
}

function createUser() {
  if (validate()) {
    axios.post(
        `http://127.0.0.1:8000/api/create-user/`,
        {
          name: form.name,
          email: form.email,
          password: form.password,
          password_confirmation: form.confirmPassword,
          department_id: form.department,
          address: form.address,
          DOB: form.DOB,
          phone_number: form.phone_number,
          salary: form.salary,
          position: form.position,
          role: form.role,
        },
        {
          headers: {Authorization: `Bearer ${user.token}`},
        }
    ).then(function () {
      emits('update_data')
      alertStore.alert = true
      alertStore.type = 'success'
      alertStore.msg = 'Create success'
    }).catch((e) => {
      alertStore.alert = true
      alertStore.type = 'error'
      alertStore.msg = 'Something went wrong'
      console.log(e)
    })
  }
}

function updateUser() {
  if (validate()) {
    axios
        .put(
            `http://127.0.0.1:8000/api/update-user/${prop.user_id}`,
            {
              name: form.name,
              email: form.email,
              department_id: form.department,
              address: form.address,
              DOB: form.DOB,
              phone_number: form.phone_number,
              salary: form.salary,
              position: form.position,
              role: form.role,
            },
            {
              headers: {Authorization: `Bearer ${user.token}`},
            }
        ).then(function (){
          emits('update_data')
          alertStore.alert = true
          alertStore.type = 'success'
          alertStore.msg = 'Create success'
        }).catch((e) => {
          alertStore.alert = true
          alertStore.type = 'error'
          alertStore.msg = 'Something went wrong'
          console.log(e)
        })
  }
}
</script>
