<template>
  <main>
    <div class="container">
      <div class="checkin-container">
        <h2>{{ user.name }}</h2>
        <div class="date">
          <span class="date-span">{{ currentDate }}</span>
        </div>
        <div class="time">
          <span id="time-span">{{ currentTime }}</span>
        </div>
        <div class="button-check">
          <button
            id="check-in-button"
            class="square-button"
            @click="handleCheckIn"
          >
            Check In
          </button>
          <button
            id="check-out-button"
            class="square-button"
            @click="handleCheckOut"
          >
            Check Out
          </button>
        </div>
      </div>
    </div>
  </main>
</template>
<style scoped>
main {
  max-width: 100vw;
  min-height: 80vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #f2f2f2;
}
.container {
  display: flex;
  justify-content: center;
  align-items: center;
}
.checkin-container {
  min-height: 20px;
  min-width: 30%;
  background-color: rgb(255, 255, 255);
  padding: 50px;
  margin-bottom: 100px;
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
}

.time {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}

.time img {
  max-height: 50px;
  margin-right: 10px;
}

.button-check {
  display: flex;
}

.square-button {
  width: 120px;
  height: 120px;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  cursor: pointer;
  margin: 10px;
}
#time-span {
  font-size: 30px;
}
</style>

<script setup>
import { ref, reactive, onMounted } from "vue";
import router from "../router";
import axios from "axios";
import {useUserStore} from "../stores/user";

const user = useUserStore().user
if (user.token === '') {
  router.push({ path: "/login" });
}
let checkLanding = reactive({
  isCheckedIn: true,
  isCheckedOut: true,
});
const currentDate = ref(getCurrentDate());
const currentTime = ref(getCurrentTime());

function getCurrentDate() {
  const now = new Date();
  const options = {
    day: "numeric",
    month: "short",
    year: "numeric",
  };
  return now.toLocaleString("vi-VN", options);
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
              checkLanding.isCheckedIn = true;
              checkLanding.isCheckedOut = true;
            } else {
              checkLanding.isCheckedIn = true;
              checkLanding.isCheckedOut = false;
            }
          } else {
            checkLanding.isCheckedIn = false;
            checkLanding.isCheckedOut = true;
          }
        }
      });
  } catch (e) {
    console.log(e);
  }
  updateDateAndButton();
};
function updateDateAndButton() {
  currentDate.value = getCurrentDate();

  const checkInButton = document.getElementById("check-in-button");
  const checkOutButton = document.getElementById("check-out-button");

  if (!checkLanding.isCheckedIn) {
    checkInButton.style.backgroundColor = "#007bff";
    checkInButton.style.color = "white";
    checkInButton.disabled = false;
  } else {
    checkInButton.style.backgroundColor = "";
    checkInButton.style.color = "";
    checkInButton.disabled = true;
  }

  if (checkLanding.isCheckedIn && !checkLanding.isCheckedOut) {
    checkOutButton.style.backgroundColor = "#007bff";
    checkOutButton.style.color = "white";
    checkOutButton.disabled = false;
  } else {
    checkOutButton.style.backgroundColor = "";
    checkOutButton.style.color = "";
    checkOutButton.disabled = true;
  }
}

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
    console.log(e);
  }
};

const handleCheckOut = async () => {
    try {
      await axios
        .put(
          `http://127.0.0.1:8000/api/check-out/`,
          {
            time: moment().format("YYYY-MM-DD HH:mm:ss"),
          },
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
      console.log(e);
    }
}

setInterval(() => {
  currentTime.value = getCurrentTime();
  updateDateAndButton();
}, 1000);
</script>
