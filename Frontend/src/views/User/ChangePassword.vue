<template>
  <main>
    <div class="container">
      <div class="form-container">
        <h3>ChangePassword</h3>
        <div class="form-input">
          <div class="form-info">
            <div class="label-info">
              <label>Current password</label>
            </div>
            <div class="input-info">
              <input v-model="info.old_password" type="password" />
              <h5>{{ checkOldPassword }}</h5>
            </div>
          </div>
          <div class="form-info">
            <div class="label-info">
              <label>New password</label>
            </div>
            <div class="input-info">
              <input v-model="info.new_password" type="password" />
              <h5>{{ checkNewPassword }}</h5>
            </div>
          </div>
          <div class="form-info">
            <div class="label-info">
              <label>Confirm</label>
            </div>
            <div class="input-info">
              <input v-model="info.new_password_confirm" type="password" />
              <h5>{{ checkNewPasswordConfirm }}</h5>
            </div>
          </div>
          <div class="btn-submit">
            <button type="submit" @click="login">Change</button>
          </div>
        </div>
        <div v-if="fail_change">
          <span style="color: red">Wrong password</span>
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
</style>

<script setup>
import { reactive, computed } from "vue";
import axios from "axios";
import router from "../../router";
import {ref} from "vue";
import {useUserStore} from "../../stores/user";
import {useAlertStore} from "../../stores/alert";

const alertStore = useAlertStore()
const user = useUserStore().user
const fail_change = ref(false)

if(user.token === ''){
  router.push({name : 'login'})
}
let info = reactive({
  old_password: "",
  new_password: "",
  new_password_confirm: "",
});

const checkOldPassword = computed(() => {
  if (info.old_password.length === 0) {
    return "Please enter your current password";
  } else {
    return "";
  }
});

const checkNewPassword = computed(() => {
  if (info.new_password.length === 0) {
    return "Please enter new password";
  } else {
    return "";
  }
});

const checkNewPasswordConfirm = computed(() => {
  if (info.new_password_confirm.length === 0) {
    return "Please enter confirmation password";
  } else if(info.new_password_confirm !== info.new_password){
    return "Confirm password is not matched"
  } else {
    return "";
  }
});

const login = async () => {
  if (checkOldPassword.value === '' && checkNewPassword.value === '' && checkNewPasswordConfirm.value === '') {
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
      console.log(e);
    }
  }
};
</script>
