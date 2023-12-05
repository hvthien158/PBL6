<template>
    <el-dialog v-model="visible" :close-on-press-escape="false" :close-on-click-modal="false" :show-close="false"
        @keyup.esc="$emit('invisible')"  style="width:30%">
        <template #header>
            <div class="my-header">
                <h4>Department Manager</h4>
                <el-button type="danger" @click="$emit('invisible')">
                    <el-icon class="el-icon--left">
                        <CircleCloseFilled />
                    </el-icon>
                    Close
                </el-button>
            </div>
        </template>
        <div class="user">
            <el-avatar style="margin-right: 10px;" shape="circle" :size="100" :src="manager.avatar" />
            <el-text size="large" type="primary">{{ manager.name }}</el-text>
        </div>
        <div class="info-user">
            <el-text>Email: {{ manager.email }}</el-text>
            <el-text>Address: {{ manager.address }}</el-text>
            <el-text>Phone number: {{ manager.phone_number }}</el-text>
            <el-text>DOB: {{ manager.DOB }}</el-text>
            <el-text>Position: {{ manager.position }}</el-text>
            <el-text>Role: {{ manager.role }}</el-text>
            <el-text>Salary: {{ manager.salary }}</el-text>
        </div>
        <template #footer>
            <span class="dialog-footer">
                <el-button @click="$emit('invisible')">Cancel</el-button>
            </span>
        </template>
    </el-dialog>
</template>
  
<style scoped>
.my-header {
    display: flex;
    justify-content: space-between;
}

.user {
    display: flex;
    align-items: center;
    min-width: 30%
}
.user .el-text {
    font-size: 20px;
    font-weight: 700;
}
.info-user {
    cursor: default;
}
.info-user .el-text{
    display: block;
    margin-top: 15px;
    font-size: 16px;
    border: 1px solid #ebeef5;
    padding: 8px;
    box-sizing: border-box;
}
</style>
  
<script setup>
import { ref, defineProps } from "vue";
import { useUserStore } from "../stores/user";
import { useAlertStore } from "../stores/alert";
import UserAPI from "../services/UserAPI";
const prop = defineProps({
    userId: {
        type: Number
    }
})
let visible = ref(false)
const manager = ref();
const user = useUserStore().user
const alertStore = useAlertStore()
const emits = defineEmits(['invisible', 'updateData'])
const loadUser = async () => {
    try {
        await UserAPI.get1User(user.token, prop.userId).then((response) => {
            console.log(response)
            manager.value = response.data.data
            visible.value = true
        })
    } catch (e) {
        console.log(e)
        messages('error', e.data.message)
    }
}
loadUser()
const messages = (type, msg) => {
    alertStore.alert = true;
    alertStore.type = type;
    alertStore.msg = msg
}
</script>
  