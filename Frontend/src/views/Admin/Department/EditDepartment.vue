<template>
  <main>
    <SlideBar></SlideBar>
    <div class="add-department">
      <div class="input-container">
        <h1>Thêm cơ quan</h1>
        <div class="form-floating">
          <input
            type="text"
            class="form-control"
            placeholder="Tên cơ quan"
            required
          />
        </div>
        <div class="invalid-feedback">
          Please field valid department
        </div>
        <div class="form-floating is-invalid">
          <input
            type="text"
            class="form-control"
            placeholder="Địa chỉ"
            required
          />
        </div>
        <div class="invalid-feedback" >
          Please field valid address
        </div>
        <div class="form-floating" >
          <input
            type="password"
            class="form-control"
            placeholder="Password"
            required
          />
        </div>
        <div class="invalid-feedback" >
          Please field password > 6
        </div>
        <div class="form-floating">
          <input
            
            type="password"
            class="form-control"
            placeholder="Confirmation Password"
            required
          />
        </div>
        <div class="invalid-feedback" >
          Confirm Password not match
        </div>
        <div class="form-floating is-invalid">
          <input
            type="text"
            class="form-control"
            placeholder="Address"
            required
          />
        </div>
        <div class="form-floating">
          <input
            type="date"
            class="form-control"
          />
        </div>
        <div class="form-floating">
          <input
            type="text"
            class="form-control"
            placeholder="Phone Number"
            required
          />
        </div>
        <div class="invalid-feedback">
          Please field valid phone number
        </div>
        <div class="form-floating">
          <input
            type="text"
            class="form-control"
            placeholder="Salary"
            required
          />
        </div>
        <!-- <div class="form-floating">
          <select class="custom-select">
            <option v-for="options in position" :value="options.value">
              {{ options.name }}
            </option>
          </select>
        </div> -->
        
        <div class="btn-submit">
          <button type="button" class="btn btn-primary" >
            Thêm Cơ Quan Mới
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
import {useRoute} from 'vue-router'
import {useUserStore} from "../../../stores/user";
import router from '../../../router'
const user = useUserStore().user

</script>
  