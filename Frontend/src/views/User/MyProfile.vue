<template>
  <div class="container-custom">
    <div class="profile-container">
      <input type="file" name="file" id="file" class="inputfile" @input="onFileChange"/>
      <label for="file">
        <img style="height: 150px; width: 150px; border-radius: 50%; margin: 20px"
             :src="imgPath">
      </label>
      <div style="display:flex; flex-direction: column">
        <div style="display: flex">
          <span style="color: #f3952d; font-size: xx-large">{{ user.name }}</span>
          <span style="color: #9b9b9b; line-height: 60px"> &emsp; {{user.email}}</span>
        </div>
        <div class="child">
          <span>Date of birth: </span>
          <span v-if="!edit_mode">{{ displayDOB }}</span>
          <el-date-picker
              v-else
              v-model="DOB"
              type="date"
              placeholder="dd/mm/yyyy"
              format="DD/MM/YYYY"
              value-format="YYYY-MM-DD"
          />
        </div>
        <div class="child">
          <span>Address: </span>
          <span v-if="!edit_mode">{{user.address}}</span>
          <el-input class="w-50" type="text" v-else v-model="address" placeholder="Da Nang"/>
        </div>
        <div class="child">
          <span>Phone number: </span>
          <span v-if="!edit_mode">{{user.phone_number}}</span>
          <el-input class="w-50" type="text" v-else v-model="phone_number" placeholder="xxx-xxxx-xxxx"/>
        </div>
        <div class="child">
          <span>Position: </span>
          <span>{{user.position}}</span>
        </div>
        <ButtonLoading
            v-if="!edit_mode"
            @click="edit_mode = true"
            style="width: 70px; font-size: 15px; margin-top: 25px"
            size="large"
            type="warning" round
        >Edit</ButtonLoading>
        <div v-else>
          <ButtonLoading
              @click="updateProfile"
              style="width: 70px; font-size: 15px; margin-top: 25px"
              size="large"
              type="warning" round
          >Save</ButtonLoading>
          <ButtonLoading
              @click="edit_mode = !edit_mode"
              style="width: 70px; font-size: 15px; margin-top: 25px"
              size="large"
              round
          >Cancel</ButtonLoading>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.container-custom{
  display: flex;
  align-items: center;
  justify-content: center;
}
.profile-container{
  display:flex;
  padding: 50px;
  background-color: white;
  border-radius: 16px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
.child{
  margin-top: 20px !important;
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
import {useUserStore} from "../../stores/user";
import {onMounted, ref} from "vue";
import {useRouter} from "vue-router";
import ButtonLoading from "../../components/ButtonLoading.vue";
import axios from "axios";
import {useAlertStore} from "../../stores/alert";
import moment from "moment";

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
const displayDOB = ref('')

function mapData(){
  DOB.value = user.DOB
  displayDOB.value = moment(user.DOB).format('DD/MM/YYYY')
  address.value = user.address
  phone_number.value = user.phone_number
}

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
  console.log(DOB.value)
  axios.post('http://127.0.0.1:8000/api/update-profile', formData,
      {
        headers: {
          Authorization: `Bearer ${user.token}`,
        },
      })
      .then((response) => {
        console.log(response)
        user.address = response.data.user[0].address
        user.phone_number = response.data.user[0].phone_number
        user.DOB = response.data.user[0].DOB
        user.avatar = response.data.user[0].avatar
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

onMounted(() => {
  mapData()
})
</script>
