<template>
  <main>
    <SlideBar></SlideBar>
    <div class="shift">
      <span style="font-size: 32px; font-weight: 700; text-align: center; ">Shift management</span>
      <div class="title-table">

        <div>
          <el-input v-model="dataSearch" placeholder="Type to search" />
        </div>
      </div>
      <el-table :data="shift" height="50vh" border stripe>
        <el-table-column prop="id" label="ID" min-width="50"></el-table-column>
        <el-table-column prop="name" label="Shift name" min-width="180"></el-table-column>
        <el-table-column prop="timeValidCheckIn" label="Time Valid Check In" min-width="300"></el-table-column>
        <el-table-column prop="timeValidCheckOut" label="Time Valid Check Out" min-width="300"></el-table-column>
        <el-table-column prop="amount" min-width="100" label="Amount"></el-table-column>
        <!-- <el-table-column fixed="right" label="Operations" width="140">
              <template #default="scope">
                <el-button link type="primary" @click="handleEdit(scope.row.id)">Edit</el-button>
                <el-button link type="danger" @click="handleDelete(scope.row.id)">Delete</el-button>
              </template>
            </el-table-column> -->
      </el-table>
      <div class="pagination">
        <el-button @click="previousPage" :disabled="currentPage === 1">
          Previous
        </el-button>
        <span>{{ currentPage }} / {{ totalPage }}</span>
        <el-button @click="nextPage" :disabled="currentPage === totalPage">
          Next
        </el-button>
      </div>
    </div>
    <!-- <ConfirmBox v-if="confirmBox" title="Are you sure?" msg="Delete this shift?" @confirm="deleteShift()"
          @cancel="confirmBox = false">
        </ConfirmBox> -->
    <!-- <div class="form-shift">
          <NewShift @updateData="displayShift" @invisible="visibleMode = false" :mode="operationMode"
            :shiftId="shiftId" v-if="visibleMode"></NewShift>
        </div> -->
  </main>
</template>
<style scoped>
main {
  box-sizing: border-box;
  display: flex;
}

.form-shift {
  margin-top: 50px;
  max-width: 938px;
}

.card-header {
  text-align: center;
  background-color: #f3952d;
  font-size: 20px;
  font-weight: 700;
}

.title-table {
  width: 100%;
  display: flex;
  justify-content: flex-end;
  margin: 10px 0 10px 0
}

.title-table div {
  display: flex;
  align-items: end;
  justify-content: center;
}

.shift {
  width: 80vw;
  margin-top: -40px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin-left: 20px;
}


.el-form {
  display: flex;
  justify-content: space-between;
}

.shift h1 {
  margin-bottom: 20px;
  text-align: center;
}

.table td {
  border: 1px solid #dee2e6;
}

.table td a {
  cursor: pointer;
}

.pagination {
  margin-top: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.pagination span {
  margin: 0 10px;
}
</style>
<script setup>
import SlideBar from "../../../components/SlideBar.vue";
import { ref, onMounted, watch} from "vue";
import { useUserStore } from "../../../stores/user";
import axios from "axios";
import router from "../../../router";
import { useAlertStore } from "../../../stores/alert";
import { saveAs } from "file-saver";
import { utils, write } from "xlsx";
import ConfirmBox from "../../../components/ConfirmBox.vue";
let dataSearch = ref('');
let currentPage = ref(1)
const totalPage = ref(1)
const debounceSearch = ref(null);
const user = useUserStore().user;
const alertStore = useAlertStore();
const shift = ref();
onMounted(() => {
  if (user.role !== "admin") {
    router.push({ path: "/" });
  }
  displayShift();
});

const displayShift = async () => {
  try {
    await axios
      .post(`http://127.0.0.1:8000/api/list-shift/${currentPage.value - 1}`, {
        name: dataSearch.value
      },
        {
          headers: { Authorization: `Bearer ${user.token}` }
        })
      .then(function (response) {
        shift.value = response.data.shift;
        totalPage.value = response.data.totalPage
      });
  } catch (e) {
    console.log(e);
  }
}
watch(dataSearch, () => {
  if (debounceSearch.value) {
    clearTimeout(debounceSearch.value);
  }
  debounceSearch.value = setTimeout(() => {
    currentPage.value = 1;
    displayShift();
  }, 500);
});
const nextPage = () => {
  if (currentPage.value < totalPage.value) {
    currentPage.value++;
  }
};
const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};
const messages = (type, msg) => {
  alertStore.alert = true;
  alertStore.type = type;
  alertStore.msg = msg;
};
</script>
