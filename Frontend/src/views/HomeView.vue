<template>
  <main>
    <div class="container">
      <div class="checkin-container">
        <div class="time">
          <span id="time-span">{{ currentTime }}</span>
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
            Check In
          </button>
          <button
            id="check-out-button"
            :class="[checkout ? 'active-button' : 'disable-button']"
            @click="handleCheckOut"
            :disabled="!checkout"
          >
            Check Out
          </button>
        </div>
        <router-link to="/schedule">
          <button class="active-button" style="margin-top: 20px; height: 40px">History</button>
        </router-link>
      </div>
    </div>
  </main>
</template>
<style scoped>
main {
  max-width: 100vw;
  min-height: 82vh;
  display: flex;
  justify-content: center;
  align-items: center;
}
.container {
  display: flex;
  justify-content: center;
  align-items: center;
}
.checkin-container {
  min-height: 20px;
  padding: 30px;
  min-width: 50%;
  background-color: #2b2b2b;
  margin-bottom: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  border-radius: 10px;
}

.date {
  margin: 10px 0px 10px 0px;
}

.date-span {
  font-size: 18px;
  font-family: "JetBrains Mono",monospace;
}

.time {
  display: flex;
  align-items: center;
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
  height: 120px;
  margin: 10px;
  cursor: pointer;
  align-items: center;
  appearance: none;
  background-image: radial-gradient(100% 100% at 100% 0, #f9ac38 0, #d95827 100%);
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
  height: 120px;
  margin: 10px;
  align-items: center;
  appearance: none;
  background-image: radial-gradient(100% 100% at 100% 0, #FCFCFD 0, #9ca3af 100%);;
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
import schedule from "../assets/image/schedule.png";

const user = useUserStore().user
if (user.token === '') {
  router.push({ path: "/login" });
}
const checkin = ref(false)
const checkout = ref(false)
const currentDate = ref(getCurrentDate());
const currentTime = ref(getCurrentTime());

function getCurrentDate() {
  const now = new Date();
  const options = {
    day: "numeric",
    month: "short",
    year: "numeric",
  };
  return now.toLocaleString("us-EN", options);
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
        if (response.status == 200) {
          if (response.data.time_check_in) {
            if (response.data.time_check_out) {
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
        }
      });
  } catch (e) {
    console.log(e);
  }
  currentDate.value = getCurrentDate();
};

const handleCheckIn = async () => {
  try {
    await axios

      .post("http://127.0.0.1:8000/api/check-in",{} ,
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
    console.log(user.token);
  }
};

const handleCheckOut = async () => {
    try {
      await axios
        .put(
          `http://127.0.0.1:8000/api/check-out/`, null,
          {
            headers: {
              Authorization: `Bearer ${user.token}`,
            },
          }
        )
        .then(function (response) {
          console.log(response)
          getTimeKeeping();
        });
    } catch (e) {
      console.log(`Bearer ${user.token}`);
    }
}

setInterval(() => {
  currentTime.value = getCurrentTime();
  currentDate.value = getCurrentDate();
}, 1000);
</script>
