<template>
  <div class="audio-app">
    <div class="audio-container">
      <h2>Ghi âm</h2>
      <audio id="audio" controls></audio>
      <div class="record-controls">
        <button @click="startRecording" v-if="!isRecording" class="record-btn start-btn">Bắt đầu ghi âm</button>
        <button @click="stopRecording" v-if="isRecording" class="record-btn stop-btn">Dừng ghi âm</button>
        <button @click="uploadAudio" :disabled="!audioBlob" class="upload-btn">Tải lên</button>
      </div>
    </div>

    <div class="result-container">
      <h2>Kết quả</h2>
      <div v-if="result.length === 0" class="empty-result">Không có kết quả</div>
      <p v-for="item in result" :key="item" class="result-item">
        {{ item.replace('\"', '') }}
      </p>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios';
import {ref} from "vue";
import {getWaveBlob} from "webm-to-wav-converter";

let device = navigator.mediaDevices.getUserMedia({ audio: true });
let chunks = [];
let recorder;
const isRecording = ref(false);
const audioBlob = ref(null);
const wavBlob = ref(null);
const result = ref('');

const startRecording = () => {
  device.then((stream) => {
    recorder = new MediaRecorder(stream);

    recorder.ondataavailable = (e) => {
      chunks.push(e.data);

      if (recorder.state == 'inactive') {
        audioBlob.value = new Blob(chunks, { type: 'audio/wav; codecs=MS_PCM'});
        document.getElementById('audio').innerHTML =
            '<source src="' + URL.createObjectURL(audioBlob.value) + '" type="audio/wav"/>';
        getWaveBlob(audioBlob.value,false).then((blob) => {
          wavBlob.value = blob
        });
      }
    };
    recorder.start(1000);
    isRecording.value = true;
  });
};

const stopRecording = () => {
  if (recorder) {
    recorder.stop();
    isRecording.value = false;
  }
};

const uploadAudio = () => {
  console.log(wavBlob.value)
  if (wavBlob.value) {
    const formData = new FormData();
    formData.append('file', wavBlob.value, new Date().getMilliseconds() + '_audio.wav');

    axios
        .post('http://127.0.0.1:8001/upload/', formData)
        .then((response) => {
          result.value = ''
          result.value = response.data;
        })
        .catch((e) => {
          console.error(e);
        });
  }
};
</script>

<style scoped>
.audio-app {
  padding: 50px;
  display: flex;
  flex-direction: column;
}

.audio-container {
  background-color: #f0f0f0;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.record-controls button {
  margin-right: 10px;
}

.upload-btn {
  background-color: #4caf50;
  color: #fff;
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.upload-btn:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.result-container {
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
}

.empty-result {
  color: #777;
  text-align: center;
  font-style: italic;
}

.result-item {
  margin-bottom: 8px;
}

.record-btn {
  background-color: #3498db;
  color: #fff;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s ease;
}

.start-btn:hover {
  background-color: #2980b9;
}

.stop-btn:hover {
  background-color: #e74c3c;
}
</style>
