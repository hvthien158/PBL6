<template>
  <main>
    <div class="home-container">
      <div class="calendar" v-if="!responsive">
        <HomeTimeKeepCard :date="moment().format('YYYY-MM-DD')" :user_id="user.id" :status_AM_prop="today.status_AM"
          :status_PM_prop="today.status_PM"></HomeTimeKeepCard>
      </div>
      <div class="checkin-container">
        <div class="time">
          <Clock style="scale: 0.9"></Clock>
        </div>
        <div class="date">
          <span class="date-span">{{ currentDate }}</span>
        </div>
        <div class="button-check">
          <button id="check-in-button" :class="[checkin ? 'active-button' : 'disable-button']" @click="handleCheckIn"
            :disabled="!checkin">
            Check in
          </button>
          <button id="check-out-button" :class="[checkout ? 'active-button' : 'disable-button']" @click="handleCheckOut"
            :disabled="!checkout">
            Check out
          </button>
        </div>
        <button @click="router.push({ name: 'schedule' })" class="active-button no-underline"
          style="margin-top: 20px; height: 40px">History</button>
      </div>
      <div class="calendar" v-if="responsive">
        <HomeTimeKeepCard :date="moment().format('YYYY-MM-DD')" :user_id="user.id" :status_AM_prop="today.status_AM"
          :status_PM_prop="today.status_PM"></HomeTimeKeepCard>
      </div>
      <div class="home-status" v-if="user.role === 'admin'">
        <HomeStatusCard></HomeStatusCard>
      </div>
      <div v-else style="min-width: 20vw;"></div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from "vue";
import router from "../router";
import { useUserStore } from "../stores/user";
import moment from "moment";
import Clock from "../components/Clock.vue";
import HomeStatusCard from "../components/HomeStatusCard.vue";
import HomeTimeKeepCard from "../components/HomeTimeKeepCard.vue";
import { useStatusStore } from "../stores/status";
import { useAlertStore } from "../stores/alert";
import TimeKeepAPI from "../services/TimeKeepAPI";
const user = useUserStore().user
const status = useStatusStore()
const alertStore = useAlertStore()
if (user.token === '') {
  router.push({ name: 'login' });
}
const checkin = ref(false)
const checkout = ref(false)
const responsive = ref(false)
const currentDate = ref(getCurrentDate());
const currentTime = ref(getCurrentTime());
const today = ref({
  status_AM: 0,
  status_PM: 0,
})
onMounted(() => {
  if (window.innerWidth <= 1280) {
    responsive.value = true
  } else {
    responsive.value = false
  }
})
window.addEventListener('resize', () => {
  if (window.innerWidth <= 1280) {
    responsive.value = true
  } else {
    responsive.value = false
  }
})
function getCurrentDate() {
  const now = new Date();
  const options = {
    day: "numeric",
    month: "long",
    year: "numeric",
  };
  return now.toLocaleDateString("en-US", options);
}

function getCurrentTime() {
  const now = new Date();
  const options = {
    hour: "numeric",
    minute: "numeric",
    second: "numeric",
    hour12: false,
  };

  let hours = now.getHours();
  let minutes = now.getMinutes();
  let seconds = now.getSeconds();

  if (hours === 23 && minutes === 59 && seconds === 59) {
    hours = 0;
    minutes = 0;
    seconds = 0;
  }

  const formattedTime = `${hours.toString().padStart(2, "0")}:${minutes
    .toString()
    .padStart(2, "0")}:${seconds.toString().padStart(2, "0")}`;

  return formattedTime;
}
onMounted(() => {
  getTimeKeeping();
});
const getTimeKeeping = async () => {
  try {
    await TimeKeepAPI.getMyTodayTimeKeep(user.token)
      .then(function (response) {
        today.value.status_AM = response.data.status_AM
        today.value.status_PM = response.data.status_PM
        status.status_AM = today.value.status_AM
        status.status_PM = today.value.status_PM
        if (response.data.data) {
          if (response.data.data.time_check_in && response.data.data.time_check_in !== '00:00:00') {
            if (response.data.data.time_check_out && response.data.data.time_check_out !== '00:00:00') {
              checkin.value = false;
              checkout.value = false;
            } else {
              checkin.value = false;
              checkout.value = true;
            }
          } else {
            checkin.value = true;
            checkout.value = false;
          }
        } else if (response.data.weekend){
          checkin.value = false;
          checkout.value = false;
        } else {
          checkin.value = true;
          checkout.value = false;
        }
      });
  } catch (e) {
    console.log(e);
  }
  currentDate.value = getCurrentDate();
}

const handleCheckIn = async () => {
  try {
    await TimeKeepAPI.checkIn(user.token)
      .then(function (response) {
        getTimeKeeping();
      });
  } catch (e) {
    messages('error', e.response.data.message)
  }
};

const handleCheckOut = async () => {
  try {
    await TimeKeepAPI.checkOut(user.token)
      .then(function (response) {
        getTimeKeeping();
      });
  } catch (e) {
    messages('error', e.response.data.message)
  }
}
const messages = (type, msg) => {
  alertStore.alert = true;
  alertStore.type = type;
  alertStore.msg = msg
}
setInterval(() => {
  currentTime.value = getCurrentTime();
  currentDate.value = getCurrentDate();
}, 1000);
</script>

<style scoped>
main {
  display: flex;
  justify-content: center;
  align-items: center;
}

.home-container {
  width: 90vw;
  display: flex;
  justify-content: space-between;
  align-items: center;
  overflow: auto;
}

.checkin-container {
  min-height: 20px;
  padding: 30px;
  min-width: 50%;
  margin-bottom: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  border-radius: 10px;
  margin-bottom: auto;
}
.date {
  margin: 50px 0 10px 0px;
}

.date-span {
  font-size: 18px;
  font-family: "JetBrains Mono", monospace;
}

.time {
  display: flex;
  align-items: center;
  height: 30vh;
}

.time img {
  max-height: 50px;
  margin-right: 10px;
}

.button-check {
  display: flex;
  margin-top: 32px;
}

.active-button {
  width: 120px;
  height: 40px;
  margin: 10px;
  cursor: pointer;
  align-items: center;
  appearance: none;
  background-image: radial-gradient(100% 100% at 100% 0, #62f197 0, #1cb966 100%);
  border: 0;
  border-radius: 6px;
  box-shadow: rgba(45, 35, 66, .4) 0 2px 4px, rgba(45, 35, 66, .3) 0 7px 13px -3px, rgba(58, 65, 111, .5) 0 -3px 0 inset;
  box-sizing: border-box;
  color: #fff;
  display: inline-flex;
  font-family: "JetBrains Mono", monospace;
  justify-content: center;
  line-height: 1;
  list-style: none;
  overflow: hidden;
  padding-left: 16px;
  padding-right: 16px;
  position: relative;
  text-align: left;
  text-decoration: none;
  transition: box-shadow .15s, transform .15s;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  white-space: nowrap;
  will-change: box-shadow, transform;
  font-size: 18px;
}

.active-button:hover {
  box-shadow: rgba(45, 35, 66, .4) 0 4px 8px, rgba(45, 35, 66, .3) 0 7px 13px -3px, rgba(58, 65, 111, .5) 0 -3px 0 inset;
  transform: translateY(-2px);
}

.disable-button {
  width: 120px;
  height: 40px;
  margin: 10px;
  align-items: center;
  appearance: none;
  background-image: radial-gradient(100% 100% at 100% 0, #FCFCFD 0, #9ca3af 100%);
  border-radius: 4px;
  border-width: 0;
  box-shadow: rgba(45, 35, 66, 0.4) 0 2px 4px, rgba(45, 35, 66, 0.3) 0 7px 13px -3px, #D6D6E7 0 -3px 0 inset;
  box-sizing: border-box;
  color: #36395A;
  display: inline-flex;
  font-family: "JetBrains Mono", monospace;
  justify-content: center;
  line-height: 1;
  list-style: none;
  overflow: hidden;
  padding-left: 16px;
  padding-right: 16px;
  position: relative;
  text-align: left;
  text-decoration: none;
  transition: box-shadow .15s, transform .15s;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  white-space: nowrap;
  will-change: box-shadow, transform;
  font-size: 18px;
}

#time-span {
  color: white;
  font-size: 100px;
  font-family: 'alarm clock', sans-serif;
}

@media screen and (max-width : 1280px) {

  .home-status {
    display: none;
  }

  .home-container {
    display: block;
    justify-content: center;
  }

  .calendar {
    margin-top: auto;
    display: flex;
    justify-content: center;
  }
  .time{
    max-height: 20vh;
  }
}

@media screen and (max-width: 768px) {

}
</style>
