<template>
  <main>
    <SlideBar></SlideBar>
    <div class="add-department">
      <el-form class="input-container">
        <h2 v-if="!isUpdateDepartment">Add department</h2>
        <h2 v-else>Edit department</h2>
        <el-form-item>
          <el-input v-model="dataDepartment.departmentName" placeholder="Department name" clearable />
        </el-form-item>
        <div class="invalid-feedback">
          {{ checkName }}
        </div>
        <el-form-item>
          <el-input v-model="dataDepartment.address" placeholder="Address" clearable />
        </el-form-item>
        <div class="invalid-feedback">
          {{ checkAddress }}
        </div>
        <el-form-item>
          <el-input v-model="dataDepartment.email" placeholder="Email" clearable />
        </el-form-item>
       
        <div class="invalid-feedback">{{ checkEmail }}</div>
        <el-form-item>
          <el-input v-model="dataDepartment.phoneNumber" placeholder="Phone Number" clearable />
        </el-form-item>
        <div class="invalid-feedback">
          {{ checkPhoneNumber }}
        </div>
        <div class="btn-submit" v-if="!isUpdateDepartment">
          <button @click="createDepartment" type="button" class="btn btn-primary">
            Add department
          </button>
        </div>
        <div class="btn-submit" v-else>
          <button @click="updateDepartment" type="button" class="btn btn-primary">
            Edit department
          </button>
        </div>
      </el-form>
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

.el-card {
  width: 100%;
  height: 100%;
}

.card-content {
  display: flex;
  justify-content: center;
}
.el-form-item{
  margin-bottom: 5px
}
.add-department {
  width: 85vw;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 100px;
}

.add-department h1 {
  color: black;
}

.input-container {
  width: 40%;
}

.input-content {
  display: block;
}

.add-department h2 {
  text-align: center;
  margin-bottom: 30px;
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
import { useUserStore } from "../../../stores/user";
import { useRoute } from "vue-router";
import router from "../../../router";
import { useAlertStore } from "../../../stores/alert";
const alertStore = useAlertStore();
const user = useUserStore().user;
const route = useRoute();
let isUpdateDepartment = false;
onMounted(() => {
  if (route.path === `/admin/update-department/${route.params.id}`) {
    isUpdateDepartment = true;
    displayDepartment();
  } else isUpdateDepartment = false;
});
const dataDepartment = reactive({
  departmentName: null,
  address: null,
  email: null,
  phoneNumber: null,
});
const checkName = computed(() => {
  if (dataDepartment.departmentName == '') {
    return `Please enter department's name`
  } else {
    return ''
  }
})
const checkAddress = computed(() => {
  if (dataDepartment.address == '') {
    return `Please enter department's address`
  } else {
    return ''
  }
})
const checkEmail = computed(() => {
  if (dataDepartment.email == null || isEmail(dataDepartment.email)) {
    return ''
  } else {
    if (dataDepartment.email == '') {
      return `Please enter department's email`
    } else if (!isEmail(dataDepartment.email)) {
      return `Please enter valid email`
    }
  }
});
const isEmail = (email) => {
  let filter =
    /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return filter.test(email);
};
const checkPhoneNumber = computed(() => {
  if (dataDepartment.phoneNumber == null || (isPhoneNumber(dataDepartment.phoneNumber))) {
    return ''
  } else {
    if (dataDepartment.phoneNumber == '') {
      return `Please enter department's phone number`
    } else if (!(isPhoneNumber(dataDepartment.phoneNumber))) {
      return `Please enter valid phone number`
    }
  }
});
const isPhoneNumber = (phoneNumber) => {
  let filter = /^([0-9]{10})+$/;
  return filter.test(phoneNumber);
};
const createDepartment = async () => {
  if (
    dataDepartment.departmentName &&
    dataDepartment.address &&
    dataDepartment.email &&
    dataDepartment.phoneNumber &&
    checkEmail.value == '' &&
    checkPhoneNumber.value == ''
  ) {
    try {
      await axios
        .post(
          "http://127.0.0.1:8000/api/create-department",
          {
            departmentName: dataDepartment.departmentName,
            address: dataDepartment.address,
            email: dataDepartment.email,
            phoneNumber: dataDepartment.phoneNumber,
          },
          {
            headers: { Authorization: `Bearer ${user.token}` },
          }
        )
        .then(function (response) {
          console.log(response);
          router.push({ path: "/admin/list-department" });
        });
    } catch (e) {
      errorMessage('error', e.response.data.message)
      console.log(e);
    }
  } else {
    if (dataDepartment.departmentName == null) dataDepartment.departmentName = ''
    if (dataDepartment.address == null) dataDepartment.address = ''
    if (dataDepartment.email == null) dataDepartment.email = ''
    if (dataDepartment.phoneNumber == null) dataDepartment.phoneNumber = ''
    errorMessage('error', 'Please field all info')
  }
};
const displayDepartment = async () => {
  try {
    await axios
      .get(`http://127.0.0.1:8000/api/department/${route.params.id}`, {
        headers: { Authorization: `Bearer ${user.token}` },
      })
      .then(function (response) {
        if (response.status === 200) {
          let data = response.data.data[0];
          dataDepartment.departmentName = data.name;
          dataDepartment.address = data.address;
          dataDepartment.email = data.email;
          dataDepartment.phoneNumber = data.phoneNumber;
        }
      });
  } catch (e) {
    console.log(e);
  }
};
const updateDepartment = async () => {
  if (
    dataDepartment.departmentName &&
    dataDepartment.address &&
    dataDepartment.email &&
    dataDepartment.phoneNumber
  ) {
    try {
      await axios
        .put(
          `http://127.0.0.1:8000/api/update-department/${route.params.id}`,
          {
            departmentName: dataDepartment.departmentName,
            address: dataDepartment.address,
            email: dataDepartment.email,
            phoneNumber: dataDepartment.phoneNumber,
          },
          {
            headers: { Authorization: `Bearer ${user.token}` },
          }
        )
        .then(function (response) {
          response.status === 200
            ? router.push({ path: "/admin/list-department" })
            : console.log(response);
        });
    } catch (e) {
      errorMessage('error', e.message)
      console.log(e);
    }
  } else {
    errorMessage('error', 'Please enter all info')
  }
};
const errorMessage = (type, msg) => {
  alertStore.alert = true;
  alertStore.type = type;
  alertStore.msg = msg;
};
</script>
