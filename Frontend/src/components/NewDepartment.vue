<template>
    <div class="department-container">
        <div>
            <el-input v-if="prop.departmentId == 0" disabled style="min-width: 50px" type="text" placeholder="ID"></el-input>
            <el-input v-else v-model = prop.departmentId disabled style="min-width: 50px" type="text" placeholder="ID"></el-input>
        </div>
        <div>
            <el-input v-model="form.name" style="min-width: 180px;" type="text" placeholder="Name"></el-input>
        </div>
        <div>
            <el-input v-model="form.email" style="min-width: 330px" type="text" placeholder="Email"></el-input>
        </div>
        <div>
            <el-input v-model="form.address" style="min-width: 330px" type="text" placeholder="Address"></el-input>
        </div>
        <div>
            <el-input v-model="form.phoneNumber" style="min-width: 200px" type="text" placeholder="Phone Number"></el-input>
        </div>
    </div> 
    <div class="operation">
        <el-button v-if="prop.mode === 'create'" type="primary" plain @click="createDepartment">Add</el-button>
        <el-button v-else type="primary" plain @click="updateDepartment">Change</el-button>
        <el-button @click="$emit('invisible')">Cancel</el-button>
        <span style="color: red; margin-top: 0.1rem; margin-left: 16px">{{ fail_validation }}</span>
    </div>
</template>
  
<style scoped>
.department-container {
    background-color: #ccc;
    padding: 10px 0 10px 0;
    display: flex;
    align-items: center;
    overflow-x: scroll;
    padding-left: 8px;
    border-radius: 4px;
}

.department-container::-webkit-scrollbar {
    height: 6px;
    background-color: #f0f0f0;
}

.department-container::-webkit-scrollbar-thumb {
    height: 6px;
    background-color: #e0e0e0;
}

.department-container div input {
    padding: 0 8px;
}

.department-container div {
    padding-right: 4px;
}

.operation {
    display: flex;
    padding: 12px;
}
</style>
  
<script setup>
import { reactive, ref, watch, defineProps } from "vue";
import axios from "axios";
import { useUserStore } from "../stores/user";
import { useAlertStore } from "../stores/alert";
const prop = defineProps({
    mode: {
        type: String,
        default: 'create'
    },
    departmentId: {
        type: Number
    }
})
const user = useUserStore().user
const alertStore = useAlertStore()
const fail_validation = ref('')
const form = reactive({
    name: '',
    email: '',
    address: '',
    phoneNumber: ''
})
const emits = defineEmits(['invisible', 'updateData'])
watch(() => prop.departmentId,
    () => {
        loadDepartment()
    })
watch(() => prop.mode,
    () => {
        if (prop.mode === 'create') {
            form.name = ''
            form.email = ''
            form.address = ''
            form.phoneNumber = ''
        }
    })
const loadDepartment = async () => {
    if (prop.mode === 'update') {
        try {
            await axios.get(`http://127.0.0.1:8000/api/department/${prop.departmentId}`, {
                headers: {
                    Authorization: `Bearer ${user.token}`
                },
            }).then((response) => {
                form.email = response.data.data[0].email
                form.name = response.data.data[0].name
                form.address = response.data.data[0].address
                form.phoneNumber = response.data.data[0].phoneNumber
                console.log(response)
            })
        } catch (e) {
            console.log(e)
            messages('error', e.data.message)
        }
    }
}
if (prop.mode === 'update') {
    loadDepartment()
}
const isEmail = (email) => {
    let filter =
        /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return filter.test(email);
};
const isPhoneNumber = (phoneNumber) => {
    let filter = /^([0-9]{10})+$/;
    return filter.test(phoneNumber);
};
function validate() {
    if (form.name === '') {
        fail_validation.value = 'Please enter name'
        return false
    } else if (form.address === '') {
        fail_validation.value = 'Please enter address'
        return false
    }
    else if (form.email === '') {
        fail_validation.value = 'Please enter email'
        return false
    } else if (!isEmail(form.email)) {
        fail_validation.value = 'Invalid email'
        return false
    } else if (form.phoneNumber === '') {
        fail_validation.value = 'Please enter phone number'
        return false
    } else if (isPhoneNumber(form.isPhoneNumber)) {
        fail_validation.value = 'Invalid phone number'
        return false
    }
    return true
}

function createDepartment() {
    if (validate()) {
        axios.post(
            `http://127.0.0.1:8000/api/create-department/`,
            {
                departmentName: form.name,
                address: form.address,
                email: form.email,
                phoneNumber: form.phoneNumber
            },
            {
                headers: { Authorization: `Bearer ${user.token}` },
            }
        ).then(function () {
            emits('updateData')
            form.email = ''
            form.name = ''
            form.address = ''
            form.phoneNumber = ''
            messages('success', 'Create success')
        }).catch((e) => {
            messages('error', e.response.data.message)
            console.log(e)
        })
    }
}

function updateDepartment() {
    if (validate()) {
        axios
            .put(
                `http://127.0.0.1:8000/api/update-department/${prop.departmentId}`,
                {
                    departmentName: form.name,
                    address: form.address,
                    email: form.email,
                    phoneNumber: form.phoneNumber
                },
                {
                    headers: { Authorization: `Bearer ${user.token}` },
                }
            ).then(function () {
                emits('updateData')
                messages('success', 'Update successfully')
            }).catch((e) => {
                messages('success', 'Something went wrong')
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
  