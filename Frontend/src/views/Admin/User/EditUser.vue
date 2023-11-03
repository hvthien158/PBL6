<template>
  <main>
    <SlideBar></SlideBar>
    <div class="add-user">
      <div class="input-container">
        <h1 v-if="!isUpdateUser">Add new account</h1>
        <h1 v-else>Update user</h1>
        <div class="form-floating">
          <input
            v-model="dataPost.email"
            type="text"
            class="form-control"
            :class="{ 'is-invalid': !checkEmail }"
            placeholder="Email"
            required
          />
        </div>
        <div class="invalid-feedback" v-if="!checkEmail">
          Please enter valid email
        </div>
        <div class="form-floating is-invalid">
          <input
            v-model="dataPost.name"
            type="text"
            class="form-control"
            :class="{ 'is-invalid': !checkName }"
            placeholder="Name"
            required
          />
        </div>
        <div class="invalid-feedback" v-if="!checkName">
          Please enter valid name
        </div>
        <div class="form-floating" v-if="!isUpdateUser">
          <input
            v-model="dataPost.password"
            type="password"
            class="form-control"
            :class="{ 'is-invalid': !checkPassword }"
            placeholder="Password"
            required
          />
        </div>
        <div class="invalid-feedback" v-if="!checkPassword && !isUpdateUser" >
          Please enter password > 6
        </div>
        <div class="form-floating">
          <input
            type="text"
            v-model="dataPost.salary"
            class="form-control"
            placeholder="Salary"
            required
          />
        </div>
        <div class="form-floating">
          <select class="custom-select" v-model="dataPost.position">
            <option v-for="options in position" :value="options.value">
              {{ options.name }}
            </option>
          </select>
        </div>
        <div class="form-floating">
          <select class="custom-select" v-model="dataPost.role">
            <option v-for="options in role" :value="options.value">
              {{ options.name }}
            </option>
          </select>
        </div>
        <select
          class="custom-select"
          v-model="dataPost.department"
          :class="{ 'is-invalid': !dataPost.department }"
          required
        >
          <option :value="null" disabled selected hidden>
            Choose department
          </option>
          <option v-for="options in data" :value="options.id">
            {{ options.name }}
          </option>
        </select>
        <div class="invalid-feedback" v-if="!dataPost.department">
          Please choose a department
        </div>
        <div class="btn-submit">
          <button v-if="!isUpdateUser" type="button" class="btn btn-primary" @click="createUser">
            Create
          </button>
          <button v-else type="button" class="btn btn-primary" @click="updateUser">
            Update
          </button>
        </div>
      </div>
    </div>
  </main>
</template>
<style scoped>
main {
  min-height: 100vh;
  border-top: 0.1em solid black;
  box-sizing: border-box;
  display: flex;
}
.add-user {
  width: 85vw;
  display: flex;
  justify-content: center;
  padding-bottom: 32px;
}
.input-container {
  width: 40%;
}
.add-user h1 {
  text-align: center;
}
.input-group-text {
  width: 25%;
}
.form-floating {
  margin-bottom: 10px;
}
.invalid-feedback {
  display: block;
  margin-bottom: 5px;
}
.btn-submit {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}
</style>
<script setup>
import SlideBar from "../../../components/SlideBar.vue";
import axios from "axios";
import { ref, reactive, onMounted, computed } from "vue";
import {useRoute} from 'vue-router'
import {useUserStore} from "../../../stores/user";
import router from '../../../router'
const user = useUserStore().user
const route = useRoute()
let isUpdateUser = false
let data = ref();
let dataPost = reactive({
  email: "",
  name: "",
  password: "",
  confirmPassword: "",
  address: "",
  DOB: "",
  phoneNumber: "",
  avatar: "",
  salary: "",
  position: 'intern',
  role: "admin",
  department: 1,
});
const role = [
  {
    name: "Admin",
    value: "admin",
  },
  {
    name: "User",
    value: 'user',
  },
];
const position = [
  {
    name: 'Intern',
    value: "intern",
  },
  {
    name: 'Fresher',
    value: "fresher",
  },
  {
    name: 'Junior',
    value: "junior",
  },
  {
    name: 'Senior',
    value: "senior",
  },
];
onMounted(() => {
  displayDepartment();
  isUpdateUser = (route.path === `/admin/update-user/${route.params.id}`)
  if(isUpdateUser) {
    displayUser()
  }
});
const displayUser = async () => {
    try {
        await axios.get(`http://127.0.0.1:8000/api/user/${route.params.id}`,{
            headers : { Authorization: `Bearer ${user.token}`}
        }).then(function(response){
            console.log(response)
            dataPost.email = response.data.data[0].email
            dataPost.name = response.data.data[0].name
            dataPost.address = response.data.data[0].address
            if(response.data.data[0].phoneNumber) dataPost.phoneNumber = response.data.data[0].phoneNumber
            dataPost.DOB = response.data.data[0].DOB
        })
    } catch(e) {
        console.log(e);
    }
}
const displayDepartment = async () => {
  try {
    await axios
      .get("http://127.0.0.1:8000/api/department")
      .then(function (response) {
        data.value = response.data.data;
      });
  } catch (e) {
    console.log(e);
  }
};

const checkName = computed(() => {
  return dataPost.name.length !== 0;
});
const checkEmail = computed(() => {
  return isEmail(dataPost.email);
});
const checkPassword = computed(() => {
  return dataPost.password.length > 6;
});
const checkConfirmPassword = computed(() => {
  return dataPost.password === dataPost.confirmPassword;
});
const checkPhoneNumber = computed(() => {
  return isPhoneNumber(dataPost.phoneNumber) || dataPost.phoneNumber.length === 0;
});
const isEmail = (email) => {
  let filter =
    /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return filter.test(email);
};
const isPhoneNumber = (phoneNumber) => {
  let filter = /^([0-9]{10})+$/;
  return filter.test(phoneNumber);
};
const createUser = async () => {
  if (
    checkEmail &&
    checkName &&
    checkPhoneNumber &&
    checkPassword &&
    checkConfirmPassword &&
    dataPost.department
  ) {
    try { 
      console.log(`Bearer ${user.token}`)
      await axios.post(`http://127.0.0.1:8000/api/create-user/`, {
        name : dataPost.name,
        email: dataPost.email,
        password: dataPost.password,
        department_id: dataPost.department,
        salary: dataPost.salary,
        position: dataPost.position,
        role: dataPost.role
      },{
        headers : { Authorization : `Bearer ${user.token}`}
      }).then(function(response){
        if(response.status === 200) {
          router.push({path : '/admin/list-user'})
        }
      });
    } catch (e) {
      console.log(e);
    }
  }
};
const updateUser = async () => {
  if (
    checkEmail &&
    checkName &&
    checkPhoneNumber &&
    dataPost.department
  ) {
    try { 
      console.log(`Bearer ${user.token}`)
      await axios.put(`http://127.0.0.1:8000/api/update-user/${route.params.id}`, {
        name : dataPost.name,
        email: dataPost.email,
        department_id: dataPost.department,
        address: dataPost.address,
        DOB: dataPost.DOB,
        phone_number: dataPost.phoneNumber,
        salary: dataPost.salary,
        position: dataPost.position,
        role: dataPost.role
      },{
        headers : { Authorization : `Bearer ${user.token}`}
      }).then(function(response){
        if(response.status === 200) {
          router.push({path : '/admin/list-user'})
        }
      });
    } catch (e) {
      console.log(e);
    }
  }
};

</script>
