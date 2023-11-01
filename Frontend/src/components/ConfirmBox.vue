<template>
  <div class='dialog-overlay'>
    <div class='dialog'>
      <header>
        <h3>{{ props.title }}</h3>
        <i class='fa fa-close'></i>
      </header>
      <div class='dialog-msg'>
        <p>{{ props.msg }}</p>
      </div>
      <footer>
        <div class='controls'>
          <button class='button button-danger' @click="confirmHandle">Confirm</button>
          <button class='button button-default' @click="cancelHandle">Cancel</button>
        </div>
      </footer>
    </div>
  </div>
</template>

<style scoped>
.dialog-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.50);
  z-index: 999999
}
.dialog-overlay .dialog {
  width: 400px;
  margin: 100px auto 0;
  background-color: #fff;
  box-shadow: 0 0 20px rgba(0,0,0,.2);
  border-radius: 3px;
  overflow: hidden
}
.dialog-overlay .dialog header {
  padding: 10px 8px;
  background-color: #f6f7f9;
  border-bottom: 1px solid #e5e5e5
}
.dialog-overlay .dialog header h3 {
  font-size: 14px;
  margin: 0;
  color: #555;
  display: inline-block
}
.dialog-overlay .dialog header .fa-close {
  float: right;
  color: #c4c5c7;
  cursor: pointer;
  transition: all .5s ease;
  padding: 0 2px;
  border-radius: 1px
}
.dialog-overlay .dialog header .fa-close:hover {
  color: #b9b9b9
}
.dialog-overlay .dialog header .fa-close:active {
  box-shadow: 0 0 5px #673AB7;
  color: #a2a2a2
}
.dialog-overlay .dialog .dialog-msg {
  padding: 12px 10px
}
.dialog-overlay .dialog .dialog-msg p{
  margin: 0;
  font-size: 15px;
  color: #333
}
.dialog-overlay .dialog footer {
  border-top: 1px solid #e5e5e5;
  padding: 8px 10px
}
.dialog-overlay .dialog footer .controls {
  direction: rtl
}
.dialog-overlay .dialog footer .controls .button {
  padding: 5px 15px;
  border-radius: 3px
}
.button {
  cursor: pointer
}
.button-default {
  margin: 0 6px;
  background-color: rgb(248, 248, 248);
  border: 1px solid rgba(204, 204, 204, 0.5);
  color: #5D5D5D;
}
.button-danger {
  background-color: #f44336;
  border: 1px solid #d32f2f;
  color: #f5f5f5;
  margin: 0 6px;
}
</style>

<script setup>
const props = defineProps({
  title: String,
  msg: String
})

const emits = defineEmits(['confirm', 'cancel'])

function confirmHandle(){
  emits('confirm')
}

function cancelHandle(){
  emits('cancel')
}
</script>
