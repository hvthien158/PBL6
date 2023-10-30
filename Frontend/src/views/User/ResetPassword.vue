<template>
  <main>
    <div class="container">
      <div class="form-container">
        <h3>Đặt lại mật khẩu</h3>
        <div class="form-input">
          <div class="form-info">
            <div class="label-info">
              <label>Mật khẩu mới</label>
            </div>
            <div class="input-info">
              <input v-model="info.password" type="password" />
              <h5>{{ checkPassword }}</h5>
            </div>
          </div>
          <div class="form-info">
            <div class="label-info">
              <label>Nhập lại mật khẩu</label>
            </div>
            <div class="input-info">
              <input v-model="info.password_confirm" type="password" />
              <h5>{{ checkPasswordConfirm }}</h5>
            </div>
          </div>
          <div class="btn-submit">
            <button type="submit" @click="login">Đặt lại</button>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped>
main {
  max-width: 100vw;
  min-height: 100vh;
}
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 80vh;
  min-height: 80vh;
  background-color: #f2f2f2;
}
.form-container {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  margin-bottom: 100px;
}
.additional-content {
  text-align: center;
  margin-top: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.additional-content p {
  margin-bottom: 10px;
}

.form-input {
  display: block;
  padding: 20px;
  background-color: #f2f2f2;
  border-radius: 10px;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
  width: 300px;
}

.form-info {
  display: flex;
  margin-bottom: 10px;
}

.form-info .label-info {
  display: flex;
  align-items: center;
  width: 80px;
  padding-right: 5px;
  color: #333333;
  font-weight: bold;
}

.form-input input {
  padding: 10px;
  width: 100%;
  border-radius: 2px;
  display: block;
  border: 1px solid #999999;
  transition: border-color 0.3s ease-in-out;
}

.form-input input:focus {
  outline: none;
  border-color: #62d58c;
}

.btn-submit {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

.btn-submit button {
  width: 100%;
  height: 40px;
  border-radius: 5px;
  background-color: #62d58c;
  color: #ffffff;
  font-weight: bold;
  cursor: pointer;
  border: none;
  transition: background-color 0.3s ease-in-out;
}

.btn-submit button:hover {
  background-color: #3ca66e;
}

h5 {
  margin-top: 5px;
  color: rgb(214, 34, 34);
  font-size: 14px;
}
</style>

<script setup>
import { reactive, computed } from "vue";
import axios from "axios";
import router from "../../router";
import {ref} from "vue";
import {useUserStore} from "../../stores/user";
import {useRoute} from "vue-router";

const user = useUserStore().user
const route = useRoute()

if(user.token !== ''){
  router.push({name : 'home'})
}
let info = reactive({
  token: route.query.token,
  email: route.query.email,
  password: "",
  password_confirm: "",
});
const checkPassword = computed(() => {
  if (info.password.length === 0) {
    return "Vui lòng nhập mật khẩu";
  } else {
    return "";
  }
});
const checkPasswordConfirm = computed(() => {
  if (info.password_confirm.length === 0) {
    return "Vui lòng nhập mật khẩu";
  } else if (info.password_confirm !== info.password) {
    return "Mật khẩu xác nhận không trùng khớp"
  } else {
    return "";
  }
});
const login = async () => {
  if (info.email && info.password) {
    try {
      await axios
          .post('http://127.0.0.1:8000/api/reset-password', {
            token: info.token,
            email: info.email,
            password: info.password,
            password_confirmation: info.password_confirm,
          })
          .then(function () {
            //component alert success
            router.push({
              name: 'login'
            })
          });
    } catch (e) {
      console.log(e);
    }
  }
};
</script>
