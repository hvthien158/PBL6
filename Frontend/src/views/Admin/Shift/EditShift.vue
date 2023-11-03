<template>
  <main>
    <SlideBar></SlideBar>
    <div class="add-shift">
      <div class="input-container">
        <h1 v-if="!isUpdateShift">Thêm ca làm</h1>
        <h1 v-else>Chỉnh sửa ca làm</h1>
        <div class="form-floating">
          <input
            v-model="dataShift.name"
            type="text"
            class="form-control"
            :class="{
              'is-invalid': !dataShift.name,
              'is-valid': dataShift.name,
            }"
            placeholder="Tên ca làm"
            required
          />
        </div>
        <div class="invalid-feedback" v-if="!dataShift.name">
          Điền tên ca làm hợp lệ
        </div>
        <div class="form-floating">
          <input
            v-model="dataShift.timeValidCheckIn"
            type="text"
            class="form-control"
            placeholder="Thời gian check in"
            :class="{
              'is-invalid': !dataShift.timeValidCheckIn,
              'is-valid': checkTimeCheckIn == '',
            }"
            inputmode="time"
            required
          />
        </div>
        <div class="invalid-feedback">
          {{ checkTimeCheckIn }}
        </div>
        <div class="form-floating">
          <input
            v-model="dataShift.timeValidCheckOut"
            type="text"
            class="form-control"
            :class="{
              'is-invalid': !dataShift.timeValidCheckOut,
              'is-valid': checkTimeCheckOut == '',
            }"
            placeholder="Thời gian Check Out"
            required
          />
        </div>
        <div class="invalid-feedback">
          {{ checkTimeCheckOut }}
        </div>
        <div class="form-floating">
          <input
            v-model="dataShift.amount"
            type="number"
            class="form-control"
            :class="{
              'is-valid': !dataShift.amount,
              'is-valid': dataShift.amount,
            }"
            step="0.5"
            min="0"
            max="2"
            placeholder="Số công"
            required
          />
        </div>
        <div class="invalid-feedback" v-if="!dataShift.amount">
          Điền số công
        </div>
        <div class="btn-submit" v-if="!isUpdateShift">
          <button @click="createShift" type="button" class="btn btn-primary">
            Thêm ca làm mới
          </button>
        </div>
        <div class="btn-submit" v-else>
          <button @click="updateShift" type="button" class="btn btn-primary">
            Chỉnh sửa
          </button>
        </div>
      </div>
    </div>
  </main>
</template>
<style scoped>
main {
  min-height: 100vh;
  border-top: 0.1em solid black;
  box-sizing: border-box;
  display: flex;
}
.add-shift {
  width: 85vw;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 100px;
}
.input-container {
  width: 40%;
}
.add-shift h1 {
  text-align: center;
  margin-bottom: 30px;
}
.input-group-text {
  width: 25%;
}
.form-floating {
  margin-bottom: 10px;
}
.invalid-feedback {
  display: block;
  margin-bottom: 5px;
}
.btn-submit {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

</style>
<script setup>
import SlideBar from "../../../components/SlideBar.vue";
import axios from "axios";
import { reactive, onMounted, computed } from "vue";
import { useUserStore } from "../../../stores/user";
import { useRoute } from "vue-router";
import router from "../../../router";
const user = useUserStore().user;
const route = useRoute();
let isUpdateShift = false;
onMounted(() => {
  if (route.path == `/admin/update-shift/${route.params.id}`) {
    isUpdateShift = true;
    displayShift();
  } else isUpdateShift = false;
});
const dataShift = reactive({
  name: "",
  timeValidCheckIn: "",
  timeValidCheckOut: "",
  amount: 0,
});
const createShift = async () => {
  if (
    dataShift.name &&
    dataShift.checkTimeCheckIn &&
    dataShift.checkTimeCheckOut &&
    dataShift.amount
  ) {
    try {
      await axios
        .post(
          "http://127.0.0.1:8000/api/create-shift",
          {
            name: dataShift.name,
            timeValidCheckIn: dataShift.timeValidCheckIn,
            timeValidCheckOut: dataShift.timeValidCheckOut,
            amount: dataShift.amount,
          },
          {
            headers: { Authorization: `Bearer ${user.token}` },
          }
        )
        .then(function (response) {
          console.log(response);
          router.push({ path: "/admin/list-shift" });
        });
    } catch (e) {
      console.log(e);
    }
  }
};
const displayShift = async () => {
  try {
    await axios
      .get(`http://127.0.0.1:8000/api/shift/${route.params.id}`, {
        headers: { Authorization: `Bearer ${user.token}` },
      })
      .then(function (response) {
        console.log(response.data.data[0])
        if (response.status === 200) {
          let data = response.data.data[0];
          dataShift.name = data.name;
          dataShift.timeValidCheckIn = data.TimeValidCheckIn;
          dataShift.timeValidCheckOut = data.TimeValidCheckOut;
          dataShift.amount = data.amount;
        }
      });
  } catch (e) {
    console.log(e);
  }
};
const updateShift = async () => {
  try {
    await axios
      .put(
        `http://127.0.0.1:8000/api/update-shift/${route.params.id}`,
        {
          name: dataShift.name,
          timeValidCheckIn: dataShift.timeValidCheckIn,
          timeValidCheckOut: dataShift.timeValidCheckOut,
          amount: dataShift.amount,
        },
        {
          headers: { Authorization: `Bearer ${user.token}` },
        }
      )
      .then(function (response) {
        response.status == 200
          ? router.push({ path: "/admin/list-shift" })
          : console.log(response);
      });
  } catch (e) {
    console.log(e);
  }
};
const checkTimeCheckIn = computed(() => {
  if (!isTime(dataShift.timeValidCheckIn)) {
    return "Vui lòng nhập đúng định dạng(12:00:00)";
  } else {
    return ''
  }
});
const checkTimeCheckOut = computed(() => {
  if (!isTime(dataShift.timeValidCheckOut)) {
    return "Vui lòng nhập đúng định dạng(12:00:00)";
  } else {
    if (dataShift.timeValidCheckIn > dataShift.timeValidCheckOut) {
      return "Thời gian check in phải nhỏ hơn check out";
    } else {
      return "";
    }
  }
});
const isTime = (time) => {
  let filter = /^(?:[01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]+$/;
  return filter.test(time);
};
</script>
