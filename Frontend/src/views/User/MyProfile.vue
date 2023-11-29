<template>
  <div class="container-custom">
    <div class="profile-container">
      <div class="avatar-img">
        <input type="file" name="file" id="file" class="inputfile" @input="onFileChange" />
        <label for="file" class="responsive-img">
          <img style="height: 150px; width: 150px; border-radius: 50%; margin: 20px" :src="imgPath">
        </label>
      </div>
      <div style="display:flex; flex-direction: column; overflow: auto;">
        <div class="responsive-img">
          <input type="file" name="file" id="file" class="inputfile" @input="onFileChange" />
          <label for="file">
            <img style="height: 150px; width: 150px; border-radius: 50%; margin: 20px" :src="imgPath">
          </label>
        </div>
        <div class="info-user">

          <div class="content">
            <p style="color: #f3952d; font-size: xx-large">{{ user.name }}</p>
            <p style="color: #9b9b9b; line-height: 60px">{{ user.email }}</p>
          </div>
        </div>
        <div class="child">
          <span>Date of birth: </span>
          <span v-if="!edit_mode">{{ DOB }}</span>
          <el-date-picker v-else v-model="DOB" type="date" />
        </div>
        <div class="child">
          <span>Address: </span>
          <span v-if="!edit_mode">{{ user.address }}</span>
          <el-input class="w-50" type="text" v-else v-model="address" placeholder="Da Nang" />
        </div>
        <div class="child">
          <span>Phone number: </span>
          <span v-if="!edit_mode">{{ user.phone_number }}</span>
          <el-input class="w-50" type="text" v-else v-model="phone_number" placeholder="xxx-xxxx-xxxx" />
        </div>
        <div class="child">
          <span>Position: </span>
          <span>{{ user.position }}</span>
        </div>
        <ButtonLoading v-if="!edit_mode" @click="edit_mode = true" style="width: 70px; font-size: 15px; margin-top: 25px"
          size="large" type="warning" round>Edit</ButtonLoading>
        <div v-else>
          <ButtonLoading @click="updateProfile" style="width: 70px; font-size: 15px; margin-top: 25px" size="large"
            type="warning" round>Save</ButtonLoading>
          <ButtonLoading @click="edit_mode = !edit_mode" style="width: 70px; font-size: 15px; margin-top: 25px"
            size="large" round>Cancel</ButtonLoading>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.container-custom {
  display: flex;
  align-items: center;
  justify-content: center;
}

.info-user {
  display: flex;
}

.info-user .content {
  display: flex;
}

.avatar-img {
  display: block;
}
.info-user .content p:last-child {
    margin-left: 10px;
  }
.responsive-img {
  display: none;
}
span{
  display: inline-block;
}
.profile-container {
  margin: 20px;
  display: flex;
  padding: 50px;
  background-color: white;
  border-radius: 16px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  min-width: 40vw;
}

.child {
  margin-top: 20px !important;
}

.child span:first-child{
  margin-right: 5px;
}

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

.inputfile+label {
  font-size: 1.25em;
  font-weight: 700;
  display: inline-block;
  cursor: pointer;
}

.inputfile:focus+label,
.inputfile+label:hover {
  opacity: 0.8;
}

@media screen and (max-width : 768px) {
  .info-user .content {
    display: block;

  }

  .avatar-img {
    display: none;
  }

  .responsive-img {
    display: flex;
    justify-content: center;
  }
  .info-user .content p:last-child {
    margin-left: 0px;
  }
}
</style>

<script setup>
import { useUserStore } from "../../stores/user";
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import ButtonLoading from "../../components/ButtonLoading.vue";
import axios from "axios";
import { useAlertStore } from "../../stores/alert";

const router = useRouter()
const alertStore = useAlertStore()
const user = useUserStore().user
const imgPath = ref(user.avatar)
const avatar = ref(undefined)
const formData = new FormData()

const edit_mode = ref(false)

const DOB = ref('')
const address = ref('')
const phone_number = ref('')

function mapData() {
  DOB.value = user.DOB
  address.value = user.address
  phone_number.value = user.phone_number
}

function onFileChange(event) {
  avatar.value = event.target.files[0]
  imgPath.value = URL.createObjectURL(event.target.files[0])
}

function updateProfile() {
  document.querySelector("input[type='file']").value = ""
  formData.append('phone_number', phone_number.value)
  formData.append('address', address.value)
  formData.append('DOB', formatToPost(DOB.value))
  if (avatar.value) {
    formData.append('avatar', avatar.value)
  }
  axios.post('http://127.0.0.1:8000/api/update-profile', formData,
    {
      headers: {
        Authorization: `Bearer ${user.token}`,
      },
    })
    .then((response) => {
      user.address = response.data.user.address
      user.phone_number = response.data.user.phone_number
      user.DOB = response.data.user.DOB
      user.avatar = response.data.user.avatar
      imgPath.value = user.avatar
      mapData()

      edit_mode.value = false
      alertStore.alert = true
      alertStore.type = 'success'
      alertStore.msg = 'Update success'
    })
    .catch((e) => {
      alertStore.alert = true
      alertStore.type = 'error'
      alertStore.msg = 'Something went wrong'
      console.log(e)
    })
}
const formatToPost = (time) => {
  const date = new Date(time);
  const year = date.getFullYear();
  const month = (date.getMonth() + 1).toString().padStart(2, '0');
  const day = date.getDate().toString().padStart(2, '0');
  return `${year}-${month}-${day}`;
}
onMounted(() => {
  mapData()
})
</script>
