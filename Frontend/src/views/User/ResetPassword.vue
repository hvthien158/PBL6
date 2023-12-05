<template>
  <main>
    <div class="container">
      <div class="form-container">
        <div class="form-input">
          <h3 style="text-align: center; font-weight: 700; color: #e06230">RESET PASSWORD</h3>
          <div class="login__field">
            <i class="login__icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock-fill"
                   viewBox="0 0 16 16">
                <path
                    d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
              </svg>
            </i>
            <input
                v-model="info.password"
                type="password"
                class="login__input"
                placeholder="New password"
                @blur="checkpass"
                @input="checkpass"
            >
            <span class="error">{{ password_error }}</span>
          </div>
          <div class="login__field">
            <i class="login__icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                   class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                <path
                    d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
              </svg>
            </i>
            <input
                ref="passwordInput"
                v-model="info.password_confirm"
                type="password"
                class="login__input"
                placeholder="Confirm"
                @blur="checkConfirm"
                @input="checkConfirm"
                @keyup.enter="reset"
            >
            <span class="error">{{ confirm_error }}</span>
          </div>
          <div class="login__field" style="display: flex; justify-content: center">
            <ButtonLoading :loading="loading" @click="reset" style="font-size: 15px;" size="large" type="warning" round>
              Change password
            </ButtonLoading>
          </div>
          <div class="content-bottom">
            <span style="margin-right: 5px;">Already have an account? </span>
            <span id="go-login" @click="router.push({name: 'login'})">Sign in</span>
          </div>
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

.form-input {
  display: block;
  padding: 50px;
  background-color: white;
  border-radius: 16px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  min-width: 25vw;
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

.error {
  color: red;
  font-size: small;
  position: absolute;
  left: 25px;
  top: 66px;
}

.fail-login {
  color: red;
  font-size: 14px;
  text-align: center;
  position: absolute;
  left: calc(50% - 80px);
}

.content-bottom {
  display: flex;
  justify-content: center;
}

#go-login {
  color: #7875B5;
  font-weight: 700;
}

#go-login:hover {
  cursor: pointer;
  opacity: 0.8;
}
</style>

<script setup>
import {reactive, computed} from "vue";
import router from "../../router";
import {ref} from "vue";
import {useUserStore} from "../../stores/user";
import {useRoute} from "vue-router";
import {useAlertStore} from "../../stores/alert";
import ButtonLoading from "../../components/ButtonLoading.vue";
import AuthAPI from "../../services/AuthAPI";

const alertStore = useAlertStore()
const user = useUserStore().user
const route = useRoute()
const password_error = ref('')
const confirm_error = ref('')
const fail_reset = ref(false)
const loading = ref(false)

if (user.token !== '') {
  router.push({name: 'home'})
}
let info = reactive({
  token: route.params.token,
  password: "",
  password_confirm: "",
});

function checkpass() {
  password_error.value = (info.password.length === 0) ? 'Please enter your password' : ''
}

function checkConfirm() {
  confirm_error.value = (info.password_confirm.length === 0) ? 'Please enter confirmation password'
      : (info.password_confirm !== info.password) ? 'Confirm password not match'
          : ''
}

const reset = async () => {
  loading.value = true
  if (info.password && confirm_error.value === '') {
    try {
      await AuthAPI.resetPassword(
          info.token,
          localStorage.getItem('email_forgot'),
          info.password,
          info.password_confirm
      )
          .then(function () {
            //component alert success
            alertStore.alert = true
            alertStore.type = 'success'
            alertStore.msg = 'Success'

            router.push({
              name: 'login'
            })
          });
    } catch (e) {
      console.log(e)
      alertStore.alert = true
      alertStore.type = 'error'
      alertStore.msg = 'Something wrong'

      loading.value = false
    }
  }
};
</script>
