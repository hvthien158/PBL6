<template>
  <main>
    <div class="container">
      <div class="form-container">
        <div class="form-input">
          <h3 style="text-align: center; font-weight: 700; color: #e06230">CHANGE PASSWORD</h3>
          <div class="login__field">
            <i class="login__icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16">
                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
              </svg>
            </i>
            <input
                v-model="info.old_password"
                type="password"
                class="login__input"
                placeholder="Current password"
                @blur="checkpass"
                @input="checkpass"
                @keyup.enter="new_pass_input.focus()"
            >
            <span class="error">{{ password_error }}</span>
          </div>
          <div class="login__field">
            <i class="login__icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16">
                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
              </svg>
            </i>
            <input
                ref="new_pass_input"
                v-model="info.new_password"
                type="password"
                class="login__input"
                placeholder="New password"
                @blur="checkNewpass"
                @input="checkNewpass"
                @keyup.enter="confirm_input.focus()"
            >
            <span class="error">{{ new_password_error }}</span>
          </div>
          <div class="login__field">
            <i class="login__icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
              </svg>
            </i>
            <input
                ref="confirm_input"
                v-model="info.new_password_confirm"
                type="password"
                class="login__input"
                placeholder="Confirm"
                @blur="checkConfirm"
                @input="checkConfirm"
                @keyup.enter="openConfirmBox"
            >
            <span class="error">{{ confirm_error }}</span>
          </div>
          <div class="login__field" style="display: flex; justify-content: center">
            <ButtonLoading :loading="loading" @click="openConfirmBox" style="font-size: 15px;" size="large" type="warning" round>Change password</ButtonLoading>
          </div>
          <p class="fail-change" v-if="fail_change">Wrong current password</p>
        </div>
      </div>
    </div>
    <ConfirmBox
        v-if="confirm_box"
        title="Confirm"
        msg="Change new password?"
        @confirm="changePass"
        @cancel="handleCancelConfirmBox"
    ></ConfirmBox>
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

.form-input {
  display: block;
  padding: 50px;
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

.error{
  color: red;
  font-size: small;
  position: absolute;
  left: 25px;
  top: 66px;
}
.fail-change{
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
import router from "../../router";
import {ref} from "vue";
import {useUserStore} from "../../stores/user";
import {useAlertStore} from "../../stores/alert";
import ButtonLoading from "../../components/ButtonLoading.vue";
import ConfirmBox from "../../components/ConfirmBox.vue";

const alertStore = useAlertStore()
const user = useUserStore().user
const fail_change = ref(false)
const password_error = ref('')
const new_password_error = ref('')
const confirm_error = ref('')
const new_pass_input = ref(null)
const confirm_input = ref(null)
const loading = ref(false)
const confirm_box = ref(false)

if(user.token === ''){
  router.push({name : 'login'})
}
let info = reactive({
  old_password: "",
  new_password: "",
  new_password_confirm: "",
});

function checkpass() {
  password_error.value = (info.old_password.length === 0) ? 'Please enter your password' : ''
}

function checkNewpass(){
  new_password_error.value = (info.new_password.length === 0) ? 'Please enter new password' : ''
}

function checkConfirm(){
  confirm_error.value = (info.new_password_confirm.length === 0) ? 'Please enter confirmation password'
      : (info.new_password_confirm !== info.new_password) ? 'Confirm password not match'
          : ''
}

function openConfirmBox(){
  loading.value = true
  confirm_box.value = true
}

const changePass = async () => {
  loading.value = true;
  confirm_box.value = false
  if (password_error.value === '' && new_password_error.value === '' && confirm_error.value === '') {
    try {
      await axios
          .post('http://127.0.0.1:8000/api/change-password', {
            old_password: info.old_password,
            new_password: info.new_password,
            new_password_confirmation: info.new_password_confirm,
          }, {
            headers: {
              Authorization: `Bearer ${user.token}`,
            },
          })
          .then(function () {
              //alert success
              alertStore.alert = true
              alertStore.type = 'success'
              alertStore.msg = 'Success: Change pass'

              useUserStore().logout()
              router.push({ name: 'login' });
          });
    } catch (e) {
      fail_change.value = true
      loading.value = false
    }
  }
};

function handleCancelConfirmBox(){
  loading.value = false
  confirm_box.value = false
}
</script>
