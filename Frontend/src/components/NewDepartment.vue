<template>
    <el-dialog v-model="prop.visible" :show-close="false" width="30%" >
        <template #header>
            <div class="my-header">
                <h4 v-if="prop.mode === 'create'">Create Department</h4>
                <h4 v-else>Update Department</h4>
                <el-button type="danger" @click="$emit('invisible')">
                    <el-icon class="el-icon--left">
                        <CircleCloseFilled />
                    </el-icon>
                    Close
                </el-button>
            </div>
        </template>
        <el-form :model="form">
            <el-form-item v-if="prop.mode === 'update'" label="ID" :label-width="formLabelWidth">
                <el-input v-model="prop.departmentId" autocomplete="off" disabled />
            </el-form-item>
            <el-form-item label="Department name" :label-width="formLabelWidth">
                <el-input v-model="form.name" autocomplete="off" />
                <small>{{ checkLanding.checkName }}</small>
            </el-form-item>
            <el-form-item label="Address" :label-width="formLabelWidth">
                <el-input v-model="form.address" autocomplete="off" />
                <small>{{ checkLanding.checkAddress }}</small>
            </el-form-item>

            <el-form-item label="Email" :label-width="formLabelWidth">
                <el-input v-model="form.email" autocomplete="off" />
                <small>{{ checkLanding.checkEmail }}</small>
            </el-form-item>

            <el-form-item label="Phone Number" :label-width="formLabelWidth">
                <el-input v-model="form.phoneNumber" autocomplete="off" />
                <small>{{ checkLanding.checkPhoneNumber }}</small>
            </el-form-item>
            <el-form-item label="Department Manager" :label-width="formLabelWidth">
                <el-select v-model="form.manager" type="text" >
                    <el-option label="Select Manager" :value="0"></el-option>
                    <el-option v-for="item in dataUser" :label="item.name" :value="item.id"></el-option>
                </el-select>
            </el-form-item>
        </el-form>
        <template #footer>
            <span class="dialog-footer">
                <el-button @click="$emit('invisible')">Cancel</el-button>
                <el-button v-if="prop.mode === 'create'" type="primary" @click="createDepartment">
                    Create
                </el-button>
                <el-button v-else type="primary" @click="updateDepartment">
                    Update
                </el-button>
            </span>
        </template>
    </el-dialog>
</template>
  
<style scoped>
.department-container {
    width: 100%;
    background-color: #ccc;
    padding: 10px 0;
    display: block;
    align-items: center;
    overflow-x: auto;
    border-radius: 4px;
}

.my-header {
    display: flex;
    justify-content: space-between;
}

small {
    color: red;
    margin-top: 0.1rem;
    margin-left: 16px;
    font-size: 14px;
    color: red;
    margin-top: 0.1rem;
    margin-left: 0 px;
    font-size: 14px;
}

.form-item {
    display: flex;
    align-items: center;
    margin-right: 16px;
}

.form-label {
    margin-right: 8px;
}

.el-button--text {
    margin-right: 15px;
}

.el-input {
    width: 100%;
}

.dialog-footer {
    margin-top: 16px;
}

.dialog-footer button:first-child {
    margin-right: 10px;
}

.dialog-footer button {
    margin-right: 8px;
}

span {
    display: block;
    color: red;
    margin-top: 0.5rem;
    margin-left: 16px;
}

.department-container::-webkit-scrollbar {
    height: 6px;
    background-color: #f0f0f0;
}

.department-container::-webkit-scrollbar-thumb {
    height: 6px;
    background-color: #e0e0e0;
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
    },
    visible: {
        type: Boolean
    }
})
const formLabelWidth = "150px";
const user = useUserStore().user
const alertStore = useAlertStore()
const dataUser = ref()
const form = reactive({
    name: '',
    email: '',
    address: '',
    phoneNumber: '',
    manager: 0
})
const checkLanding = reactive({
    checkName: '',
    checkEmail: '',
    checkAddress: '',
    checkPhoneNumber: '',
})
const emits = defineEmits(['invisible', 'updateData'])
watch(() => prop.departmentId,
    () => {
        loadDepartment()
    })
watch(() => prop.mode,
    () => {
        typeMode = typeof prop
        if (prop.mode === 'create') {
            form.name = ''
            form.email = ''
            form.address = ''
            form.phoneNumber = ''
            form.manager = ''
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
                form.manager = response.data.data[0].manager.id
            })
        } catch (e) {
            console.log(e)
            messages('error', e.data.message)
        }
    }
}
const displayUser = async () => {
    try {
        await axios
            .get("http://127.0.0.1:8000/api/user", {
                headers: { Authorization: `Bearer ${user.token}` },
            })
            .then(function (response) {
                dataUser.value = response.data.data;
            });
    } catch (e) {
        console.log(e);
    }
};
if (prop.mode === 'update') {
    loadDepartment()
}
displayUser()
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
        checkLanding.checkName = 'Please enter name'
    } if (form.address === '') {
        checkLanding.checkAddress = 'Please enter address'
    }
    if (form.email === '') {
        checkLanding.checkEmail = 'Please enter email'
    } if (!isEmail(form.email)) {
        checkLanding.checkEmail = 'Invalid email'
    } if (form.phoneNumber === '') {
        checkLanding.checkPhoneNumber = 'Please enter phone number'
    } if (isPhoneNumber(form.isPhoneNumber)) {
        checkLanding.checkPhoneNumber = 'Invalid phone number'
    }
    if (checkLanding.checkName == '' && checkLanding.checkAddress == '' && checkLanding.checkPhoneNumber == '' && checkLanding.checkEmail == '') {
        return true;
    } else {
        return false;
    }
}

function createDepartment() {
    if (validate()) {
        let manager = form.manager === 0 ? null : form.manager
        axios.post(
            `http://127.0.0.1:8000/api/create-department/`,
            {
                departmentName: form.name,
                address: form.address,
                email: form.email,
                phoneNumber: form.phoneNumber,
                manager : manager
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
            form.manager = 0
            messages('success', 'Create success')
        }).catch((e) => {
            messages('error', e.response.data.message)
            console.log(e)
        })
    }
}

function updateDepartment() {
    if (validate()) {
        let manager = form.manager === 0 ? null : form.manager
        console.log(manager)
        axios
            .put(
                `http://127.0.0.1:8000/api/update-department/${prop.departmentId}`,
                {
                    departmentName: form.name,
                    address: form.address,
                    email: form.email,
                    phoneNumber: form.phoneNumber,
                    manager: manager
                },
                {
                    headers: { Authorization: `Bearer ${user.token}` },
                }
            ).then(function (response) {
                emits('updateData')
                console.log(response)
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
  