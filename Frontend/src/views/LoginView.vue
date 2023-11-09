<template>
  <main>
    <div class="container">
      <div v-if="verifyQuest">
        <p style="font-weight: bold">A confirmation email has been sent to your email, please confirm to continue...</p>
      </div>
      <div class="form-container" v-else>
        <div class="form-input">
          <h3 style="text-align: center; font-weight: 700">LOGIN</h3>
          <div class="login__field">
            <i class="login__icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
              </svg>
            </i>
            <input
                v-model="info.email"
                type="text"
                class="login__input"
                placeholder="Email"
                @blur="checkmail"
                @input="checkmail"
                @keyup.enter="passwordInput.focus()"
            >
            <span class="error">{{ email_error }}</span>
          </div>
          <div class="login__field">
            <i class="login__icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16">
                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
              </svg>
            </i>
            <input
                ref="passwordInput"
                v-model="info.password"
                type="password"
                class="login__input"
                placeholder="Password"
                @blur="checkpass"
                @input="checkpass"
                @keyup.enter="login"
            >
            <span class="error">{{ password_error }}</span>
          </div>
          <div class="login__field">
            <ButtonLoading :loading="loading" @click="login" style="font-size: 15px" size="large" type="warning" round>Login</ButtonLoading>
            <span @click="goToForgot" class="login__forgot">Forgot password?</span>
          </div>
          <p class="fail-login" v-if="fail_login">Wrong email or password</p>
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped>
main {
  max-width: 100vw;
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
.form-container h3 {
  color: #e06230;
  margin-bottom: 20px
}
.form-input {
  display: block;
  padding: 50px;
  background-color: white;
  border-radius: 16px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  width: 400px;
}

.login__field {
  padding: 20px 0;
  position: relative;
}

.login__icon {
  position: absolute;
  top: 30px;
  color: #7875B5;
}

.login__input {
  border: none;
  border-bottom: 2px solid #D1D1D4;
  background: none;
  padding: 10px 10px 10px 24px;
  width: 95%;
  font-weight: 700;
  transition: .2s;
}

.login__input:active,
.login__input:focus,
.login__input:hover {
  outline: none;
  border-bottom-color: #6A679E;
}

.login__forgot{
  position: absolute;
  right: 0;
  bottom: 30px;
  font-size: 15px;
  font-weight: 600;
}

.login__forgot:hover{
  color: #7875B5;
  cursor: pointer;
}

.error{
  color: red;
  font-size: small;
  position: absolute;
  left: 25px;
  top: 66px;
}

.fail-login{
  color: red;
  font-size: 14px;
  text-align: center;
  position: absolute;
  left: calc(50% - 80px);
}
</style>

<script setup>
import { reactive, computed } from "vue";
import axios from "axios";
import router from "../router";
import {ref} from "vue";
import {useUserStore} from "../stores/user";
import {useAlertStore} from "../stores/alert";
import ButtonLoading from "../components/ButtonLoading.vue";

const verifyQuest = ref(false)
const alertStore = useAlertStore()
const user = useUserStore().user
const fail_login = ref(false)
const passwordInput = ref(null)
const email_error = ref('')
const password_error = ref('')
const loading = ref(false)

if(user.token !== ''){
  router.push({name : 'home'})
}
let info = reactive({
  email: "",
  password: "",
});
const isEmail = (email) => {
  let filter =
      /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return filter.test(email);
};

function checkmail() {
  email_error.value = (info.email.length === 0) ? 'Please enter your email' : ''
}

function checkpass() {
  password_error.value = (info.password.length === 0) ? 'Please enter your password' : ''
}

const login = async () => {
  loading.value = true
  if(!isEmail(info.email)){
    email_error.value = 'Invalid email'
    loading.value = false
  } else if (email_error.value === '' && password_error.value === '') {
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
              user.id = response.data.user.id
              user.token = response.data.access_token
              user.name = response.data.user.name
              user.email = response.data.user.email
              user.password = response.data.user.password
              user.address = response.data.user.address
              user.DOB = response.data.user.DOB
              user.phone_number = response.data.user.phone_number
              user.avatar = response.data.user.avatar
              user.position = response.data.user.position
              user.expired = response.data.expires_at
              user.role = response.data.user.role
              //alert success
              alertStore.alert = true
              alertStore.type = 'success'
              alertStore.msg = 'Login success'
              router.push({ name: "home" });
            }
          });
    } catch (e) {
      fail_login.value = true
      loading.value = false
    }
  }
};

function goToForgot(){
  router.push({
    name: 'forgot-password'
  })
}
</script>
