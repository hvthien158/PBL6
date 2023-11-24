<template>
  &emsp;
  <span v-if="total_page_prop < 6" v-for="n in total_page_prop">
    <span :class="{'current': current_page === n}" @click="current_page = n">&thinsp;{{ n }}&thinsp;</span>
  </span>
  <span v-else v-for="item in custom_page">
    <span :class="{'current': current_page === item}" @click="changePage(item)">&thinsp;{{ item }}&thinsp;</span>
  </span>
  &emsp;
</template>

<style scoped>
.current {
  font-weight: bold;
  color: #1cb966;
}
span > span:hover{
  cursor: pointer;
}
</style>

<script setup>
import {onMounted, ref, watch} from "vue";

const prop = defineProps({
  current_page_prop: {
    type: Number,
    default: 0,
  },
  total_page_prop: {
    type: Number,
    default: 0,
  },
})
const emits = defineEmits(['changePage'])
const custom_page = ref([])
const current_page = ref(0)

watch(() => prop.current_page_prop, () => {
  current_page.value = prop.current_page_prop
})

watch(() => current_page.value, () => {
  custom_page.value = []
  if(prop.total_page_prop >= 6){
    custom_page.value.push(1)
    if([1, 2, 3].includes(current_page.value)){
      custom_page.value.push(2)
      custom_page.value.push(3)
      custom_page.value.push(4)
      custom_page.value.push('...')
    } else if ([prop.total_page_prop - 2, prop.total_page_prop - 1, prop.total_page_prop].includes(current_page.value)) {
      custom_page.value.push('...')
      custom_page.value.push(prop.total_page_prop - 3)
      custom_page.value.push(prop.total_page_prop - 2)
      custom_page.value.push(prop.total_page_prop - 1)
    } else {
      custom_page.value.push('...')
      custom_page.value.push(current_page.value - 1)
      custom_page.value.push(current_page.value)
      custom_page.value.push(current_page.value + 1)
      custom_page.value.push('...')
    }
    custom_page.value.push(prop.total_page_prop)
  }
  emits('changePage', current_page.value)
})

function changePage(page){
  if(page !== '...'){
    current_page.value = page
  }
}

onMounted(() => {
  current_page.value = prop.current_page_prop
})
</script>
