<template>
  <div style="margin-top: 40px">
    <h1>Cập nhật thông tin cá nhân:</h1>
    <div style="display: flex; align-items: center">
      <input type="file" name="file" id="file" class="inputfile" @input="onFileChange"/>
      <label for="file">
        <img :src="imgPath" alt="" style="max-width: 100px; border-radius: 50%">
      </label>
      <div style="display: flex; flex-direction: column; padding-left: 20px">
        <span>Địa chỉ: </span>
        <input v-model="address" type="text" name="" id="">
        <span>Ngày sinh: </span>
        <input v-model="DOB" type="text" name="" id="">
        <span>SĐT: </span>
        <input v-model="phone_number" type="text" name="" id="">
      </div>
    </div>
    <button @click="updateProfile">Cập nhật</button>
  </div>
</template>

<style>
.inputfile {
  width: 0.1px;
  height: 0.1px;
  opacity: 0;
  overflow: hidden;
  position: fixed;
  bottom: 0;
  right: 0;
  z-index: -1;
}
.inputfile + label {
  font-size: 1.25em;
  font-weight: 700;
  display: inline-block;
  cursor: pointer;
}
.inputfile:focus + label,
.inputfile + label:hover {
  opacity: 0.8;
}
</style>

<script setup>
import { ref , reactive} from "vue";
import router from "../../router";
import axios from "axios";
import {useUserStore} from "../../stores/user";

const avatar = ref(undefined)
const formData = new FormData()
const user = useUserStore().user

if (user.token === '') {
  router.push({ path: "/login" });
}

const address = ref(user.address)
const DOB = ref(user.DOB)
const phone_number = ref(user.phone_number)
const imgPath = ref(user.avatar? 'http://127.0.0.1:8000/storage/' + user.avatar : null)

function onFileChange(event){
  avatar.value = event.target.files[0]
  imgPath.value = URL.createObjectURL(event.target.files[0])
}

function updateProfile(){
  document.querySelector("input[type='file']").value = ""
  formData.append('phone_number', phone_number.value)
  formData.append('address', address.value)
  formData.append('DOB', DOB.value)
  if(avatar.value){
    formData.append('avatar', avatar.value)
  }
  axios.post('http://127.0.0.1:8000/api/update-profile', formData,
      {
        headers: {
          Authorization: `Bearer ${user.token}`,
        },
      })
      .catch((e) => {
        console.log(e)
      })
}
</script>
