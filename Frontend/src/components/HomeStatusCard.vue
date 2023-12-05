<template>
  <div>
    <el-card class="box-card">
      <div style="border-bottom: 1px solid #ccc">
        Department:
        <el-select v-model="value" class="m-2" placeholder="Department" size="small">
          <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value" />
        </el-select>
      </div>
      <div class="card-content">
        <div class="user">
          <div v-for="item in data">
            <el-avatar style="margin-right: 12px" :src="item.avatar" :size="40" />
            <div style="height: 20px; display: flex; flex-direction: column; justify-content: space-between">
              <span v-if="item.id !== user.id"
                style="font-size: 14px; margin-top: -12px; font-weight: 600; color: #6A679E"> {{ item.id + ' - ' + item.name
                }}</span>
              <span v-else style="font-size: 14px; margin-top: -12px; font-weight: 600; color: #6A679E">{{ item.id }} -
                You</span>
              <div style="display: flex">
                <div :class="bg_color[item.status_AM]" style="height: 6px; width: 40px; margin-right: 1px;"></div>
                <div :class="bg_color[item.status_PM]" style="height: 6px; width: 40px;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="pagination">
        <el-button size="small" @click="previousPage" :disabled="currentPage === 1">
          Prev
        </el-button>
        <!--        <span style="margin: 0 10px">{{ currentPage }} / {{ totalPage }}</span>-->
        <Pagination :current_page_prop="currentPage" :total_page_prop="totalPage"
          @change-page="(page) => currentPage = page"></Pagination>
        <el-button size="small" @click="nextPage" :disabled="currentPage === totalPage">
          Next
        </el-button>
      </div>
    </el-card>
  </div>
</template>

<style scoped>
.box-card {
  border-radius: 12px;
}

.card-content {
  height: 50vh;
  width: 100%;
  overflow-y: scroll;
}

.card-content::-webkit-scrollbar {
  width: 6px;
  background-color: white;
}

.card-content:hover::-webkit-scrollbar-thumb {
  width: 6px;
  background-color: #ababab;
  border-radius: 12px;
}

.user>div {
  display: flex;
  align-items: center;
  margin-top: 12px;
  padding-bottom: 8px;
}

.user>div:not(:last-child) {
  border-bottom: 1px solid var(--el-border-color);
}

.pagination {
  margin-top: 10px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.bg-green {
  background-color: #04fc43;
}

.bg-purple {
  background-color: rgba(0, 120, 248, 0.57);
}

.bg-gray {
  background-color: #ccc;
}
</style>

<script setup>
import { ref, watch } from "vue";
import { useUserStore } from "../stores/user";
import { useStatusStore } from "../stores/status";
import Pagination from "./Pagination.vue";
import DepartmentAPI from "../services/DepartmentAPI";
import UserAPI from "../services/UserAPI";

const value = ref('')

const options = ref([])
const data = ref()
const department = ref([])
const currentPage = ref(1);
const totalPage = ref(1);
const user = useUserStore().user
const bg_color = ref(['bg-green', 'bg-purple', 'bg-gray'])
const status = useStatusStore()
const displayDepartment = async () => {
  try {
    await DepartmentAPI.getAllDepartment()
      .then(function (response) {
        department.value = response.data.data;
        value.value = department.value[0].id
        department.value.forEach((department) => {
          options.value.push({
            value: department.id,
            label: department.name,
          })
        })
      });
  } catch (e) {
    console.log(e)
  }

}
displayDepartment()
watch(() => value.value, () => {
  displayUser()
})

const displayUser = async () => {
  try {
    await UserAPI.getUserInDepartment(user.token, currentPage.value - 1, value.value)
        .then(function (response) {
          data.value = response.data.user
          totalPage.value = Math.ceil(response.data.totalUser / 8) === 0 ? 1 : Math.ceil(response.data.totalUser / 8)
        });
  } catch (e) {
    console.log(e);
  }
};

watch(() => currentPage.value, () => {
  displayUser()
})

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
</script>
