<template>
  <time>
    <span id="hours">{{ hour }}</span>
    <span class="blink">&thinsp;:&thinsp;</span>
    <span id="minutes">{{ minute }}</span>
  </time>
</template>

<style scoped>
@import url("https://fonts.googleapis.com/css?family=Montserrat:400,600,800");
* {
  font-family: "Montserrat", sans-serif;
}

.wobble {
  -webkit-animation: wobble 0.8s both;
  animation: wobble 0.8s both;
}

@-webkit-keyframes wobble {
  0%, 100% {
    transform: translateX(0%);
    transform-origin: 50% 50%;
  }
  15% {
    transform: translateX(-15px) rotate(-3deg);
  }
  30% {
    transform: translateX(7.5px) rotate(3deg);
  }
  45% {
    transform: translateX(-7.5px) rotate(-1.8deg);
  }
  60% {
    transform: translateX(4.5px) rotate(1.2deg);
  }
  75% {
    transform: translateX(-3px) rotate(-0.6deg);
  }
}

@keyframes wobble {
  0%, 100% {
    transform: translateX(0%);
    transform-origin: 50% 50%;
  }
  15% {
    transform: translateX(-15px) rotate(-3deg);
  }
  30% {
    transform: translateX(7.5px) rotate(3deg);
  }
  45% {
    transform: translateX(-7.5px) rotate(-1.8deg);
  }
  60% {
    transform: translateX(4.5px) rotate(1.2deg);
  }
  75% {
    transform: translateX(-3px) rotate(-0.6deg);
  }
}
* {
  background-size: cover;
  font-size: 192px;
  font-weight: 800;
  color: rgba(243, 149, 45, 0.7);
  margin: 0 auto;
  display: flex;
  justify-content: center;
  align-items: center;
}

time {
  width: 800px;
  height: 400px;
  padding: 24px 128px;
  border: 10px solid rgba(243, 149, 45, 0.7);
}

@-webkit-keyframes blink {
  to {
    visibility: hidden;
  }
}
.blink {
  -webkit-animation: blink 2s steps(2, start) infinite;
  animation: blink 2s steps(2, start) infinite;
}
</style>

<script setup>
import moment from "moment"
import {onUnmounted, ref} from "vue";

const hour = ref('00')
const minute = ref('00')

const time = setInterval(() => {
  hour.value = moment().format('HH')
  minute.value = moment().format('mm')
}, 1000)

onUnmounted(() => {
  clearInterval(time)
})
</script>
