<template>
    <div class="shift-container">
        <div>
            <el-input v-if="prop.shiftId == 0" disabled style="min-width: 50px" type="text" placeholder="ID"></el-input>
            <el-input v-else v-model=prop.shiftId disabled style="min-width: 50px" type="text" placeholder="ID"></el-input>
        </div>
        <div>
            <el-input v-model="form.name" style="min-width: 180px;" type="text" placeholder="Name"></el-input>
        </div>
        <div>
            <el-time-picker v-model="form.timeValidCheckIn" style="min-width: 130px"
                placeholder="Time Valid Check In"></el-time-picker>
        </div>
        <div>
            <el-time-picker v-model="form.timeValidCheckOut" style="min-width: 130px" type="text"
                placeholder="Time Valid Check Out"></el-time-picker>
        </div>
        <div>
            <el-input v-model="form.amount" style="min-width: 200px" type="text" placeholder="Amount"></el-input>
        </div>
    </div>
    <div class="operation">
        <el-button v-if="prop.mode === 'create'" type="primary" plain @click="createShift">Add</el-button>
        <el-button v-else type="primary" plain @click="updateShift">Change</el-button>
        <el-button @click="$emit('invisible')">Cancel</el-button>
        <span style="color: red; margin-top: 0.1rem; margin-left: 16px">{{ fail_validation }}</span>
    </div>
</template>
  
<style scoped>
.shift-container {
    background-color: #ccc;
    padding: 10px 0 10px 0;
    display: flex;
    align-items: center;
    overflow-x: scroll;
    padding-left: 8px;
    border-radius: 4px;
}

.shift-container::-webkit-scrollbar {
    height: 6px;
    background-color: #f0f0f0;
}

.shift-container::-webkit-scrollbar-thumb {
    height: 6px;
    background-color: #e0e0e0;
}

.shift-container div input {
    padding: 0 8px;
}

.shift-container div {
    padding-right: 4px;
}

.operation {
    display: flex;
    padding: 12px;
}
</style>
  
<script setup>
import { reactive, ref, watch, defineProps, computed } from "vue";
import axios from "axios";
import { useUserStore } from "../stores/user";
import { useAlertStore } from "../stores/alert";
const prop = defineProps({
    mode: {
        type: String,
        default: 'create'
    },
    shiftId: {
        type: Number
    }
})
const user = useUserStore().user
const alertStore = useAlertStore()
const fail_validation = ref('')
const form = reactive({
    name: '',
    timeValidCheckIn: '',
    timeValidCheckOut: '',
    amount: ''
})

const emits = defineEmits(['invisible', 'updateData'])
watch(() => prop.shiftId,
    () => {
        loadShift()
    })
watch(() => prop.mode,
    () => {
        if (prop.mode === 'create') {
            form.name = ''
            form.timeValidCheckIn = ''
            form.timeValidCheckOut = ''
            form.amount = ''
        }
    })
const loadShift = async () => {
    if (prop.mode === 'update') {
        try {
            await axios.get(`http://127.0.0.1:8000/api/shift/${prop.shiftId}`, {
                headers: {
                    Authorization: `Bearer ${user.token}`
                },
            }).then((response) => {
                console.log(response)
                form.name = response.data.data.name
                form.timeValidCheckIn = formatToDisplay(response.data.data.timeValidCheckIn)
                form.timeValidCheckOut = formatToDisplay(response.data.data.timeValidCheckOut)
                form.amount = response.data.data.amount
            })
        } catch (e) {
            console.log(e)
            messages('error', e.data.message)
        }
    }
}
if (prop.mode === 'update') {
    loadShift()
}
const validate = () => {
    if (form.name === '') {
        fail_validation.value = 'Please enter name'
        return false
    } else if (form.timeValidCheckIn === '') {
        fail_validation.value = 'Please enter time checkin'
        return false
    } else if (form.timeValidCheckOut === '') {
        fail_validation.value = 'Please enter time checkout'
        return false
    } else if (form.amount === '') {
        fail_validation.value = 'Please enter amount'
        return false
    }
    return true
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
const createShift = () => {
    if (validate()) {
        axios.post(
            `http://127.0.0.1:8000/api/create-shift/`,
            {
                name: form.name,
                timeValidCheckIn: formatToPost(form.timeValidCheckIn),
                timeValidCheckOut: formatToPost(form.timeValidCheckOut),
                amount: form.amount
            },
            {
                headers: { Authorization: `Bearer ${user.token}` },
            }
        ).then(function (response) {
            emits('updateData')
            form.name = ''
            form.timeValidCheckIn = ''
            form.timeValidCheckOut = ''
            form.amount = ''
            messages('success', response.data.message)
        }).catch((e) => {
            messages('error', e.response.data.message)
            console.log(e)
        })
    }
}

const updateShift = () => {
    if (validate()) {
        axios
            .put(
                `http://127.0.0.1:8000/api/update-shift/${prop.shiftId}`,
                {
                    name: form.name,
                    timeValidCheckIn: formatToPost(form.timeValidCheckIn),
                    timeValidCheckOut: formatToPost(form.timeValidCheckOut),
                    amount: form.amount
                },
                {
                    headers: { Authorization: `Bearer ${user.token}` },
                }
            ).then(function (response) {
                emits('updateData')
                form.name = ''
                form.timeValidCheckIn = ''
                form.timeValidCheckOut = ''
                form.amount = ''
                messages('success', response.data.message)
            }).catch((e) => {
                messages('success', e.response.data.message)
                console.log(e)
            })
    }
}
const messages = (type, msg) => {
    alertStore.alert = true;
    alertStore.type = type;
    alertStore.msg = msg
}
</script>