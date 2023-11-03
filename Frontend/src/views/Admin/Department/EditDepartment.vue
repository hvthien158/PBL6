<template>
  <main>
    <SlideBar></SlideBar>
    <div class="add-department">
      <div class="input-container">
        <h1 v-if="!isUpdateDepartment">Add department</h1>
        <h1 v-else>Edit department</h1>
        <div class="form-floating">
          <input
            v-model="dataDepartment.departmentName"
            type="text"
            class="form-control"
            :class="{
              'is-invalid': !dataDepartment.departmentName,
              'is-valid': dataDepartment.departmentName,
            }"
            placeholder="Department name"
            required
          />
        </div>
        <div class="invalid-feedback" v-if="!dataDepartment.departmentName">
          Please field valid department
        </div>
        <div class="form-floating">
          <input
            v-model="dataDepartment.address"
            type="text"
            class="form-control"
            placeholder="Address"
            :class="{
              'is-invalid': !dataDepartment.address,
              'is-valid': dataDepartment.address,
            }"
            required
          />
        </div>
        <div class="invalid-feedback" v-if="!dataDepartment.address">
          Please field valid department
        </div>
        <div class="form-floating">
          <input
            v-model="dataDepartment.email"
            type="text"
            class="form-control"
            :class="{ 'is-invalid': !checkEmail, 'is-valid': checkEmail }"
            placeholder="Email"
            required
          />
        </div>
        <div class="invalid-feedback" v-if="!checkEmail">Please field valid email</div>
        <div class="form-floating">
          <input
            v-model="dataDepartment.phoneNumber"
            type="text"
            class="form-control"
            :class="{
              'is-valid': !checkPhoneNumber,
              'is-valid': checkPhoneNumber,
            }"
            placeholder="Phone number"
            required
          />
        </div>
        <div class="invalid-feedback" v-if="!checkPhoneNumber">
          Please field valid phone number
        </div>
        <div class="btn-submit" v-if="!isUpdateDepartment">
          <button
            @click="createDepartment"
            type="button"
            class="btn btn-primary"
          >
            Add department
          </button>
        </div>
        <div class="btn-submit" v-else>
          <button
            @click="updateDepartment"
            type="button"
            class="btn btn-primary"
          >
            Edit department
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
.add-department h1 {
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
  if (route.path == `/admin/update-department/${route.params.id}`) {
    isUpdateDepartment = true;
    displayDepartment();
  } else isUpdateDepartment = false;
});
const dataDepartment = reactive({
  departmentName: "",
  address: "",
  email: "",
  phoneNumber: "",
});
const checkEmail = computed(() => {
  return !isEmail(dataDepartment.email) ? false : true;
});
const isEmail = (email) => {
  let filter =
    /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return filter.test(email);
};
const checkPhoneNumber = computed(() => {
  return !isPhoneNumber(dataDepartment.phoneNumber) ? false : true;
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
    dataDepartment.phoneNumber
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
      errorMessage('error',e.response.data.message)
      console.log(e);
    }
  } else {
    errorMessage('error','Please field all info')
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
          response.status == 200
            ? router.push({ path: "/admin/list-department" })
            : console.log(response);
        });
    } catch (e) {
      errorMessage('error',e.message)
      console.log(e);
    }
  } else {
    errorMessage('error','Please field all info')
  }
};
const errorMessage = (type,msg) => {
  alertStore.alert = true;
  alertStore.type = type;
  alertStore.msg = msg;
};
</script>
