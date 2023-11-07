<template>
  <main>
    <SlideBar></SlideBar>
    <div class="add-shift">
      <div class="input-container">
        <h2 v-if="!isUpdateShift">Add Shift</h2>
        <h2 v-else>Edit Shift</h2>
        <el-form-item label="Shift name">
          <el-input v-model="dataShift.name" clearable />
        </el-form-item>
        <div class="invalid-feedback">
          {{ checkName }}
        </div>
        <el-form-item label="Time Valid Check In">
          <el-input v-model="dataShift.timeValidCheckIn" placeholder="Time Valid Check In" clearable />
        </el-form-item>
        <div class="invalid-feedback">
          {{ checkTimeCheckIn }}
        </div>
        <el-form-item label="Time Valid Check Out">
          <el-input v-model="dataShift.timeValidCheckOut" placeholder="Time Valid Check Out" clearable />
        </el-form-item>
        <div class="invalid-feedback">
          {{ checkTimeCheckOut }}
        </div>
        <el-form-item label="Amount">
          <el-input v-model="dataShift.amount" placeholder="amount" clearable />
        </el-form-item>
        <div class="invalid-feedback">
          {{ checkAmount }}
        </div>
        <div class="btn-submit" v-if="!isUpdateShift">
          <button @click="createShift" type="button" class="btn btn-primary">
            Add Shift
          </button>
        </div>
        <div class="btn-submit" v-else>
          <button @click="updateShift" type="button" class="btn btn-primary">
            Edit Shift
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
label {
  margin-bottom: 0px;
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

.add-shift h2 {
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
import { reactive, onMounted, computed, ref } from "vue";
import { useUserStore } from "../../../stores/user";
import { useRoute } from "vue-router";
import router from "../../../router";
import { useAlertStore } from "../../../stores/alert";
const user = useUserStore().user;
const route = useRoute();
const alertStore = useAlertStore();
let isUpdateShift = false;
onMounted(() => {
  if (route.path == `/admin/update-shift/${route.params.id}`) {
    isUpdateShift = true;
    displayShift();
  } else isUpdateShift = false;
});
const dataShift = reactive({
  name: null,
  timeValidCheckIn: null,
  timeValidCheckOut: null,
  amount: null,
});
const createShift = async () => {
  if (
    dataShift.name &&
    checkTimeCheckIn.value == '' &&
    checkTimeCheckOut.value == '' &&
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
          messages('success', response.data.message)
          router.push({ path: "/admin/list-shift" });
        });
    } catch (e) {
      console.log(e);
      messages('error', e.data.message)
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
        if (response.status == 200) {
          router.push({ path: "/admin/list-shift" })
          messages('success', response.data.message)
        }
      });
  } catch (e) {
    console.log(e);
    messages('error', e.data.message)
  }
};
const checkName = computed(() => {
  if (dataShift.name == null) {
    return ''
  } else {
    if (dataShift.name != '') {
      return ''
    } else {
      return `Please enter shift's name`
    }
  }
})
const checkAmount = computed(() => {
  if (dataShift.amount == null) {
    return ''
  } else {
    if (dataShift.amount != '') {
      return ''
    } else {
      return `Please enter shift's amount`
    }
  }
})
const checkTimeCheckIn = computed(() => {
  if (dataShift.timeValidCheckIn == null) {
    return "";
  } else {
    if (dataShift.timeValidCheckIn == '') {
      return `Please enter time checkin`
    } else if (isTime(dataShift.timeValidCheckIn)) {
      return ''
    }
    else {
      return `Please enter valid time checkout`
    }
  }
});
const checkTimeCheckOut = computed(() => {
  if (dataShift.timeValidCheckOut == null) {
    return "";
  } else {
    if (dataShift.timeValidCheckOut == '') {
      return `Please enter time checkout`
    } else if (isTime(dataShift.timeValidCheckOut)) {
      if (dataShift.timeValidCheckIn < dataShift.timeValidCheckOut) {
        return ''
      } else {
        return `Time checkout must be bigger than time checkin`
      }
    } else {
      return `Please enter valid time checkout`
    }
  }
});
const isTime = (time) => {
  let filter = /^(?:[01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]+$/;
  return filter.test(time);
};
const messages = (type, msg) => {
  alertStore.alert = true;
  alertStore.type = type;
  alertStore.msg = msg;
};
</script>
