<template>
<div :class="[props.type]" class="alert-container">
  <el-icon v-if="props.type === 'success'" style="font-weight: bold" size="30"><CircleCheckFilled /></el-icon>
  <el-icon v-if="props.type === 'warning'" style="font-weight: bold" size="30"><WarningFilled /></el-icon>
  <el-icon v-if="props.type === 'error'" style="font-weight: bold" size="30"><CircleCloseFilled /></el-icon>
  <span style="font-weight: 600; margin-left: 6px;">{{ props.msg }}</span>
</div>
</template>

<script setup>
import {CircleCheckFilled, CircleCloseFilled, WarningFilled} from "@element-plus/icons-vue";

const props = defineProps({
  type: String,
  msg: String
})
</script>

<style scoped>
*{
  color: white;
}
.alert-container{
  height: 80px;
  width: 400px;
  position: fixed;
  z-index: 999;
  right: 2vw;
  bottom: 2vw;
  border-radius: 10px;
  display: flex;
  align-items: center;
  padding-left: 20px;
  font-size: large;
}
.success{
  background-color: #3ebd61;
}
.warning{
  background-color: #ef9400;
}
.error{
  background-color: #ec4d2b;
}
</style>
