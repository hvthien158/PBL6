<template>
  <div class="profile-container">
    <img style="height: 150px; width: 150px; border-radius: 50%; margin: 20px"
         :src="imgPath ? imgPath : 'https://thumbs.dreamstime.com/b/default-avatar-profile-icon-vector-social-media-user-image-182145777.jpg'">
    <div style="display:flex; flex-direction: column">
      <div style="display: flex">
        <span style="color: #f3952d; font-size: xx-large">{{ user.name }}</span>
        <span style="color: #9b9b9b; line-height: 60px"> &emsp; {{user.email}}</span>
      </div>
      <span>Ngày sinh: {{user.DOB}}</span>
      <span>Địa chỉ: {{user.address}}</span>
      <span>SĐT: {{user.phone_number}}</span>
      <el-button style="width: 50px; margin-top: 24px" @click="goToEdit">Edit</el-button>
    </div>
  </div>
</template>

<style scoped>
.profile-container{
  display:flex;
  border-bottom: #99a9bf 1px solid;
  padding: 50px;
}
span{
  margin-top: 20px !important;
}
</style>

<script setup>
import {useUserStore} from "../../stores/user";
import {ref} from "vue";
import {useRouter} from "vue-router";

const router = useRouter()
const user = useUserStore().user
const imgPath = ref(user.avatar? 'http://127.0.0.1:8000/storage/' + user.avatar : null)

function goToEdit(){
  router.push({
    name: 'update-profile'
  })
}
</script>
