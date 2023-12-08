<template>
  <main>
    <div id="time">
      <div class="circle" style="--clr: #ff2972">
        <div class="dots hr_dot"></div>
        <svg>
          <circle :cx="circle.cx" :cy="circle.cy" :r="circle.r"></circle>
          <circle :cx="circle.cx" :cy="circle.cy" :r="circle.r" id="hh"></circle>
        </svg>
        <div id="hours">{{ hour }}</div>
      </div>
      <div class="circle" style="--clr: #fee800">
        <div class="dots min_dot"></div>
        <svg>
          <circle :cx="circle.cx" :cy="circle.cx" :r="circle.r"></circle>
          <circle :cx="circle.cx" :cy="circle.cx" :r="circle.r" id="mm"></circle>
        </svg>
        <div id="minutes">{{ minute }}</div>
      </div>
      <div class="circle" style="--clr: #04fc43">
        <div class="dots sec_dot"></div>
        <svg>
          <circle :cx="circle.cx" :cy="circle.cx" :r="circle.r"></circle>
          <circle :cx="circle.cx" :cy="circle.cx" :r="circle.r" id="ss"></circle>
        </svg>
        <div id="seconds">{{ second }}</div>
      </div>
      <div class="ap">
        <div id="ampm" style="font-size: 0.5em">{{ ampm }}</div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { onMounted, onUnmounted, reactive, ref } from "vue";
import moment from "moment"

const hour = ref('')
const minute = ref('')
const second = ref('')
const ampm = ref('')
const time = ref(0);
let circle = reactive({
  cx: 70,
  cy: 70,
  r: 70
})
onMounted(() => {
  if (window.innerWidth <= 600) {
    circle.cx = 40;
    circle.cy = 40
    circle.r = 40
  } else {
    circle.cx = 70;
    circle.cy = 70
    circle.r = 70
  }
})
window.addEventListener('resize', () => {
  if (window.innerWidth <= 600) {
    circle.cx = 40;
    circle.cy = 40
    circle.r = 40

  } else {
    circle.cx = 70;
    circle.cy = 70
    circle.r = 70
  }
})
onMounted(() => {
  let hh = document.getElementById('hh')
  let mm = document.getElementById('mm')
  let ss = document.getElementById('ss')
  let hr_dot = document.querySelector('.hr_dot')
  let min_dot = document.querySelector('.min_dot')
  let sec_dot = document.querySelector('.sec_dot')

  time.value = setInterval(() => {
    hour.value = moment().format('hh')
    minute.value = moment().format('mm')
    second.value = moment().format('ss')
    ampm.value = moment().format('A')

    hh.style.strokeDashoffset = '' + ((circle.r * 2 * 3.14 - (circle.r * 2 * 3.14 * parseInt(hour.value)) / 12))
    mm.style.strokeDashoffset = '' + ((circle.r * 2 * 3.14 - (circle.r * 2 * 3.14 * parseInt(minute.value)) / 60))
    ss.style.strokeDashoffset = '' + ((circle.r * 2 * 3.14 - (circle.r * 2 * 3.14 * parseInt(second.value)) / 60))
    hr_dot.style.transform = `rotate(${hour.value * 30}deg)`
    min_dot.style.transform = `rotate(${minute.value * 6}deg)`
    sec_dot.style.transform = `rotate(${second.value * 6}deg)`
  }, 1000)
})

onUnmounted(() => {
  clearInterval(time.value)
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800,900&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Poppins', sans-serif;
}

main {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 300px;
}

#time {
  padding-left: 2rem;
  display: flex;
  gap: 40px;
  color: #000;
}

#time .circle {
  position: relative;
  width: 150px;
  height: 150px;
  display: flex;
  justify-content: center;
  align-items: center;
}

#time .circle svg {
  position: relative;
  width: 150px;
  height: 150px;
  transform: rotate(270deg);
}

#time .circle svg circle {
  width: 100%;
  height: 100%;
  fill: transparent;
  stroke: #808080;
  stroke-width: 8;
  transform: translate(5px, 5px);
}

#time .circle svg circle:nth-child(2) {
  stroke: var(--clr);
  stroke-dasharray: 440;
}

#time div {
  position: absolute;
  text-align: center;
  font-weight: 500;
  font-size: 3rem;
}

#time .ap {
  position: relative;
  transform: translateX(-20px);
}

.dots {
  position: absolute;
  width: 100%;
  height: 100%;
  z-index: 10;
  display: flex;
  justify-content: center;
}

.dots::before {
  content: '';
  position: absolute;
  top: -3px;
  width: 15px;
  height: 15px;
  background: var(--clr);
  border-radius: 50%;
  box-shadow: 0 0 20px var(--clr), 0 0 60px var(--clr);
}

@media screen and (max-width : 600px) {
  .dots::before {
    top: 0px;
    width: 12px;
    height: 12px;
  }

  #time .circle svg circle:nth-child(2) {
    stroke: var(--clr);
    stroke-dasharray: 251.1;
  }

  #time .circle {
    width: 90px;
    height: 90px;
  }

  #time .circle svg {
    width: 90px;
    height: 90px;
  }

  #time {
    max-width: 100%;
    padding-left: 0;
    gap: 20px
  }
  #time div {
    font-weight: 700;
    font-size: 2rem;
  }
}
</style>
