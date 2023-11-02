<template>
  <main>
    <SlideBar></SlideBar>
    <div class="add-department">
      <div class="input-container">
        <h1 v-if="isUpdateDepartment">Thêm cơ quan</h1>
        <h1 v-else>Chỉnh sửa cơ quan</h1>
        <div class="form-floating">
          <input
            v-model="dataDepartment.departmentName"
            type="text"
            class="form-control"
            :class="{
              'is-invalid': !dataDepartment.departmentName,
              'is-valid': dataDepartment.departmentName,
            }"
            placeholder="Tên cơ quan"
            required
          />
        </div>
        <div class="invalid-feedback" v-if="!dataDepartment.departmentName">
          Điền tên cơ quan hợp lệ
        </div>
        <div class="form-floating">
          <input
            v-model="dataDepartment.address"
            type="text"
            class="form-control"
            placeholder="Địa chỉ"
            :class="{
              'is-invalid': !dataDepartment.address,
              'is-valid': dataDepartment.address,
            }"
            required
          />
        </div>
        <div class="invalid-feedback" v-if="!dataDepartment.address">
          Điền địa chỉ hợp lệ
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
        <div class="invalid-feedback" v-if="!checkEmail">Điền email hợp lệ</div>
        <div class="form-floating">
          <input
            v-model="dataDepartment.phoneNumber"
            type="text"
            class="form-control"
            :class="{
              'is-valid': !checkPhoneNumber,
              'is-valid': checkPhoneNumber,
            }"
            placeholder="Số điện thoại"
            required
          />
        </div>
        <div class="invalid-feedback" v-if="!checkPhoneNumber">
          Điền số điện thoại hợp lệ
        </div>
        <div class="btn-submit" v-if="!isUpdateDepartment">
          <button
            @click="createDepartment"
            type="button"
            class="btn btn-primary"
          >
            Thêm Cơ Quan Mới
          </button>
        </div>
        <div class="btn-submit" v-else>
          <button
            @click="updateDepartment"
            type="button"
            class="btn btn-primary"
          >
            Chỉnh sửa
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
}
.input-container {
  width: 40%;
}
.add-department h1 {
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
import { useUserStore } from "../../../stores/user";
import { useRoute } from "vue-router";
import router from "../../../router";
const user = useUserStore().user;
const route = useRoute();
let isUpdateDepartment = false;
onMounted(() => {
  isUpdateDepartment =
    route.path == `/admin/update-department/${route.params.id}` ? true : false;
  displayDepartment();
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
      console.log(e);
    }
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
  try {
    await axios.put(
      `http://127.0.0.1:8000/api/update-department/${route.params.id}`,
      {
        departmentName: dataDepartment.departmentName,
        address: dataDepartment.address,
        email: dataDepartment.email,
        phoneNumber: dataDepartment.phoneNumber,
      },{
        headers : { Authorization : `Bearer ${user.token}`}
      }).then(function(response){
        response.status == 200 ? router.push({ path : '/admin/list-department'}) : console.log(response)
      }) 
  } catch (e) {
    console.log(e);
  }
};
</script>
