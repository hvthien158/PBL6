<template>
  <main>
    <div class="container">
      <div v-if="verifyQuest">
        <p style="font-weight: bold">A confirmation email has been sent to your email, please confirm to continue...</p>
      </div>
      <div class="form-container" v-else>
        <h3>LOGIN</h3>
        <div class="form-input">
          <div class="form-info">
            <div class="label-info">
              <label>Email</label>
            </div>
            <div class="input-info">
              <input @keyup.enter="passwordInput.focus()" v-model="info.email" type="text" />
              <h5>{{ checkEmail }}</h5>
            </div>
          </div>
          <div class="form-info">
            <div class="label-info">
              <label>Password</label>
            </div>
            <div class="input-info">
              <input @keyup.enter="login" ref="passwordInput" v-model="info.password" type="password" />
              <h5>{{ checkPassword }}</h5>
            </div>
          </div>
          <div class="btn-submit">
            <button type="submit" @click="login">Login</button>
          </div>
        </div>
        <div v-if="fail_login">
          <span style="color: red">Wrong email or password</span>
        </div>
        <div style="margin-top: 12px">
          <span id="forgot" @click="goToForgot">Forgot password?</span>
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
}
.form-container {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
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
  background-color: white;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  width: 400px;
}

.form-info {
  display: flex;
  margin-bottom: 10px;
}

.form-info .label-info {
  display: flex;
  align-items: center;
  width: 150px;
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
#forgot:hover{
  color: blue;
  cursor: pointer;
}
</style>

<script setup>
import { reactive, computed } from "vue";
import axios from "axios";
import router from "../router";
import {ref} from "vue";
import {useUserStore} from "../stores/user";
import {useAlertStore} from "../stores/alert";

const verifyQuest = ref(false)
const alertStore = useAlertStore()
const user = useUserStore().user
const fail_login = ref(false)
const passwordInput = ref(null)

if(user.token !== ''){
  router.push({name : 'home'})
}
let info = reactive({
  email: "",
  password: "",
});
const checkEmail = computed(() => {
  if(info.email === ''){
    return "Please enter your email"
  } else if (!isEmail(info.email)) {
    return "Invalid email";
  } else {
    return "";
  }
});
const checkPassword = computed(() => {
  if (info.password.length === 0) {
    return "Please enter your password";
  } else {
    return "";
  }
});
const isEmail = (email) => {
  let filter =
      /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return filter.test(email);
};
const login = async () => {
  if (checkEmail.value === '' && checkPassword.value === '') {
    try {
      await axios
          .post('http://127.0.0.1:8000/api/login', {
            email: info.email,
            password: info.password,
          })
          .then(function (response) {
            if(response.data.verify_quest){
              verifyQuest.value = true
            } else {
              console.log(response)
              user.id = response.data.user.id
              user.token = response.data.access_token
              user.name = response.data.user.name
              user.email = response.data.user.email
              user.password = response.data.user.password
              user.address = response.data.user.address
              user.DOB = response.data.user.DOB
              user.phone_number = response.data.user.phone_number
              user.avatar = response.data.user.avatar
              user.expired = response.data.expires_at
              user.role = response.data.user.role

              //alert success
              alertStore.alert = true
              alertStore.type = 'success'
              alertStore.msg = 'Logged in'

              router.push({ name: 'home' });
            }
          });
    } catch (e) {
      fail_login.value = true
      console.log(e);
    }
  }
};

function goToForgot(){
  router.push({
    name: 'forgot-password'
  })
}
</script>
