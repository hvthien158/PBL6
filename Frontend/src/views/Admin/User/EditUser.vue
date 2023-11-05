<template>
  <main>
    <SlideBar></SlideBar>
    <div class="add-user">
      <div class="input-container" @keyup.enter="handleEnterKey">
        <h1 v-if="!isUpdateUser">Add User</h1>
        <h1 v-else>Update User</h1>
        <div class="form-floating">
          <input v-model="dataPost.email" type="text" class="form-control" placeholder="Email" required />
          <el-icon :color="currentColor">
            <WarningFilled />
          </el-icon>
        </div>

        <div class="invalid-feedback">
          {{ checkEmail }}
        </div>
        <div class="form-floating">
          <input v-model="dataPost.name" type="text" class="form-control" placeholder="Name" required />
          <el-icon :color="currentColor">
            <WarningFilled />
          </el-icon>
        </div>
        <div class="invalid-feedback">
          {{ checkName }}
        </div>
        <div v-if="isUpdateUser">
          <div class="form-floating">
            <input type="text" v-model="dataPost.address" class="form-control" placeholder="Address" required />
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text">Date of birth</span>
            <input type="date" class="form-control" v-model="dataPost.DOB" />
          </div>
          <div class="form-floating">
            <input v-model="dataPost.phoneNumber" type="text" class="form-control" placeholder="Phone Number" :class="{
              'is-invalid': !checkPhoneNumber == '',
              'is-valid': !checkPhoneNumber == '',
            }" required />
          </div>
        </div>
        <div class="invalid-feedback">
          {{ checkPhoneNumber }}
        </div>
        <div class="form-floating">
          <input type="text" v-model="dataPost.salary" class="form-control" placeholder="Salary" required />
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">Position</span>
          <select class="custom-select" v-model="dataPost.position">
            <option v-for="options in position" :value="options.value">
              {{ options.name }}
            </option>
          </select>
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">Role</span>
          <select class="custom-select" v-model="dataPost.role">
            <option v-for="options in role" :value="options.value">
              {{ options.name }}
            </option>
          </select>
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">Department</span>
          <select class="custom-select" v-model="dataPost.department" :class="{
            'is-invalid': !checkDepartment == '',
            'is-valid': checkDepartment,
          }" required>
            <option v-for="options in data" :value="options.id">
              {{ options.name }}
            </option>
          </select>
          <el-icon :color="currentColor">
            <WarningFilled />
          </el-icon>
        </div>
        <div class="invalid-feedback">
          {{ checkDepartment }}
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
  min-height: 82vh;
  border-top: 0.1em solid black;
  box-sizing: border-box;
  display: flex;
}

.add-user {
  width: 85vw;
  display: flex;
  justify-content: center;
  padding: 20px;
  /* align-items: center; */
}

.input-container {
  width: 40%;
}

.add-user h1 {
  text-align: center;
  color: black;
  margin: 20px 0 20px 0;
}

.input-group-text {
  width: 25%;
}

.form-floating {
  margin-bottom: 10px;
  display: flex;
  position: relative;
  align-items: center;
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

.mb-3 {
  position: relative;
  display: flex;
  align-items: center;
}

.el-icon {
  left: 100%;
  position: absolute;
  margin-left: 10px;
}
</style>
<script setup>
import SlideBar from "../../../components/SlideBar.vue";
import axios from "axios";
import { ref, reactive, onMounted, computed } from "vue";
import { useRoute } from "vue-router";
import { useUserStore } from "../../../stores/user";
import router from "../../../router";
import { useAlertStore } from "../../../stores/alert";
const user = useUserStore().user;
const route = useRoute();
const alertStore = useAlertStore();
let isUpdateUser = false;
let currentColor = '#f12727'
let checkLanding = reactive({
  name: false,
  email: false,
  password: false,
  confirmPassword: false,
  phoneNumber: true,
  department: false,
});
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
  position: "",
  role: "",
  department: "",
});
const role = [
  {
    name: 'Select role',
    value: '',
  },
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
    name: 'Select Position',
    value: '',
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
onMounted(() => {
  displayDepartment();
  isUpdateUser =
    route.path == `/admin/update-user/${route.params.id}` ? true : false;
  if (isUpdateUser) {
    displayUser();
  }
});
const displayUser = async () => {
  try {
    await axios
      .get(`http://127.0.0.1:8000/api/user/${route.params.id}`, {
        headers: { Authorization: `Bearer ${user.token}` },
      })
      .then(function (response) {
        console.log(response);
        dataPost.email = response.data.data[0].email;
        dataPost.name = response.data.data[0].name;
        dataPost.address = response.data.data[0].address;
        dataPost.phoneNumber = response.data.data[0].phoneNumber;
        dataPost.DOB = response.data.data[0].DOB;
        dataPost.salary = response.data.data[0].salary;
        dataPost.position = response.data.data[0].position;
        dataPost.role = response.data.data[0].role == "admin" ? "admin" : "user";
        dataPost.department = response.data.data[0].department.id;
      });
  } catch (e) {
    console.log(e);
  }
};
const displayDepartment = async () => {
  try {
    await axios
      .get("http://127.0.0.1:8000/api/department")
      .then(function (response) {
        data.value = response.data.data;
        dataPost.department = response.data.data[0].id
      });
  } catch (e) {
    console.log(e);
  }
};

const checkName = computed(() => {
  if (dataPost.name.length <= 4 && dataPost.name != "") {
    checkLanding.name = false;
    return "Please field name > 4";
  } else {
    checkLanding.name = true;
    return "";
  }
});
const checkEmail = computed(() => {
  if (!isEmail(dataPost.email) && dataPost.email != "") {
    checkLanding.email = false;
    return "Please field valid email";
  } else {
    checkLanding.email = true;
    return "";
  }
});
const checkPhoneNumber = computed(() => {
  if (!isPhoneNumber(dataPost.phoneNumber) && !(dataPost.phoneNumber.length == 0)) {
    checkLanding.phoneNumber = false;
    return "Please field valid phone number";
  } else {
    checkLanding.phoneNumber = true;
    return "";
  }
});
const checkDepartment = computed(() => {
  if (dataPost.department == "" && dataPost.department == null) {
    checkLanding.department = false;
    return "Please field department";
  } else {
    checkLanding.department = true;
    return "";
  }
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
    checkLanding.name &&
    checkLanding.email &&
    checkLanding.department
  ) {
    try {
      await axios
        .post(
          `http://127.0.0.1:8000/api/create-user/`,
          {
            name: dataPost.name,
            email: dataPost.email,
            department_id: dataPost.department,
            salary: dataPost.salary,
            position: dataPost.position,
            role: dataPost.role,
          },
          {
            headers: { Authorization: `Bearer ${user.token}` },
          }
        )
        .then(function (response) {
          if (response.status == 200) {
            message('success', response.data.message);
            router.push({ path: "/admin/list-user" });
          }
        });
    } catch (e) {
      message("error", e.response.data.message);
    }
  } else {
    message("error", "Please field valid full infomation");
  }
};
const updateUser = async () => {
  if (
    checkLanding.name &&
    checkLanding.email &&
    checkLanding.department &&
    checkLanding.phoneNumber
  ) {
    try {
      await axios
        .put(
          `http://127.0.0.1:8000/api/update-user/${route.params.id}`,
          {
            name: dataPost.name,
            email: dataPost.email,
            department_id: dataPost.department,
            address: dataPost.address,
            DOB: dataPost.DOB,
            phone_number: dataPost.phoneNumber,
            salary: dataPost.salary,
            position: dataPost.position,
            role: dataPost.role,
          },
          {
            headers: { Authorization: `Bearer ${user.token}` },
          }
        )
        .then(function (response) {
          message("success", response.data.message);
          router.push({ path: "/admin/list-user" });
        });
    } catch (e) {
      console.log(e);
      message("error", e.response.data.message);
    }
  } else {
    message("error", "Please field full infomation");
  }
};
const message = (type, msg) => {
  alertStore.alert = true;
  alertStore.type = type;
  alertStore.msg = msg;
};
const handleEnterKey = () => {
  if (!isUpdateUser) {
    createUser();
  } else {
    updateUser();
  }
};
</script>
