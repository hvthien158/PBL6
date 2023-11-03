<template>
  <main>
    <SlideBar></SlideBar>
    <div class="user">
      <h1>List user</h1>
      <div class="table-responsive-md">
      <table class="table">
        <thead style="background-color: #ef9400">
            <tr>
                <td scope="col">ID</td>
                <td>Name</td>
                <td>Email</td>
                <td>Address</td>
                <td>Birthday</td>
                <td>Phone number</td>
                <td>Salary</td>
                <td>Position</td>
                <td>Role</td>
                <td>Department</td>
                <td>Working date</td>
                <td>Profile</td>
                <td>Delete</td>
            </tr>
        </thead>
        <tbody>
            <tr v-for="item in data" :key="item.id">
                <td scope="row">{{ item.id }}</td>
                <td>{{ item.name }}</td>
                <td>{{ item.email }}</td>
                <td>{{ item.address }}</td>
                <td>{{ item.DOB }}</td>
                <td>{{ item.phoneNumber }}</td>
                <td>{{ item.salary }}</td>
                <td>{{ item.position }}</td>
                <td>{{ item.role === 'admin' ? 'admin' : 'user'}}</td>
                <td>{{ item.department.name }}</td>
                <td><a @click = "router.push({ path: `/admin/list-timekeeping/${item.id}` })">Check</a></td>
                <td><a @click = "router.push({ path: `/admin/update-user/${item.id}` })">Update</a></td>
                <td><a @click = "deleteUser(item.id)" v-if="item.id !== user.id">Delete</a></td>
            </tr>
        </tbody>
      </table>
    </div>
</div>
  </main>
</template>
<style scoped>
@import "https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css";
main {
  min-height: 100vh;
  border-top: 0.1em solid black;
  box-sizing: border-box;
  display: flex;
  font-size: small;
}
.user {
  display: block;
}
.user h1 {
  text-align: center;
}
.table{
  background-color: white;
  margin-left: 32px;
}
.table td{
    border: 1px solid #dee2e6;
}
a:hover{
  cursor: pointer;
  color: #f3952d !important;
}
</style>
<script setup>
import SlideBar from "../../../components/SlideBar.vue";
import { ref, onMounted } from 'vue'
import {useUserStore} from '../../../stores/user'
import axios from "axios";
import router from '../../../router'
let data = ref()
const user = useUserStore().user
onMounted(() =>{
    if(user.role !== 'admin'){
        router.push({ path : '/'})
    }
    displayUser()
})
const displayUser = async () => {
    try {
        await axios.get('http://127.0.0.1:8000/api/user/',{
            headers : { Authorization: `Bearer ${user.token}`}
        }).then(function(response){
            data.value = response.data.data
            console.log(data.value);
        })
    } catch(e) {
        console.log(e);
    }
}
const deleteUser = async(id) => {
  try {
    await axios.delete(`http://127.0.0.1:8000/api/delete-user/${id}`,{
      headers: { Authorization: `Bearer ${user.token}`}
    })
  } catch(e){
    console.log(e)
  }
  await displayUser()
}
</script>
