<template>
  <div class="countdown">
    <div class="time-section" id="hours">
      <div class="time-group">
        <div class="time-segment">
          <div class="segment-display">
            <div class="segment-display__top"></div>
            <div class="segment-display__bottom"></div>
            <div class="segment-overlay">
              <div class="segment-overlay__top"></div>
              <div class="segment-overlay__bottom"></div>
            </div>
          </div>
        </div>
        <div class="time-segment">
          <div class="segment-display">
            <div class="segment-display__top"></div>
            <div class="segment-display__bottom"></div>
            <div class="segment-overlay">
              <div class="segment-overlay__top"></div>
              <div class="segment-overlay__bottom"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="dots">
      <div class="up-dots"></div>
      <div class="down-dots"></div>
    </div>
    <div class="time-section" id="minutes">
      <div class="time-group">
        <div class="time-segment">
          <div class="segment-display">
            <div class="segment-display__top"></div>
            <div class="segment-display__bottom"></div>
            <div class="segment-overlay">
              <div class="segment-overlay__top"></div>
              <div class="segment-overlay__bottom"></div>
            </div>
          </div>
        </div>
        <div class="time-segment">
          <div class="segment-display">
            <div class="segment-display__top"></div>
            <div class="segment-display__bottom"></div>
            <div class="segment-overlay">
              <div class="segment-overlay__top"></div>
              <div class="segment-overlay__bottom"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="dots">
      <div class="up-dots"></div>
      <div class="down-dots"></div>
    </div>
    <div class="time-section" id="seconds">
      <div class="time-group">
        <div class="time-segment">
          <div class="segment-display">
            <div class="segment-display__top"></div>
            <div class="segment-display__bottom"></div>
            <div class="segment-overlay">
              <div class="segment-overlay__top"></div>
              <div class="segment-overlay__bottom"></div>
            </div>
          </div>
        </div>
        <div class="time-segment">
          <div class="segment-display">
            <div class="segment-display__top"></div>
            <div class="segment-display__bottom"></div>
            <div class="segment-overlay">
              <div class="segment-overlay__top"></div>
              <div class="segment-overlay__bottom"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
* {
  box-sizing: border-box;
  font-family: "Helvetica Neue", Helvetica, sans-serif;
}


.countdown {
  width: 30%;
  display: flex;
  gap: 16px;
  font-family: sans-serif;
}

.time-section {
  text-align: center;
  font-size: 16px;
}

.time-group {
  display: flex;
  gap: 10px;
}

.time-segment {
  display: block;
  font-size: 90px;
  font-weight: 700;
  width: 90px;
}

.segment-display {
  position: relative;
  height: 100%;
  box-shadow: 2px 2px 2px 1px rgba(0, 0, 0, 0.2);
}

.segment-display__top,
.segment-display__bottom {
  overflow: hidden;
  text-align: center;
  width: 100%;
  height: 50%;
  position: relative;
}

.segment-display__top {
  line-height: 1.5;
  color: #ccc;
  background-color: #333;
}

.segment-display__bottom {
  line-height: 0;
  color: #ccc;
  background-color: #333;
}

.segment-overlay {
  position: absolute;
  top: 0;
  perspective: 300px;
  height: 100%;
  width: 89px;
}

.segment-overlay__top,
.segment-overlay__bottom {
  position: absolute;
  overflow: hidden;
  text-align: center;
  width: 100%;
  height: 50%;
}

.segment-overlay__top {
  top: 0;
  line-height: 1.5;
  color: #ccc;
  background-color: #333;
  transform-origin: bottom;
}

.segment-overlay__bottom {
  bottom: 0;
  line-height: 0;
  color: #ccc;
  background-color: #333;
  border-top: 1px solid black;
  transform-origin: top;
}

.segment-overlay.flip .segment-overlay__top {
  animation: flip-top 0.8s linear;
}

.segment-overlay.flip .segment-overlay__bottom {
  animation: flip-bottom 0.8s linear;
}

@keyframes flip-top {
  0% {
    transform: rotateX(0deg);
  }
  50%,
  100% {
    transform: rotateX(-90deg);
  }
}

@keyframes flip-bottom {
  0%,
  50% {
    transform: rotateX(90deg);
  }
  100% {
    transform: rotateX(0deg);
  }
}

.dots{
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.up-dots, .down-dots {
  height: 15px;
  width: 15px;
  background-color: #333;
  border-radius: 50%;
  margin: 6px 0;
}
</style>

<script setup>
import {onMounted, onUnmounted} from "vue";
import moment from "moment"

function getTimeSegmentElements(segmentElement) {
  const segmentDisplay = segmentElement.querySelector(
      '.segment-display'
  );
  const segmentDisplayTop = segmentDisplay.querySelector(
      '.segment-display__top'
  );
  const segmentDisplayBottom = segmentDisplay.querySelector(
      '.segment-display__bottom'
  );

  const segmentOverlay = segmentDisplay.querySelector(
      '.segment-overlay'
  );
  const segmentOverlayTop = segmentOverlay.querySelector(
      '.segment-overlay__top'
  );
  const segmentOverlayBottom = segmentOverlay.querySelector(
      '.segment-overlay__bottom'
  );

  return {
    segmentDisplayTop,
    segmentDisplayBottom,
    segmentOverlay,
    segmentOverlayTop,
    segmentOverlayBottom,
  };
}

function updateSegmentValues(
    displayElement,
    overlayElement,
    value
) {
  displayElement.textContent = value;
  overlayElement.textContent = value;
}

function updateTimeSegment(segmentElement, timeValue) {
  const segmentElements =
      getTimeSegmentElements(segmentElement);

  if (
      parseInt(
          segmentElements.segmentDisplayTop.textContent,
          10
      ) === timeValue
  ) {
    return;
  }

  segmentElements.segmentOverlay.classList.add('flip');

  updateSegmentValues(
      segmentElements.segmentDisplayTop,
      segmentElements.segmentOverlayBottom,
      timeValue
  );

  function finishAnimation() {
    segmentElements.segmentOverlay.classList.remove('flip');
    updateSegmentValues(
        segmentElements.segmentDisplayBottom,
        segmentElements.segmentOverlayTop,
        timeValue
    );

    this.removeEventListener(
        'animationend',
        finishAnimation
    );
  }

  segmentElements.segmentOverlay.addEventListener(
      'animationend',
      finishAnimation
  );
}

function updateTimeSection(sectionID, timeValue) {
  const firstNumber = Math.floor(timeValue / 10) || 0;
  const secondNumber = timeValue % 10 || 0;
  const sectionElement = document.getElementById(sectionID);
  const timeSegments =
      sectionElement.querySelectorAll('.time-segment');

  updateTimeSegment(timeSegments[0], firstNumber);
  updateTimeSegment(timeSegments[1], secondNumber);
}

function updateAllSegments() {
  updateTimeSection('seconds', moment().second());
  updateTimeSection('minutes', moment().minute());
  updateTimeSection('hours', moment().hour());
}

const interval = setInterval(() => {
  updateAllSegments();
}, 1000);

onMounted(() => {
  updateAllSegments();
})

onUnmounted(() => {
  clearInterval(interval)
})
</script>
