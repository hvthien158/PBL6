<template>
  <main>
    <div class="container">
      <div v-if="verifyQuest">
        <p style="font-weight: bold">A confirmation email has been sent to your email, please confirm to continue...</p>
      </div>
      <div class="form-container" v-else>
        <h3>FORGOT PASSWORD</h3>
        <div class="form-input">
          <div class="form-info">
            <div class="label-info">
              <label>Email</label>
            </div>
            <div class="input-info">
              <input v-model="email" type="text" />
              <h5>{{ checkEmail }}</h5>
            </div>
          </div>
          <div class="btn-submit">
            <button type="submit" @click="forgot">Send</button>
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
  border-radius: 10px;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
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
</style>

<script setup>
import { computed } from "vue";
import axios from "axios";
import router from "../../router";
import {ref} from "vue";
import {useUserStore} from "../../stores/user";

const verifyQuest = ref(false)
const email_not_signed = ref(false)
const user = useUserStore().user

if(user.token !== ''){
  router.push({name : 'home'})
}

const email = ref('')

const checkEmail = computed(() => {
  if(email.value === ''){
    return "Please enter your email"
  } else if (!isEmail(email.value)) {
    return "Invalid email";
  } else {
    return "";
  }
});

const isEmail = (email) => {
  let filter =
      /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return filter.test(email);
};
const forgot = async () => {
  if (email.value !== '') {
      await axios.post('http://127.0.0.1:8000/api/check-email', {
        email: email.value
      }).then(() => {
        verifyQuest.value = true
        axios.post('http://127.0.0.1:8000/api/forgot-password', {
              email: email.value,
            })
            .then(function () {
              localStorage.setItem('email_forgot', email.value)
            })
            .catch((e)=> {
              console.log(e)
            })
      }).catch(() => {
        email_not_signed.value = true
      })
  }
};

</script>
