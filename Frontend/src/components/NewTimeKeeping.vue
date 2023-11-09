<template>
    <div class="timekeeping-container">
            <div>
                <el-input v-model=prop.timekeepingId disabled style="min-width: 50px" type="text"
                    placeholder="ID"></el-input>
            </div>
            <div>
                <el-time-picker v-model="form.timeCheckIn" style="min-width: 130px"
                    placeholder="Time Check In"></el-time-picker>
            </div>
            <div>
                <el-time-picker v-model="form.timeCheckOut" style="min-width: 130px"
                    placeholder="Time Check Out"></el-time-picker>
            </div>
    </div>
    
    <div class="operation">
        <el-button type="primary" plain @click="updateTimeKeeping">Change</el-button>
        <el-button @click="$emit('invisible')">Cancel</el-button>
        <span style="color: red; margin-top: 0.1rem; margin-left: 16px">{{ failValidation }}</span>
    </div>
</template>
  
<style scoped>
.timekeeping-container {
    background-color: #ccc;
    padding: 10px 0 10px 0;
    display: flex;
    align-items: center;
    overflow-x: scroll;
    padding-left: 8px;
    border-radius: 4px;
    width: 650px;
}

.timekeeping-container::-webkit-scrollbar {
    height: 6px;
    background-color: #f0f0f0;
}

.timekeeping-container::-webkit-scrollbar-thumb {
    height: 6px;
    background-color: #e0e0e0;
}

.timekeeping-container div input {
    padding: 0 8px;
}

.timekeeping-container div {
    padding-right: 4px;
}

.operation {
    display: flex;
    padding: 12px;
}
</style>
  
<script setup>
import { reactive, ref, defineProps } from "vue";
import axios from "axios";
import { useUserStore } from "../stores/user";
import { useAlertStore } from "../stores/alert";
const prop = defineProps({
    timekeepingId: {
        type: Number
    }
})
const user = useUserStore().user
const alertStore = useAlertStore()
let failValidation = ref('')
const form = reactive({
    timeCheckIn: '',
    timeCheckOut: ''
})
const emits = defineEmits(['invisible', 'updateData'])
const loadTimeKeeping = async () => {
    try {
        await axios.get(`http://127.0.0.1:8000/api/manage-timekeeping/${prop.timekeepingId}`, {
            headers: {
                Authorization: `Bearer ${user.token}`
            },
        }).then((response) => {
            form.timeCheckIn = formatToDisplay(response.data.data[0].timeCheckIn)
            form.timeCheckOut = formatToDisplay(response.data.data[0].timeCheckOut)
        })
    } catch (e) {
        messages('error', e.data.message)
    }
}
loadTimeKeeping()
function validate() {
    if (formatToPost(form.timeCheckIn) > formatToPost(form.timeCheckOut)) {
        failValidation.value = 'Time check out must be bigger than time check in'
        return false
    } 
    return true
}
function updateTimeKeeping() {
    if (validate()) {
        axios
            .put(
                `http://127.0.0.1:8000/api/update-timekeeping/${prop.timekeepingId}`,
                {
                    timeCheckIn: formatToPost(form.timeCheckIn),
                    timeCheckOut: formatToPost(form.timeCheckOut)
                },
                {
                    headers: { Authorization: `Bearer ${user.token}` },
                }
            ).then(function (response) {
                emits('updateData')
                console.log(response)
                messages('success', response.data.message)
            }).catch((e) => {
                messages('error', 'Something went wrong')
                console.log(e)
            })
    }
}
const formatToDisplay = (time) => {
    const currentDate = new Date();
    const dateParts = currentDate.toDateString().split(' ');
    const dateString = dateParts[0] + ' ' + dateParts[1] + ' ' + dateParts[2] + ' ' + currentDate.getFullYear() + ' ' + time;

    const date = new Date(dateString);
    return date.toString();
}
const formatToPost = (time) => {
    const formatTimeCheckIn = new Date(time)
    return formatTimeCheckIn.toLocaleTimeString('en-US', { hour12: false });
}
const messages = (type, msg) => {
    alertStore.alert = true;
    alertStore.type = type;
    alertStore.msg = msg
}
</script>