<template>
  <main>
    <SlideBar></SlideBar>
    <div class="add-user">
      <div class="input-container" @keyup.enter="handleEnterKey">
        <h1 v-if="!isUpdateUser">Add User</h1>
        <h1 v-else>Update User</h1>
        <div class="form-floating">
          <input
            v-model="dataPost.email"
            type="text"
            class="form-control"
            :class="{
              'is-invalid': checkEmail !== '',
              'is-valid': checkEmail === '',
            }"
            placeholder="Email"
            required
          />
        </div>
        <div class="invalid-feedback">
          {{ checkEmail }}
        </div>
        <div class="form-floating is-invalid">
          <input
            v-model="dataPost.name"
            type="text"
            class="form-control"
            :class="{
              'is-invalid': checkName !== '',
              'is-valid': checkName === '',
            }"
            placeholder="Name"
            required
          />
        </div>
        <div class="invalid-feedback">
          {{ checkName }}
        </div>
        <div class="form-floating" v-if="!isUpdateUser">
          <input
            v-model="dataPost.password"
            type="password"
            class="form-control"
            :class="{
              'is-invalid': checkPassword !== '',
              'is-valid': checkPassword === '',
            }"
            placeholder="Password"
            required
          />
        </div>
        <div class="invalid-feedback" v-if="!isUpdateUser">
          {{ checkPassword }}
        </div>
        <div class="form-floating" v-if="!isUpdateUser">
          <input
            v-model="dataPost.confirmPassword"
            type="password"
            class="form-control"
            :class="{
              'is-invalid': checkConfirmPassword !== '',
              'is-valid': checkConfirmPassword === '',
            }"
            placeholder="Confirmation Password"
            required
          />
        </div>
        <div class="invalid-feedback" v-if="!isUpdateUser">
          {{ checkConfirmPassword }}
        </div>
        <div class="form-floating is-invalid">
          <input
            type="text"
            v-model="dataPost.address"
            class="form-control"
            placeholder="Address"
            required
          />
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">Date of birth</span>
          <input type="date" class="form-control" v-model="dataPost.DOB" />
        </div>
        <div class="form-floating">
          <input
            v-model="dataPost.phone_number"
            type="text"
            class="form-control"
            placeholder="Phone Number"
            :class="{
              'is-invalid': checkPhoneNumber !== '',
              'is-valid': checkPhoneNumber === '',
            }"
            required
          />
        </div>
        <div class="invalid-feedback">
          {{ checkPhoneNumber }}
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
          <select
            class="custom-select"
            v-model="dataPost.department"
            :class="{
              'is-invalid': checkDepartment !== '',
              'is-valid': checkDepartment,
            }"
            required
          >
            <option v-for="options in data" :value="options.id">
              {{ options.name }}
            </option>
          </select>
        </div>
        <div class="invalid-feedback">
          {{ checkDepartment }}
        </div>
        <div class="btn-submit">
          <button
            v-if="!isUpdateUser"
            type="button"
            class="btn btn-primary"
            @click="createUser"
          >
            Create
          </button>
          <button
            v-else
            type="button"
            class="btn btn-primary"
            @click="updateUser"
          >
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
import { useRoute } from "vue-router";
import { useUserStore } from "../../../stores/user";
import router from "../../../router";
import { useAlertStore } from "../../../stores/alert";

const user = useUserStore().user;
const route = useRoute();
const alertStore = useAlertStore();
let isUpdateUser = false;
let checkLanding = reactive({
  name: false,
  email: false,
  password: false,
  confirmPassword: false,
  phone_number: true,
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
  phone_number: "",
  avatar: "",
  salary: "",
  position: "",
  role: "",
  department: "",
});
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
onMounted(() => {
  displayDepartment();
  isUpdateUser = (route.path === `/admin/update-user/${route.params.id}`)
  if(isUpdateUser) {
    displayUser()
  }
});
const displayUser = async () => {
  try {
    await axios
      .get(`http://127.0.0.1:8000/api/user/${route.params.id}`, {
        headers: { Authorization: `Bearer ${user.token}` },
      })
      .then(function (response) {
        dataPost.email = response.data.data[0].email;
        dataPost.name = response.data.data[0].name;
        dataPost.address = response.data.data[0].address;
        dataPost.phone_number = response.data.data[0].phone_number;
        dataPost.DOB = response.data.data[0].DOB;
        dataPost.salary = response.data.data[0].salary;
        dataPost.position = response.data.data[0].position;
        dataPost.role =
          response.data.data[0].role === "admin" ? "admin" : "user";
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
      });
  } catch (e) {
    console.log(e);
  }
};

const checkName = computed(() => {
  if (dataPost.name.length <= 4) {
    checkLanding.name = false;
    return "Please enter name > 4";
  } else {
    checkLanding.name = true;
    return "";
  }
});
const checkEmail = computed(() => {
  if (!isEmail(dataPost.email)) {
    checkLanding.email = false;
    return "Please enter valid email";
  } else {
    checkLanding.email = true;
    return "";
  }
});
const checkPassword = computed(() => {
  if (dataPost.password.length <= 6) {
    checkLanding.password = false;
    return "Please enter password more than 6 character";
  } else {
    checkLanding.password = true;
    return "";
  }
});
const checkConfirmPassword = computed(() => {
  if (dataPost.password !== dataPost.confirmPassword) {
    checkLanding.confirmPassword = false;
    return "Password not match";
  } else {
    checkLanding.confirmPassword = true;
    return "";
  }
});
const checkPhoneNumber = computed(() => {
  if (!isPhoneNumber(dataPost.phone_number) && !(dataPost.phone_number.length === 0)) {
    checkLanding.phone_number = false;
    return "Please enter valid phone number";
  } else {
    checkLanding.phone_number = true;
    return "";
  }
});
const checkDepartment = computed(() => {
  if (dataPost.department === "") {
    checkLanding.department = false;
    return "Please enter department";
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
const isPhoneNumber = (phone_number) => {
  let filter = /^([0-9]{10})+$/;
  return filter.test(phone_number);
};
const createUser = async () => {
  if (
    checkLanding.name &&
    checkLanding.email &&
    checkLanding.password &&
    checkLanding.confirmPassword &&
    checkLanding.department &&
    checkLanding.phone_number
  ) {
    try {
      await axios
        .post(
          `http://127.0.0.1:8000/api/create-user/`,
          {
            name: dataPost.name,
            email: dataPost.email,
            password: dataPost.password,
            password_confirmation: dataPost.confirmPassword,
            department_id: dataPost.department,
            address: dataPost.address,
            DOB: dataPost.DOB,
            phone_number: dataPost.phone_number,
            salary: dataPost.salary,
            position: dataPost.position,
            role: dataPost.role,
          },
          {
            headers: { Authorization: `Bearer ${user.token}` },
          }
        )
        .then(function (response) {
          if (response.status === 200) {
            message('success', "Create success");
            router.push({ path: "/admin/list-user" });
          }
        });
    } catch (e) {
      message("error", "Something wrong");
    }
  } else {
    message("error", "Please enter valid full information");
  }
};
const updateUser = async () => {
  if (
    checkLanding.name &&
    checkLanding.email &&
    checkLanding.department &&
    checkLanding.phone_number
  ) {
    try {
      console.log(`Bearer ${user.token}`);
      await axios
        .put(
          `http://127.0.0.1:8000/api/update-user/${route.params.id}`,
          {
            name: dataPost.name,
            email: dataPost.email,
            department_id: dataPost.department,
            address: dataPost.address,
            DOB: dataPost.DOB,
            phone_number: dataPost.phone_number,
            salary: dataPost.salary,
            position: dataPost.position,
            role: dataPost.role,
          },
          {
            headers: { Authorization: `Bearer ${user.token}` },
          }
        )
        .then(function (response) {
          message("success", "Update success");
          router.push({ path: "/admin/list-user" });
        });
    } catch (e) {
      console.log(e);
      message("error", "Something wrong");
    }
  } else {
    message("error", "Please enter full information");
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
