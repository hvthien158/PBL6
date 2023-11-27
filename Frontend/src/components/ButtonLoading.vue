<template>
  <el-button :disabled="exec" style="position: relative;">
    <div v-if="exec">
      <img src="../assets/loading.svg">
    </div>
    <span v-else class="Button__Content">
      <slot/>
    </span>
    <span v-if="warning" class="icon-warn">
      <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-circle-fill" viewBox="0 0 16 16">
        <circle cx="8" cy="8" r="8"/>
      </svg>
    </span>
  </el-button>
</template>

<style scoped>
.icon-warn{
  position: absolute;
  top: -4px;
  right: -2px;
  color: #ff3c00;
}
</style>

<script setup>
import {ref, watch, watchEffect} from "vue";

const prop = defineProps({
  loading: {
    type: Boolean,
    default: false
  },
  warning: {
    type: Boolean,
  }
})
const exec = ref(false)

watch(() => prop.loading,
    () => {
      if (prop.loading === true) {
        exec.value = true
      } else {
        setTimeout(() => {
          exec.value = false
        }, 1000)
      }
    })
</script>
