<script setup>
import { reactive, computed } from "vue";
import router from "../router";

let info = reactive({
  email: "",
  password: "",
});

const checkEmail = computed(() => {
  if (!isEmail(info.email)) {
    return "Email không đúng định dạng";
  } else {
    return "";
  }
});

const checkPassword = computed(() => {
  if (info.password.length === 0) {
    return "Vui lòng nhập mật khẩu";
  } else {
    return "";
  }
});

const isEmail = (email) => {
  let filter =
    /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return filter.test(email);
};

const login = () => {
  router.push({ path: "/" });
};
</script>

<template>
  <main>
    <div class="container">
      <form class="form-container">
        <div class="form-input">
          <div class="form-info">
            <div class="label-info">
              <label>Email</label>
            </div>
            <div class="input-info">
              <input v-model="info.email" type="text" />
              <h5>{{ checkEmail }}</h5>
            </div>
          </div>
          <div class="form-info">
            <div class="label-info">
              <label>Mật khẩu</label>
            </div>
            <div class="input-info">
              <input v-model="info.password" type="password" />
              <h5>{{ checkPassword }}</h5>
            </div>
          </div>
          <div class="btn-submit">
            <button type="submit" @click="login">Đăng nhập</button>
          </div>
        </div>
        <div class="additional-content">
          <p>Quên mật khẩu?</p>
        </div>
      </form>
    </div>
  </main>
</template>
<style>
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
