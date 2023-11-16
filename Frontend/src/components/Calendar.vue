<template>
  <div class="calendar">
    <div class="month">
      <div>{{ moment().format('MMMM') }} <span class="year">{{ moment().format('YYYY') }}</span></div>
    </div>
    <div class="days">
      <span>Mon</span>
      <span>Tue</span>
      <span>Wed</span>
      <span>Thu</span>
      <span>Fri</span>
      <span>Sat</span>
      <span>Sun</span>
    </div>
    <div class="dates">
      <button v-for="n in moment().daysInMonth()" class="day" :class="{'today': checkToday(n)}">
        <time>{{ n }}</time>
      </button>
    </div>
  </div>
</template>

<style scoped>
@import url("https://fonts.googleapis.com/css?family=Questrial&display=swap");
* {
  font-family: "Questrial", sans-serif;
  display: flex;
  justify-content: center;
  align-items: center;
}

.calendar {
  display: inline-grid;
  justify-content: center;
  align-items: center;
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 40px 30px -20px rgba(0, 0, 0, 0.3);
}
.calendar .month {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 14px;
  margin-bottom: 20px;
  font-weight: 300;
}
.calendar .month .year {
  font-weight: 600;
  margin-left: 10px;
}
.calendar .month .nav {
  display: flex;
  justify-content: center;
  align-items: center;
  text-decoration: none;
  color: #0a3d62;
  width: 10px;
  height: 10px;
  border-radius: 40px;
  transition-duration: 0.2s;
  position: relative;
}
.calendar .month .nav:hover {
  background: #eee;
}
.calendar .days {
  display: grid;
  justify-content: center;
  align-items: center;
  grid-template-columns: repeat(7, 40px);
  color: #999;
  font-weight: 600;
  margin-bottom: 15px;
}
.calendar .days span {
  width: 50px;
  font-size: 14px;
  justify-self: center;
  align-self: center;
  text-align: center;
}
.calendar .dates {
  display: grid;
  grid-template-columns: repeat(7, 40px);
}
.calendar .dates button {
  cursor: pointer;
  outline: 0;
  border: 0;
  background: transparent;
  font-family: "Montserrat", sans-serif;
  font-size: 14px;
  justify-self: center;
  align-self: center;
  width: 30px;
  height: 30px;
  border-radius: 50px;
  margin: 2px;
  transition-duration: 0.2s;
  padding-top: 2px;
}
.calendar .dates button.today {
  background-color: rgba(243, 149, 45, 0.5);
}
.calendar .dates button:hover {
  background: #eee;
}
.calendar .dates button:focus {
  background: #0a3d62;
  color: #fff;
  font-weight: 600;
}
</style>

<script setup>
import moment from "moment"
import {onMounted} from "vue";

function checkToday(n){
  return '' + n === moment().format('D');
}

onMounted(() => {
  let first_day = document.getElementsByClassName('day')
  first_day[0].style.gridColumn = moment().startOf('month').day()
})
</script>
