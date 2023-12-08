<template>
  <div class="btn-status">
    <div @click="changeStatus" class="status-btn" :class="{'bg-green': index === 0, 'bg-purple': index === 1, 'bg-gray': index === 2}">
      <span class="unselectable">{{ quotes }}</span>
    </div>
  </div>
</template>

<script setup>
import {computed, ref, watch} from "vue";

const prop = defineProps({
  status_index: {
    type: Number,
    default: 0,
  }
})
const emits = defineEmits(['changeStatus'])

const option = ['Work', 'Remote', 'Not work']
const index = ref(prop.status_index)

watch(() => prop.status_index, () => {
  index.value = prop.status_index
})

const quotes = computed(() => {
  return option[index.value]
})

function changeStatus(){
  index.value = (index.value === 2) ? 0 : index.value + 1
  emits('changeStatus', index.value)
}
</script>

<style scoped>
.btn-status{
  width: 100%;
}
.status-btn{
  height: 30px;
  border-radius: 6px;
  text-align: center;
  line-height: 30px;
  font-size: small;
  margin: 12px;
  font-weight: bold;
  box-shadow: 0 1px 1px 0 #b0b0b0;
}
.status-btn:hover{
  cursor: pointer;
}
.bg-green{
  background-color: #04fc43;
}
.bg-gray{
  background-color: #ccc;
}
.bg-purple{
  background-color: rgba(0,120,248,0.57)
}
.unselectable {
  -webkit-user-select: none;
  -webkit-touch-callout: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
</style>
