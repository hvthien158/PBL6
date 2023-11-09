<template>
  <div class="timekeeping-management">
    <el-card>
      <div slot="header" class="card-header">
        Time Keeping
      </div>
      <div class="card-content">
        <el-form inline>
          <el-form-item label="For date">
            <el-date-picker v-model="dataSearch.startDate" type="date"
              placeholder="Select date"></el-date-picker>
          </el-form-item>
          <el-form-item label="To date">
            <el-date-picker v-model="dataSearch.endDate" type="date"
              placeholder="Select date"></el-date-picker>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="exportExcel">Export Excel</el-button>
            <el-button type="primary" @click="exportCSV">Export CSV</el-button>
          </el-form-item>
        </el-form>

        <el-table :data="getCurrentPageData" border stripe>
          <el-table-column prop="date" label="Date"></el-table-column>
          <el-table-column prop="timeCheckIn" label="Time Checkin"></el-table-column>
          <el-table-column prop="timeCheckOut" label="Time Checkout"></el-table-column>
          <el-table-column prop="timeWork" label="Time Work"></el-table-column>
          <el-table-column prop="shift.name" label="Shift"></el-table-column>
          <el-table-column label="Request">
            <template #default="scope">
              <el-button class="el-button--text">Edit</el-button>
            </template>
          </el-table-column>
        </el-table>
        <div class="pagination">
          <el-button @click="previousPage" :disabled="currentPage === 1">
            Previous
          </el-button>
          <span>{{ currentPage }} / {{ getTotalPage }}</span>
          <el-button @click="nextPage" :disabled="currentPage === getTotalPage">
            Next
          </el-button>
        </div>
      </div>
    </el-card>
  </div>
</template>

<style scoped>
.timekeeping-management {
  padding: 20px;
  display: flex;
  justify-content: center;
}

.el-form {
  margin-bottom: 20px;
}

.card-header {
  font-size: 18px;
  font-weight: bold;
}

.el-card {
  min-width: 50vw;
}

.card-content {
  margin-top: 20px;
}

.export {
  margin-top: 20px;
  margin-bottom: 20px;
  display: flex;
  justify-content: flex-end;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 10px;
}

.el-form-item {
  margin-bottom: 0;
}

.pagination span {
  margin: 0 10px;
}
</style>

<script setup>
import { saveAs } from "file-saver";
import { read, utils, write } from "xlsx";
import { ref, reactive, onMounted, defineProps, computed } from "vue";
import { useUserStore } from "../stores/user";
import axios from "axios";

const user = useUserStore().user;
let dataSearch = reactive({
  startDate: null,
  endDate: null,
});
let data = ref()
let currentPage = ref(1);
const pageSize = 10;

onMounted(() => {
  getListTimeKeeping();
});
const getListTimeKeeping = async () => {
  try {
    await axios
      .get("http://127.0.0.1:8000/api/get-list-timekeeping", {
        headers: {
          Authorization: `Bearer ${user.token}`,
        },
      })
      .then(function (response) {
        data.value = response.data.data;
      });
    dataSearch.startDate = null
    dataSearch.endDate = null
  } catch (e) {
    console.log(e);
  }
};
const formatDate = (date) => {
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  return `${year}-${month}-${day}`;
};
const filteredData = computed(() => {
  if (dataSearch.startDate && dataSearch.endDate) {
    const startDate = formatDate(dataSearch.startDate)
    const endDate = formatDate(dataSearch.endDate)
    return data.value.filter((item) => {
      const datePart = item.date.split("-");
      const itemDay = datePart[0]
      const itemMonth = datePart[1]
      const itemYear = datePart[2] 
      const itemDate = `${itemYear}-${itemMonth}-${itemDay}`;
      return (itemDate >= startDate && itemDate <= endDate)
    })
  }
  else {
    return data.value;
  }
});
const getTotalPage = computed(() => {
  if (!(dataSearch.startDate || dataSearch.endDate) && filteredData.value && data.value) {
    let countTotal = 0;
    let month = 0;
    let year = 0;
    data.value.filter((item) => {
      const datePart = item.date.split("-");
      const itemMonth = datePart[1]
      const itemYear = datePart[2]
      if (itemMonth != month || itemYear != year) {
        countTotal += 1
        month = itemMonth
        year = itemYear
      }
    })
    return countTotal
  } else {
    if (filteredData.value) {
      const filteredDataLength = filteredData.value.length;
      return Math.ceil(filteredDataLength / getPageSize.value);
    }
  }
})
let temp = 0
const getDistinctMonthsAndYears = () => {
  const distinctMonthsAndYears = [];
  let topTime = 1
  data.value.forEach((item) => {
    const datePart = item.date.split("-");
    const itemMonth = parseInt(datePart[1]);
    const itemYear = parseInt(datePart[2]);
    const monthYear = { month: itemMonth, year: itemYear, time : topTime };
    const exists = distinctMonthsAndYears.some(
      (my) => my.month === itemMonth && my.year === itemYear
    );

    if (!exists) {
      distinctMonthsAndYears.push(monthYear);
      topTime += 1
      console.log(monthYear)
    }
  });

  return distinctMonthsAndYears;
};

const getPageSize = computed(() => {
  if (currentPage.value && data.value && !(dataSearch.startDate || dataSearch.endDate)) {
    const distinctMonthsAndYears = getDistinctMonthsAndYears();

    const currentPageItem = distinctMonthsAndYears.find((item) => item.time === currentPage.value);
    if (currentPageItem) {
      const currentMonth = currentPageItem.month;
      const currentYear = currentPageItem.year;
      let countPageSize = 0;
      data.value.forEach((item) => {
        const datePart = item.date.split("-");
        const itemMonth = parseInt(datePart[1]);
        const itemYear = parseInt(datePart[2]);
        console.log(itemMonth, itemYear)
        if (itemMonth === currentMonth && itemYear === currentYear) {
          countPageSize += 1; 
        }
      });
      console.log(countPageSize)
      return countPageSize;
    }
  }

  return pageSize;
});
let tempEndIndex
const getCurrentPageData = computed(() => {
  if (filteredData.value) {
    let startIndex = 0
    if(currentPage.value == 1){
      startIndex = 0
    } else {
      startIndex = tempEndIndex
    }
    let endIndex = startIndex + getPageSize.value;
    tempEndIndex = endIndex
    
    return filteredData.value.slice(startIndex, endIndex);
  }
});
const nextPage = () => {
  if (currentPage.value < getTotalPage.value) {
    currentPage.value++;
  }
};
const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    tempEndIndex = tempEndIndex - getPageSize.value 
  }
};
//
const exportExcel = () => {
  const excelData = filteredData.value.map((item) => {
    return {
      id: item.id,
      date: item.date,
      timeCheckIn: item.timeCheckIn,
      timeCheckOut: item.timeCheckOut,
      timeWork: item.timeWork,
      ShiftName: item.shift.name,
      ShiftAmount: item.shift.amount,
    };
  });
  const worksheet = utils.json_to_sheet(excelData);
  const workbook = utils.book_new();
  utils.book_append_sheet(workbook, worksheet, "Timekeeping");
  const excelBuffer = write(workbook, {
    bookType: "xlsx",
    type: "array",
  });
  const dataBlob = new Blob([excelBuffer], {
    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
  });
  saveAs(dataBlob, "timekeeping.xlsx");
};

const exportCSV = () => {
  let csvContent = "data:text/csv;charset=utf-8,";
  const headers = [
    "id",
    "date",
    "timeCheckIn",
    "timeCheckOut",
    "timeWork",
    "Shiftname",
    "Shiftamount",
  ];
  csvContent += headers.join(",") + "\n";
  filteredData.value.forEach((item) => {
    const row = headers
      .map((header) => {
        if (header.includes("Shift")) {
          const shiftField = header.replace("Shift", "");
          return item.shift[shiftField];
        } else {
          return item[header];
        }
      })
      .join(",");
    csvContent += row + "\n";
  });

  const encodedUri = encodeURI(csvContent);
  const link = document.createElement("a");
  link.setAttribute("href", encodedUri);
  if (!(dataSearch.startDate || dataSearch.endDate)) {
    link.setAttribute("download", `${user.name}.csv`);
  } else {
    link.setAttribute("download", `${user.name}_${dataSearch.startDate}_${dataSearch.endDate}.csv`);
  }
  link.click();
};
</script>