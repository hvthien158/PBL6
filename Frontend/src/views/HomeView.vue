<template>
  <main>
    <div class="home-container">
      <div>
        <HomeTimeKeepCard
            :date="moment().format('YYYY-MM-DD')"
            :user_id="user.id"
            :status_AM_prop="today.status_AM"
            :status_PM_prop="today.status_PM"
        ></HomeTimeKeepCard>
      </div>
      <div class="checkin-container">
        <div class="time">
          <Clock style="scale: 0.9"></Clock>
        </div>
        <div class="date">
          <span class="date-span">{{ currentDate }}</span>
        </div>
        <div class="button-check">
          <button
            id="check-in-button"
            :class="[checkin ? 'active-button' : 'disable-button']"
            @click="handleCheckIn"
            :disabled="!checkin"
          >
            Check in
          </button>
          <button
            id="check-out-button"
            :class="[checkout ? 'active-button' : 'disable-button']"
            @click="handleCheckOut"
            :disabled="!checkout"
          >
            Check out
          </button>
        </div>
        <button @click="router.push({name: 'schedule'})" class="active-button no-underline" style="margin-top: 20px; height: 40px">History</button>
      </div>
      <div v-if="user.role === 'admin'">
        <HomeStatusCard></HomeStatusCard>
      </div>
      <div v-else style="min-width: 20vw;"></div>
    </div>
  </main>
</template>
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
}

.date {
  margin: 50px 0 10px 0px;
}

.date-span {
  font-size: 18px;
  font-family: "JetBrains Mono",monospace;
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
  box-shadow: rgba(45, 35, 66, .4) 0 2px 4px,rgba(45, 35, 66, .3) 0 7px 13px -3px,rgba(58, 65, 111, .5) 0 -3px 0 inset;
  box-sizing: border-box;
  color: #fff;
  display: inline-flex;
  font-family: "JetBrains Mono",monospace;
  justify-content: center;
  line-height: 1;
  list-style: none;
  overflow: hidden;
  padding-left: 16px;
  padding-right: 16px;
  position: relative;
  text-align: left;
  text-decoration: none;
  transition: box-shadow .15s,transform .15s;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  white-space: nowrap;
  will-change: box-shadow,transform;
  font-size: 18px;
}
.active-button:hover{
  box-shadow: rgba(45, 35, 66, .4) 0 4px 8px, rgba(45, 35, 66, .3) 0 7px 13px -3px, rgba(58, 65, 111, .5) 0 -3px 0 inset;
  transform: translateY(-2px);
}
.disable-button{
  width: 120px;
  height: 40px;
  margin: 10px;
  align-items: center;
  appearance: none;
  background-image: radial-gradient(100% 100% at 100% 0, #FCFCFD 0, #9ca3af 100%);
  border-radius: 4px;
  border-width: 0;
  box-shadow: rgba(45, 35, 66, 0.4) 0 2px 4px,rgba(45, 35, 66, 0.3) 0 7px 13px -3px,#D6D6E7 0 -3px 0 inset;
  box-sizing: border-box;
  color: #36395A;
  display: inline-flex;
  font-family: "JetBrains Mono",monospace;
  justify-content: center;
  line-height: 1;
  list-style: none;
  overflow: hidden;
  padding-left: 16px;
  padding-right: 16px;
  position: relative;
  text-align: left;
  text-decoration: none;
  transition: box-shadow .15s,transform .15s;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  white-space: nowrap;
  will-change: box-shadow,transform;
  font-size: 18px;
}

#time-span {
  color: white;
  font-size: 100px;
  font-family: 'alarm clock', sans-serif;
}
</style>

<script setup>
import { ref, reactive, onMounted } from "vue";
import router from "../router";
import axios from "axios";
import {useUserStore} from "../stores/user";
import moment from "moment";
import Clock from "../components/Clock.vue";
import HomeStatusCard from "../components/HomeStatusCard.vue";
import HomeTimeKeepCard from "../components/HomeTimeKeepCard.vue";
import {useStatusStore} from "../stores/status";
import { useAlertStore } from "../stores/alert";
const user = useUserStore().user
const status = useStatusStore()
const alertStore = useAlertStore()
if (user.token === '') {
  router.push({ path: "/login" });
}
const checkin = ref(false)
const checkout = ref(false)
const currentDate = ref(getCurrentDate());
const currentTime = ref(getCurrentTime());
const today = ref({
  status_AM: 0,
  status_PM: 0,
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
    await axios
      .get("http://127.0.0.1:8000/api/time-keeping", {
        headers: {
          Authorization: `Bearer ${user.token}`
        },
      })
      .then(function (response) {
        today.value.status_AM = response.data.status_AM
        today.value.status_PM = response.data.status_PM
        status.status_AM = today.value.status_AM
        status.status_PM = today.value.status_PM
        if(response.data.data){
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
    console.log(1)
    await axios.post("http://127.0.0.1:8000/api/check-in",{} ,
        {
          headers: {
            Authorization: `Bearer ${user.token}`,
          },
        }
      )
      .then(function (response) {
        getTimeKeeping();
      });
  } catch (e) {
    messages('error', e.response.data.message)
  }
};

const handleCheckOut = async () => {
    try {
      await axios.put(
          `http://127.0.0.1:8000/api/check-out/`, null,
          {
            headers: {
              Authorization: `Bearer ${user.token}`,
            },
          }
        )
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
