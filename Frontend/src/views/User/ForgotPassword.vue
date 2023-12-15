<template>
  <main>
    <div class="container">
      <div v-if="verifyQuest">
        <p style="font-weight: bold">A confirmation email has been sent to your email, please confirm to continue...</p>
      </div>
      <div class="form-container" v-else>
        <div class="form-input">
          <h3 style="text-align: center; font-weight: 700; color: #e06230">FORGOT PASSWORD</h3>
          <div class="login__field">
            <i class="login__icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                   class="bi bi-person-fill"
                   viewBox="0 0 16 16">
                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
              </svg>
            </i>
            <input v-model="email" type="text" class="login__input" placeholder="Email" @blur="checkmail"
                   @input="checkmail" @keyup.enter="forgot">
            <span class="error">{{ email_error }}</span>
          </div>

          <div class="login__field" style="display: flex; justify-content: center">
            <ButtonLoading :loading="loading" @click="forgot" style="font-size: 15px;" size="large" type="warning"
                           round>
              Send mail
            </ButtonLoading>
          </div>
          <p class="fail-login" v-if="email_not_signed">Email not exist</p>
          <div class="content-bottom">
            <span style="margin-right: 5px;">Already have an account? </span>
            <span id="go-login" @click="router.push({ name: 'login' })">Sign in</span>
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
import router from "../../router";
import {ref} from "vue";
import {useUserStore} from "../../stores/user";
import ButtonLoading from "../../components/ButtonLoading.vue";
import AuthAPI from "../../services/AuthAPI";

const verifyQuest = ref(false)
const email_not_signed = ref(false)
const user = useUserStore().user
const email_error = ref('')
const loading = ref(false)

if (user.token !== '') {
  router.push({name: 'home'})
}

const email = ref('')

function checkmail() {
  email_error.value = (email.value.length === 0) ? 'Please enter your email' : ''
}

const isEmail = (email) => {
  let filter =
      /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return filter.test(email);
};
const forgot = async () => {
  loading.value = true
  if (!isEmail(email.value)) {
    email_error.value = 'Invalid email'
    loading.value = false
  } else if (email.value !== '') {
    await AuthAPI.checkEmail(email.value)
        .then(() => {
          verifyQuest.value = true
          AuthAPI.forgotPassword(email.value)
              .then(function () {
                localStorage.setItem('email_forgot', email.value)
              })
              .catch((e) => {
                console.log(e)
              })
        }).catch((e) => {
          console.log(e)
          email_not_signed.value = true
          loading.value = false
        })
  }
};

</script>