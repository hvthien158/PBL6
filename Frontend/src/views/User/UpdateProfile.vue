<template>
  <div style="margin-top: 40px; padding: 0 100px">
    <h1>Update your profile:</h1>
    <div style="display: flex; align-items: center">
      <input type="file" name="file" id="file" class="inputfile" @input="onFileChange"/>
      <label for="file">
        <img :src="imgPath ? imgPath : 'https://thumbs.dreamstime.com/b/default-avatar-profile-icon-vector-social-media-user-image-182145777.jpg'" style="width: 100px; height: 100px; border-radius: 50%">
      </label>
      <div style="display: flex; flex-direction: column; padding-left: 20px">
        <span>Address: </span>
        <input v-model="address" type="text" name="" id="">
        <span>Birthday: </span>
        <input v-model="DOB" type="text" name="" id="">
        <span>Phone number: </span>
        <input v-model="phone_number" type="text" name="" id="">
      </div>
    </div>
    <br>
    <el-button @click="updateProfile">Update</el-button>
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
import {useAlertStore} from "../../stores/alert";

const avatar = ref(undefined)
const formData = new FormData()
const user = useUserStore().user

if (user.token === '') {
  router.push({ path: "/login" });
}
const alertStore = useAlertStore()
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
      .then(() => {
        user.address = address.value
        user.phone_number = phone_number.value
        user.DOB = DOB.value
        user.avatar = avatar.value

        router.go(0)
      })
      .catch((e) => {
        console.log(e)
      })
}
</script>
